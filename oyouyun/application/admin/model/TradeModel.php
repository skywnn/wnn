<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 11:40
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class TradeModel extends BaseModel
{
    protected $name = 'trade';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getTradeByWhere 分页获取全部行业]
     */
    public function getTradeByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllTrade 根据条件获取全部行业]
     */
    public function getAllTrade($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountTrade 根据条件获取所有数据数量]
     */
    public function getCountTrade($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertTrade 添加行业]
     */
    public function insertTrade($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行业添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行业添加失败'];
        }
    }

    /**
     * [editTrade 编辑行业]
     * @author
     */
    public function editTrade($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行业编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行业编辑失败'];
        }
    }

    /**
     * [getOneTrade 根据行业id获取一条信息]
     */
    public function getOneTrade($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delTrade 删除行业]
     */
    public function delTrade($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '行业删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '行业删除失败'];
        }
    }

    /**
     * [tradeState 行业状态]
     */
    public function tradeState($id,$num)
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