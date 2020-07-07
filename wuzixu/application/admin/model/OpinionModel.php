<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/5/27
 * Time: 11:09
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class OpinionModel extends BaseModel
{
    protected $name = 'opinion';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getCateByWhere 分页获取全部分类]
     */
    public function getOpinionByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllOpinion 根据条件获取全部分类]
     */
    public function getAllOpinion($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountOpinion 根据条件获取所有数据数量]
     */
    public function getCountOpinion($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertOpinion 添加分类]
     */
    public function insertOpinion($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加失败'];
        }
    }

    /**
     * [editOpinion 编辑分类]
     * @author
     */
    public function editOpinion($param)
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
     * [getOneOpinion 根据分类id获取一条信息]
     */
    public function getOneOpinion($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delOpinion 删除分类]
     */
    public function delOpinion($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '删除失败'];
        }
    }


    /**
     * [opinionState 分类状态]
     */
    public function opinionState($id,$num)
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