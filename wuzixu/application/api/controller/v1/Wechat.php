<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:52
 */

namespace app\api\controller\v1;

use app\api\controller\Base;

use app\api\model\MemberModel;
use think\facade\Config;
use think\db;
class Wechat extends Base
{
    public function __construct()
    {
        $this->appid = 'wxea685f7bfc02bc1e';
        $this->appsecret = 'bae920e05bd87106ea7375be18831b9f';
    }


    public function Index()
    {
        $callback ='https://www.wty24.com/Api/v1.Wechat/Index';
        $snsapi = 'snsapi_base';
        $state = 'repeat';

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . 'appid=' . $this->appid . '&redirect_uri=' . urlencode($callback) . '&response_type=code&scope=' . $snsapi . '&state=' . $state . '#wechat_redirect';
    }


    public function wechatRespont()
    {
        $data= input();

        if(empty($data['unionid'])){
            return json(['code' => 500, 'data' => '', 'msg' => '缺少必要参数']);
        }

        $map[] = ['unionid','=',$data['unionid']];
        $member = new MemberModel();
        $info = $member->field('id,nick_name,phone,sex,is_auth,status')->where($map)->find();
        if($info){
            if($info['status'] ==0){
                return json(['code' => 100, 'data' => [], 'msg' => '登录失败，您的账户已禁用！']);
            }
            //用户已经存在，直接登录
            session('token', $info['id']);
            $login_time = time();
            $data = [
                'create_time' => $login_time,
                'login_time' => $login_time,
                'app_id'=> $data['app_id'],
                'icon' => $data['avatarUrl'],
                'nick_name' =>$data['nickName'],
                'unionid'=>$data['unionid'],
                'openid'=>$data['openid'],
                'id' => $info['id'],
            ];
            $member = new MemberModel();
            $member->update($data);
            return json(['code' => 200, 'data' => $info, 'msg' => '登录成功']);

        }else{
            //用户不存在，注册后保存登录信息
            if(empty($data['unionid'])){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息获取失败，请退出后重新获取！']);
            }
            $data = [
                'openid' => $data['openid'],
                'icon' => $data['avatarUrl'],
                'app_id'=> $data['app_id'],
                'nick_name' => $data['nickName'],
                'login_time' => time(),
                'unionid'=>$data['unionid'],
                'openid'=>$data['openid'],
            ];
           $user_id = DB::name('member')->insertGetId($data);

            return json(['code' => 200, 'data' => $user_id, 'msg' => '登录成功']);

        }
    }



    public function callBack()
    {
        $code = input('code');
        if(!isset($code)){

            return json(['code' => 500, 'data' => '', 'msg' => '获取错误']);
        }
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $result = $this->http_get($url. 'appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code');

        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {

                return json(['code' => $json['errcode'],'msg' => $json['errmsg']]);
            }

        }
        if($json['access_token'] &&  $json['openid']){

            $lang= 'zh_CN';
            $user_url = ' https://api.weixin.qq.com/sns/userinfo?';
            $result = $this->http_get($user_url. 'access_token=' . $json['access_token'] . '&openid=' .  $json['openid'] . '&lang=' . $lang);

            if ($result) {
                $json = json_decode($result, true);
                if (!$json || !empty($json['errcode'])) {
                    return json(['code' => $json['errcode'],'msg' => $json['errmsg']]);
                }






                return $json;
            }
        }
        return json(['code' => 500, 'data' => '', 'msg' => '获取错误']);

    }


    private function http_get($url)
    {
        $oCurl = curl_init();

        if (stripos($url, 'https://') !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if (intval($aStatus['http_code']) == 200) {
            return $sContent;
        }
        else {
            return false;
        }
    }

    private function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();

        if (stripos($url, 'https://') !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }

        if (is_string($param) || $post_file) {
            $strPOST = $param;
        }
        else {
            $aPOST = array();

            foreach ($param as $key => $val) {
                $aPOST[] = $key . '=' . urlencode($val);
            }

            $strPOST = join('&', $aPOST);
        }

        if (class_exists('\\CURLFile')) {
            curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, true);
        }
        else if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if (intval($aStatus['http_code']) == 200) {
            return $sContent;
        }
        else {
            return false;
        }
    }



}