<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 14:55
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class MemberModel extends BaseModel
{
    protected $name = 'member';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getUserByWhere 根据条件获取用户列表信息]
     */
    public function getMemberByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }
    /**
     * [getCountRole 根据条件获取所有的数量]
     */
    public function getCountMember($where)
    {
        return $this->where($where)->count();
    }


    /**
     * 根据搜索条件获取所有的用户数量
     */
    public function getAllMember($where)
    {
        return $this->where($where)->count();
    }
    /**
     * [根据id获取一条信息]
     */
    public function getOneMember($id)
    {
        return $this->where('id', $id)->find();
    }
    /**
     * [ 编辑]
     */
    public function editMember($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '修改成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '修改失败'];
        }
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
            return ['code' => 200, 'data' => '', 'msg' => '文章删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '文章删除失败'];
        }
    }
}