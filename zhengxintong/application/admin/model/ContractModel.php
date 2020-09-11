<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:41
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class ContractModel extends BaseModel
{
    protected $name = 'contract';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getContractByWhere 根据搜索条件获取协议列表信息]
     */
    public function getContractByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }
    /**
     * [getCountContract 根据条件获取所有数据数量]
     */
    public function getCountContract($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertContract 添加协议]
     */
    public function insertContract($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '协议添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'协议添加失败'];
        }
    }

    /**
     * [updateContract 编辑协议]
     */
    public function editContract($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '协议编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '协议编辑失败'];
        }
    }

    /**
     * [getOneContract 根据协议id获取一条信息]
     */
    public function getOneContract($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delContract 删除协议]
     */
    public function delContract($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '协议删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '协议删除失败'];
        }
    }

    /**
     * [contractState 协议状态]
     */
    public function contractState($id,$num){
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            $this->where('id',$id)->setField(['status'=>$num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }

}