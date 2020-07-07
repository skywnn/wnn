<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/12
 * Time: 14:55
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class UserModel extends BaseModel
{
    protected $name = 'user';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getUserByWhere 分页获取全部用户]
     */
    public function getUserByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllUser 根据条件获取全部用户]
     */
    public function getAllUser($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountUser 根据条件获取所有数据数量]
     */
    public function getCountUser($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertUser 添加用户]
     */
    public function insertUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '用户添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '用户添加失败'];
        }
    }

    /**
     * [editUser 编辑用户]
     * @author
     */
    public function editUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '用户编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '用户编辑失败'];
        }
    }

    /**
     * [getOneUser 根据用户id获取一条信息]
     */
    public function getOneUser($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delUser 删除用户]
     */
    public function delUser($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '用户删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '用户删除失败'];
        }
    }


    /**
     * [userState 用户状态]
     */
    public function userState($id,$num)
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