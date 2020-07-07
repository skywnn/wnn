<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 11:01
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class ArticleModel extends BaseModel
{
    protected $name = 'article';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getArticleByWhere 根据搜索条件获取文章列表信息]
     */
    public function getArticleByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.remark,r.thum_pic,r.pic,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('article_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getArticleInfo 获取文章详细信息]
     */
    public function getArticleInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.thum_pic,r.pic,r.remark,r.content,r.remark_en,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('article_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }
    /**
     * [getArticleInfo 获取文章信息]
     */
    public function getArticleTitle($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.remark,r.thum_pic,r.pic,r.remark_en,r.summary,r.pic,r.status,rc.name')
            ->join('article_cate rc', 'r.cate_id = rc.id')
            ->whereTime('r.start_time', '<=', time())
            ->whereTime('r.end_time', '>', time())
            ->where($map)
            ->find();
    }
    /**
     * [getOneCate 根据分类id获取一条信息]
     */
    public function getOneArticle($map)
    {
        return $this->where($map)->find();
    }
}