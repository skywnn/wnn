<?php


namespace app\admin\model;


use think\Db;

class TicketModel extends BaseModel
{
    protected $name = 'ticket';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * 根据条件查询票种信息
     */
    public function getTicketByWhere($map, $Nowpage, $limits,$od){
        $res = $this->alias('t')->where($map)->page($Nowpage,$limits)->order($od)->select();
        return $res;
    }

    /**
     * [editTicket 编辑票种信息]
     */
    public function editTicket($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑票种成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑票种失败'];
        }
    }

    public function addTicket($param){
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加票种成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加票种失败'];
        }
    }
}