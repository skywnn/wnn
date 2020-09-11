<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\HelpModel;

class Help extends Base
{
    /**
     * [helpIndex 常见问题列表]
     */
    public function helpIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $help = new HelpModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $help->getCountHelp($map);  //总数据数目
            $lists = $help ->getHelpByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("member_index");
    }
}