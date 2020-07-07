<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/17
 * Time: 18:50
 */

namespace app\admin\service;

use app\admin\model\RoleModel;
use app\admin\model\CompanyModel;
use app\admin\model\UserModel;
use think\db;
class User
{
    /**
     * [managerState 管理员状态]
     */
    public function managerState($id,$company_id,$role_id,$num){

        if($num == 2){
            $msg = '冻结';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            if($id == session('user_id')){
                return ['code'=>100,'data' => '','msg'=>'不可禁用自己','type'=>'no'];
            }else {
                $map[] = ['company_id','=',$company_id];
                $map[] = ['role_id','=',$role_id];
                $map[] = ['user_id','=',$id];
                Db::name('role_user')->where($map)->setField('status', $num);
                Db::commit();// 提交事务
                return ['code' => 200, 'data' => $map, 'msg' => '已'.$msg];
            }
        }catch( \Exception $e){
            Db::rollback ();//回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
    /**
     * [getManagerIds 获取某一公司所有管理员ID]
     */
    public function getManagerIds($companyid){
        $map[] = ['company_id','=',$companyid];
        //查绑定本公司相关角色及相关用户id
        $roleuser = Db::name('role_user')
            ->where($map)
            ->select();
        $userids = array();
        if(!empty($roleuser)){
            foreach($roleuser as $key => $vo){
                $userids[$key] = $vo['user_id'];
            }
            $userids = array_unique($userids);
        }
        return $userids;
    }
    /**
     * [getManagerId 根据管理员ID码获取id]
     */
    public static function getManagerId($recommend_code){
        $user = new UserModel();
        $userDate = $user->where('recommend_code','=',$recommend_code)->field('id,recommend_code')->find();
        if(!empty($userDate)){
            return $userDate['id'];
        }else{
            return '';
        }
    }
    /**
     * [getManagerInfo 根据管理员ID查出管理员的角色及公司信息]
     */
    public function getManagerInfo($map){
        $lists['hasRole'] = Db::name('role_user')
            ->where($map)
            ->find();

        //获取该管理员的所属角色信息
        $role = new RoleModel();
        $lists['roleInfo'] = $role->getOneRole($lists['hasRole']['role_id']);

        //获取该管理员的所属公司信息
        $company = new CompanyModel();
        $lists['companyInfo'] = $company->getOneCompany($lists['hasRole']['company_id']);

        return $lists;
    }

    /**
     * [bindManager 绑定管理员]
     */
    public function bindManager($param){
        $listData = Db::name('role_user')
            ->where('user_id','=',$param['user_id'])
            ->select();
        if(!empty($listData)){
            $this->insertData($param);
        }else{
            $param['is_default'] = 1;
            $this->insertData($param);
        }
    }
    /**
     * [insertRole 插入角色信息]
     */
    public function insertData($param)
    {
        Db::startTrans();// 启动事务
        try{
            Db::name('role_user')->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加失败'];
        }
    }
}