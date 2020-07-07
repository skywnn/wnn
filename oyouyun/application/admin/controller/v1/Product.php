<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/25
 * Time: 11:22
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ProductCateModel;
use app\admin\model\ProductModel;
use app\admin\model\ProductDocModel;
use app\admin\model\OrderModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\db;
class Product extends Base
{
//*********************************************企业服务管理*********************************************//
    /**
     * [productIndex 企业服务列表]
     */
    public function productIndex(){
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
            $count = Db::name('product')->alias('r')->where($map)->count();//计算总页面
            $product = new ProductModel();
            $lists = $product->getProductByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('product_index');
    }

    /**
     * [add_product 添加企业服务]
     */
    public function addProduct()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $product = new ProductModel();
            $flag = $product->insertProduct($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $cate = new ProductCateModel();
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_product',['cate'=>$cate->getAllCate($map)]);
    }

    /**
     * [edit_product 编辑企业服务]
     */
    public function editProduct()
    {
        $product = new ProductModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $product->editProduct($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new ProductCateModel();
        $data = $product->getOneProduct($id);
        $map[] = ['company_id','=',session('company_id')];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_product');
    }
    /**
     * [productPic 模板图片]
     */
    public function productPic($id){
        $product = new ProductModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $product->editProduct($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $product->getOneProduct($id);
        $this->assign('item',$data);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('product_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Product_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $product = new ProductModel();
            if(!empty($id)){
                $bra = $product->getOneProduct($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $product->editProduct($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_product 删除企业服务]
     */
    public function delProduct()
    {
        $id = input('param.id');
        $product = new ProductModel();
        $flag = $product->delProduct($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [product_state 企业服务状态]
     */
    public function productState()
    {
        extract(input());
        $product = new ProductModel();
        $flag = $product->productState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [activityAll 订单列表]
     */
    public function productOrder(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['title','like','%'.$keyword.'%'];
            }
            $map[] = ['con_name','=','企业服务'];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od= $field." ".$order;
            }else{
                $od="create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('order')->alias('r')->where($map)->count();//计算总页面
            $order = new OrderModel();
            $lists = $order->getOrderByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('product_order');
    }
    /**
     * [activityAll 订单审核]
     */
    public function auditProduct(){
        extract(input());
        $order = new OrderModel();
        $flag = $order->orderState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [readActivityOrder 订单查看]
     */
    public function readProductOrder(){
        $id = input('param.id');
        $order = new OrderModel();
        $flag = $order->getOneOrder($id);
        $this->assign('item',$flag);
        return $this->fetch('read_product_order');
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
                $od="sort";
            }
            $cate = new ProductCateModel();
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
            $cate = new ProductCateModel();
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
        $cate = new ProductCateModel();
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
     * [cateIcon 上传分类图标]
     */
    public function cateIcon($id){
        $cate = new ProductCateModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        $icon['width'] = Config::get('app.upload_image.iconWidth');
        $icon['height'] = Config::get('app.upload_image.iconHeight');
        $this->assign('icon',$icon);
        return $this->fetch('cate_icon');
    }
    /**
     * [uplaodIcon 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'ProductCate_icon';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $cate = new ProductCateModel();
            if(!empty($id)){
                $bra = $cate->getOneCate($id);
                $oldurl=$bra['icon'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'icon' => $pic['data'],
                'id' => $id,
            );
            $flag = $cate->editCate($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

    /**
     * [del_cate 删除分类]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new ProductCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new ProductCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [editdoc 添加概论]editDocPic
     */
    public function editDoc(){
        $doc = new ProductDocModel();
        if(request()->isPost()){
            $param = input('post.');
            if($param['type'] ==2){
                $flag = $doc->insertProductDoc($param);
            }elseif($param['type'] ==1){
                $flag = $doc->editProductDoc($param);
            }
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $docDate = $doc->where('cate_id','=',$id)->find();
        if(!empty($docDate)){
            $item = $docDate;
            $item['type'] =1;
        }else{
            $item['cate_id'] = $id;
            $item['type'] =2;
        }
        $this->assign('item',$item);
        return $this->fetch('edit_doc');
    }
    /**
     * [productPic 概论图片]
     */
    public function editDocPic(){
        $product = new ProductDocModel();
        if(request()->isPost()){
            $param = input('post.');
            if($param['type'] ==2){
                return json(['code' => 100, 'data' => [], 'msg' => '请先创建概论']);
            }elseif($param['type'] ==1){
                $flag = $product->editProductDoc($param);
                $flag = $product->editProductDoc($param);
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
            }
        }
        $id = input('param.id');
        $docDate = $product->where('cate_id','=',$id)->find();
        if(!empty($docDate)){
            $item = $docDate;
            $item['type'] =1;
        }else{
            $item['cate_id'] = $id;
            $item['type'] =2;
        }
        $this->assign('item',$item);
        $pic['width'] = Config::get('app.upload_image.picWidth');
        $pic['height'] = Config::get('app.upload_image.picHeight');
        $this->assign('pic',$pic);
        return $this->fetch('edit_doc_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodDocPic(){
        if(request()->isPost()){
            $id = input('param.id');
            if(empty($id)){
                return json(['code' => 100, 'data' => [], 'msg' => '请先创建概论']);
            }
            $pathNewName = 'ProductDoc_pic';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $product = new ProductDocModel();
            if(!empty($id)){
                $bra = $product->getOneProductDoc($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $product->editProductDoc($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }

}