<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/12
 * Time: 14:54
 */

namespace app\api\controller;

use app\api\controller\Base;
use app\api\service\AppId;
use app\api\service\User as UserService;
use app\api\service\Message;
use app\api\model\UserModel;
use think\Exception;
use think\db;
class Login extends Base
{
    /**
     * [login 用户登录]
     */
    public function login()
    {
        //接受客户端传过来的AppId
        $AppID = input('param.app_id');
        try{
            //查询应用小程序的信息
            AppId::checkAppID($AppID);
            $appletsData = AppId::getAppIdInfo($AppID);

            //code用户获取用户相关信息
            $code = input('param.code');
            $nick_name = input('param.nickName');
            $icon = input('param.avatarUrl');
            $sex = input('param.gender');
            if(empty($code) || empty($nick_name) || empty($icon)){
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常！']);
            }
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='. $appletsData['xcx_appid'] .'&secret='. $appletsData['xcx_appsecret'] .'&js_code='. $code .'&grant_type=authorization_code';
            $result = curl_get($url);
            $wxResult = json_decode($result, true);
            if(empty($wxResult)){
                return json(['code' => 100, 'data' => [], 'msg' => '获取openid及session时异常']);
            }else{
                $loginFail = array_key_exists('errcode', $wxResult);
                if($loginFail) {
                    return json(['code' => 100, 'data' => [], 'msg' => '微信调用失败']);
                }
            }

            //判断用户是否已经注册
            $openid = $wxResult['openid'];	        //获取用户的openid
            $sessionKey = $wxResult['session_key'];	//获取用户的session_key
            //session('3rd_session_'.$openid,$sessionKey,7200);//缓存用户的session_key
            $map[] = ['openid','=',$openid];
            $map[] = ['app_id','=',$AppID];
            $user = new UserModel();
            $info = $user->field('id,nick_name,phone,sex,is_auth,status')->where($map)->find();

            //查看是否有推荐，获取$scene_id表示推荐人id
            $scene_id = input('param.scene_id',0);
            if($info){
                if($info['status'] ==0){
                    return json(['code' => 100, 'data' => [], 'msg' => '登录失败，您的账户已禁用！']);
                }
                if(empty($info['recommend_code'])){
                    $recommend_code = $this->setIdCode($info['id']);
                    $user->where('id' , $info['id'])->setField (['recommend_code' => $recommend_code]);
                }
                //用户已经存在，直接登录
                session('token', $info['id']);
                $login_time = time();
                $data = [
                    'login_time' => $login_time,
                    'icon' => $icon,
                    'nick_name' => $nick_name,
                    'id' => $info['id']
                ];
                $user->update($data);
                return json(['code' => 200, 'data' => $data, 'msg' => '登录成功']);
            }else{
                //用户不存在，注册后保存登录信息
                if(empty($openid)){
                    return json(['code' => 100, 'data' => [], 'msg' => '用户信息获取失败，请退出后重新获取！']);
                }
                $data = [
                    'openid' => $openid,
                    'sex' => $sex,
                    'icon' => $icon,
                    'password' => 'e10adc3949ba59abbe56e057f20f883e',
                    'parent_id' => $scene_id,
                    'nick_name' => $nick_name,
                    'login_time' => time(),
                    'app_id' => $AppID
                ];

                $user->strict(false)->insert($data);
                $userData = $user->field('id,nick_name,phone,sex,is_auth,status')->where('openid','=',$openid)->find();
                if($userData){
                    $recommend_code = $this->setIdCode($userData['id']);
                    $user->where('id' , $userData['id'])->setField (['recommend_code' => $recommend_code]);
                    //通知消息
                    if(!empty($scene_id)){
                        Message::sendMessage('推荐','尊敬的'.'恭喜你推荐的'. $nick_name .'，已经成功注册华如！',$userData['id'],$userData['default_cid'],$scene_id,$scene_cid,$AppID);
                        //                        //直推人数加1
                        $user->where('id','=',$scene_id)->setInc('zhi_num', 1);
                    }
                    //通知消息
                    Message::sendMessage('注册',"恭喜你成为华如云的一员，我们将为你提供更好的服务，么么哒！",$userData['id'],$userData['default_cid'],$userData['id'],$scene_cid,$AppID);
                    session('token', $userData['id']);
                    return json(['code' => 200, 'data' => $userData, 'msg' => '登录成功']);
                } else {
                    return json(['code' => 100, 'data' => $userData, 'msg' => '登录失败']);

                }
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }

    }
}