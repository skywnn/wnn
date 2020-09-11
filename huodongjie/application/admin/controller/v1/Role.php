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
use think\Db;

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

    /**
     * 添加权限
     */
    public function rolePower(){
        $param = array();
        $id = input('param.id');
        if(request()->isPost())
        {
            $data = input('post.');
            $param['id'] = $data['id'];
            $param['rules'] = implode(',',$data['rules']);
            $access = new RoleModel();
            $data = $access->getOneRole($param['id']);
            if(!empty($data)){
                $res = $access->editRole($param);
            }
            return json(['code' => $res['code'], 'data' => $res['data'], 'msg' => $res['msg']]);
        }
        $accessList = $this->getJsTree($id);
        $this->assign('id',$id);
        $this->assign('accessList',$accessList);
        return $this->fetch('role_power');
    }

    /**
     * 获取权限列表 jstree格式
     * @param int $role_id 当前角色id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getJsTree($role_id = null)
    {
        $accessIds = is_null($role_id) ?[] : RoleModel::getAccessIds($role_id);
        $accessIds=explode(',',isset($accessIds[0])?$accessIds[0]:[]);
        $jsTree = [];
        $access = new RoleModel();
        $list=$access->getAll();
        foreach ( $list as  $k=>$item) {
            $jsTree[] = [
                'id' => $item['id'],
                'parent' => $item['parent_id'] > 0 ? $item['parent_id'] : '#',
                'text' => $item['name'],
                'state' => [
                    'selected' => (in_array($item['id'], $accessIds) && !$this->hasChildren($item['id']))
                ]
            ];
        }
        return json_encode($jsTree);
    }

    /**
     * 是否存在子集
     * @param $access_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function hasChildren($access_id)
    {
        $access = new RoleModel();
        $list=$access->getAll();
        foreach ($list as $k=>$item) {
            if ($item['parent_id'] == $access_id)
                return true;
        }
        return false;
    }

    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postData($key = null)
    {
        return $this->request->post(is_null($key) ? '' : $key . '/a');
    }
}