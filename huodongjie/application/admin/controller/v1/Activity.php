<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:10
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ActivityModel;
use app\admin\model\ActivityCateModel;
use app\admin\model\ActivitySceneModel;
use app\admin\model\OrderModel;
use app\common\place\Area;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Activity extends Base
{
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
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $article = new ActivityModel();
            $count = $article->alias('r')->where($map)->count();
            //$map[] = ['gen_cate','=',input('cate_id')];
            $lists = $article->getActivityByWhere($map, $Nowpage, $limits,$od);
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
            $article = new ActivityModel();
            $flag = $article->insertActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cate = Db::name('activity_cate')->select();
        $this->assign('cate',$cate);
        $area = new Area();
        $province = $area->province();
        $this->assign('province',$province);
        $company = Db::name('company')->select();
        $this->assign('company',$company);
        return $this->fetch('add_activity');
    }

    /**
     * [edit_activity 编辑活动]
     */
    public function editActivity()
    {
        $activity = new ActivityModel();
        if(request()->isPost()){
            $param = input('post.');
            //dump($param);die();
            $flag = $activity->editActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $activity->getOneActivity($id);
        $this->assign('item',$data);
        $cate = Db::name('activity_cate')->select();
        $this->assign('cate',$cate);
        $area = new Area();
        $province = $area->province();
        $this->assign('province',$province);
        $city = Db::name('area')->where(['name'=>$data['city'],'level'=>2])->find();
        $citys = Db::name('area')->where('parent_id',$city['parent_id'])->select();
        $this->assign('citys',$citys);
        $district = Db::name('area')->where(['name'=>$data['district'],'level'=>3])->find();
        $districts = Db::name('area')->where('parent_id',$district['parent_id'])->select();
        $this->assign('districts',$districts);
        $company = Db::name('company')->select();
        $this->assign('company',$company);
        return $this->fetch('edit_activity');
    }

    /**
     * [activityPic 模板图片]
     */
    public function activityPic($id){
        $article = new ActivityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneActivity($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 410;
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
            $width = 750;
            $height = 410;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new ActivityModel();
            if(!empty($id)){
                $bra = $article->getOneActivity($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editActivity($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
    /**
     * [activityPic 模板图片]
     */
    public function activityIcon($id){
        $article = new ActivityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editActivity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneActivity($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 310;
        $this->assign('pic',$pic);
        return $this->fetch('activity_thum_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Activity_icon';
            $width = 750;
            $height = 310;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new ActivityModel();
            if(!empty($id)){
                $bra = $article->getOneActivity($id);
                $oldurl=$bra['thum_pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'thum_pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editActivity($date);
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
        $article = new ActivityModel();
        $flag = $article->delActivity($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [activity_state 活动状态]
     */
    public function activityState()
    {
        extract(input());
        $article = new ActivityModel();
        $flag = $article->ActivityState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //*********************************************场次管理*********************************************//

    /**
     * 查询场次
     */
    public function activityScene(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['as.title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="as.".$field." ".$order;
            }else{
                $od="as.create_time desc";
            }
            $id = input('param.id');
            $map[] = ['as.activity_id','=',$id];
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $actScene = new ActivitySceneModel();
            $count = $actScene->alias('as')->where($map)->count();
            //$map[] = ['gen_cate','=',input('cate_id')];
            $lists = $actScene->alias('as')
                ->where($map)->page($Nowpage,$limits)->order($od)->select();
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $id = input('param.id');
        $item = Db::name('activity')->where('id',$id)->find();
        $this->assign('item',$item);
        return $this->fetch('scene_index');
    }

    /**
     * 添加场次
     */
    public function addScene(){
        if (request()->isPost()) {
            $param = input('post.');
            $weekarray=array("日","一","二","三","四","五","六");
            $param['week_day']='周'.$weekarray[date("w",strtotime($param['activity_time']))];
            $param['activity_time'] = strtotime($param['activity_time']);
            $as = new ActivitySceneModel();
            $flag = $as->insertScene($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $act = new ActivityModel();
        $item = $act->where('id',$id)->find();
        $this->assign('item',$item);
        return $this->fetch('add_scene');
    }

    public function editScene(){
        if (request()->isPost()) {
            $param = input('post.');
            $weekarray=array("日","一","二","三","四","五","六");
            $param['week_day']='周'.$weekarray[date("w",strtotime($param['activity_time']))];
            $param['activity_time'] = strtotime($param['activity_time']);
            $as = new ActivitySceneModel();
            $flag = $as->editScene($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.aId');
        $item = Db::name('activity')->where('id',$id)->find();
        $this->assign('item',$item);
        $scene = Db::name('activity_scene')
            ->where('id',input('param.id'))->find();
        $this->assign('scene',$scene);
        return $this->fetch('edit_scene');
    }

//*********************************************活动管理*********************************************//
    /**
     * 动态列表
     */
    public function trendsIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['at.title','like','%'.$keyword.'%'];
            }
            //属于商家编辑的公司需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="at.".$field." ".$order;
            }else{
                $od="at.create_time desc";
            }
            $map[] = ['at.activity_id','=',input('param.id')];
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('activity_trends')->alias('at')->where($map)->count();
            $lists = Db::name('activity_trends at')
                ->join('activity a','a.id=at.activity_id')
                ->join('member m','m.id=at.member_id')
                ->where($map)->field('at.*,m.nick_name')->page($nowpage,$limits)->select();
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $id = input('param.id');
        $item = Db::name('activity')->where('id',$id)->find();
        $this->assign('item',$item);
        return $this->fetch("trends_index");
    }

    /**
     * 动态详情
     */
    public function trendsDetail(){
        $id = input('param.id');
        $trends = new TrendsModel();
        $item = $trends->alias('t')
            ->join('member m','m.id=t.member_id')
            ->field('m.nick_name,t.*')
            ->where('t.id',$id)
            ->find();
        $pics = Db::name('company_trends_pic')->where('trends_id',$id)->select();
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
            $trends = new TrendsModel();
            $flag = $trends->trendsState($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
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
                $od="sort";
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
        $icon['width'] = Config::get('app.upload_image.picWidth');
        $icon['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('icon',$icon);
        return $this->fetch('cate_icon');
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