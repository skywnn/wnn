<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 14:15
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class PaymentSlipModel extends BaseModel
{
    protected $name = 'payment_slip';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getPaymentSlipByWhere 根据搜索条件获取缴费列表信息]
     */
    public function getPaymentSlipByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.merchant_id,r.lto_dian,r.order_sn,r.leo_dian,r.number,r.unit_price,r.start_time,r.remind_time,r.end_time,r.summary,r.cate_name,r.cost,r.views,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.title as company_title')
            ->join('merchant rc', 'r.merchant_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountPaymentSlip 根据条件获取所有数据数量]
     */
    public function getCountPaymentSlip($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertPaymentSlip 添加缴费]
     */
    public function insertPaymentSlip($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '缴费添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'缴费添加失败'];
        }
    }

    /**
     * [updatePaymentSlip 编辑缴费]
     */
    public function editPaymentSlip($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '缴费编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '缴费编辑失败'];
        }
    }

    /**
     * [getOnePaymentSlip 根据缴费id获取一条信息]
     */
    public function getOnePaymentSlip($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delPaymentSlip 删除缴费]
     */
    public function delPaymentSlip($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '缴费删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '缴费删除失败'];
        }
    }
    /**
     * [paymentSlipState 缴费状态]
     */
    public function paymentSlipState($id,$num){
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            $this->where('id',$id)->setField(['status'=>$num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}