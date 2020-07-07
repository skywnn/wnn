<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/20
 * Time: 16:44
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class NodeModel extends BaseModel
{
    protected $name = 'node';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getNodeByWhere 分页获取全部权限节点]
     */
    public function getNodeByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllNode 根据条件获取全部权限节点]
     */
    public function getAllNode($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountNode 根据条件获取所有数据数量]
     */
    public function getCountNode($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertNode 添加权限节点]
     */
    public function insertNode($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '权限节点添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '权限节点添加失败'];
        }
    }

    /**
     * [editNode 编辑权限节点]
     * @author
     */
    public function editNode($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '权限节点编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '权限节点编辑失败'];
        }
    }

    /**
     * [getOneNode 根据权限节点id获取一条信息]
     */
    public function getOneNode($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delNode 删除权限节点]
     */
    public function delNode($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '权限节点删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '权限节点删除失败'];
        }
    }

    /**
     * [nodeState 权限节点状态]
     */
    public function nodeState($id,$num)
    {
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
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