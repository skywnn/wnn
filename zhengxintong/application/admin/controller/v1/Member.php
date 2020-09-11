<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:11
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\MemberModel;
class Member extends Base
{
    /**
     * [index 用户列表]
     */
    public function memberIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['username','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $role = new MemberModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $role ->getCountMember($map);  //总数据数目
            $lists = $role ->getMemberByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("member_index");
    }

}