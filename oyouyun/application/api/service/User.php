<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/12
 * Time: 14:58
 */

namespace app\api\service;

use app\api\model\UserModel;
use app\lib\exception\ApiErrorException;
class User
{
    /**
     * 微信登录
     */
    public static function getOpenid($code,$appid,$appSecret)
    {
        $wxLoginUrl = sprintf(
            config('wx.login_url'), $appid, $appSecret, $code);

        $result = curl_get($wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new ApiErrorException('获取session_key及openID时异常，微信内部错误', 600);
        }else{
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                throw new ApiErrorException($wxResult['errmsg'], $wxResult['errcode']);
            }else{
                return $wxResult;
            }
        }
    }
    /**
     * 验证用户ID是否有效并存在
     */
    public static  function checkUserID($uid){

        $where['id'] = $uid;
        $where['status'] = 1;
        $user = new UserModel();
        $memberData = $user->field('id')->where($where)->find();
        if(empty($memberData)){
            return false;
        }
        return true;
    }
    /**
     * 获取用户详细信息
     */
    public static function getUserInfo($uid,$field){
        if(empty($uid)){
            return '用户信息丢失，请重新登录';
        }
        $user = new UserModel();
        if(!empty($field)){
            $userinfo = $user->field($field)->find($uid);
        }else{
            $userinfo = $user->find($uid);
        }

        if($userinfo){
            return $userinfo;
        } else {
            return '';
        }
    }
}