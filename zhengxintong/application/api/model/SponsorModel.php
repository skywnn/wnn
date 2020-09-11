<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 22:00
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\Db;
class SponsorModel extends BaseModel
{
    protected $name = 'sponsor';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [editCate 编辑]
     */
    public function editSponsor($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑失败'];
        }
    }
    /**
     * [getOneSponsor 根据id获取一条信息]
     */
    public function getOneSponsor($id)
    {
        return $this->where('id', $id)->find();
    }
}