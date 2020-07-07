<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/29
 * Time: 22:39
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class BlogDayModel extends BaseModel
{
    protected $name = 'blog_day';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getBlogDayByWhere 分页获取全部分类]
     */
    public function getBlogDayByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order('create_time desc')->select();
    }

    /**
     * [getAllBlogDay 根据条件获取全部分类]
     */
    public function getAllBlogDay($map)
    {
        return $this->where($map)->select();
    }

    /**
     * [getCountBlogDay 根据条件获取所有数据数量]
     */
    public function getCountBlogDay($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [insertBlogDay 添加分类]
     */
    public function insertBlogDay($param)
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
     * [editBlogDay 编辑分类]
     * @author
     */
    public function editBlogDay($param)
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
     * [getOneBlogDay 根据分类id获取一条信息]
     */
    public function getOneBlogDay($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delBlogDay 删除分类]
     */
    public function delBlogDay($id)
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
    public function blogDayState($id,$num)
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