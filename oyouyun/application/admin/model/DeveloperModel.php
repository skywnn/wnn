<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/1
 * Time: 17:23
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class DeveloperModel extends BaseModel
{
    protected $name = 'developer';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getDeveloperByWhere 分页获取全部开发文档]
     */
    public function getDeveloperByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllDeveloper 根据条件获取全部开发文档]
     */
    public function getAllDeveloper($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountDeveloper 根据条件获取所有数据数量]
     */
    public function getCountDeveloper($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertDeveloper 添加开发文档]
     */
    public function insertDeveloper($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '开发文档添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '开发文档添加失败'];
        }
    }

    /**
     * [editDeveloper 编辑开发文档]
     * @author
     */
    public function editDeveloper($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '开发文档编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '开发文档编辑失败'];
        }
    }

    /**
     * [getOneDeveloper 根据开发文档id获取一条信息]
     */
    public function getOneDeveloper($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delDeveloper 删除开发文档]
     */
    public function delDeveloper($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '开发文档删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '开发文档删除失败'];
        }
    }


    /**
     * [developerState 开发文档状态]
     */
    public function developerState($id,$num)
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