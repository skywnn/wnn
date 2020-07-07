<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\DealingsModel;
use think\Db;

class Dealings extends Base
{

    /**
     * [dealingsIndex 商机列表]
     */
    public function dealingsIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['d.company','like','%'.$keyword.'%'];
            }
            $map[] = ['d.company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="d.".$field." ".$order;
            }else{
                $od="d.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('crm_dealings')->alias('d')->where($map)->count();//计算总页面
            $deal = new DealingsModel();
            $lists = $deal->getDealingsByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('dealings_index');
    }

    /**
     * [edit_Dealings 编辑往来]
     */
    public function editDealings()
    {
        $dealings = new DealingsModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $dealings->editDealings($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $dealings->getOneDealings($id);
        $this->assign('item',$data);
        $area = new \app\common\place\Area;
        $this->assign('province',$area->province());
        $con_list = Db::name('crm_contacts')->where('company_id',session('company_id'))->select();
        $this->assign('conList',$con_list);
        $opp_list = Db::name('crm_opportunity')->where('company_id',session('company_id'))->select();
        $this->assign('oppList',$opp_list);
        return $this->fetch('edit_dealings');
    }

    /**
     * [del_activity 删除活动]
     */
    public function delDealings()
    {
        $id = input('param.id');
        $dealings = new DealingsModel();
        $flag = $dealings->delDealings($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}