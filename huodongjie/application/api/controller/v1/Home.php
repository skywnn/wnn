<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:52
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\service\AppId;
use app\lib\exception\ApiErrorException;
use app\api\model\MemberModel;
use app\api\service\Member;
use app\api\model\ActivityCateModel;
use app\api\model\ActivityModel;
use app\api\model\CompanyModel;
use app\api\model\FavoriteModel;
use think\facade\Config;
use think\db;
class Home extends Base
{
    /**
     * [查出所有的圈子]
     */
    public function getActivityCate(){

        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取圈子列表
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $cate = new ActivityCateModel();
            $data = $cate->order('sort desc')->where($map)->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    if(!empty($v['icon'])){
                        $v['icon'] = Config::get('app.baseUrl').$v['icon'];
                    }
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }

    /**
     * [被推荐的活动第一个]
     */
    public function getOneActivity(){

        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取一个推荐的活动
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $map[] = ['is_rec','=',1];
            $activity = new ActivityModel();
            $data = $activity->where($map)->find();
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '暂无相关活动']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }

    /**
     * [活动列表]
     */
    public function getActivityList(){

        $AppId = input('param.app_id');
        $userId = input('param.token');
        $page = input('param.page');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取活动列表
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $od = 'r.create_time desc';
            $Nowpage = $page ? $page:1;
            $limits = 5;
            $activity = new ActivityModel();
            $data = $activity->getActivityByWhere($map,$Nowpage,$limits,$od);
            if(!empty($data)){
                foreach ($data as &$v){
                    if(!empty($v['pic'])){
                        $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                    }
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '暂无相关活动']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }

    /**
     * [主办方列表]
     */
    public function getRecCompany(){

        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取主办方列表
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $map[] = ['is_rec','=',1];
            $company = new CompanyModel();
            $data = $company->order('sort')->where($map)->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    if(!empty($v['logo'])){
                        $v['logo'] = Config::get('app.baseUrl').$v['logo'];
                    }
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }

    /**
     * [活动详情]
     */
    public function getActivityDetail(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $activity_id = input('param.activity_id');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取一条活动详情
            $map[] = ['r.id','=',$activity_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $activity = new ActivityModel();
            $data = $activity->getActivityInfo($map);
            if(!empty($data)){
                $data['start_time'] = date('m-d H:i', strtotime($data['start_time']));
                $data['end_time'] = date('m-d H:i', strtotime($data['end_time']));
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
                $activity->where('id','=',$activity_id)->setInc('views',1);
                if(!empty($data['keywords'])){
                    $data['keywords'] = explode(',',$data['keywords']);
                }
            } else {
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常，请确认！']);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }


    /**
     * 首页-收藏、取消收藏
     */
    public function favorite(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conId = input('param.con_id');
        $type = input('param.type');//0-请求1-收藏 2-取消
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //处理收藏逻辑
            $map[] = ['app_id','=',$AppId];
            $map[] = ['favorite_id','=',$conId];
            $map[] = ['user_id','=',$userId];
            $favorite = new FavoriteModel();
            $info = $favorite->where($map)->find();
            if($type == 0){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已收藏']);
                } else{
                    return json(['code' => 100, 'data' => [], 'msg' => '未收藏']);
                }
            }elseif ($type == 1){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已收藏不能重复收藏']);
                }else{
                    $data = array(
                        'app_id' => $AppId,
                        'user_id' => $userId,
                        'favorite_id' => $conId,
                        'create_time' =>time()
                    );
                    $favorite->strict(false)->insert($data);
                    Db::name('activity')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    return json(['code' => 200, 'data' => [], 'msg' => '收藏成功']);
                }
            }elseif($type == 2){
                if($info){
                    $favorite->where($map)->delete();
                    Db::name('activity')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    return json(['code' => 200, 'data' => [], 'msg' => '取消收藏成功']);
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂时没有收藏不能取消收藏']);
                }
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 我的-收藏统计
     */

    /**
     * [发布动态]
     */
    /**
     * [购票报名]
     */




}