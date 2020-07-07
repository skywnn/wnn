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
        $map[] = ['status','=',1];
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
        $map[] = ['status','=',1];
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
        $pic['width'] = 750;
        $pic['height'] = 750;
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
            $width = 750;
            $height = 750;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
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
     * [productPic 模板图片]
     */
    public function productIcon($id){
        $product = new ProductModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $product->editProduct($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $product->getOneProduct($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 750;
        $this->assign('pic',$pic);
        return $this->fetch('product_thum_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodTumPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Product_pic';
            $width = 200;
            $height = 200;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $product = new ProductModel();
            if(!empty($id)){
                $bra = $product->getOneProduct($id);
                $oldurl=$bra['thum_pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'thum_pic' => $pic['data'],
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
            $map[] = ['parent_id','=',0];
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
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
     * [index_cate 子分类列表]
     */
    public function subItem(){
        $parentId = input('param.parent_id');
        $cate = new ProductCateModel();

        if(request()->isAjax()){
            extract(input());
            $map = Array();
            $map[] = ['parent_id','=',$parentId];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $cate->getCountCate($map);//计算总条数
            $lists = $cate->getCateByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('parentId',$parentId);
        return $this->fetch('sub_item');
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
     * [add_cate 添加分类]
     */
    public function addSumCate()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new ProductCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('parentId',$id);
        return $this->fetch('add_sum_cate');
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
        $icon['width'] = 750;
        $icon['height'] = 310;
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
            $width = 750;
            $height = 310;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
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
}