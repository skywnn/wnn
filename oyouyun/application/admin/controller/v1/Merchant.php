<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 12:52
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\MerchantModel;
use app\admin\model\MerchantCateModel;
use app\admin\model\MerchantUserModel;
use app\admin\model\PaymentSlipModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Merchant extends Base
{
//*********************************************商户管理*********************************************//
    /**
     * [merchantIndex 商户列表]
     */
    public function merchantIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.operate_id','=',session('user_id')];
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
            $count = Db::name('merchant')->alias('r')->where($map)->count();//计算总页面
            $merchant = new MerchantModel();
            $lists = $merchant->getMerchantByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('merchant_index');
    }
    /**
     * [merchantIndex 商户列表]
     */
    public function merchantAllIndex(){
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
            $count = Db::name('merchant')->alias('r')->where($map)->count();//计算总页面
            $merchant = new MerchantModel();
            $lists = $merchant->getMerchantByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('merchant_index');
    }
    /**
     * [add_merchant 添加商户]
     */
    public function addMerchant()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $merchant = new MerchantModel();
            $flag = $merchant->insertMerchant($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new MerchantCateModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_merchant',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_merchant 编辑商户]
     */
    public function editMerchant()
    {
        $merchant = new MerchantModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $merchant->editMerchant($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new MerchantCateModel();
        $data = $merchant->getOneMerchant($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_merchant');
    }
    /**
     * [noticePic 模板图片]
     */
    public function merchantIcon(){
        $merchant = new MerchantModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $merchant->editMerchant($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $merchant->getOneMerchant($id);
        $this->assign('item',$data);
        $icon['width'] = Config::get('app.upload_image.iconWidth');
        $icon['height'] = Config::get('app.upload_image.iconHeight');
        $this->assign('icon',$icon);
        return $this->fetch('merchant_icon');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodIcon(){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Merchant_pic';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $merchant = new MerchantModel();
            if(!empty($id)){
                $bra = $merchant->getOneMerchant($id);
                $oldurl=$bra['logo'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'logo' => $pic['data'],
                'id' => $id,
            );
            $flag = $merchant->editMerchant($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
    /**
     * [del_merchant 删除商户]
     */
    public function delMerchant()
    {
        $id = input('param.id');
        $merchant = new MerchantModel();
        $flag = $merchant->delMerchant($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [merchant_state 商户状态]
     */
    public function merchantState()
    {
        extract(input());
        $merchant = new MerchantModel();
        $flag = $merchant->merchantState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [setPayment 创建缴费单]
     */
    public function setPaymentDian()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $arr = rand(1,9999);
            $dir = $arr * 79;
            $param['order_sn'] = 'HR'.time().substr($dir,0,2);
            $paymentSlip = new PaymentSlipModel();
            $flag = $paymentSlip->insertPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.merchant_id');
        $merchant = new MerchantModel();
        $data = $merchant->getOneMerchant($id);
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$id];
        $map[] = ['cate_name','=','电费'];
        $payData = $paymentSlip->where($map)->order('create_time desc')->find();
        $data['lto_dian'] = $payData['leo_dian'];
        $this->assign('item',$data);
        return $this->fetch('set_payment_dian');
    }
    /**
     * [setPayment 创建缴费单]
     */
    public function setPaymentShui()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $arr = rand(1,9999);
            $dir = $arr * 79;
            $param['order_sn'] = 'HR'.time().substr($dir,0,2);
            $paymentSlip = new PaymentSlipModel();
            $flag = $paymentSlip->insertPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.merchant_id');
        $merchant = new MerchantModel();
        $data = $merchant->getOneMerchant($id);
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$id];
        $map[] = ['cate_name','=','水费'];
        $payData = $paymentSlip->where($map)->order('create_time desc')->find();
        $data['lto_dian'] = $payData['leo_dian'];
        $this->assign('item',$data);
        return $this->fetch('set_payment_shui');
    }
    /**
     * [setPayment 创建缴费单]
     */
    public function setPaymentFang()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $arr = rand(1,9999);
            $dir = $arr * 79;
            $param['order_sn'] = 'HR'.time().substr($dir,0,2);
            $paymentSlip = new PaymentSlipModel();
            $flag = $paymentSlip->insertPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.merchant_id');
        $merchant = new MerchantModel();
        $data = $merchant->getOneMerchant($id);
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$id];
        $map[] = ['cate_name','=','房租'];
        $payData = $paymentSlip->where($map)->order('create_time desc')->find();
        $data['start_time'] = $payData['end_time'];
        $this->assign('item',$data);
        return $this->fetch('set_payment_fang');
    }
    /**
     * [setPayment 创建缴费单]
     */
    public function setPaymentChe()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $arr = rand(1,9999);
            $dir = $arr * 79;
            $param['order_sn'] = 'HR'.time().substr($dir,0,2);
            $paymentSlip = new PaymentSlipModel();
            $flag = $paymentSlip->insertPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.merchant_id');
        $merchant = new MerchantModel();
        $data = $merchant->getOneMerchant($id);
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$id];
        $map[] = ['cate_name','=','停车费'];
        $payData = $paymentSlip->where($map)->order('create_time desc')->find();
        $data['start_time'] = $payData['end_time'];
        $this->assign('item',$data);
        return $this->fetch('set_payment_che');
    }
    /**
     * [subMerchant 某一分类下的商户列表]
     */
    public function subMerchant(){

        $cate_id = input('param.id');
        $map = [];
        $map[] = ['cate_id','=',$cate_id];
        $merchant = new MerchantModel();

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
            $count = Db::name('merchant')->alias('r')->where($map)->count();//计算总页面
            $lists = $merchant->getMerchantByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('merchant_index');
    }
    /**
     * [merchantAll 商户列表]
     */
    public function merchantAll(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
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
            $count = Db::name('merchant')->alias('r')->where($map)->count();//计算总页面
            $merchant = new MerchantModel();
            $lists = $merchant->getMerchantByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('merchant_all');
    }
    /**
     * [merchantUser 商户员工列表]
     */
    public function merchantUser(){
        $merchantId = input('param.id');
        $merchant = new MerchantUserModel();
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['rc.real_name','like','%'.$keyword.'%'];
            }
            $map[] = ['r.merchant_id','=',$merchantId];
            $od="r.id desc";
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = Db::name('merchant_user')->alias('r')->where($map)->count();//计算总页面
            $lists = $merchant->getMerchantUserByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('merchantId',$merchantId);
        return $this->fetch("merchant_user");
    }
    /**
     * [editMerchantUser 设置成负责人]
     */
    public function editMerchantUser(){
        $merchant = new MerchantUserModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $merchant->editMerchantUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $map[] =['r.id','=',$id];
        $map[] = ['company_id','=',session('company_id')];
        $data = $merchant->getMerchantUserInfo($map);
        $this->assign('item',$data);
        return $this->fetch('edit_merchant_user');
    }
    /**
     * [delMerchantUser 删除商户员工]
     */
    public function delMerchantUser()
    {
        $id = input('param.id');
        $merchant = new MerchantUserModel();
        $flag = $merchant->delMerchantUser($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [merchantPayLists 商户信息统计]
     */
    public function merchantPayLists(){
        $merchantId = input('param.id');
        $merchant = new MerchantModel();
        $merchantDate = $merchant->where('id','=',$merchantId)->find();
        $paymentSlip = new PaymentSlipModel();
        $payDate = $paymentSlip->where('merchant_id','=',$merchantId)->order('create_time desc')->select();
        $payment = [];
        if(!empty($payDate)){
            foreach ($payDate as &$v){
                if($v['cate_name'] =='房租'){
                    $payment['房租'][] = $v;
                }elseif ($v['cate_name'] =='电费'){
                    $payment['电费'][] = $v;
                }elseif ($v['cate_name'] =='停车费'){
                    $payment['停车费'][] = $v;
                }elseif ($v['cate_name'] =='水费'){
                    $payment['水费'][] = $v;
                }
            }
        }
        $this->assign('payment',$payment);
        $this->assign('merchantDate',$merchantDate);
        return $this->fetch("merchant_pay_lists");
    }
    /**
     * [ 商户房租流水]
     */
    public function paymentFang(){
        $merchantId = input('param.id');
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$merchantId];
        $map[] = ['cate_name','=','房租'];
        $lists = $paymentSlip->where($map)->order('create_time desc')->select();
        $count = $paymentSlip->where($map)->count();
        return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
    }
    /**
     * [ 商户电费流水]
     */
    public function paymentDian(){
        $merchantId = input('param.id');
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$merchantId];
        $map[] = ['cate_name','=','电费'];
        $lists = $paymentSlip->where($map)->order('create_time desc')->select();
        $count = $paymentSlip->where($map)->count();
        return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
    }
    /**
     * [ 商户停车费流水]
     */
    public function paymentChe(){
        $merchantId = input('param.id');
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$merchantId];
        $map[] = ['cate_name','=','停车费'];
        $lists = $paymentSlip->where($map)->order('create_time desc')->select();
        $count = $paymentSlip->where($map)->count();
        return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
    }
    /**
     * [ 商户水费流水]
     */
    public function paymentShui(){
        $merchantId = input('param.id');
        $paymentSlip = new PaymentSlipModel();
        $map[] = ['merchant_id','=',$merchantId];
        $map[] = ['cate_name','=','水费'];
        $lists = $paymentSlip->where($map)->order('create_time desc')->select();
        $count = $paymentSlip->where($map)->count();
        return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
    }

    //*********************************************分类管理*********************************************//

    /**
     * [index_cate 分类列表]
     */
    public function cateIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $map[] = ['company_id','=',session('company_id')];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $cate = new MerchantCateModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $cate->getCountCate($map);//计算总条数
            $lists = $cate->getCateByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('cate_index');
    }

    /**
     * [add_cate 添加分类]
     */
    public function addCate()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new MerchantCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_cate');
    }

    /**
     * [edit_cate 编辑分类]
     */
    public function editCate()
    {
        $cate = new MerchantCateModel();
        if(request()->isPost()){
            $param = input('param.');
            dump($param);die;
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch();
    }

    /**
     * [del_cate 删除分类]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new MerchantCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new MerchantCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}