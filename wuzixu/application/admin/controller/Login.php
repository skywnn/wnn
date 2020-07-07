<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:07
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
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
     * 登录数据提交---用户名登录
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
            ->field('id,username,password,icon,phone,status,login_num')
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

        //赋值于session
        session('user_id', $hasUser['id']);             //用户ID
        session('user_name', $hasUser['username']);     //用户名
        session('icon', $hasUser['icon']);              //用户头像
        session('phone', $hasUser['phone']);            //手机号
        session('last_time',time());                    //角色登录时间点

        //更新管理员访问数据
        $param = [
            'login_num' => $hasUser['login_num'] + 1,
            'login_ip' => request()->ip(),
            'login_time' => time()
        ];
        Db::name('user')->where('id', $hasUser['id'])->update($param);

        return json(['code' => 200, 'url' => url('admin/Index/index'), 'msg' => '登录成功！']);

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
}