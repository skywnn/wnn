<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:10
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\ActivityModel;
use think\facade\Config;
use think\db;
class Activity extends Base
{
    /**
     * 园区活动 活动列表
     */
    public function getAllActivityList(){
        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['r.company_id','=',$company_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];

//            $cate_id = input('param.cate_id','');
//            $timeType = input('param.timeType','');
//            $payType = input('param.payType','');
            $orderType = input('param.orderType','');
//            $map[] = ['cate_id','=',$cate_id];
//            if($payType ==1){
//                $map[] = ['price','=',0];
//            }elseif($payType == 2){
//                $map[] = ['price','gt',0];
//            }
//
//            $time = time();
//            //0全部  1今天以内 2三天以内 3 一周以内 4一月以内
//            if($timeType == 1){
//                $map[] = ['start_time','gt',$time - (24*60*60)];
//            }elseif($timeType == 2){
//                $map[] = ['start_time','gt',$time - (3*24*60*60)];
//            }elseif($timeType == 3){
//                $map[] = ['start_time','gt',$time - (7*24*60*60)];
//            }elseif($timeType == 4){
//                $map[] = ['start_time','gt',$time - (30*24*60*60)];
//            }
            //综合排序：1-new-最新发布  2-views热门点击 3-space距离最近
            if($orderType == 1){
                $od = "r.create_time desc";
            }elseif($orderType == 2){
                $od = "r.views desc";
            }else{
                $od = "r.create_time desc";
            }
            $activity = new ActivityModel();
            $data = $activity->getActivityByWhere($map,18,$od);
            foreach($data as $k=>&$v){
                $v['start_time'] = date('m-d H:i', strtotime($v['start_time']));
                $v['end_time'] = date('m-d H:i', strtotime($v['end_time']));
                $v['pic'] = Config::get('app.baseUrl').$v['pic'];
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 园区活动 活动详情
     */
    public function getActivityDetail(){
        $AppID = input('param.app_id');
        $activity_id = input('param.activity_id');
        try{
            AppId::checkAppID($AppID);
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
                $this->getFail([],'参数异常，请确认！',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}