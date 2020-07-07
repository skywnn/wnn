<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 22:45
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\SpaceModel;
use app\admin\model\SpaceCateModel;
use app\admin\model\SpaceOrderModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Space extends Base
{
//*********************************************办公空间管理*********************************************//
    /**
     * [spaceIndex 办公空间列表]
     */
    public function spaceIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('space')->alias('r')->where($map)->count();//计算总页面
            $space = new SpaceModel();
            $lists = $space->getSpaceByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('space_index');
    }

    /**
     * [add_space 添加办公空间]
     */
    public function addSpace()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $space = new SpaceModel();
            $flag = $space->insertSpace($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new SpaceCateModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_space',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_space 编辑办公空间]
     */
    public function editSpace()
    {
        $space = new SpaceModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $space->editSpace($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new SpaceCateModel();
        $data = $space->getOneSpace($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_space');
    }
    /**
     * [spacePic 模板图片]
     */
    public function spacePic($id){
        $space = new SpaceModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $space->editSpace($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $space->getOneSpace($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('space_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Space_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $space = new SpaceModel();
            if(!empty($id)){
                $bra = $space->getOneSpace($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $space->editSpace($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_space 删除办公空间]
     */
    public function delSpace()
    {
        $id = input('param.id');
        $space = new SpaceModel();
        $flag = $space->delSpace($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [space_state 办公空间状态]
     */
    public function spaceState()
    {
        extract(input());
        $space = new SpaceModel();
        $flag = $space->spaceState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subSpace 某一分类下的办公空间列表]
     */
    public function subSpace(){

        $cate_id = input('param.id');
        $map = [];
        $map[] = ['cate_id','=',$cate_id];
        $space = new SpaceModel();

        if(request()->isAjax ()){
            extract(input());
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
            $count = Db::name('space')->alias('r')->where($map)->count();//计算总页面
            $lists = $space->getSpaceByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('space_index');
    }
    /**
     * [spaceAll 办公空间列表]
     */
    public function spaceAll(){
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
            $count = Db::name('space')->alias('r')->where($map)->count();//计算总页面
            $space = new SpaceModel();
            $lists = $space->getSpaceByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('space_all');
    }
    /**
     * [activityAll 订单列表]
     */
    public function spaceOrder(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['title','like','%'.$keyword.'%'];
            }
            $map[] = ['con_name','=','会议室预定'];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od= $field." ".$order;
            }else{
                $od="create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('order')->alias('r')->where($map)->count();//计算总页面
            $order = new SpaceOrderModel();
            $lists = $order->getSpaceOrderByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('space_order');
    }
    /**
     * [activityAll 订单审核]
     */
    public function auditSpace(){
        extract(input());
        $order = new SpaceOrderModel();
        $flag = $order->spaceOrderState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [readActivityOrder 订单查看]
     */
    public function readSpaceOrder(){
        $id = input('param.id');
        $order = new SpaceOrderModel();
        $flag = $order->getOneSpaceOrder($id);
        $this->assign('item',$flag);
        return $this->fetch('read_space_order');
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
            $map[] = ['company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $cate = new SpaceCateModel();
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
            $cate = new SpaceCateModel();
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
        $cate = new SpaceCateModel();
        if(request()->isPost()){
            $param = input('param.');
            dump($param);die;
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch();
    }

    /**
     * [cateIcon 上传分类图标]
     */
    public function cateIcon($id){
        $cate = new SpaceCateModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        $icon['width'] = Config::get('app.upload_image.iconWidth');
        $icon['height'] = Config::get('app.upload_image.iconHeight');
        $this->assign('icon',$icon);
        return $this->fetch('cate_icon');
    }
    /**
     * [uplaodIcon 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'SpaceCate_icon';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $cate = new SpaceCateModel();
            if(!empty($id)){
                $bra = $cate->getOneCate($id);
                $oldurl=$bra['icon'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'icon' => $pic['data'],
                'id' => $id,
            );
            $flag = $cate->editCate($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_cate 删除分类]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new SpaceCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new SpaceCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}