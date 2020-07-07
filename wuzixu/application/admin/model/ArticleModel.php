<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/18
 * Time: 19:17
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class ArticleModel extends BaseModel
{
    protected $name = 'article';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getArticleByWhere 根据搜索条件获取文章列表信息]
     */
    public function getArticleByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.thum_pic,r.pic,r.remark,r.remark_en,r.summary,r.keywords,r.content,r.views,r.add_like,r.is_rec,r.source,r.editor,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('article_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountArticle 根据条件获取所有数据数量]
     */
    public function getCountArticle($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertArticle 添加文章]
     */
    public function insertArticle($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '文章添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'文章添加失败'];
        }
    }

    /**
     * [updateArticle 编辑文章]
     */
    public function editArticle($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '文章编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '文章编辑失败'];
        }
    }

    /**
     * [getOneArticle 根据文章id获取一条信息]
     */
    public function getOneArticle($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delArticle 删除文章]
     */
    public function delArticle($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '文章删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '文章删除失败'];
        }
    }
    /**
     * [articleState 文章状态]
     */
    public function articleState($id,$num){
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