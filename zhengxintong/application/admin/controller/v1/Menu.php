<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/16
 * Time: 16:56
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\MenuModel;
use think\db;
use think\Env;
class Menu extends Base
{
    /**
     * [menuIndex 菜单列表]
     */
    public function menuIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['parent_id','=',0];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $menu = new MenuModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $menu ->getCountMenu($map);  //总数据数目
            $lists = $menu ->getMenuByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("menu_index");
    }
    /**
     * [add 添加菜单]
     */
    public function addMenu()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $menu = new MenuModel();
            $flag = $menu->insertMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_menu');
    }

    /**
     * [edit 编辑菜单]
     */
    public function editMenu()
    {
        $menu = new MenuModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $menu->editMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneMenu = $menu->getMenuInfo($id);
        $this->assign('item',$oneMenu);
        return $this->fetch('edit_menu');
    }
    /**
     * [delete 删除菜单]
     */
    public function delMenu()
    {
        $id = input('param.id');
        $menu = new MenuModel();
        $flag = $menu->delMenu($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [menu_state 菜单状态]
     */
    public function menuState()
    {
        extract(input());
        $menu = new MenuModel();
        $flag = $menu->menuState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [submenuIndex 子菜单列表]
     */
    public function submenuIndex(){
        $id = input('param.id');
        $menu = new MenuModel();
        $oneMenu = $menu->getMenuInfo($id);
        $oneMenu['level'] += 1;
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['parent_id','=',$id];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $menu ->getCountMenu($map);  //总数据数目
            $lists = $menu ->getMenuByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('item',$oneMenu);
        return $this->fetch("submenu_index");
    }
    /**
     * [addSubmenu 添加子菜单]
     */
    public function addSubmenu()
    {
        $menu = new MenuModel();
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $flag = $menu->insertMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $level = input('param.level');
        $oneMenu = $menu->getMenuInfo($id);
        $oneMenu['level'] = $level;
        $this->assign('item',$oneMenu);
        return $this->fetch('add_submenu');
    }
    /**
     * [iconMenu 设置菜单图标]
     */
    public function iconMenu()
    {
        $menu = new MenuModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $menu->editMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneMenu = $menu->getMenuInfo($id);
        $this->assign('item',$oneMenu);
        return $this->fetch('icon_menu');
    }
}