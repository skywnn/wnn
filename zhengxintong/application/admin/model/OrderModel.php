<?php


namespace app\admin\model;


use think\Db;

class OrderModel extends BaseModel
{
    protected $name = 'order';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getOrderByWhere 根据搜索条件获取订单列表信息]
     */
    public function getOrderByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    public function orderStatus($param){
        $msg = "";
        if($param['order_status'] == 2){
            $param['verify_status'] = 1;
            $param['verification_code'] = mt_rand(100000,999999);
            $msg = '审核通过';
        }elseif($param['order_status']==3){
            $msg = '拒绝';
        }
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param,['id'=>$param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => $msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }

    /**
     * 查询单个订单
     */
    public function getOneOrder($id){
       $res = $this->alias('o')
            ->join('activity act','act.id=o.activity_id')
            ->join('company c','c.id=act.company_id')
            ->join('member m','m.id=o.member_id')
            ->join('ticket t','t.id=o.ticket_id')
            ->field('o.*,t.title as t_title,t.buy_time,t.price,t.v_price,t.m_price,m.nick_name,act.title as act_title,c.title as c_title,act.start_time,act.end_time,act.tel,act.province,act.city,act.district,act.address')
            ->find();
        return $res;
    }

    public function editOrder($id){
        Db::startTrans();// 启动事务
        try{
            $res = $this->where('activity_id',$id)->column('scene_id');
            $item = Db::name('activity_scene')
                ->whereIn('id',$res)->field('id,activity_time')->select();
            foreach ($item as $k=>$v) {
                $map = [];
                if (time()-$v['activity_time']>=15*60) {
                    $map[] = ['scene_id','=',$v['id']];
                    $map[] = ['verify_status','<>',2];
                    $map[] = ['verify_status','<>',0];
                    $row = Db::name('order')->where($map)->update(['verify_status'=>3]);
                }
            }
            Db::commit();// 提交事务
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => ''];
        }

    }
}