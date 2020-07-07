<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 12:53
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class MerchantModel extends BaseModel
{
    protected $name = 'merchant';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getMerchantByWhere 根据搜索条件获取商户列表信息]
     */
    public function getMerchantByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.room_num,r.pic,r.abbreviation,r.logo,r.views,r.is_rec,r.source,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('merchant_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getAllMerchant 获取全部分类]
     */
    public function getAllMerchant($map)
    {
        return $this->where($map)->select();
    }
    /**
     * [getCountMerchant 根据条件获取所有数据数量]
     */
    public function getCountMerchant($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertMerchant 添加商户]
     */
    public function insertMerchant($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '商户添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'商户添加失败'];
        }
    }

    /**
     * [updateMerchant 编辑商户]
     */
    public function editMerchant($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商户编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '商户编辑失败'];
        }
    }

    /**
     * [getOneMerchant 根据商户id获取一条信息]
     */
    public function getOneMerchant($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delMerchant 删除商户]
     */
    public function delMerchant($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商户删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '商户删除失败'];
        }
    }
    /**
     * [merchantState 商户状态]
     */
    public function merchantState($id,$num){
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            $this->where('id',$id)->setField(['status'=>$num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}