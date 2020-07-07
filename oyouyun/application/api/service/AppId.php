<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/12
 * Time: 14:59
 */

namespace app\api\service;

use app\admin\model\ThirdDevconfigModel;
use app\lib\exception\ApiErrorException;
class AppId
{
    /**
     * 验证企业app_id，是否存在以及是否过期或被禁用
     */
    public static function checkAppID($AppId){
        //如果为空
        if(empty($AppId)){
            throw new ApiErrorException('应用ID不能为空！', 600);
        }else{
            $map[] = ['xcx_appid','=',$AppId];
            $devconfig = new ThirdDevconfigModel();
            $appletsData = $devconfig->field('id,status,expired_time,is_delete')->where($map)->find();
            if(empty($appletsData)){
                throw new ApiErrorException('应用ID不存在！', 601);
            }
            if($appletsData['is_delete'] == 1){
                throw new ApiErrorException('无法查询该应用id的相关信息！', 602);
            }
            if($appletsData['status'] == 0){
                throw new ApiErrorException('应用ID被禁用！', 602);
            }
//            if($appletsData['expired_time'] < time()){
//                throw new ApiErrorException('应用ID已过期，暂时无法使用！请平台续费', 603);
//            }
            return true;
        }
    }
    /**
     * 获取企业AppId，的相关信息
     */
    public static function getAppIdInfo($AppId){
        if(empty($AppId)){
            throw new ApiErrorException('应用ID不能为空！', 600);
        }else{
            $map[] = ['xcx_appid','=',$AppId];
            $devconfig = new ThirdDevconfigModel();
            $appletsData = $devconfig->where($map)->find();
            if(empty($appletsData)){
                throw new ApiErrorException('应用ID不存在！', 601);
            }else{
                return $appletsData;
            }
        }
    }
}