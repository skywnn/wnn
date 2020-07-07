<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 15:17
 */

namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\AreaModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\db;
class Area extends Base
{
//*********************************************行政区划管理*********************************************//

    /**
     * [areaIndex 行政区域列表]
     */
    public function areaIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['company_id','=',session('company_id')];
            $map[] = ['parent_id','=',1];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $area = new AreaModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $area ->getCountArea($map);  //总数据数目
            $lists = $area ->getAreaByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("area_index");
    }
    /**
     * [add 添加行政区域]
     */
    public function addArea()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $area = new AreaModel();
            $flag = $area->insertArea($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_area');
    }

    /**
     * [edit 编辑行政区域]
     */
    public function editArea()
    {
        $area = new AreaModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $area->editArea($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneArea = $area->getOneArea($id);
        $this->assign('item',$oneArea);
        return $this->fetch('edit_area');
    }
    /**
     * [delete 删除行政区域]
     */
    public function delArea()
    {
        $id = input('param.id');
        $area = new AreaModel();
        $flag = $area->delArea($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [area_state 行政区域状态]
     */
    public function areaState()
    {
        extract(input());
        $area = new AreaModel();
        $flag = $area->areaState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subareaIndex 子行政区域列表]
     */
    public function subareaIndex(){
        $id = input('param.id');
        $area = new AreaModel();
        $oneArea = $area->getOneArea($id);
        $oneArea['level'] += 1;
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['parent_id','=',$id];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $area ->getCountArea($map);  //总数据数目
            $lists = $area ->getAreaByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('item',$oneArea);
        return $this->fetch("subarea_index");
    }
    /**
     * [addSubarea 添加子行政区域]
     */
    public function addSubarea()
    {
        $area = new AreaModel();
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $flag = $area->insertArea($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $level = input('param.level');
        $oneArea = $area->getOneArea($id);
        $oneArea['level'] = $level;
        $this->assign('item',$oneArea);
        return $this->fetch('add_subarea');
    }

    /**
     * [picArea 设置行政区域图片]
     */
    public function picArea($id){
        $area = new AreaModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $area->editArea($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $area->getOneArea($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('pic_area');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Area_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $area = new AreaModel();
            if(!empty($id)){
                $bra = $area->getOneArea($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $area->editArea($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
}