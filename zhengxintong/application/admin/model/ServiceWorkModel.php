<?php


namespace app\admin\model;


use think\Db;

class ServiceWorkModel extends BaseModel
{
    protected $name = 'service_work';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getServiceWorkByWhere 根据条件获取业务列表信息]
     */
    public function getServiceWorkByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [addServiceWork 添加新业务]
     */
    public function insertServiceWork($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加业务成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [editServiceWork 编辑业务]
     */
    public function editServiceWork($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑业务成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑业务失败'];
        }
    }

    /**
     * [getOneServiceWork 根据业务id获取业务信息]
     */
    public function getOneServiceWork($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [serviceWorkState 业务状态]
     */
    public function serviceWorkState($id,$num)
    {
        if($num == 1){
            $msg = '禁用';
        }elseif($num == 0){
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

    /**
     * [delServiceWork 删除权限节点]
     */
    public function delServiceWork($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除业务成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '删除业务失败'];
        }
    }
}