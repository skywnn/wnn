<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:54
 */

namespace app\api\service;

use app\api\model\ThirdDevconfigModel;
use think\Model;
class AppId extends Model
{
    /**
     * 验证企业app_id，是否存在以及是否过期或被禁用
     */
    public static function checkAppID($AppId){
        //如果为空
        if(empty($AppId)){
            return json(['code' => 600, 'data' => [], 'msg' => '应用ID不能为空！']);
        }else{
            $map[] = ['xcx_appid','=',$AppId];
            $devConfig = new ThirdDevconfigModel();
            $appletsData = $devConfig->field('id,status,expired_time,is_delete')->where($map)->find();
            if(empty($appletsData)){
                return json(['code' => 601, 'data' => [], 'msg' => '应用ID不存在！']);
            }
            if($appletsData['is_delete'] == 1){
                return json(['code' => 602, 'data' => [], 'msg' => '无法查询该应用id的相关信息！']);
            }
            return true;
        }
    }
    /**
     * 根据企业AppId获取的公司id
     */
    public static function getAppIdCompanyId($AppId){

        if(!empty($AppId)){
            $map[] = ['xcx_appid','=',$AppId];
            $devConfig = new ThirdDevconfigModel();
            $appletsData = $devConfig->where($map)->find();
            if(!empty($appletsData)){
                return $appletsData['company_id'];
            }else{
                return json(['code' => 601, 'data' => [], 'msg' => '应用ID不存在！']);
            }
        }else{
            return json(['code' => 600, 'data' => [], 'msg' => '应用ID不能为空！']);
        }
    }
    /**
     * 获取企业AppId，的相关信息
     */
    public static function getAppIdInfo($AppId){
        if(empty($AppId)){
            return json(['code' => 600, 'data' => [], 'msg' => '应用ID不能为空！']);
        }else{
            $map[] = ['xcx_appid','=',$AppId];
            $devConfig = new ThirdDevconfigModel();
            $appletsData = $devConfig->where($map)->find();
            if(empty($appletsData)){
                return json(['code' => 601, 'data' => [], 'msg' => '应用ID不存在！']);
            }else{
                return $appletsData;
            }
        }
    }
}