<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 10:46
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ActivityModel;
use app\admin\model\ActivityCateModel;
use app\admin\model\OrderModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Activity extends Base
{
//*********************************************活动管理*********************************************//
    /**
     * [activityIndex 活动列表]
     */
    public function activityIndex(){
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
            $count = Db::name('activity')->alias('r')->where($map)->count();//计算总页面
            $activity = new ActivityModel();
            $lists = $activity->getActivityByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('activity_index');
    }

    /**
     * [add_activity 添加活动]
     */
    public function addActivity()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $activity = new ActivityModel();
            $flag = $activity->insertActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $cate = new ActivityCateModel();
        $area = new \app\common\place\Area;
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_activity',['province'=>$area->province(),'cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_activity 编辑活动]
     */
    public function editActivity()
    {
        $activity = new ActivityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $activity->editActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new ActivityCateModel();
        $data = $activity->getOneActivity($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        $area = new \app\common\place\Area;
        $this->assign('province',$area->province());
        return $this->fetch('edit_activity');
    }
    /**
     * [activityPic 模板图片]
     */
    public function activityPic($id){
        $activity = new ActivityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $activity->editActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $activity->getOneActivity($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('activity_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Activity_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $activity = new ActivityModel();
            if(!empty($id)){
                $bra = $activity->getOneActivity($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $activity->editActivity($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_activity 删除活动]
     */
    public function delActivity()
    {
        $id = input('param.id');
        $activity = new ActivityModel();
        $flag = $activity->delActivity($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [activity_state 活动状态]
     */
    public function activityState()
    {
        extract(input());
        $activity = new ActivityModel();
        $flag = $activity->activityState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subActivity 某一分类下的活动列表]
     */
    public function subActivity(){

        $cate_id = input('param.id');
        $map = [];
        $map[] = ['cate_id','=',$cate_id];
        $activity = new ActivityModel();

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
            $count = Db::name('activity')->alias('r')->where($map)->count();//计算总页面
            $lists = $activity->getActivityByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('activity_index');
    }
    /**
     * [activityAll 活动列表]
     */
    public function activityAll(){
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
            $count = Db::name('activity')->alias('r')->where($map)->count();//计算总页面
            $activity = new ActivityModel();
            $lists = $activity->getActivityByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('activity_all');
    }
    /**
     * [activityAll 活动订单列表]
     */
    public function activityOrder(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['title','like','%'.$keyword.'%'];
            }
            $map[] = ['con_name','=','活动'];
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
            $order = new OrderModel();
            $lists = $order->getOrderByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('activity_order');
    }
    /**
     * [activityAll 活动订单审核]
     */
    public function auditActivity(){
        extract(input());
        $order = new OrderModel();
        $flag = $order->orderState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [readActivityOrder 活动订单审核]
     */
    public function readActivityOrder(){
        $id = input('param.id');
        $order = new OrderModel();
        $flag = $order->getOneOrder($id);
        $this->assign('item',$flag);
        return $this->fetch('read_activity_order');
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
            $cate = new ActivityCateModel();
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
            $cate = new ActivityCateModel();
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
        $cate = new ActivityCateModel();
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
     * [cateIcon 上传分类图标]
     */
    public function cateIcon($id){
        $cate = new ActivityCateModel();
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
            $pathNewName = 'ActivityCate_icon';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $cate = new ActivityCateModel();
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
        $cate = new ActivityCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new ActivityCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}