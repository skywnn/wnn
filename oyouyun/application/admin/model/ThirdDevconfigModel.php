<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/1
 * Time: 13:12
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class ThirdDevconfigModel extends BaseModel
{
    protected $name = 'third_devconfig';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getThirdDevconfigByWhere 分页获取全部第三方平台]
     */
    public function getThirdDevconfigByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllThirdDevconfig 根据条件获取全部第三方平台]
     */
    public function getAllThirdDevconfig($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountThirdDevconfig 根据条件获取所有数据数量]
     */
    public function getCountThirdDevconfig($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertThirdDevconfig 添加第三方平台]
     */
    public function insertThirdDevconfig($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '第三方平台添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '第三方平台添加失败'];
        }
    }

    /**
     * [editThirdDevconfig 编辑第三方平台]
     * @author
     */
    public function editThirdDevconfig($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '第三方平台编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '第三方平台编辑失败'];
        }
    }

    /**
     * [getOneThirdDevconfig 根据第三方平台id获取一条信息]
     */
    public function getOneThirdDevconfig($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delThirdDevconfig 删除第三方平台]
     */
    public function delThirdDevconfig($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '第三方平台删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '第三方平台删除失败'];
        }
    }


    /**
     * [thirdDevconfigState 第三方平台状态]
     */
    public function thirdDevconfigState($id,$num)
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