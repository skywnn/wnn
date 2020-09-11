<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/17
 * Time: 20:39
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\db;
class CompanyModel extends BaseModel
{
    protected $name = 'company';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getCompanyByWhere 根据条件获取公司列表信息]
     */
    public function getCompanyByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getCountCompany 根据条件获取所有的公司数量]
     */
    public function getCountCompany($where)
    {
        return $this->where($where)->count();
    }
    /**
     * [insertCompany 插入公司信息]
     */
    public function insertCompany($param)
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
     * [editCompany 编辑公司信息]
     */
    public function editCompany($param)
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
     * [getOneCompany 根据公司id获取公司信息]
     */
    public function getOneCompany($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delCompany 删除公司]
     */
    public function delCompany($id)
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
     * [companyState 公司状态]
     */
    public function companyState($id,$num)
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