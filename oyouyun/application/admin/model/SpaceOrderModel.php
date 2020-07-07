<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 0:11
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\db;
class SpaceOrderModel extends BaseModel
{
    protected $name = 'space_order';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getActivityByWhere 根据搜索条件获取活动列表信息]
     */
    public function getSpaceOrderInfoByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.con_name,r.contacts,r.phone,r.con_id,r.title,r.pic,r.price,r.add_like,r.is_rec,r.start_time,r.end_time,r.address,r.price,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('user rc', 'r.user_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCateByWhere 分页获取全部分类]
     */
    public function getSpaceOrderByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllSpaceOrder 根据条件获取全部分类]
     */
    public function getAllSpaceOrder($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountSpaceOrder 根据条件获取所有数据数量]
     */
    public function getCountSpaceOrder($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertSpaceOrder 添加分类]
     */
    public function insertSpaceOrder($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类添加失败'];
        }
    }

    /**
     * [editSpaceOrder 编辑分类]
     * @author
     */
    public function editSpaceOrder($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类编辑失败'];
        }
    }

    /**
     * [getOneSpaceOrder 根据分类id获取一条信息]
     */
    public function getOneSpaceOrder($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delSpaceOrder 删除分类]
     */
    public function delSpaceOrder($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类删除失败'];
        }
    }


    /**
     * [spaceOrderState 分类状态]
     */
    public function spaceOrderState($id,$num)
    {
        if($num == 2){
            $msg = '驳回';
        }else{
            $msg = '审核';
        }
        Db::startTrans();// 启动事务
        try {
            $this->where ('id' , $id)->setField (['status' => $num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        } catch (\Exception $e) {
            Db::rollback();// 回滚事务
            return ['code' => 100 , 'data' => '' , 'msg' => $msg.'失败'];
        }
    }
}