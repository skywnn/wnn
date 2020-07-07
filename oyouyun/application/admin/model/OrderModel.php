<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 19:36
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\db;
class OrderModel extends BaseModel
{
    protected $name = 'order';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getActivityByWhere 根据搜索条件获取活动列表信息]
     */
    public function getOrderInfoByWhere($map, $Nowpage, $limits,$od)
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
    public function getOrderByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllOrder 根据条件获取全部分类]
     */
    public function getAllOrder($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountOrder 根据条件获取所有数据数量]
     */
    public function getCountOrder($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertOrder 添加分类]
     */
    public function insertOrder($param)
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
     * [editOrder 编辑分类]
     * @author
     */
    public function editOrder($param)
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
     * [getOneOrder 根据分类id获取一条信息]
     */
    public function getOneOrder($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delOrder 删除分类]
     */
    public function delOrder($id)
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
     * [orderState 分类状态]
     */
    public function orderState($id,$num)
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