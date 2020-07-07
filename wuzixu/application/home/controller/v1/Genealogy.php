<?php


namespace app\home\controller\v1;


use app\home\controller\Base;
use app\home\model\GenealogyModel;
use app\home\model\WebMenuModel;
use think\Db;

class Genealogy extends Base
{
    /**
     * [articleIndex 文章列表]
     */
    public function genealogyIndex(){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['title','like','%'.$keyword.'%'];
            }
            if(isset($cateId)&&$cateId!=''){
                $map[] = ['gen_cate','=',$cateId];
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
            $limits = input("limit")?input("limit"):2;
            $count = Db::name('genealogy')->where($map)->count();//计算总页面
            $genealogy = new GenealogyModel();
            $web = new WebMenuModel();
            $menuMap[] = ['pid','=',input('pid')];
            $menuList = $web->getAllWebMenu($menuMap);
            $oneMenu = $web->getWebMenuInfo(input('pid'));
            $lists = $genealogy->getGenealogyByWhere($map, $Nowpage, $limits,$od);
            $this->assign('item',$lists);
            $this->assign('page',$Nowpage);
            $this->assign('count',ceil($count/$limits));
            $this->assign('pid',input('pid'));
            $this->assign('menuList',$menuList);
            $this->assign('oneMenu',$oneMenu);
            return $this->fetch('genealogy_index');
    }
    public function genealogyShow(){
        $celebrity = new GenealogyModel();
        $res = $celebrity->getOneGenealogy(input('get.id'));
        $this->assign('item',$res);
        return $this->fetch('genealogy_show');
    }


}