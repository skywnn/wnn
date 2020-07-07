<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/25
 * Time: 22:45
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class BlogModel extends BaseModel
{
    protected $name = 'blog';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getBlogByWhere 根据搜索条件获取时辰养生列表信息]
     */
    public function getBlogByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.keywords,r.summary,r.content,r.views,r.is_rec,r.editor,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('blog_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountBlog 根据条件获取所有数据数量]
     */
    public function getCountBlog($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertBlog 添加时辰养生]
     */
    public function insertBlog($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '时辰养生添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'时辰养生添加失败'];
        }
    }

    /**
     * [updateBlog 编辑时辰养生]
     */
    public function editBlog($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '时辰养生编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '时辰养生编辑失败'];
        }
    }

    /**
     * [getOneBlog 根据时辰养生id获取一条信息]
     */
    public function getOneBlog($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delBlog 删除时辰养生]
     */
    public function delBlog($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '时辰养生删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '时辰养生删除失败'];
        }
    }

    /**
     * [blogState 时辰养生状态]
     */
    public function blogState($id,$num){
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