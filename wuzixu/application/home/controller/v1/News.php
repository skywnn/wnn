<?php


namespace app\home\controller\v1;


use app\home\controller\Base;
use app\home\model\NewsModel;
use app\home\model\WebMenuModel;
use think\Db;

class News extends Base
{
    public function newsIndex(){
        extract(input());
        $map = [];
        if(isset($keyword)&&$keyword!=""){
            $map[] = ['name','like','%'.$keyword.'%'];
        }
        $field=input('field');//字段
        $order=input('order');//排序方式
        if($field && $order){
            $od="".$field." ".$order;
        }else{
            $od="create_time desc";
        }
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = input("limit")?input("limit"):10;
        $count = Db::name('news')->where($map)->count();//计算总页面
        $article = new NewsModel();
        $web = new WebMenuModel();
        $menuMap[] = ['pid','=',input('pid')];
        $menuList = $web->getAllWebMenu($menuMap);
        $oneMenu = $web->getWebMenuInfo(input('pid'));
        $lists = $article->getNewsByWhere($map, $Nowpage, $limits,$od);
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',ceil($count/$limits));
        $this->assign('pid',input('pid'));
        $this->assign('menuList',$menuList);
        $this->assign('oneMenu',$oneMenu);
        return $this->fetch('news_index');
    }

    public function newsShow(){
        $celebrity = new NewsModel();
        $res = $celebrity->getOneNews(input('get.id'));
        $this->assign('item',$res);
        return $this->fetch('news_show');
    }
}