<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/16
 * Time: 16:57
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class MenuModel extends BaseModel
{
    protected  $name = 'menu';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getMenuByWhere 根据条件获取菜单列表信息]
     */
    public function getMenuByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllMenu 根据条件获取所有菜单信息]
     */
    public function getAllMenu($where)
    {
        return $this->where($where)->order('sort')->select();
    }

    /**
     * [getMenuByWhere 根据条件获取所有的菜单数量]
     */
    public function getCountMenu($where)
    {
        return $this->where($where)->count();
    }

    /**
     * [insertMenu 插入菜单信息]
     */
    public function insertMenu($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加菜单成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加菜单失败'];
        }
    }

    /**
     * [editMenu 编辑菜单信息]
     */
    public function editMenu($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑菜单成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑菜单失败'];
        }
    }

    /**
     * [getMenuInfo 根据菜单id获取菜单信息]
     */
    public function getMenuInfo($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delMenu 删除菜单]
     */
    public function delMenu($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除菜单成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '删除菜单失败'];
        }
    }

    /**
     * [roleState 菜单状态]
     */
    public function menuState($id,$num){

        if($num == 2){
            $msg = '禁用';
        }elseif($num == 1){
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            $this->where ('id' , $id)->setField (['status' => $num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}