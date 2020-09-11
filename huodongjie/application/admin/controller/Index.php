<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 15:47
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\MenuModel;
use think\Db;

class Index extends Base
{
    /**
     * [index 管理后台首页]
     */
    /**
     * [index 管理后台首页]
     */
    public function index(){
        //根据角色类型显示不同的菜单
        $map[] = ['level','=',0];
        $map[] = ['status','=',1];
        $whe[] =['status','=',1];
        $user_id = session('user_id');
        $role_id = session('role_id');
        //dump($role_id);die;
        $menu = new MenuModel();
        $res = Db::name('role')->where('id',$role_id)->find();
        $menu_id = explode(',', $res['rules']);
        $map[] = ['id','in',$menu_id];
        $map[] = ['level','=',0];
        $map[] = ['status','=',1];
        $menuList = $menu->getAllMenu($map);
        $menuList = $menuList->toArray();
        foreach ($menuList as $key => $value){

            $menuList[$key]['second'] = $menu->where('parent_id','=',$value['id'])->where($whe)->order('sort')->select();
            $menuList[$key]['second'] = $menuList[$key]['second']->toArray();

            if(isset($menuList[$key]['second']) && !empty($menuList[$key]['second'])){

                foreach ($menuList[$key]['second'] as $v => $mo){

                    $menuList[$key]['second'][$v]['third'] = $menu->where('parent_id','=',$mo['id'])->where($whe)->order('sort')->select();


                    $menuList[$key]['second'][$v]['third'] = $menuList[$key]['second'][$v]['third']->toArray();

                    foreach ($menuList[$key]['second'][$v]['third'] as $k => $vs) {
                        if(strstr($res['rules'],(string)$vs['id']) == false && $res['rules'] != 0){
                            unset($menuList[$key]['second'][$v]['third'][$k]);continue;
                            //unset();
                        }
                    }
                    if(strstr($res['rules'],(string)$mo['id']) == false && $res['rules'] != 0){
                        unset($menuList[$key]['second'][$v]);continue;
                        //unset();
                    }
                    if(empty($menuList[$key]['second'][$v]['third'])){
                        $menuList[$key]['second'][$v]['third'] = '';
                    }

                }

            }

        }
        $this->assign('menuList',$menuList);
        return $this->fetch('index');
    }
    /**
     * [adminMain 运营平台主页]
     */
    public function adminMain(){
        return $this->fetch('admin_main');
    }
}