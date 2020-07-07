<?php


namespace app\admin\model;


use think\Db;

class OpportunityModel extends BaseModel
{
    protected $name = 'crm_opportunity';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getActivityByWhere 根据搜索条件获取商机列表信息]
     */
    public function getOpportunityByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias('o')->join('goods g','o.goods_id=g.id')
            ->field("o.*,g.title as 'goods_name'")
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }


    /**
     * [updateOpportunity 编辑商机]
     */
    public function editOpportunity($param)
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
     * [getOneActivity 根据商机id获取一条信息]
     */
    public function getOneOpportunity($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delOpportunity 删除商机]
     */
    public function delOpportunity($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商机删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '商机删除失败'];
        }
    }

}