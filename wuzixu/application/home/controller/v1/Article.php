<?php


namespace app\home\controller\v1;

use app\home\controller\Base;
use app\home\model\ArticleModel;
use app\home\model\WebMenuModel;
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

        if(isset($cateId)&&$cateId!=0){
            $map[] = ['r.cate_id','=',$cateId];
            $this->assign('cateId',$cateId);
        } else {
            $this->assign('cateId',0);
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
        $count = Db::name('article')->alias('r')->where($map)->count();//计算总页面
        $article = new ArticleModel();
        $web = new WebMenuModel();
        $menuMap[] = ['pid','=',input('pid')];
        $menuList = $web->getAllWebMenu($menuMap);
        $oneMenu = $web->getWebMenuInfo(input('pid'));
        if(isset($cateId)&&$cateId!=''){
            $map[] = ['r.cate_id','=',$cateId];
            $this->assign('cateId',$cateId);
        } else {
            $this->assign('cateId',0);
        }
        $lists = $article->getArticleByWhere($map, $Nowpage, $limits,$od);
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',ceil($count/$limits));
        $this->assign('pid',input('pid'));
        $this->assign('menuList',$menuList);
        $this->assign('oneMenu',$oneMenu);
        return $this->fetch('article_index');
    }

    /**
     * [articleIndex 文章列表]
     */
    public function articleShow(){
        $article = new ArticleModel();
        $id = input('id');
        $flag = $article->getOneArticle($id);
        $this->assign('item',$flag);
        return $this->fetch('article_show');
    }
}