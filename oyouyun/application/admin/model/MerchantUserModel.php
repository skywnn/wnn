<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/16
 * Time: 23:31
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class MerchantUserModel extends BaseModel
{
    protected $name = 'merchant_user';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getMerchantUserByWhere 分页获取全部分类]
     */
    public function getMerchantUserByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->alias ('r')
            ->field('r.id,r.user_id,r.merchant_id,r.role_type,r.is_default,r.company_id,r.status,rc.nick_name,rc.real_name,rc.phone')
            ->join('user rc', 'r.user_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    public function getMerchantUserInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.user_id,r.merchant_id,r.role_type,r.is_default,r.company_id,r.status,rc.nick_name,rc.real_name,rc.phone')
            ->join('user rc', 'r.user_id = rc.id')
            ->where($map)
            ->find();
    }
    /**
     * [getAllMerchantUser 根据条件获取全部分类]
     */
    public function getAllMerchantUser($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountMerchantUser 根据条件获取所有数据数量]
     */
    public function getCountMerchantUser($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertMerchantUser 添加分类]
     */
    public function insertMerchantUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类添加失败'];
        }
    }

    /**
     * [editMerchantUser 编辑分类]
     * @author
     */
    public function editMerchantUser($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类编辑失败'];
        }
    }

    /**
     * [getOneMerchantUser 根据分类id获取一条信息]
     */
    public function getOneMerchantUser($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delMerchantUser 删除分类]
     */
    public function delMerchantUser($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '分类删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '分类删除失败'];
        }
    }


    /**
     * [merchantUserState 分类状态]
     */
    public function merchantUserState($id,$num)
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