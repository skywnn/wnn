<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/17
 * Time: 16:31
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class WorkOrderModel extends BaseModel
{
    protected $name = 'work_order';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getWorkOrderByWhere 根据搜索条件获取工单列表信息]
     */
    public function getWorkOrderByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.content,r.is_rec,r.contacts,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.tel,rc.name')
            ->join('work_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }

    /**
     * [updateWorkOrder 编辑工单]
     */
    public function editWorkOrder($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '工单编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '工单编辑失败'];
        }
    }

    /**
     * [getOneWorkOrder 根据工单id获取一条信息]
     */
    public function getOneWorkOrder($id)
    {
        return $this->where('id', $id)->find();
    }
}