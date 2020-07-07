<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\OpportunityModel;
use think\Db;

class Opportunity extends Base
{
    /**
     * [opportunityIndex 商机列表]
     */
    public function opportunityIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['o.title','like','%'.$keyword.'%'];
            }
            $map[] = ['o.company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="o.".$field." ".$order;
            }else{
                $od="o.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('crm_opportunity')->alias('o')->where($map)->count();//计算总页面
            $activity = new OpportunityModel();
            $lists = $activity->getOpportunityByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('opportunity_index');
    }

    /**
     * [edit_opportunity 编辑商机]
     */
    public function editOpportunity()
    {
        $opportunity = new OpportunityModel();
        if(request()->isPost()){
            $param = input('post.');
            $param['planned_time'] = strtotime($param['planned_time']);
            $flag = $opportunity->editOpportunity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $res = Db::name('goods')->where('company_id',session('company_id'))->select();
        $this->assign('goods',$res);
        $data = $opportunity->getOneOpportunity($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('item',$data);
        $area = new \app\common\place\Area;
        $this->assign('province',$area->province());
        return $this->fetch('edit_opportunity');
    }


    /**
     * [del_activity 删除活动]
     */
    public function delOpportunity()
    {
        $id = input('param.id');
        $activity = new OpportunityModel();
        $flag = $activity->delOpportunity($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }




}