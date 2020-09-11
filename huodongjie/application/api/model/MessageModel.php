<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:48
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class MessageModel extends BaseModel
{
    protected $name = 'message';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getMessageByWhere 分页获取全部雷达消息]
     */
    public function getMessageByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllMessage 根据条件获取全部雷达消息]
     */
    public function getAllMessage($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountMessage 根据条件获取所有数据数量]
     */
    public function getCountMessage($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertMessage 添加雷达消息]
     */
    public function insertMessage($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '雷达消息添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '雷达消息添加失败'];
        }
    }

    /**
     * [editMessage 编辑雷达消息]
     * @author
     */
    public function editMessage($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '雷达消息编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '雷达消息编辑失败'];
        }
    }

    /**
     * [getOneMessage 根据雷达消息id获取一条信息]
     */
    public function getOneMessage($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delMessage 删除雷达消息]
     */
    public function delMessage($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '雷达消息删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '雷达消息删除失败'];
        }
    }


    /**
     * [messageState 雷达消息状态]
     */
    public function messageState($id,$num)
    {
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try {
            $this->where ('id' , $id)->setField(['status' => $num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        } catch (\Exception $e) {
            Db::rollback();// 回滚事务
            return ['code' => 100 , 'data' => '' , 'msg' => $msg.'失败'];
        }
    }
}