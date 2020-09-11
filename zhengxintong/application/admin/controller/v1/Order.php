<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\ActivityModel;
use app\admin\model\OrderModel;
use think\Db;

class Order extends Base
{
    public function activityOrder(){
        if ($this->request->isAjax()) {
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['o.name','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="o.".$field." ".$order;
            }else{
                $od="o.create_time desc";
            }
            $id = input('param.id');
            $map[] = ['o.activity_id','=',$id];
            $order = new OrderModel();
            $order->editOrder($id);
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $order->alias('o')->where($map)->count();
            $item = $order->alias('o')->join('member m','m.id=o.member_id')
                ->join('ticket t','t.id=o.ticket_id')
                ->join('activity_scene as','as.id=o.scene_id')
                ->where($map)
                ->page($Nowpage,$limits)
                ->order($od)
                ->field('as.activity_time,m.nick_name,o.*,t.title')
                ->select();
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$item]);
        }
        $act = new ActivityModel();
        $id = input('id');
        $data = $act->where('id',$id)->find();
        $this->assign('item',$data);
        return $this->fetch('activity_order');

    }

    /**
     * 修改订单状态
     */
    public function orderStatus(){
        $param = input();
        $order = new OrderModel();
        $flag = $order->orderStatus($param);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 订单详情
     */
    public function orderDetail(){
        $id = input('param.id');
        $order = new OrderModel();
        $item = $order->getOneOrder($id);
        $this->assign('item',$item);
        return $this->fetch('order_detail');
    }

}