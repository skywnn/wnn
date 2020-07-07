<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 20:39
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class WorkOrderModel extends BaseModel
{
    protected $name = 'work_order';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getWorkOrderByWhere 根据搜索条件获取工单列表信息]
     */
    public function getWorkOrderByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.content,r.is_rec,r.contacts,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.tel,rc.name')
            ->join('work_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountWorkOrder 根据条件获取所有数据数量]
     */
    public function getCountWorkOrder($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertWorkOrder 添加工单]
     */
    public function insertWorkOrder($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '工单添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'工单添加失败'];
        }
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

    /**
     * [delWorkOrder 删除工单]
     */
    public function delWorkOrder($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '工单删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '工单删除失败'];
        }
    }

    /**
     * [workOrderState 工单状态]
     */
    public function workOrderState($id,$num){
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