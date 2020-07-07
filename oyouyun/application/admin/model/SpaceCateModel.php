<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 22:46
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class SpaceCateModel extends BaseModel
{
    protected $name = 'space_cate';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getCateByWhere 分页获取全部分类]
     */
    public function getCateByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getAllCate 根据条件获取全部分类]
     */
    public function getAllCate($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountCate 根据条件获取所有数据数量]
     */
    public function getCountCate($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertCate 添加分类]
     */
    public function insertCate($param)
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
     * [editCate 编辑分类]
     * @author
     */
    public function editCate($param)
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
     * [getOneCate 根据分类id获取一条信息]
     */
    public function getOneCate($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delCate 删除分类]
     */
    public function delCate($id)
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
     * [cateState 分类状态]
     */
    public function cateState($id,$num)
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