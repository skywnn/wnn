<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 11:40
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\TradeModel;
use think\db;
class Trade extends Base
{
    //*********************************************行业管理*********************************************//
    public function index(){
        $nav=new TradeModel();
        $arr0=array();
        $arr0=$nav->where('parent_id','=',0)->select();
        $arr0 = $arr0->toArray();
        foreach ($arr0 as $key => $value) {
            $nav->where ('id' , $value['id'])->setField (['level' => 1]);
            $arr0[$key]['second']=$nav->where('parent_id','=',$value['id'])->select();
            $arr0[$key]['second']=$arr0[$key]['second']->toArray();
            foreach ($arr0[$key]['second'] as $key2 => $value2) {
                $nav->where ('id' , $value2['id'])->setField (['level' => 2]);
                $arr0[$key]['second'][$key2]['third']=$nav->where('parent_id','=',$value2['id'])->select();
                $arr0[$key]['second'][$key2]['third'] = $arr0[$key]['second'][$key2]['third']->toArray();
                foreach ($arr0[$key]['second'][$key2]['third'] as $key3 => $value3) {
                    $nav->where ('id' , $value3['id'])->setField (['level' => 3]);
                }
            }
        }
        echo '<pre>';
        print_r($arr0);
        echo '</pre>';
        die;
    }

    /**
     * [tradeIndex 行业类型列表]
     */
    public function tradeIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['company_id','=',session('company_id')];
            $map[] = ['parent_id','=',0];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $trade = new TradeModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $trade ->getCountTrade($map);  //总数据数目
            $lists = $trade ->getTradeByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("trade_index");
    }
    /**
     * [add 添加行业类型]
     */
    public function addTrade()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $trade = new TradeModel();
            $flag = $trade->insertTrade($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_trade');
    }

    /**
     * [edit 编辑行业类型]
     */
    public function editTrade()
    {
        $trade = new TradeModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $trade->editTrade($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneTrade = $trade->getOneTrade($id);
        $this->assign('item',$oneTrade);
        return $this->fetch('edit_trade');
    }
    /**
     * [delete 删除行业类型]
     */
    public function delTrade()
    {
        $id = input('param.id');
        $trade = new TradeModel();
        $flag = $trade->delTrade($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [trade_state 行业类型状态]
     */
    public function tradeState()
    {
        extract(input());
        $trade = new TradeModel();
        $flag = $trade->tradeState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subtradeIndex 子行业类型列表]
     */
    public function subtradeIndex(){
        $id = input('param.id');
        $trade = new TradeModel();
        $oneTrade = $trade->getOneTrade($id);
        $oneTrade['level'] += 1;
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['parent_id','=',$id];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $trade ->getCountTrade($map);  //总数据数目
            $lists = $trade ->getTradeByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('item',$oneTrade);
        return $this->fetch("subtrade_index");
    }
    /**
     * [addSubtrade 添加子行业类型]
     */
    public function addSubtrade()
    {
        $trade = new TradeModel();
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $flag = $trade->insertTrade($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $level = input('param.level');
        $oneTrade = $trade->getOneTrade($id);
        $oneTrade['level'] = $level;
        $this->assign('item',$oneTrade);
        return $this->fetch('add_subtrade');
    }
}