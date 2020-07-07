<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:10
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\RoleModel;
class Role extends Base
{
    /**
     * [index 角色列表]
     */
    public function roleIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $role = new RoleModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $role ->getCountRole($map);  //总数据数目
            $lists = $role ->getRoleByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("role_index");
    }

    /**
     * [add 添加角色]
     */
    public function addRole()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $role = new RoleModel();
            $flag = $role->insertRole($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_role');
    }

    /**
     * [edit 编辑角色]
     */
    public function editRole()
    {
        $role = new RoleModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $role->editRole($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneRole = $role->getOneRole($id);
        $this->assign('item',$oneRole);
        return $this->fetch('edit_role');
    }
    /**
     * [delete 删除角色]
     */
    public function delRole()
    {
        $id = input('param.id');
        if($id == session('role_id')){
            return json(['code'=>100,'msg'=>'不能删除自己的角色']);
        }else{
            $role = new RoleModel();
            $flag = $role->delRole($id);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }

    /**
     * checkRole 验证角色名称唯一性
     */
    public function checkRole(){
        extract(input());
        if(isset($id)&&$id!=""){
            $uid = $id;
        }else{
            $uid = '';
        }
        $role = new RoleModel();
        $flag = $role->checkRole ($title,$uid);
        return json(['code' => $flag['code'], 'msg' => $flag['msg']]);
    }

    /**
     * [role_state 角色状态]
     */
    public function roleState()
    {
        extract(input());
        $role = new RoleModel();
        $flag = $role->roleState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}