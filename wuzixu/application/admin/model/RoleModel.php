<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:33
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class RoleModel extends BaseModel
{
    protected  $name = 'role';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getRoleByWhere 根据条件获取角色列表信息]
     */
    public function getRoleByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllRole 获取所有的角色信息]
     */
    public function getAllRole($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountRole 根据条件获取所有的角色数量]
     */
    public function getCountRole($where)
    {
        return $this->where($where)->count();
    }

    /**
     * checkName 验证角色名称唯一性
     */
    public function checkRole($title,$uid){
        if($uid != ''){
            $uname = $this->where('id',$uid)->value('title');
            if($uname == $title){
                return ['code' => 200, 'msg' => 'true'];
            }
        }
        $result = $this->where('title',$title)->find();
        if($result){
            return ['code' => 100, 'msg' => 'false'];
        }else{
            return ['code' => 200, 'msg' => 'true'];
        }
    }

    /**
     * [insertRole 插入角色信息]
     */
    public function insertRole($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加角色成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加角色失败'];
        }
    }

    /**
     * [editRole 编辑角色信息]
     */
    public function editRole($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑角色成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑角色失败'];
        }
    }

    /**
     * [getOneRole 根据角色id获取角色信息]
     */
    public function getOneRole($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delRole 删除角色]
     */
    public function delRole($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除角色成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '删除角色失败'];
        }
    }

    /**
     * [getRoleInfo 获取角色信息]
     */
    public function getRoleInfo($id){

        $result = Db::name('role')->where('id', $id)->find();
//        if($result['rules'] == "SUPERAUTH"){
//            $res = Db::name('auth_rule')->field('name')->select();
//
//            foreach($res as $key=>$vo){
//                if('#' != $vo['name']){
//                    $result['name'][] = $vo['name'];
//                }
//            }
//        }elseif(empty($result['rules'])){
//            $result['title'] ="";
//            $result['rules'] ="";
//            $result['name'] ="";
//        }else{
//            $where = 'id in('.$result['rules'].')';
//            $res = Db::name('auth_rule')->field('name')->where($where)->select();
//
//            foreach($res as $key=>$vo){
//                if('#' != $vo['name']){
//                    $result['name'][] = $vo['name'];
//                }
//            }
//        }
        return $result;
    }

    /**
     * [roleState 角色状态]
     */
    public function roleState($id,$num){
        $name= $this->where('id',$id)->value('name');
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            if($id == session('role_id')){
                return ['code'=>100,'data' => '','msg'=>'不可禁用自己的角色','type'=>'no'];
            }else{
                $this->where ('id' , $id)->setField (['status' => $num]);
                Db::commit();// 提交事务
                return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
            }
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}