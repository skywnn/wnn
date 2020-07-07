<?php


namespace app\home\controller\v1;


use app\home\controller\Base;
use app\home\model\ActivityModel;
use app\home\model\WebMenuModel;
use think\Db;

class Activity extends Base
{
    public function activityIndex(){
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
        $count = Db::name('activity')->count();//计算总页面
        $article = new ActivityModel();
        $web = new WebMenuModel();
        $menuMap[] = ['pid','=',input('pid')];
        $menuList = $web->getAllWebMenu($menuMap);
        $oneMenu = $web->getWebMenuInfo(input('pid'));
        $lists = $article->getActivityByWhere($map, $Nowpage, $limits,$od);
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',ceil($count/$limits));
        $this->assign('pid',input('pid'));
        $this->assign('menuList',$menuList);
        $this->assign('oneMenu',$oneMenu);
        return $this->fetch('activity_index');
    }

    public function activityShow(){
        $celebrity = new ActivityModel();
        $res = $celebrity->getOneActivity(input('get.id'));
        $this->assign('item',$res);
        return $this->fetch('activity_show');
    }
}