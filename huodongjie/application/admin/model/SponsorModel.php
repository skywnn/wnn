<?php


namespace app\admin\model;


use think\Db;

class SponsorModel extends BaseModel
{
    protected $name = 'sponsor';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getSponsorByWhere 根据条件获取主办方列表信息]
     */
    public function getSponsorByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getCountSponsor 根据条件获取所有的主办方数量]
     */
    public function getCountSponsor($where)
    {
        return $this->where($where)->count();
    }
    /**
     * [insertSponsor 插入主办方信息]
     */
    public function insertSponsor($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加公司成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [editSponsor 编辑主办方信息]
     */
    public function editSponsor($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑公司成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑公司失败'];
        }
    }

    /**
     * [getOneSponsor 根据公司id获取主办方信息]
     */
    public function getOneSponsor($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delSponsor 删除公司]
     */
    public function delSponsor($id)
    {
        $name = $this->where('id',$id)->value('name');
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除公司成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '删除公司失败'];
        }
    }


    /**
     * [sponsorState 主办方状态]
     */
    public function sponsorState($id,$num)
    {
        if($num == 2){
            $msg = '禁用';
        }elseif($num == 1){
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