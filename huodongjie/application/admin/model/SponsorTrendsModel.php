<?php


namespace app\admin\model;


use think\Db;

class SponsorTrendsModel extends BaseModel
{
    protected $name = 'sponsor_trends';
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [trendsState 动态状态]
     */
    public function trendsState($param){
        $msg = "";
        $data = [];
        switch ($param['status']) {
            case 1:
                $data['status'] = $param['status'];
                $msg = "审核成功";
                break;
            case 2:
                $data['status'] = $param['status'];
                $data['refuse_cause'] = $param['refuse_cause'];
                $msg = "拒绝成功";
                break;
        }
        Db::startTrans();// 启动事务
        try{
            $this->where('id',$param['id'])->setField($data);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => $msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}