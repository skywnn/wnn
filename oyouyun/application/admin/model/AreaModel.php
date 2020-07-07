<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 15:18
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class AreaModel extends BaseModel
{
    protected $name = 'area';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getAreaByWhere 分页获取全部行政区域]
     */
    public function getAreaByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllArea 根据条件获取全部行政区域]
     */
    public function getAllArea($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountArea 根据条件获取所有数据数量]
     */
    public function getCountArea($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertArea 添加行政区域]
     */
    public function insertArea($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行政区域添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行政区域添加失败'];
        }
    }

    /**
     * [editArea 编辑行政区域]
     * @author
     */
    public function editArea($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行政区域编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行政区域编辑失败'];
        }
    }

    /**
     * [getOneArea 根据行政区域id获取一条信息]
     */
    public function getOneArea($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delArea 删除行政区域]
     */
    public function delArea($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行政区域删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行政区域删除失败'];
        }
    }


    /**
     * [areaState 行政区域状态]
     */
    public function areaState($id,$num)
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