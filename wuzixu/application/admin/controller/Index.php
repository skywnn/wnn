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
    public function index(){
        //根据角色类型显示不同的菜单
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