<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/10
 * Time: 21:05
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class MerchantPicsModel extends BaseModel
{
    protected $name = 'merchant_pics';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [添加]
     */
    public function insertMerchantPics($param)
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
    /**
     * [ 根据id获取一条信息]
     */
    public function getOneMerchantPics($id)
    {
        return $this->where('id', $id)->find();
    }
}