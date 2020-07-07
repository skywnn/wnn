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
class Base extends Controller
{
    protected $adminuser = null;
    //初始化
    protected function initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        //检查是否登录
        if(!session('?user_id')||!session('?user_name')){
            $this->error('当前页面需要登录',url('admin/Login/index'));
        }
        //检查登录信息是否真实
        $adminSta = Db::name ('user')->where ('id' , session ('user_id'))->field ('status,username')->find ();
        if ( is_null ($adminSta[ 'username' ]) ) {
            $this->error ('抱歉，账户不存在' , 'admin/Login/index');
        }

    }
}