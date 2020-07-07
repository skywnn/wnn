<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/16
 * Time: 16:54
 */

namespace app\admin\controller;

use app\common\chuanglan\Sms;
use think\Controller;
use think\Db;
use app\admin\service\User as userService;
use org\Verify;
class Login extends Controller
{
    /**
     * 登录页面
     */
    public function index()
    {
        return $this->fetch('index');
    }
    /**
     * 登录数据提交
     */
    public function login()
    {
        $username = input("param.username");
        $password = input("param.password");
        $code = input("param.yzm");
        $verify = new Verify();
        if($verify->check($code)){
            return  $this->checkAdmin($username,$password);
        }else{
            return json(['code' => -1, 'url' => '', 'msg' => '验证码错误']);
        }
    }

    /**
     * 验证登录帐号和密码
     */
    public function checkAdmin($username,$password){
        //判断用户登录信息
        $hasUser = Db::name('user')
            ->where('username', $username)
            ->field('id,username,password,icon,user_type,phone,status,login_num')
            ->find();
        if(empty($hasUser)){
            return json(['code' => -1, 'url' => '', 'msg' => '管理员不存在']);
        }
        if(md5($password) != $hasUser['password']){
            return json(['code' => -2, 'url' => '', 'msg' => '密码错误']);
        }
        if(1 != $hasUser['status']){
            return json(['code' => -3, 'url' => '', 'msg' => '抱歉，该账号被禁用']);
        }
        if(1 != $hasUser['user_type']){
            return json(['code' => -4, 'url' => '', 'msg' =>'抱歉，'.$hasUser['username'].'所属身份非企业管理员']);
        }

        //根据用户信息获取角色与所属公司信息
        $map['user_id'] = $hasUser['id'];
        $map['is_default'] = 1;
        $userData = new userService();
        $lists = $userData->getManagerInfo($map);
        if(1 != $lists['hasRole']['status']){
            return json(['code' => -5, 'url' => '', 'msg' =>'抱歉，'.$hasUser['username'].'身份被禁用,请联系管理员！']);
        }

        //赋值于session
        session('user_id', $hasUser['id']);             //用户ID
        session('user_name', $hasUser['username']);     //用户名
        session('icon', $hasUser['icon']);              //用户头像
        session('phone', $hasUser['phone']);            //手机号
        session('role_id', $lists['roleInfo']['id']);            //角色id
        session('role_name', $lists['roleInfo']['name']);        //角色名称
        session('role_type', $lists['roleInfo']['role_type']);   //角色类型
        session('company_id', $lists['companyInfo']['id']);      //公司ID
        session('company_name', $lists['companyInfo']['name']);  //公司名称
        session('company_title', $lists['companyInfo']['title']);//公司名称
        session('company_logo', $lists['companyInfo']['logo']);          //公司LOGO
        session('last_time',time());                    //角色登录时间点
        //更新管理员访问数据
        $param = [
            'login_num' => $hasUser['login_num'] + 1,
            'login_ip' => request()->ip(),
            'login_time' => time()
        ];
        Db::name('user')->where('id', $hasUser['id'])->update($param);

        return json(['code' => 1, 'url' => url('admin/Index/index'), 'msg' => '登录成功！']);

    }

    /**
     * [checkVerify 验证码]
     */
    public function checkVerify(){
        $config =    [
            'imageH' => 36,// 验证码图片高度
            'imageW' => 120,// 验证码图片宽度
            'codeSet' => '02345689',// 验证码字符集合
            'useZh' => false,//使用中文验证码
            'length' => 4,// 验证码位数
            'useNoise' => true,//是否添加杂点
            'useCurve' => false,//是否画混淆曲线
            'useImgBg' => false,//使用背景图片
            'fontSize' => 16// 验证码字体大小(px)
        ];
        $verify = new Verify($config);
        return $verify->entry();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session(null);
        $this->redirect('admin/Login/index');
    }

    public function send(){
        extract(input());
        $sms = new Sms();
        $res = $sms->sendMsg($phone,$type,$msg);
        return $res;
    }

}