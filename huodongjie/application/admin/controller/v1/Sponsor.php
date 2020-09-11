<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\SponsorModel;
use app\admin\model\SponsorTrendsModel;
use app\admin\service\Message;
use app\common\upload\Uplaod;
use think\Db;

class Sponsor extends Base
{
    /**
     * [sponsorIndex 主办方列表]
     */
    public function sponsorIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的公司需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $sponsor = new SponsorModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $sponsor->getCountSponsor($map);//计算总条数
            $lists = $sponsor->getSponsorByWhere($map, $nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("sponsor_index");
    }

    /**
     * [addSponsor 添加主办方]developer
     */
    public function addSponsor()
    {
        if(request()->isPost()){
            $param = input('post.');
            $data = [];
            if (!empty($_FILES['pic']['name'])) {
                $data['pic'] = $this->request->file('pic');
            }
            if (!empty($_FILES['logo']['name'])) {
                $data['logo'] = $this->request->file('logo');
            }
            if (!empty($data)) {
                $uplaod = new Uplaod();
                foreach ($data as $k=>$v) {
                    switch ($k) {
                        case 'logo':
                            $logo = $uplaod
                                ->picArrayUplaod($v,'Sponsor_logo',true,uniqid(),400,400);
                            $param['logo'] = $logo['data'];
                            break;
                        case 'pic':
                            $pic = $uplaod
                                ->picArrayUplaod($v,'Sponsor_pic',true,uniqid(),400,400);
                            $param['pic'] = $pic['data'];
                            break;
                    }
                }
            }
            $sponsor = new SponsorModel();
            $flag = $sponsor->insertSponsor($param);
            if ($flag['code']==200) {
                echo "<script>history.go(-2)</script>";die();
            } else {
                $message = new Message();
                exit($message->alert_errors($flag['msg']));
            }
        }
        return $this->fetch("add_sponsor");
    }
    /**
     * [editSponsor 编辑主办方]
     */
    public function editSponsor()
    {
        $sponsor = new SponsorModel();
        if(request()->isPost()){
            $param = input('post.');
            $data = [];
            if (!empty($_FILES['pic']['name'])) {
                $data['pic'] = $this->request->file('pic');
            }
            if (!empty($_FILES['logo']['name'])) {
                $data['logo'] = $this->request->file('logo');
            }
            if (!empty($data)) {
                $uplaod = new Uplaod();
                foreach ($data as $k=>$v) {
                    switch ($k) {
                        case 'logo':
                            $logo = $uplaod
                                ->picArrayUplaod($v,'Sponsor_logo',true,$param['id'],400,400);
                            $param['logo'] = $logo['data'];
                            if (!empty($param['id'])) {
                                $item = Db::name('sponsor')->where('id',$param['id'])->find();
                                $oldurl=$item['logo'];
                                @unlink('static'.$oldurl);
                            }
                            break;
                        case 'pic':
                            $pic = $uplaod
                                ->picArrayUplaod($v,'Sponsor_pic',true,$param['id'],400,400);
                            $param['pic'] = $pic['data'];
                            if (!empty($param['id'])) {
                                $item = Db::name('sponsor')->where('id',$param['id'])->find();
                                $oldurl=$item['pic'];
                                @unlink('static'.$oldurl);
                            }
                            break;
                    }
                }
            }
            $flag = $sponsor->editSponsor($param);
            if ($flag['code']==200) {
                echo "<script>history.go(-2)</script>";die();
            } else {
                $message = new Message();
                exit($message->alert_errors($flag['msg']));
            }
        }
        $id = input('param.id');
        $item = $sponsor->getOneSponsor($id);
        $this->assign('sponsor',$item);
        $ids = explode(',',$item['member_ids']);
        $names = Db::name('member')->whereIn('id',$ids)->column('nick_name');
        $nameList = implode('  ',$names);
        $this->assign('nameList',$nameList);
        return $this->fetch("edit_sponsor");
    }

    public function selSponsor(){
        $id = input('param.id');
        $sponsor = new SponsorModel();
        $item = $sponsor->where('id',$id)->find();
        $ids = explode(',',$item['member_ids']);
        $members = Db::name('member')->whereIn('id',$ids)->column('nick_name');
        $memberName = implode(' ',$members);
        $this->assign('memberName',$memberName);
        $this->assign('item',$item);
        return $this->fetch('sponsor_detail');
    }


    /**
     * [delSponsor 删除主办方]
     */
    public function delSponsor()
    {
        $id = input('param.id');
        $sponsor = new SponsorModel();
        $flag = $sponsor->delSponsor($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [sponsorState 主办方状态]
     */
    public function sponsorState()
    {
        extract(input());
        $sponsor = new SponsorModel();
        $flag = $sponsor->sponsorState($id, $num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    //***************************************动态管理*********************************************

    /**
     * 动态列表
     */
    public function trendsIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['st.title','like','%'.$keyword.'%'];
            }
            //属于商家编辑的公司需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="st.".$field." ".$order;
            }else{
                $od="st.create_time desc";
            }
            $map[] = ['st.sponsor_id','=',input('param.id')];
            $sponsor = new SponsorModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('sponsor_trends')->alias('st')->where($map)->count();
            $lists = Db::name('sponsor_trends st')
                ->join('sponsor s','s.id=st.sponsor_id')
                ->join('member m','m.id=st.member_id')
                ->where($map)->field('st.*,m.nick_name')->select();
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $id = input('param.id');
        $item = Db::name('sponsor')->where('id',$id)->find();
        $this->assign('item',$item);
        return $this->fetch("trends_index");
    }

    /**
     * 动态详情
     */
    public function trendsDetail(){
        $id = input('param.id');
        $trends = new SponsorTrendsModel();
        $item = $trends->alias('t')
            ->join('member m','m.id=t.member_id')
            ->field('m.nick_name,t.*')
            ->where('t.id',$id)
            ->find();
        $pics = Db::name('sponsor_trends_pic')->where('trends_id',$id)->select();
        $this->assign('pics',$pics);
        $this->assign('item',$item);
        return $this->fetch('trends_detail');
    }

    /**
     * 修改动态状态
     */
    public function trendsState(){
        if (request()->isPost()) {
            $param = input('post.');
            $trends = new SponsorTrendsModel();
            $flag = $trends->trendsState($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }
}