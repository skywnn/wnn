<?php
/**
 * Created by PhpStorm.
 * Member: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:50
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class MemberModel extends BaseModel
{
    protected $name = 'member';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";


    /**
     * [getMemberByWhere 分页获取全部用户]
     */
    public function getMemberByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllMember 根据条件获取全部用户]
     */
    public function getAllMember($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountMember 根据条件获取所有数据数量]
     */
    public function getCountMember($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertMember 添加用户]
     */
    public function insertMember($param)
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
     * [editMember 编辑用户]
     * @author
     */
    public function editMember($param)
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
     * [getOneMember 根据用户id获取一条信息]
     */
    public function getOneMember($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delMember 删除用户]
     */
    public function delMember($id)
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
     * [memberState 用户状态]
     */
    public function memberState($id,$num)
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