<?php


namespace app\home\controller;


use app\home\model\ArticleModel;
use think\Db;

class Article extends Base
{
    /**
     * [articleIndex 文章列表]
     */
    public function articleIndex(){
        extract(input());
        $map = [];
        if(isset($keyword)&&$keyword!=""){
            $map[] = ['r.title','like','%'.$keyword.'%'];
        }

        if(isset($cateId)&&$cateId!=''){
            $map[] = ['r.cate_id','=',$cateId];
            $this->assign('cateId',$cateId);
        }
        $field=input('field');//字段
        $order=input('order');//排序方式
        if($field && $order){
            $od="r.".$field." ".$order;
        }else{
            $od="r.create_time desc";
        }
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = input("limit")?input("limit"):10;
        $count = Db::name('article')->alias('r')->count();//计算总页面
        $article = new ArticleModel();
        //$map[] = ['r.cate_id','=',input('cate_id')];
        $lists = $article->getArticleByWhere($map, $Nowpage, $limits,$od);
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',$count);
        return $this->fetch('article/index');
    }

    /**
     * [articleIndex 文章列表]
     */
    public function articleShow(){
        $article = new ArticleModel();
        $id = input('id');
        $flag = $article->getOneArticle($id);
        $this->assign('item',$flag);
        return $this->fetch('article/article_show');
    }


}