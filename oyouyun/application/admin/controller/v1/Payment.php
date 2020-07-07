<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/12
 * Time: 14:13
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\PaymentSlipModel;
use app\admin\model\MerchantModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Payment extends Base
{
//*********************************************缴费管理*********************************************//
    /**
     * [paymentSlipIndex 缴费列表]
     */
    public function paymentIndex(){
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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $paymentSlip = new PaymentSlipModel();
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('payment_index');
    }

    /**
     * [add_paymentSlip 添加缴费单]
     */
    public function addPayment()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');

            $paymentSlip = new PaymentSlipModel();
            $flag = $paymentSlip->insertPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $merchant = new MerchantModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_payment',['merchant'=>$merchant->getAllMerchant($map)]);
    }

    /**
     * [edit_paymentSlip 编辑缴费单]
     */
    public function editPaymentShui()
    {
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        return $this->fetch('edit_payment_shui');
    }
    /**
     * [edit_paymentSlip 编辑缴费单]
     */
    public function editPaymentDian()
    {
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        return $this->fetch('edit_payment_dian');
    }
    /**
     * [edit_paymentSlip 编辑缴费单]
     */
    public function editPaymentFang()
    {
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        return $this->fetch('edit_payment_fang');
    }
    /**
     * [edit_paymentSlip 编辑缴费单]
     */
    public function editPaymentChe()
    {
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        return $this->fetch('edit_payment_che');
    }
    /**
     * [paymentSlipPic 模板图片]
     */
    public function paymentPic($id){
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('payment_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'PaymentSlip_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,false,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $paymentSlip = new PaymentSlipModel();
            if(!empty($id)){
                $bra = $paymentSlip->getOnePaymentSlip($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $paymentSlip->editPaymentSlip($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_paymentSlip 删除缴费]
     */
    public function delPayment()
    {
        $id = input('param.id');
        $paymentSlip = new PaymentSlipModel();
        $flag = $paymentSlip->delPaymentSlip($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [paymentSlip_state 缴费状态]
     */
    public function paymentState()
    {
        extract(input());
        $paymentSlip = new PaymentSlipModel();
        $flag = $paymentSlip->paymentSlipState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subPaymentSlip 某一分类下的缴费列表]
     */
    public function subPayment(){

        $cate_id = input('param.id');
        $map = [];
        $map[] = ['cate_id','=',$cate_id];
        $paymentSlip = new PaymentSlipModel();

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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('payment_index');
    }
//*********************************************公共缴费管理*********************************************//
    /**
     * [wuyeIndex 缴费列表---物业费]
     */
    public function shuiIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.operate_id','=',session('user_id')];
            $map[] = ['r.cate_name','=','水费'];
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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $paymentSlip = new PaymentSlipModel();
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('shui_index');
    }
    /**
     * [dianIndex 缴费列表---电费]
     */
    public function dianIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.operate_id','=',session('user_id')];
            $map[] = ['r.cate_name','=','电费'];
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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $paymentSlip = new PaymentSlipModel();
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('dian_index');
    }
    /**
     * [fangzuIndex 缴费列表---房租]
     */
    public function fangzuIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.operate_id','=',session('user_id')];
            $map[] = ['r.cate_name','=','房租'];
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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $paymentSlip = new PaymentSlipModel();
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('fangzu_index');
    }
    /**
     * [cheIndex 缴费列表---停车费]
     */
    public function cheIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $map[] = ['r.operate_id','=',session('user_id')];
            $map[] = ['r.cate_name','=','停车费'];
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
            $count = Db::name('payment_slip')->alias('r')->where($map)->count();//计算总页面
            $paymentSlip = new PaymentSlipModel();
            $lists = $paymentSlip->getPaymentSlipByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('che_index');
    }
    /**
     * [auditPayment 缴费列表---审核]
     */
    public function auditPayment(){
        $paymentSlip = new PaymentSlipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $paymentSlip->editPaymentSlip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            //return $this->fetch('fangzu_index');
        }
        $id = input('param.id');
        $data = $paymentSlip->getOnePaymentSlip($id);
        $this->assign('item',$data);
        return $this->fetch('audit_payment');
    }
}