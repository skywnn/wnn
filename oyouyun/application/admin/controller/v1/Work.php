<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 20:36
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\WorkCateModel;
use app\admin\model\WorkOrderModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\db;
class Work extends Base
{
//*********************************************工单管理*********************************************//
    /**
     * [workIndex 工单列表]
     */
    public function workIndex(){
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
            $count = Db::name('work_order')->alias('r')->where($map)->count();//计算总页面
            $workOrder = new WorkOrderModel();
            $lists = $workOrder->getWorkOrderByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('work_index');
    }

    /**
     * [add_work 添加工单]
     */
    public function addWork()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $workOrder = new WorkOrderModel();
            $flag = $workOrder->insertWorkOrder($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new WorkCateModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_work',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_work 编辑工单]
     */
    public function editWork()
    {
        $workOrder = new WorkOrderModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $workOrder->editWorkOrder($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new WorkCateModel();
        $data = $workOrder->getOneWorkOrder($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_work');
    }
    /**
     * [workPic 模板图片]
     */
    public function workPic(){
        $workOrder = new WorkOrderModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $workOrder->editWorkOrder($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $workOrder->getOneWorkOrder($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('work_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic(){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'WorkOrder_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $workOrder = new WorkOrderModel();
            if(!empty($id)){
                $bra = $workOrder->getOneWorkOrder($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $workOrder->editWorkOrder($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_work 删除工单]
     */
    public function delWork()
    {
        $id = input('param.id');
        $workOrder = new WorkOrderModel();
        $flag = $workOrder->delWorkOrder($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [work_state 工单状态]
     */
    public function workState()
    {
        extract(input());
        $workOrder = new WorkOrderModel();
        $flag = $workOrder->workOrderState($id,$num);
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
            $map[] = ['company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $cate = new WorkCateModel();
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
            $cate = new WorkCateModel();
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
        $cate = new WorkCateModel();
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
        $cate = new WorkCateModel();
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
            $pathNewName = 'WorkCate_icon';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $cate = new WorkCateModel();
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
        $cate = new WorkCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new WorkCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}