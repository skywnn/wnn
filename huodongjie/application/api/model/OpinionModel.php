<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 21:49
 */

namespace app\api\model;

use app\admin\model\BaseModel;
use think\Db;
class OpinionModel extends BaseModel
{
    protected $name = 'opinion';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [insertCate 添加分类]
     */
    public function insertOpinion($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加失败'];
        }
    }
}