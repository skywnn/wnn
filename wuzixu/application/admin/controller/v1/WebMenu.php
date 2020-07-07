<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\WebMenuModel;

class WebMenu extends Base
{
    /**
     * [menuIndex 菜单列表]
     */
    public function webMenuIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['pid','=',0];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $menu = new WebMenuModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $menu ->getCountWebMenu($map);  //总数据数目
            $lists = $menu ->getWebMenuByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("web_menu_index");
    }
    /**
     * [add 添加菜单]
     */
    public function addWebMenu()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $menu = new WebMenuModel();
            $flag = $menu->insertWebMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('web_add_menu');
    }

    /**
     * [edit 编辑菜单]
     */
    public function editWebMenu()
    {
        $menu = new WebMenuModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $menu->editWebMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneMenu = $menu->getWebMenuInfo($id);
        $this->assign('item',$oneMenu);
        return $this->fetch('web_edit_menu');
    }
    /**
     * [delete 删除菜单]
     */
    public function delWebMenu()
    {
        $id = input('param.id');
        $menu = new WebMenuModel();
        $flag = $menu->delWebMenu($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [menu_state 菜单状态]
     */
    public function menuState()
    {
        extract(input());
        $menu = new WebMenuModel();
        $flag = $menu->webMenuState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [submenuIndex 子菜单列表]
     */
    public function subWebMenuIndex(){
        $id = input('param.id');
        $menu = new WebMenuModel();
        $oneMenu = $menu->getWebMenuInfo($id);
        $oneMenu['level'] += 1;
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['pid','=',$id];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $menu ->getCountWebMenu($map);  //总数据数目
            $lists = $menu ->getWebMenuByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('item',$oneMenu);
        return $this->fetch("web_submenu_index");
    }
    /**
     * [addSubmenu 添加子菜单]
     */
    public function addSubWebMenu()
    {
        $menu = new WebMenuModel();
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $flag = $menu->insertWebMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $level = input('param.level');
        $oneMenu = $menu->getWebMenuInfo($id);
        $oneMenu['level'] = $level;
        $this->assign('item',$oneMenu);
        return $this->fetch('web_add_submenu');
    }
    /**
     * [iconMenu 设置菜单图标]
     */
    public function iconWebMenu()
    {
        $menu = new WebMenuModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $menu->editWebMenu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneMenu = $menu->getWebMenuInfo($id);
        $this->assign('item',$oneMenu);
        return $this->fetch('icon_webMenu');
    }
}