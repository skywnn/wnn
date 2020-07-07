<?php


namespace app\home\model;


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
     * [getOneArticle 根据文章id获取一条信息]
     */
    public function getOneArticle($id)
    {
        return $this->where('id', $id)->find();
    }

    protected function test()
    {
         return 1111;
    }
}