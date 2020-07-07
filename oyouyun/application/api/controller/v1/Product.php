<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/23
 * Time: 15:34
 */

namespace app\api\controller\v1;

use app\admin\model\ProductDocModel;
use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\AdverModel;
use app\api\model\AdverCateModel;
use app\api\model\ProductModel;
use app\api\model\ProductCateModel;
use think\facade\Config;
use think\db;
class Product extends Base
{
    /**
     * 企业服务 获取轮播广告
     */
    public function getAdver(){

        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);

            $whe[] = ['name','=','企服'];
            $whe[] = ['company_id','=',1];
            $whe[] = ['status','=',1];
            $whe[] = ['is_delete','=',0];
            $adverCate = new AdverCateModel();
            $cate_id = $adverCate->getAdverCate($whe);

            $map[] = ['cate_id','=',$cate_id];
            $map[] = ['company_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $field ='id,pic,con_url,sort';
            $adver = new AdverModel();
            $data = $adver->getAdverByWhere($map,$field,2);
            foreach($data as $k=>&$v){
                $v['pic'] = Config::get('app.baseUrl').$v['pic'];
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企业服务 获取企业服务一级分类
     */
    public function getProductCate(){

        $AppId = input('param.app_id');

        try{
            AppId::checkAppID($AppId);

            $map[] = ['company_id','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $productCate = new ProductCateModel();
            $data = $productCate->order('sort')->field('id,name,icon,sort,create_time')->where($map)->select();
            foreach($data as $k=>&$v){
                $v['icon'] = Config::get('app.baseUrl').$v['icon'];
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企业服务 获取企业服务一级分类
     */
    public function getProductDoc(){

        $AppId = input('param.app_id');
        $cateId = input('param.cate_id');
        try{
            AppId::checkAppID($AppId);


            $map[] = ['cate_id','=',$cateId];
            $productDoc = new ProductDocModel();
            $data = $productDoc->where($map)->find();
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '暂无相关内容']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企业服务 获取一个分类下的服务项目
     */
    public function getProductList(){

        $AppId = input('param.app_id');
        $cateId = input('param.cate_id');
        try{
            AppId::checkAppID($AppId);

            $map[] = ['company_id','=',1];
            $map[] = ['cate_id','=',$cateId];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $product = new ProductModel();
            $data = $product->order('sort')->field('id,title,pic,m_price,is_rec,sort,create_time')->limit(18)->where($map)->select();
            foreach($data as $k=>&$v){
                $v['pic'] = Config::get('app.baseUrl').$v['pic'];
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 企业服务 服务详情
     */
    public function getProductDetail(){
        $AppID = input('param.app_id');
        $product_id = input('param.product_id');
        try{
            AppId::checkAppID($AppID);

            $map[] = ['r.id','=',$product_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $product = new ProductModel();
            $data = $product->getProductInfo($map);
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
                $product->where('id','=',$product_id)->setInc('views',1);
                if(!empty($data['keywords'])){
                    $data['keywords'] = explode(',',$data['keywords']);
                }
            } else {
                $this->getFail([],'参数异常，请确认！',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}