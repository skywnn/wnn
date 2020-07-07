<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/17
 * Time: 16:55
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\UserModel;
use app\admin\service\User as UserService;
use app\admin\model\RoleModel;
use app\admin\model\ThirdDevconfigModel;
use think\Db;
class User extends Base
{
    /**
     * [managerIndex 本公司管理员列表]
     */
    public function managerIndex(){

        if(request()->isAjax ()){
            $companyid = session('company_id');
            $userSer = new userService();
            $userids = $userSer->getManagerIds($companyid);

            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['nick_name','like','%'.$keyword.'%'];
            }
            $map[] = ['id','in',$userids];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $user = new UserModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $user ->getCountUser($map);  //总数据数目
            $lists = $user ->getUserByWhere($map, $Nowpage, $limits, $od);
            if(!empty($lists)){
                foreach ($lists as &$v){
                    $roleName = $this->getManagerRole($v['id'],$companyid);
                    $v['role_name'] = $roleName;
                    $roleStatus = $this->getRoleStatus($v['id'],$companyid);
                    $v['role_status'] = $roleStatus;
                }
            }
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("manager_index");
    }
    /**
     * [getManagerRole 根据用户id获取用户管理员角色]
     */
    public function getManagerRole($id,$company_id){
        $map['user_id'] = $id;
        $map['company_id'] = $company_id;
        $arra = Db::name('role_user')->where($map)->find();
        $role = new RoleModel();
        $roleDate = $role->where('id','=',$arra['role_id'])->find();
        if(!empty($roleDate)){
            $roleName = $roleDate['name'];
        }else{
            $roleName = "";
        }
        return $roleName;
    }
    /**
     * [getManagerRole 根据用户id获取用户管理员身份状态]
     */
    public function getRoleStatus($id,$company_id){
        $map['user_id'] = $id;
        $map['company_id'] = $company_id;
        $arra = Db::name('role_user')->where($map)->find();
        if(!empty($arra)){
            $roleStatus = $arra['status'];
        }else{
            $roleStatus = 0;
        }
        return $roleStatus;
    }
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
            $companyid = session('company_id');
            $devconfig = new ThirdDevconfigModel();
            $appletsData = $devconfig->where('company_id','=',$companyid)->field('id,xcx_appid')->find();
            if(!empty($appletsData)){
                $map[] = ['app_id','=',$appletsData['xcx_appid']];
            }else{
                return json(['code'=>100,'msg'=>'尚未开通应用','count'=>0,'data'=>'']);
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
    public function addManager(){
        $companyid = session('company_id');
        $userid = session('user_id');
        if(request()->isPost()){
            $param = input('post.');
            $user_id = UserService::getManagerId($param['recommend_code']);
            if(empty($user_id)){
                return json(['code' => 100, 'data' => '', 'msg' => '用户ID号异常']);
            }
            if($user_id == $userid){
                return json(['code' => 100, 'data' => '', 'msg' => '不能重复提交自己']);
            }
            $where[] = ['company_id','=',$companyid];
            $where[] = ['user_id','=',$user_id];
            $arra = Db::name('role_user')->where($where)->find();
            if(!empty($arra)){
                return json(['code' => 100, 'data' => '', 'msg' => '同一家公司不能重复提交']);
            }
            $roleUser['company_id'] = $companyid;
            $roleUser['user_id'] = $user_id;
            $roleUser['role_type'] = session('role_type');
            $roleUser['role_id'] = $param['role_id'];
            $roleUser['is_default'] = 1;
            Db::name('role_user')->insert($roleUser);
            //改变用户身份
            $user = new UserModel();
            $user->where('id','=',$user_id)->setField(['user_type'=>1]);
            //排查默认身份状态
            $map[] = ['company_id','<>',$companyid];
            $map[] = ['user_id','=',$user_id];
            Db::name('role_user')->where($map)->setField('is_default', 0);


            return json(['code' => 200, 'data' => [], 'msg' => '管理员添加成功']);
        }
        $aee[] = ['company_id','=',$companyid];
        $role = new RoleModel();
        $roleList = $role->getAllRole($aee);
        $this->assign('role',$roleList);
        return $this->fetch("add_manager");
    }

    /**
     * [resetManager 重置管理员身份]
     */
    public function resetManager(){
        $companyid = session('company_id');
        $userid = session('user_id');
        if(request()->isPost()){
            $param = input('post.');
            //判断不能提交自己
            if($param['user_id'] == $userid){
                return json(['code' => 100, 'data' => '', 'msg' => '不能重复提交自己']);
            }
            //判断不能提交超级管理员
            $boolRole = $this->compareRole($param['old_role'],$param['role_id']);
            if($boolRole == false){
                return json(['code' => 100, 'data' => '', 'msg' => '不能修改超级管理员']);
            }
            $role_id = $param['role_id'];
            $where[] = ['user_id','=',$param['user_id']];
            $where[] = ['company_id','=',$companyid];
            Db::name('role_user')->where($where)->setField('role_id', $role_id);
            return json(['code' => 200, 'data' => [], 'msg' => '管理员身份重置成功']);
        }
        $id = input('param.id');
        $aee[] = ['company_id','=',$companyid];
        $role = new RoleModel();
        $roleList = $role->getAllRole($aee);
        $this->assign('role',$roleList);
        $map[] = ['company_id','=',$companyid];
        $map[] = ['user_id','=',$id];
        $arra = Db::name('role_user')->where($map)->find();
        $this->assign('roleUser',$arra);
        return $this->fetch("reset_manager");
    }
    /**
     * [adminState 判断两个角色是否属于同一个公司]
     */
    public function compareRole($oldRole,$newRole){
        $role = new RoleModel();
        $old = $role->where('id','=',$oldRole)->find();
        $new = $role->where('id','=',$newRole)->find();
        if($old['company_id'] == $new['company_id']){
            $boolRole = true;
        }else{
            $boolRole = false;
        }
        return $boolRole;
    }
    /**
     * [adminState 管理员身份状态]
     */
    public function adminState(){
        extract(input());
        $company_id = session('company_id');
        $userid = session('user_id');
        $id = input('param.id');
        $num = input('param.num');
        $map[] = ['user_id','=',$id];
        $map[] = ['company_id','=',$company_id];
        $arra = Db::name('role_user')->where($map)->find();
        $role_id = $arra['role_id'];

        //判断不能提交自己
        if($id == $userid){
            return json(['code' => 100, 'data' => '', 'msg' => '不能修改自己状态']);
        }
        //判断不能提交超级管理员
        $role = new RoleModel();
        $roleDate = $role->where('id','=',$role_id)->find();
        if($company_id != $roleDate['company_id']){
            return json(['code' => 100, 'data' => '', 'msg' => '不能修改超级管理员']);
        }
        $user = new UserService();
        $flag = $user->managerState($id,$company_id,$role_id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [delManager 撤销管理员身份]
     */
    public function delManager(){
        $companyid = session('company_id');
        $user_id = input('param.id');
        $userid = session('user_id');
        //判断不能提交自己
        if( $user_id == $userid){
            return json(['code' => 100, 'data' => '', 'msg' => '不能撤销自己']);
        }
        //删除用户角色配置
        $map[] = ['company_id','=',$companyid];
        $map[] = ['user_id','=',$user_id];
        Db::name('role_user')->where($map)->delete();
        //判断是否有别的身份，有则设置默认身份；无则改变用户表身份
        $roleDate = Db::name('role_user')->where('user_id','=',$user_id)->find();
        if(!empty($roleDate)){
            $resStatus = false;
            foreach ($roleDate as &$v){
                if($v['is_default'] =1){
                    $resStatus = true;
                }
            }
            if($resStatus = false){
                $where[] = ['user_id','=',$roleDate[0]['user_id']];
                Db::name('role_user')->where($where)->setField(['is_default'=>1]);
            }
        }else{
            //改变用户身份
            $user = new UserModel();
            $user->where('id','=',$user_id)->setField(['user_type'=>0]);
        }
        return json(['code' => 200, 'data' => '', 'msg' => '已成功撤销']);
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
                return json(['code' => 200, 'data' => '', 'msg' => '修改成功']);
            }else{
                return json(['code' => 100, 'data' => '', 'msg' => '原密码错误']);
            }
        }
        $userDate = $user->where('id','=',$userid)->field('id,nick_name,username,password')->find();
        $this->assign('item',$userDate);
        return $this->fetch("set_password");
    }

    /**
     * [editManager 修改管理员身份]
     */
    public function editManager(){
        $companyid = session('company_id');
        if(request()->isPost()){
            $param = input('post.');
            $managerData = new UserService();
            $flag = $managerData->bindManager($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['company_id','=',$companyid];
        $role = new RoleModel();
        $roleList = $role->getAllRole($map);
        $this->assign('role',$roleList);
        return $this->fetch("edit_manager");
    }


//    ----------------------------------------------------------------------------------------------------


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
     * [changeAdmin 管理员增设]
     */
    public function addAdmin(){

        return $this->fetch("add_admin");
    }
    /**
     * [changeAdmin 管理员身份更改]
     */
    public function changeAdmin(){
        return $this->fetch("user/change_admin");
    }
    /**
     * [setSafety 安全设置页面]
     */
    public function setSafety(){
        return $this->fetch("user/set_safety");
    }

    /**
     * [delete 删除角色]
     */
    public function delete()
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
     * [userEdit 编辑用户]
     * @return [type] [description]
     * @author
     */
    public function userEdit()
    {
        $user = new UserModel();
        if(request()->isPost()){
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $base64url = $param['portrait'];
            $res = base64_img($base64url,true);
            $have = "";
            if($res['code'] == 200){
                $param['portrait'] = $res['msg'];
                //判断编辑的是不是自己的头像
                if(session('uid')==$param['id']){
                    $have = "have";
                }
            }elseif($res['code'] == 100){
                writelog('编辑管理员【'.$param['username'].'】上传头像失败',100);
                return json($res);
            }
            $flag = $user->editUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg'],'type'=>$have]);
        }

        $id = input('param.id');
        if($id != "1"){
            $role = new UserType();
            $this->assign([
                'user' => $user->getOneUser($id),
                'role' => $role->getRole()
            ]);
            //普通管理员编辑页面
            return $this->fetch("user/useredit");
        }else{
            $this->assign([
                'user' => $user->getOneUser($id)
            ]);
            //超级管理员编辑页面
            return $this->fetch("user/editadmin");
        }

    }

    /**
     * [adminEdit 编辑超级管理员]
     */
    public function adminEdit(){
        $user = new UserModel();
        $oldpassword = md5(md5(input('oldpassword')).config('auth_key'));
        if(input('type')=="checkPassword"){
            $flag =  $user->checkOldPassword ($oldpassword,session('uid'));
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }else{
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $base64url = $param['portrait'];
            $res = base64_img($base64url,true);
            $have = "";
            if($res['code'] == 200){
                $param['portrait'] = $res['msg'];
                //判断编辑的是不是自己的头像
                if(session('uid')==$param['id']){
                    $have = "have";
                }
            }elseif($res['code'] == 100){
                writelog('编辑管理员【'.$param['username'].'】上传头像失败',100);
                return json($res);
            }
            $flag = $user->editUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg'],'type'=>$have]);
        }
    }

    /**
     * [UserDel 删除用户]
     */
    public function UserDel()
    {
        $id = input('param.id');
        if(session('uid')==$id){
            return json(['code'=>100,'msg'=>'不能删除自己']);
        }else{
            $role = new UserModel();
            $flag = $role->delUser($id);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }



    /**
     * [promoterUser 绑定公司的用户列表]
     */
    public function promoterUser(){
        return $this->fetch("user/promoter_user");
    }
}