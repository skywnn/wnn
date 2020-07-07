<?php


namespace app\admin\model;

use think\Db;

class DealingsModel extends BaseModel
{
    protected $name = 'crm_dealings';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getDealingsByWhere 根据搜索条件获取往来列表信息]
     */
    public function getDealingsByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias('d')->join('crm_contacts c','d.contacts_id=c.id')
            ->join('crm_opportunity o','d.opportunity_id=o.id')
            ->where($map)
            ->field('d.*,c.name,o.title')
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [updateDealings 编辑往来]
     */
    public function editDealings($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '活动编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '活动编辑失败'];
        }
    }

    /**
     * [getOneDealings 根据往来id获取一条信息]
     */
    public function getOneDealings($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delDealings 删除往来]
     */
    public function delDealings($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '往来信息删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '往来信息删除失败'];
        }
    }
}