<?php


namespace app\home\controller\v1;


use app\home\controller\Base;
use app\home\model\OrganizeModel;
use app\home\model\WebMenuModel;
use think\Db;

class Organize extends Base
{
    public function organizeIndex(){
        extract(input());
        $map = [];
        if(isset($keyword)&&$keyword!=""){
            $map[] = ['name','like','%'.$keyword.'%'];
        }

        if(isset($cateId)&&$cateId!=0){
            $map[] = ['z_cate_id','=',$cateId];
            $this->assign('cateId',$cateId);
        } else {
            $this->assign('cateId',0);
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
        $count = Db::name('organize')->where($map)->count();//计算总页面
        $article = new OrganizeModel();
        $web = new WebMenuModel();
        $menuMap[] = ['pid','=',input('pid')];
        $menuList = $web->getAllWebMenu($menuMap);
        $oneMenu = $web->getWebMenuInfo(input('pid'));
        $lists = $article->getOrganizeByWhere($map, $Nowpage, $limits,$od);
        $this->assign('item',$lists);
        $this->assign('page',$Nowpage);
        $this->assign('count',ceil($count/$limits));
        $this->assign('pid',input('pid'));
        $this->assign('menuList',$menuList);
        $this->assign('oneMenu',$oneMenu);
        return $this->fetch('organize_index');
    }

    public function organizeShow(){
        $celebrity = new OrganizeModel();
        $res = $celebrity->getOneOrganize(input('get.id'));
        $this->assign('item',$res);
        return $this->fetch('organize_show');
    }
}