<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/17
 * Time: 12:32
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class PaymentSlipModel extends BaseModel
{
    protected $name = 'payment_slip';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getPaymentSlipByWhere 根据搜索条件获取缴费列表信息]
     */
    public function getPaymentSlipByWhere($map,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.merchant_id,r.number,r.start_time,r.order_sn,r.end_time,r.lto_dian,r.leo_dian,r.cate_name,r.unit_price,r.cost,r.type,r.pic,r.company_id,r.create_time,r.update_time,r.status,rc.room_num,rc.logo,rc.title as company_title')
            ->join('merchant rc', 'r.merchant_id = rc.id')
            ->where($map)
            ->order($od)
            ->select();
    }
    public function getOnePaymentSlipByWhere($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.merchant_id,r.number,r.order_sn,r.start_time,r.end_time,r.lto_dian,r.leo_dian,r.cate_name,r.unit_price,r.cate_name,r.cost,r.type,r.pic,r.company_id,r.create_time,r.update_time,r.status,rc.room_num,rc.logo,rc.title as company_title')
            ->join('merchant rc', 'r.merchant_id = rc.id')
            ->where($map)
            ->find();
    }
    /**
     * [updateActivity 编辑活动]
     */
    public function editPaymentSlip($param)
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
     * [getOneActivity 根据活动id获取一条信息]
     */
    public function getOnePaymentSlip($id)
    {
        return $this->where('id', $id)->find();
    }
}