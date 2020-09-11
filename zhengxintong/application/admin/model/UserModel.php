<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:32
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class UserModel extends BaseModel
{
    protected $name = 'user';
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getUserByWhere 根据条件获取用户列表信息]
     */
    public function getUserByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }
    /**
     * [getCountRole 根据条件获取所有的角色数量]
     */
    public function getCountUser($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     */
    public function getUserCount($where){
        return $this->alias('a')
            ->where($where)
            ->count();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     */
    public function getAllUsers($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入管理员信息
     */
    public function insertUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' =>"", 'msg' => '添加管理员成功'];
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            writelog('管理员【'.$param['username'].'】添加失败',100);
            return ['code' => 100, 'data' => '', 'msg' => '添加管理员失败'];
        }
    }

    /**
     * 编辑管理员信息
     */
    public function editUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            if($param['id'] != 1){
                Db::name('auth_group_access')->where('uid',$param['id'])->setField ('group_id',$param['groupid']);
            }
            Db::commit();// 提交事务
            $status = '';
            if($param['id']==session('uid')){
                session('portrait', $param['portrait']); //用户头像
                if(isset($param['password']) && $param['password'] != ""){
                    $status = 100;
                }
            }
            return ['code' => 200, 'data' => $status, 'msg' => '编辑用户成功'];
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            writelog('管理员【'.$param['username'].'】编辑失败',100);
            return ['code' => 100, 'data' => '', 'msg' =>'编辑用户失败'];
        }
    }


    /**
     * 验证原始密码
     */
    public function checkOldPassword($oldpassword,$id){
        $password =  $this->where("id",$id)->value("password");
        if($password === $oldpassword){
            return ['code' => 200, 'data' => '', 'msg' =>'true'];
        }else{
            return ['code' => 100, 'data' => '', 'msg' =>'false'];
        }

    }

    /**
     * checkName 验证管理员名称唯一性
     */
    public function checkName($username,$uid){
        if($uid != ''){
            $uname = $this->where('id',$uid)->value('username');
            if($uname == $username){
                return ['code' => 200, 'msg' => 'true'];
            }
        }
        $result = $this->where('username',$username)->find();
        if($result){
            return ['code' => 100, 'msg' => 'false'];
        }else{
            return ['code' => 200, 'msg' => 'true'];
        }
    }


    /**
     * 根据管理员id获取角色信息
     */
    public function getOneUser($id)
    {
        return $this->alias('u')
            ->join('member m','m.id=u.m_id','LEFT')
            ->where('u.id', $id)->field('u.*,m.nick_name')->find();
    }


    /**
     * 删除管理员
     */
    public function delUser($id)
    {
        $name = $this->where('id', $id)->value('username');
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::name('auth_group_access')->where('uid', $id)->delete();
            Db::commit();// 提交事务
            writelog('管理员【'.$name.'】删除成功(ID='.$id.')',200);
            return ['code' => 200, 'data' => '', 'msg' => '删除用户成功'];
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            writelog('管理员【'.$name.'】删除失败(ID='.$id.')',100);
            return ['code' => 100, 'data' => '', 'msg' => '删除用户失败'];
        }
    }


    /**
     * editPassword 修改管理员密码
     */
    public function editPassword($param){
        $name = $this->where('id',session('uid'))->value('username');
        Db::startTrans();// 启动事务
        try{
            $this->allowField (true)->save($param,['id'=>session('uid')]);
            Db::commit();// 提交事务
            writelog('管理员【'.$name.'】修改密码成功',200);
            return ['code'=>200,'msg'=>'密码修改成功，请重新登录！'];
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            writelog('管理员【'.$name.'】修改密码失败',100);
            return ['code'=>100,'msg'=>'密码修改失败'];
        }
    }

    /**
     * [userState 用户状态]
     */
    public function userState($id,$num){
        $username = $this->where('id',$id)->value('username');
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            if($id == session('uid')){
                return ['code'=>100,'data' => '','msg'=>'不可禁用自己','type'=>'no'];
            }else {
                $this->where ('id' , $id)->setField (['status' => $num]);
                Db::commit();// 提交事务
                writelog('管理员【'.$username.'】'.$msg.'成功',200);
                return ['code' => 200, 'data' => '', 'msg' => '已'.$msg];
            }
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            writelog('管理员【'.$username.'】'.$msg.'失败',100);
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}