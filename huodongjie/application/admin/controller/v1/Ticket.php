<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\TicketModel;
use think\Db;

class Ticket extends Base
{
    /**
     * [ticketIndex 票种列表]
     */
    public function ticketIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['t.title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="t.".$field." ".$order;
            }else{
                $od="t.create_time desc";
            }
            $id = input('param.id');
            $map[] = ['t.activity_id','=',$id];
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $ticket = new TicketModel();
            $count = $ticket->alias('t')->where($map)->count();
            $lists = $ticket->getTicketByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $item = Db::name('activity')->where('id',input('param.id'))->find();
        $this->assign('item',$item);
        return $this->fetch('ticket_index');
    }

    /**
     * 编辑票种
     */
    public function editTicket(){
        if (request()->isPost()) {
            $ticket = new TicketModel();
            $param = input('post.');
            $flag = $ticket->editTicket($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $res = Db::name('ticket')->where('id',$id)->find();
        $this->assign('item',$res);
        return $this->fetch('edit_ticket');
    }

    public function addTicket(){
        if (request()->isPost()) {
            $ticket = new TicketModel();
        }
        $id = input('param.id');
        $item = Db::name('activity')->where('id',$id)->find();
        $this->assign('item',$item);
        return $this->fetch('add_ticket');
    }
}