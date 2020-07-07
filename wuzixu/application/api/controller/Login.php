<?php
/**
 * Created by PhpStorm.
 * Member: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:38
 */

namespace app\api\controller;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\Member as MemberService;
use app\api\model\MemberModel;
use app\api\service\Member;
use think\facade\Config;
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
        $AppId = input('param.app_id');
        try{
            //查询应用小程序的信息
            $app_id = Config::get('app.appid');
            $secret = Config::get('app.secret');
            //code用户获取用户相关信息
            $code = input('param.code');
            $nick_name = input('param.nickName');
            $icon = input('param.avatarUrl');
            $sex = input('param.gender');
            if(empty($code) || empty($nick_name) || empty($icon)){
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常！']);
            }
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='. $app_id .'&secret='. $secret .'&js_code='. $code .'&grant_type=authorization_code';
            $result = curl_get($url);
//            $wxResult = json_decode($result);
            $wxResult = json_decode($result, true);
          
            if(empty($wxResult)){
                return json(['code' => 100, 'data' => [], 'msg' => '微信授权异常']);
            }

            //判断用户是否已经注册
            $openid = $wxResult['openid'];	        //获取用户的openid
            $sessionKey = $wxResult['session_key'];	//获取用户的session_key

            $map[] = ['openid','=',$openid];
            $map[] = ['app_id','=',$AppId];
            $member = new MemberModel();
            $info = $member->field('id,nick_name,phone,sex,is_auth,status')->where($map)->find();
            
            //查看是否有推荐，获取$scene_id表示推荐人id
            $scene_id = input('param.scene_id',0);
            if($info){
                if($info['status'] ==0){
                    return json(['code' => 100, 'data' => [], 'msg' => '登录失败，您的账户已禁用！']);
                }
                $info['sessionKey'] =$sessionKey;
                //用户已经存在，直接登录
                session('token', $info['id']);
                $login_time = time();
                $data = [
                    'login_time' => $login_time,
                    'icon' => $icon,
                    'nick_name' => $nick_name,
                    'id' => $info['id']
                ];
                $member = new MemberModel();
                $member->update($data);
                return json(['code' => 200, 'data' => $info, 'msg' => '登录成功']);
            }else{
                //用户不存在，注册后保存登录信息
                if(empty($openid)){
                    return json(['code' => 100, 'data' => [], 'msg' => '用户信息获取失败，请退出后重新获取！']);
                }
                $data = [
                    'openid' => $openid,
                    'sex' => $sex,
                    'icon' => $icon,
                    'parent_id' => $scene_id,
                    'nick_name' => $nick_name,
                    'login_time' => time(),
                    'app_id' => $AppId
                ];
                $member->strict(false)->insert($data);
                $memberData = $member->field('id,nick_name,phone,sex,is_auth,status')->where('openid','=',$openid)->find();
                if($memberData){
                    $recommend_code = $this->setIdCode($memberData['id']);
                    $member->where('id' , $memberData['id'])->setField (['recommend_code' => $recommend_code]);
                    $memberData['sessionKey'] =$sessionKey;
                    //通知消息
                    if(!empty($scene_id)){
                        //直推人数加1
                        $member->where('id','=',$scene_id)->setInc('zhi_num', 1);
                    }
                    session('token', $memberData['id']);
                    return json(['code' => 200, 'data' => $memberData, 'msg' => '登录成功']);
                } else {
                    return json(['code' => 100, 'data' => $memberData, 'msg' => '登录失败']);

                }
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * [获取手机号]
     */
    public function getPhone(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $phone = input('param.phone');
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }

            $data = [
                'phone' => $phone,
                'username' => $phone,
                'password' => md5("123456"),
                'id' => $userId
            ];
            $member = new MemberModel();
            $member->update($data);
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    public function loginInfo()
    {
        //接受客户端传过来的AppId
        $AppID = input('param.app_id');
        try{
            $app_id = Config::get('app.app_id');
            //code用户获取用户相关信息
//            $code = input('param.code');
            $nick_name = input('param.nickName');
            $icon = input('param.avatarUrl');
            $sex = input('param.gender');
            //判断用户是否已经注册
            $openid = 'openid';	        //获取用户的openid
            $unionid = 'unionid';
            $map[] = ['openid','=',$openid];
            $map[] = ['app_id','=',$AppID];
            $member = new MemberModel();
            $info = $member->field('id,nick_name,phone,sex,is_auth,status')->where($map)->find();

            //查看是否有推荐，获取$scene_id表示推荐人id
            $scene_id = input('param.scene_id',0);
            if($info){
                if($info['status'] ==0){
                    return json(['code' => 100, 'data' => [], 'msg' => '登录失败，您的账户已禁用！']);
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
                $member = new MemberModel();
                $member->update($data);
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
                    'unionid' => $unionid,
                    'parent_id' => $scene_id,
                    'nick_name' => $nick_name,
                    'login_time' => time(),
                    'app_id' => $AppID
                ];

                $member->strict(false)->insert($data);
                $memberData = $member->field('id,nick_name,phone,sex,is_auth,status')->where('openid','=',$openid)->find();
                if($memberData){
                    $recommend_code = $this->setIdCode($memberData['id']);
                    $member->where('id' , $memberData['id'])->setField (['recommend_code' => $recommend_code]);
                    //通知消息
                    if(!empty($scene_id)){
                        //直推人数加1
                        $member->where('id','=',$scene_id)->setInc('zhi_num', 1);
                    }
                    session('token', $memberData['id']);
                    return json(['code' => 200, 'data' => $memberData, 'msg' => '登录成功']);
                } else {
                    return json(['code' => 100, 'data' => $memberData, 'msg' => '登录失败']);

                }
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}