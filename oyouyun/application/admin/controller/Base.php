<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/16
 * Time: 16:53
 */

namespace app\admin\controller;


use think\Controller;
use think\db;
class Base extends Controller
{
    protected $adminuser = null;
    //初始化
    protected function initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        //检查是否登录
        if(!session('?user_id')||!session('?user_name')||!session('?role_id')||!session('?company_id')){
            $this->error('当前页面需要登录',url('admin/Login/index'));
        }
        //检查登录信息是否真实
        $adminSta = Db::name ('user')->where ('id' , session ('user_id'))->field ('status,username')->find ();
        if ( is_null ($adminSta[ 'username' ]) ) {
            $this->error ('抱歉，账户不存在' , 'admin/Login/logout');
        }
        //检查登录信息是否有效
        $map['user_id'] = session ('user_id');
        $map['role_id'] = session ('role_id');
        $map['company_id'] = session ('company_id');
        $roleSta = Db::name ('role_user')->where ($map)->field ('status,is_default')->find ();
        if ( is_null ($roleSta) ) {
            $this->error ('抱歉，身份不存在' , 'admin/Login/logout');
        }
        if ( $roleSta[ 'status' ] != 1 ) {
            $this->error ('抱歉，身份状态异常,请联系管理员' , 'admin/Login/logout');
        }
    }
    /**
     * place 三级联动
     */
    public function place()
    {
        $area = new \app\common\place\Area;
        $data = $area->area();
        return json($data);
    }
}