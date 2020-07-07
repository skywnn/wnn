<?php


namespace app\home\controller\v1;

use app\home\controller\Base;
use app\home\model\CelebrityModel;
use app\home\model\WebMenuModel;
use think\Db;

class Celebrity extends Base
{
    public function celebrityIndex(){
        extract(input());
        $map = [];
        if(isset($keyword)&&$keyword!=""){
            $map[] = ['name','like','%'.$keyword.'%'];
        }
        if (isset($h)&&$h==1) {
            $birthday = strtotime('1949');
            $map[] = ['birthday','>',$birthday];
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
        $count = Db::name('genealogy_celebrity')->where($map)->count();//计算总页面
        $article = new CelebrityModel();
        //$map[] = ['gen_cate','=',input('cate_id')];
        $lists = $article->getCelebrityByWhere($map, $Nowpage, $limits,$od);
        $web = new WebMenuModel();
        $menuMap[] = ['pid','=',input('pid')];
        $menuList = $web->getAllWebMenu($menuMap);
        $oneMenu = $web->getWebMenuInfo(input('pid'));
        //$map[] = ['r.cate_id','=',input('cate_id')];
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',ceil($count/$limits));
        $this->assign('pid',input('pid'));
        $this->assign('menuList',$menuList);
        $this->assign('oneMenu',$oneMenu);
        return $this->fetch('celebrity_index');
    }

    public function celebrityShow(){
        $celebrity = new CelebrityModel();
        $res = $celebrity->getOneCelebrity(input('get.id'));
        $this->assign('item',$res);
        return $this->fetch('celebrity_show');
    }
}