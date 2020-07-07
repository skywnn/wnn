<?php


namespace app\admin\model;


use think\Db;

class YuanQuModel extends BaseModel
{
    /**
     * [insertYuanQu 添加园区]
     */
    public function insertYuanQu($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '活动添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'活动添加失败'];
        }
    }
}