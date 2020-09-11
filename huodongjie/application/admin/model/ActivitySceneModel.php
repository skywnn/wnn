<?php


namespace app\admin\model;


use think\Db;
use think\Exception;

class ActivitySceneModel extends BaseModel
{
    protected $name = 'activity_scene';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [insertActivity 添加活动]
     */
    public function insertScene($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '活动添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>$e->getMessage()];
        }
    }

    public function editScene($param){
        Db::startTrans();
        try{
            $this->allowField(true)->save($param,['id'=>$param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '活动添加成功'];
        }catch(\Exception $e){
            Db::rollback();
            return ['code' => 100, 'data' => '', 'msg' =>$e->getMessage()];
        }
    }
}