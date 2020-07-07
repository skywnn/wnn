<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 10:42
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class ActivityModel extends BaseModel
{
    protected $name = 'activity';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getActivityByWhere 根据搜索条件获取活动列表信息]
     */
    public function getActivityByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.favorite_num,r.summary,r.keywords,r.content,r.views,r.province,r.city,r.district,r.tel,r.add_like,r.is_rec,r.start_time,r.end_time,r.address,r.price,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('activity_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountActivity 根据条件获取所有数据数量]
     */
    public function getCountActivity($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertActivity 添加活动]
     */
    public function insertActivity($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '活动添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'活动添加失败'];
        }
    }

    /**
     * [updateActivity 编辑活动]
     */
    public function editActivity($param)
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
    public function getOneActivity($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delActivity 删除活动]
     */
    public function delActivity($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '活动删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '活动删除失败'];
        }
    }

    /**
     * [activityState 活动状态]
     */
    public function activityState($id,$num){
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