<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 23:19
 */

namespace app\api\controller\s1;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
class Member extends Base
{
    /**
     * 获取当前用户的基本资料
     */
    public function getUser(){
        $userId = input('post.token');
        $AppId = input('post.app_id');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $field = 'id,icon,nick_name,phone,email,weixin,desc_words,create_time';
            $userinfo = User::getUserInfo($userId,$field);
            if(empty($userinfo)){
                $this->getFail([],'用户信息丢失，请重新登录');
            }
            $this->res($userinfo,'',200);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 获取当前用户的身份码
     */
    public function getUserCode(){
        $userId = input('post.token');
        $AppId = input('post.app_id');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->getFail([],'用户信息丢失，请重新登录',100);
            }
            $field = 'id,icon,nick_name,phone,email,code';
            $userinfo = User::getUserInfo($userId,$field);
            if(empty($userinfo)){
                $this->getFail([],'用户信息丢失，参数异常',100);
            }
            $this->res($userinfo,'',200);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            return $this->push($res);
        }
    }
}