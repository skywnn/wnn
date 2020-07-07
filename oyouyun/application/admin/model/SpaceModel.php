<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 22:46
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class SpaceModel extends BaseModel
{
    protected $name = 'space';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getSpaceByWhere 根据搜索条件获取办公空间列表信息]
     */
    public function getSpaceByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.favorite_num,r.summary,r.keywords,r.content,r.views,r.add_like,r.is_rec,r.source,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('space_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountSpace 根据条件获取所有数据数量]
     */
    public function getCountSpace($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertSpace 添加办公空间]
     */
    public function insertSpace($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '办公空间添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'办公空间添加失败'];
        }
    }

    /**
     * [updateSpace 编辑办公空间]
     */
    public function editSpace($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '办公空间编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '办公空间编辑失败'];
        }
    }

    /**
     * [getOneSpace 根据办公空间id获取一条信息]
     */
    public function getOneSpace($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delSpace 删除办公空间]
     */
    public function delSpace($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '办公空间删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '办公空间删除失败'];
        }
    }
    /**
     * [spaceState 办公空间状态]
     */
    public function spaceState($id,$num){
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