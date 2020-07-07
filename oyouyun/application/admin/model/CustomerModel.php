<?php


namespace app\admin\model;


use think\Db;
use think\Model;

class CustomerModel extends Model
{
    protected $name = 'crm_customer';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     *[getCustomers 根据条件查询客户]
     */
    public function getCustomers($map,$Nowpage,$limits,$od){
        return $this->alias('c')->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCustomerById 查询客户详情信息]
     */
    public function getCustomerBy($map){
        $res = $this->where($map)->find();
        return $res;
    }

    /**
     * [updateActivity 编辑客户信息]
     */
    public function editCustomer($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '客户编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [delActivity 删除客户信息]
     */
    public function delCustomer($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '客户删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '客户删除失败'];
        }
    }

    /**
     * [insertCustomer 添加活动]
     */
    public function insertCustomer($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '客户添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>$e->getMessage()];
        }
    }
}