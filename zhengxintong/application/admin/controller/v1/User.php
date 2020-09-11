<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:09
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\RoleModel;
use app\admin\model\UserModel;
class User extends Base
{
    /**
     * [userIndex 全部用户列表]
     */
    public function userIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['username','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $role = new UserModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $role ->getCountUser($map);  //总数据数目
            $lists = $role ->getUserByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("user_index");
    }
    /**
     * [addManager 添加管理员]
     */
    public function addUser(){
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $user = new UserModel();
            $flag = $user->insertUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $role = new RoleModel();
        $roleDate = $role->getAllRole($map);
        $this->assign('role',$roleDate);
        return $this->fetch("add_user");
    }
    /**
     * [edit 编辑角色]
     */
    public function editUser()
    {
        $user = new UserModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $user->editUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneUser = $user->getOneUser($id);
        $this->assign('item',$oneUser);
        $map[] = ['status','=',1];
        $role = new RoleModel();
        $roleDate = $role->getAllRole($map);
        $this->assign('role',$roleDate);
        return $this->fetch('edit_user');
    }
    /**
     * [user_state 用户状态]
     */
    public function userState(){
        extract(input());
        $role = new UserModel();
        $flag = $role->UserState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [setPassword 修改管理员密码]
     */
    public function setPassword(){
        $userid = session('user_id');
        $user = new UserModel();
        if(request()->isPost()){
            extract(input());
            $old_pwd = md5($oldPwd);
            $flag = $user->checkOldPassword ($old_pwd,$userid);
            if($flag){
                $user->where('id','=',$userid)->setField(['password'=>md5($password)]);
                session(null);
                $userAlert = new \app\admin\service\User();
                exit($userAlert->alert_errors('你的密码是'.$password));
                //return json(['code' => 200, 'data' => '', 'msg' => '修改成功']);
            }else{
                return json(['code' => 100, 'data' => '', 'msg' => '原密码错误']);
            }
        }
        $userDate = $user->where('id','=',$userid)->field('id,nick_name,username,password')->find();
        $this->assign('item',$userDate);
        return $this->fetch("set_password");
    }
}