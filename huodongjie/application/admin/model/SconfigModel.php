<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:34
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class SconfigModel extends BaseModel
{
    protected $name = 'sconfig';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getSconfigByWhere 分页获取全部站点配置]
     */
    public function getSconfigByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllSconfig 根据条件获取全部站点配置]
     */
    public function getAllSconfig($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountSconfig 根据条件获取所有数据数量]
     */
    public function getCountSconfig($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertSconfig 添加站点配置]
     */
    public function insertSconfig($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '站点配置添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '站点配置添加失败'];
        }
    }

    /**
     * [editSconfig 编辑站点配置]
     * @author
     */
    public function editSconfig($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '站点配置编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '站点配置编辑失败'];
        }
    }

    /**
     * [getOneSconfig 根据站点配置id获取一条信息]
     */
    public function getOneSconfig($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delSconfig 删除站点配置]
     */
    public function delSconfig($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '站点配置删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '站点配置删除失败'];
        }
    }


    /**
     * [sconfigState 站点配置状态]
     */
    public function sconfigState($id,$num)
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