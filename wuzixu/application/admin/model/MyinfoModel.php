<?php


namespace app\admin\model;


use think\Db;

class MyinfoModel extends BaseModel
{

    protected $name = 'myinfo';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getOneCompany 根据公司id获取公司信息]
     */
    public function getOneMyinfo($id)
    {
        return $this->where('id', $id)->find();
    }
    /**
     * [editCompany 编辑公司信息]
     */
    public function editMyinfo($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑公司成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑公司失败'];
        }
    }
}