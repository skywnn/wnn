<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:38
 */

namespace app\admin\controller\v1;
use app\admin\controller\Base;
use app\admin\model\AdverCateModel;
use app\admin\model\AdverModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\db;
class Adver extends Base
{
//*********************************************广告管理*********************************************//
    /**
     * [adverIndex 广告列表]
     */
    public function adverIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('adver')->alias('r')->where($map)->count();//计算总页面
            $adver = new AdverModel();
            $lists = $adver->getAdverByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('adver_index');
    }

    /**
     * [add_adver 添加广告]
     */
    public function addAdver()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $adver = new AdverModel();
            $flag = $adver->insertAdver($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new AdverCateModel();
        $map[] = ['status','=',1];
        return $this->fetch('add_adver',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_adver 编辑广告]
     */
    public function editAdver()
    {
        $adver = new AdverModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $adver->editAdver($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new AdverCateModel();
        $data = $adver->getOneAdver($id);
        $map[] = ['status','=',1];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_adver');
    }
    /**
     * [adverPic 模板图片]
     */
    public function adverPic($id){
        $adver = new AdverModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $adver->editAdver($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $adver->getOneAdver($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.thumbWidth');
        $pic['height'] = Config::get('app.upload_image.thumbHeight');
        $this->assign('pic',$pic);
        return $this->fetch('adver_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Adver_pic';
            $width = Config::get('app.upload_image.thumbWidth');
            $height = Config::get('app.upload_image.thumbHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $adver = new AdverModel();
            if(!empty($id)){
                $bra = $adver->getOneAdver($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $adver->editAdver($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_adver 删除广告]
     */
    public function delAdver()
    {
        $id = input('param.id');
        $adver = new AdverModel();
        $flag = $adver->delAdver($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [adver_state 广告状态]
     */
    public function adverState()
    {
        extract(input());
        $adver = new AdverModel();
        $flag = $adver->adverState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }


    //*********************************************分类管理*********************************************//

    /**
     * [index_cate 分类列表]
     */
    public function cateIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $cate = new AdverCateModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $cate->getCountCate($map);//计算总条数
            $lists = $cate->getCateByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('cate_index');
    }

    /**
     * [add_cate 添加分类]
     */
    public function addCate()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new AdverCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_cate');
    }

    /**
     * [edit_cate 编辑分类]
     */
    public function editCate()
    {
        $cate = new AdverCateModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch();
    }

    /**
     * [del_cate 删除分类]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new AdverCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new AdverCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}