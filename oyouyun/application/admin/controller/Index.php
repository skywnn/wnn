<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/16
 * Time: 16:56
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\MenuModel;
use app\admin\service\User as userService;
use think\Db;
class Index extends Base
{
    /**
     * [index 管理后台首页]
     */
    public function index(){
        //根据角色类型显示不同的菜单
        $map['type'] = session('role_type');
        $map['level'] = 0;

        $menu = new MenuModel();
        $menuList = $menu->getAllMenu($map);
        $menuList = $menuList->toArray();
        foreach ($menuList as $key => $value){
            $menuList[$key]['second'] = $menu->where('parent_id','=',$value['id'])->order('sort')->select();
            $menuList[$key]['second'] = $menuList[$key]['second']->toArray();

            foreach ($menuList[$key]['second'] as $v => $mo){
                $menuList[$key]['second'][$v]['third'] = $menu->where('parent_id','=',$mo['id'])->order('sort')->select();
                $menuList[$key]['second'][$v]['third'] = $menuList[$key]['second'][$v]['third']->toArray();
                if(empty($menuList[$key]['second'][$v]['third'])){
                    $menuList[$key]['second'][$v]['third'] = '';
                }
            }
        }
        //查询有没有符合条件的公司
        $hasRole = Db::name('role_user')
            ->where('user_id',session('user_id'))
            ->where('company_id','<>',session('company_id'))
            ->select();
        //无：公司数组为空数组，有：把公司查出来形成公司数组
        $companys = array();
        $my['role_type'] = session('role_type');
        if(!empty($hasRole)){
            foreach ($hasRole as $key => $va) {
                if($va['company_id'] != session('company_id')){
                    $hascompany = Db::name('company')
                        ->where('id',$va['company_id'])
                        ->find();
                    $companys[$key] = $hascompany;
                }
            }
            $my['companyOther'] = 1;
        }else{
            $companys = '';
            $my['companyOther'] = 0;
        }
        $this->assign('companys',$companys);
        $this->assign('menuList',$menuList);
        $this->assign('my',$my);
        return $this->fetch('index');
    }
    /**
     * [changeCompany 切换公司]
     */
    public function changeCompany($id){

        //根据用户信息获取角色与所属公司信息
        $map['user_id'] = session('user_id');
        $map['company_id'] = $id;
        $userData = new userService();
        $lists = $userData->getManagerInfo($map);
        if(1 != $lists['hasRole']['status']){
            return json(['code' => -5, 'url' => '', 'msg' =>'抱歉，'.$hasUser['username'].'身份被禁用,请联系管理员！']);
        }

        //赋值于session
        session('user_id', session('user_id'));         //用户ID
        session('user_name', session('user_name'));     //用户名
        session('icon', session('icon'));              //用户头像
        session('phone', session('phone'));            //手机号
        session('role_id', $lists['roleInfo']['id']);            //角色id
        session('role_name', $lists['roleInfo']['name']);        //角色名称
        session('role_type', $lists['roleInfo']['role_type']);   //角色类型
        session('company_id', $lists['companyInfo']['id']);      //公司ID
        session('company_name', $lists['companyInfo']['name']);  //公司名称
        session('company_title', $lists['companyInfo']['title']);//公司名称
        session('company_logo', $lists['companyInfo']['logo']);          //公司LOGO

        return json(['code' => 200, 'url' => url('admin/Index/index'),  'msg' => '切换成功！']);
    }
    /**
     * [adminMain 运营平台主页]
     */
    public function adminMain(){
        return $this->fetch('admin_main');
    }
    /**
     * [bossMain 商家界面主页]
     */
    public function bossMain(){
        return $this->fetch('boss_main');
    }
}