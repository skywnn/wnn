<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 22:46
 */

namespace app\api\controller\s1;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\AdverModel;
use app\api\model\ProductModel;
use think\db;
class Product extends Base
{
    /**
     * 企服 获取轮播图
     */
    public function getAdver(){
        $AppId = input('post.app_id');
        $userId = input('post.token');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['compamy_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $whe[] = ['name','=','企服'];
            $adver = new AdverModel();
            $cate_id = $adver->getAdverCate($whe);
            $map[] = ['cate_id','=',$cate_id];
            $field ='id,pic,con_url,sort';
            $data = $adver->getAdverByWhere($map,$field,2);
            $this->res($data,'',200);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企服 获取服务大类
     */
    public function getProductCate(){
        $AppId = input('post.app_id');
        $userId = input('post.token');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['compamy_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $data = Db::table('product_cate')->where($map)->field('id,name,icon')->order('sort')->select();
            if(!empty($data)){
                $this->res($data,'',200);
            }else{
                $this->res($data,'暂无服务项目',200);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企服 获取某一类服务大类的列表
     */
    public function getProductList(){
        $AppId = input('post.app_id');
        $userId = input('post.token');
        $cateId = input('post.cate_id');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($cateId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['cate_id','=',$cateId];
            $map[] = ['compamy_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $product = new ProductModel();
            $od = "r.create_time desc";
            $data = $product->getProductByWhere($map,18,$od);
            foreach($data as $k=>&$v){
                $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            }
            $this->res($data,'',200);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企服 获取某一类服务大类的概述
     */
    public function getProductDoc(){
        $AppId = input('post.app_id');
        $userId = input('post.token');
        $cateId = input('post.cate_id');
        try{
            AppId::checkAppID($AppId);
            $this->isPositiveInt($cateId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['cate_id','=',$cateId];
            $map[] = ['compamy_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $data = Db::table('product_doc')->where($map)->field('id,pic,title,views')->find();
            if(!empty($data)){
                $this->res($data,'',200);
            }else{
                $this->res($data,'暂无说明',200);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}