<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 11:32
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ContractCateModel;
use app\admin\model\ContractModel;
use think\db;
class Contract extends Base
{
//*********************************************协议管理*********************************************//
    /**
     * [contractIndex 协议列表]
     */
    public function contractIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('contract')->alias('r')->where($map)->count();//计算总页面
            $contract = new ContractModel();
            $lists = $contract->getContractByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('contract_index');
    }

    /**
     * [add_contract 添加协议]
     */
    public function addContract()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $contract = new ContractModel();
            $flag = $contract->insertContract($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new ContractCateModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_contract',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_contract 编辑协议]
     */
    public function editContract()
    {
        $contract = new ContractModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $contract->editContract($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new ContractCateModel();
        $data = $contract->getOneContract($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_contract');
    }
    /**
     * [contractPic 协议图片]
     */
    public function contractPic()
    {
        $contract = new ContractModel();
//        if(request()->isPost()){
//            $param = input('post.');
//            $flag = $contract->editContract($param);
//            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
//        }
        $id = input('param.id');
        $cate = new ContractCateModel();
        $data = $contract->getOneContract($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('contract_pic');
    }

    /**
     * [del_contract 删除协议]
     */
    public function delContract()
    {
        $id = input('param.id');
        $contract = new ContractModel();
        $flag = $contract->delContract($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [contract_state 协议状态]
     */
    public function contractState()
    {
        extract(input());
        $contract = new ContractModel();
        $flag = $contract->contractState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subContract 某一位置下的协议列表]
     */
    public function subContract(){

        $cate_id = input('param.id');
        $map = [];
        $map[] = ['cate_id','=',$cate_id];
        $contract = new ContractModel();

        if(request()->isAjax ()){
            extract(input());
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('contract')->alias('r')->where($map)->count();//计算总页面
            $lists = $contract->getContractByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('contract_index');
    }

    //*********************************************位置管理*********************************************//

    /**
     * [index_cate 位置列表]
     */
    public function cateIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的位置需设定公司条件
            $map[] = ['company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $cate = new ContractCateModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $cate->getCountCate($map);//计算总条数
            $lists = $cate->getCateByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('cate_index');
    }

    /**
     * [add_cate 添加位置]
     */
    public function addCate()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new ContractCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_cate');
    }

    /**
     * [edit_cate 编辑位置]
     */
    public function editCate()
    {
        $cate = new ContractCateModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch();
    }

    /**
     * [cateIcon 上传位置图标]
     */
    public function cateIcon()
    {
        $cate = new ContractCateModel();
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch('cate_icon');
    }

    /**
     * [del_cate 删除位置]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new ContractCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 位置状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new ContractCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}