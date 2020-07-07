<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 22:57
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\Db;
class TicketModel extends BaseModel
{
    protected $name = 'ticket';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [insertTicket 添加活动]
     */
    public function insertTicket($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'添加失败'];
        }
    }

    /**
     * [updateTicket 编辑活动]
     */
    public function editTicket($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '活动编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '活动编辑失败'];
        }
    }
    /**
     * [delTicket 删除活动]
     */
    public function delTicket($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '活动删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '活动删除失败'];
        }
    }
    /**
     * [ 根据id获取一条信息]
     */
    public function getOneTicket($id)
    {
        return $this->where('id', $id)->find();
    }
}