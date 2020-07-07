<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:52
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\model\BlogModel;
use app\api\model\ProductCateModel;
use app\api\model\UserWaterModel;
use app\lib\exception\ApiErrorException;
use app\api\model\AdverModel;
use app\api\model\AdverCateModel;
use app\api\model\ArticleModel;
use app\api\model\BlogDayModel;
use app\api\model\MemberModel;
use app\api\model\HelpModel;
use app\api\service\Member;
use app\api\model\BlogCateModel;
use app\api\model\BaseModel;
use app\api\model\ProductModel;
use app\api\model\FavoriteModel;
use app\api\model\LikeModel;
use app\common\upload\Uplaod;
use think\facade\Config;
use think\db;
class Home extends Base
{
    /**
     * 首页 获取轮播图
     */
    public function getAdver(){
        $conName = input('param.con_name');
        try{

            $whe[] = ['name','=',$conName];
            $whe[] = ['status','=',1];
            $whe[] = ['is_delete','=',0];
            $adverCate = new AdverCateModel();
            $cate_id = $adverCate->getAdverCate($whe);

            $map[] = ['cate_id','=',$cate_id];
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
     * 首页 获取轮播图
     */
    public function getOneAdver(){
        $adverId = input('param.adver_id');
        try{
            $adver = new AdverModel();
            $data = $adver->getOneAdver($adverId);
            if(!empty($data)){
                if($data['pic']){
                    $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                }
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 御膳房
     */
    public function getProduct(){

        $conName = input('param.con_name');
        try{

            if($conName =="节气菜谱"){
                $parentId = 1;
            }elseif ($conName =="节气食材"){
                $parentId = 2;
            }elseif ($conName =="中药材"){
                $parentId = 3;
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '请选择产品类型']);
            }
            $map[] = ['is_rec','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $product = new ProductModel();
            $data = $product->where($map)->order('sort')->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    $v['thum_pic'] = Config::get('app.baseUrl').$v['thum_pic'];
                    $ParentCate =$this->getParentId($v['cate_id']);
                    if($ParentCate['parent_id'] == $parentId){
                        $arra[] = $v;
                    }
                }
                return json(['code' => 200, 'data' => $arra, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '暂时没有数据']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 御膳房-栏目图
     */
    public function getProductCate(){

        $conName = input('param.con_name');
        try{

            if($conName =="节气菜谱"){
                $map[] = ['id','=',1];
            }elseif ($conName =="节气食材"){
                $map[] = ['id','=',2];
            }elseif ($conName =="中药材"){
                $map[] = ['id','=',3];
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '请选择产品类型']);
            }
            $cate = new ProductCateModel();
            $data = $cate->where($map)->find();
            if(!empty($data)){
                $data['icon'] = Config::get('app.baseUrl').$data['icon'];
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 御膳房-列表页
     */
    public function getProductList(){

        $conName = input('param.con_name');
        try{

            if($conName =="节气菜谱"){
                $parentId = 1;
            }elseif ($conName =="节气食材"){
                $parentId = 2;
            }elseif ($conName =="中药材"){
                $parentId = 3;
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '请选择产品类型']);
            }
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $product = new ProductModel();
            $data = $product->where($map)->order('create_time desc')->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    $v['thum_pic'] = Config::get('app.baseUrl').$v['thum_pic'];
                    $ParentCate =$this->getParentId($v['cate_id']);
                    if($ParentCate['parent_id'] == $parentId){
                        $arra[$parentId][] = $v;
                    }
                }
                if(!empty($arra[$parentId])){
                    $cate = new ProductCateModel();
                    $cataData = $cate->where('parent_id','=',$parentId)->select();
                    $arrd =[];
                    if(!empty($cataData)){
                        foreach ($cataData as $key=>&$va){
                            foreach ($arra[$parentId] as &$vab){
                                if($vab['cate_id'] ==$va['id']){
                                    $arrd[$key]['title']=$va['name'];
                                    $arrd[$key]['data'][]=$vab;
                                }
                            }
                        }
                    }
                }
                return json(['code' => 200, 'data' => $arrd, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '暂时没有数据']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    public function getParentId($cate_id){
        $cate = new ProductCateModel();
        $cataData = $cate->where('id','=',$cate_id)->find();
        return $cataData;
    }
    /**
     * 首页 御膳房-详情页
     */
    public function getProductInfo(){

        $conId = input('param.con_id');
        try{

            $map[] = ['r.id','=',$conId];
            $adver = new ProductModel();
            $data = $adver->getProductInfo($map);
            if(!empty($data)){
                if($data['pic']){
                    $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                }
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 花样养生
     */
    public function getArticle(){
        $conName = input('param.con_name');
        try{
            if($conName =="节气"){
                $map[] = ['r.cate_id','=',20];
            }elseif ($conName =="月份"){
                $map[] = ['r.cate_id','=',21];
            }elseif ($conName =="季节"){
                $map[] = ['r.cate_id','=',22];
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '请选择产品类型']);
            }
            $map[] = ['r.status','=',1];
            $adver = new ArticleModel();
            $data = $adver->getArticleTitle($map);
            if(!empty($data)){
                if($data['thum_pic']){
                    $data['thum_pic'] = Config::get('app.baseUrl').$data['thum_pic'];
                }
                if($conName =="月份"){
                    $data['remark'] = $this->getMon($data['remark']);
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    public function getMon($a){
        if(!empty($a)){
            switch($a)
            {
                case 1:
                    return '一';
                    break;
                case 2:
                    return '二';
                    break;
                case 3:
                    return '三';
                    break;
                case 4:
                    return '四';
                    break;
                case 5:
                    return '五';
                    break;
                case 6:
                    return '六';
                    break;
                case 7:
                    return '七';
                    break;
                case 8:
                    return '八';
                    break;
                case 9:
                    return '九';
                    break;
                case 10:
                    return '十';
                    break;
                case 11:
                    return '十一';
                    break;
                case 12:
                    return '十二';
                    break;
            }
        }else{
            return '';
        }

    }
    /**
     * 首页 花样养生-详情页
     */
    public function getArticleInfo(){
        $conId = input('param.con_id');
        try{

            $map[] = ['id','=',$conId];
            $adver = new ArticleModel();
            $data = $adver->getOneArticle($map);
            if(!empty($data)){
                if($data['pic']){
                    $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                }
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 养生百问
     */
    public function getHelp(){

        try{

            $map[] = ['is_rec','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
//            $od = 'create_time desc';
            $od = 'sort';
            $adver = new HelpModel();
            $data = $adver->getHelpByWhere($map,4,$od);
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
     * 首页 养生百问-相关文章
     */
    public function getHelpMore(){

        try{

            $map[] = ['is_rec','=',1];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $od = 'create_time desc';
            $adver = new HelpModel();
            $data = $adver->getHelpByWhere($map,2,$od);
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
     * 首页 搜索
     */
    public function search(){
        $keyword = input('param.keyword');
        $conName = input('param.con_name');
        $conCate = input('param.con_cate');
        try{
            $map[] = ['title','like','%'.$keyword.'%'];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            if($conName == '产品'){
                if($conCate =="节气菜谱"){
                    $parentId = 1;
                }elseif ($conCate =="节气食材"){
                    $parentId = 2;
                }elseif ($conCate =="中药材"){
                    $parentId = 3;
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '请选择产品类型']);
                }
                $listProduct = new ProductModel();
                $data = $listProduct->where($map)->select();

                if(!empty($data)){
                    foreach ($data as &$v){
                        $v['thum_pic'] = Config::get('app.baseUrl').$v['thum_pic'];
                        $ParentCate =$this->getParentId($v['cate_id']);
                        if($ParentCate['parent_id'] == $parentId){
                            $arra[$parentId][] = $v;
                        }
                    }
                    $arrd =[];
                    if(!empty($arra[$parentId])){
                        $cate = new ProductCateModel();
                        $cataData = $cate->where('parent_id','=',$parentId)->select();
                        if(!empty($cataData)){
                            foreach ($cataData as $key=>&$va){
                                foreach ($arra[$parentId] as &$vab){
                                    if($vab['cate_id'] ==$va['id']){
                                        $arrd[$key]['title']=$va['name'];
                                        $arrd[$key]['data'][]=$vab;
                                    }
                                }
                            }
                        }
                        return json(['code' => 200, 'data' => $arrd, 'msg' => '']);
                    }else{
                        return json(['code' => 200, 'data' => $arrd, 'msg' => '暂时没有数据']);
                    }

                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂时没有数据']);
                }
            }elseif ($conName == '文章'){
                $listHelp = new HelpModel();
                $data = $listHelp->where($map)->select();
                foreach($data as $k=>&$v){
                    $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }



    /**
     * 首页 时辰养生-时辰
     */
    public function getBlogCate(){

        try{
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $od = 'sort';
            $adver = new BlogCateModel();
            $data = $adver->where($map)->order('create_time')->select();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 时辰养生-详情
     */
    public function getBlogInfo(){

        $cateId = input('param.cate_id');
        try{
            if(!empty($cateId)){
                $catenewId = $cateId;
            }else{
                //根据当前时间判断当前的时辰
                $mytime = getdate();
                $blogCate = new BlogCateModel();
                $blogCateDate = $blogCate->where('status','=',1)->select();
                foreach ($blogCateDate as $key=>&$v){
                    $start[$key] = (int)substr($v['remark'],0,2);
                    $end[$key] = (int)substr($v['remark'],6,2);
                    if($mytime['hours'] >= $start[$key] && $mytime['hours'][$key] < $end){
                        $catenewId = $v['id'];
                    }
                }
            }
            if(empty($catenewId)){
                $catenewId = 5;
            }
            $map[] = ['cate_id','=',$catenewId];
//            $map[] = ['remark','=',date('Y-m-d')];
            $adver = new BlogModel();
            $data = $adver->where($map)->find();
            if(!empty($data)){
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 时辰养生-详情
     */
    public function getBlogDetail(){

        try{
            //根据当前时间判断当前的时辰
            $mytime = getdate();
            $catenewId = '';
            $blogCate = new BlogCateModel();
            $blogCateDate = $blogCate->where('status','=',1)->select();
            foreach ($blogCateDate as $key=>&$v){
                $start[$key] = (int)substr($v['remark'],0,2);
                $end[$key] = (int)substr($v['remark'],6,2);
                if($mytime['hours'] >= $start[$key] && $mytime['hours'][$key] < $end){
                    $catenewId = $v['id'];
                }
            }
            if(empty($catenewId)){
                $catenewId = 5;
            }
            $map[] = ['cate_id','=',$catenewId];
            $adver = new BlogModel();
            $data = $adver->where($map)->field('id,title,summary')->find();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 养生-日历
     */
    public function getBlogDay(){

        try{

            $mytime = getdate();
            $map[] = ['remark','=',date('Y-m-d')];
            $adver = new BlogDayModel();
            $data = $adver->where($map)->find();
            $data['mon'] = date('Y-m');
            $data['mday'] = $mytime['mday'];
            $aa= (int)$mytime['wday'];
            $data['wday'] = $this->getWed($aa);
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    public function getWed($ba){
        if(!empty($ba)){
            switch($ba)
            {
                case 0:
                    return '星期日';
                    break;
                case 1:
                    return '星期一';
                    break;
                case 2:
                    return '星期二';
                    break;
                case 3:
                    return '星期三';
                    break;
                case 4:
                    return '星期四';
                    break;
                case 5:
                    return '星期五';
                    break;
                case 6:
                    return '星期六';
                    break;
            }
        }else{
            return '';
        }

    }
    /**
     * 首页 养生百问-列表页
     */
    public function getHelpList(){

        $conName = input('param.con_name');
        try{

            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $od = 'sort';
            $adver = new HelpModel();
            $data = $adver->getHelpByWhere($map,18,$od);
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
     * 首页 养生百问-详情页
     */
    public function getHelpInfo(){
        $conId = input('param.con_id');
        try{

            $map[] = ['id','=',$conId];
            $adver = new HelpModel();
            $data = $adver->getOneHelp($map);
            if(!empty($data)){
                if($data['pic']){
                    $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                }
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-收藏、取消收藏
     */
    public function favorite(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conId = input('param.con_id');
        $type = input('param.type');//0-请求1-收藏 2-取消
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            $con = 'product';
            //处理收藏逻辑
            $map[] = ['app_id','=',$AppId];
            $map[] = ['favorite_id','=',$conId];
            $map[] = ['favorite_table','=',$con];
            $map[] = ['member_id','=',$userId];
            $favorite = new FavoriteModel();
            $info = $favorite->where($map)->find();
            if($type == 0){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已收藏']);
                } else{
                    return json(['code' => 100, 'data' => [], 'msg' => '未收藏']);
                }
            }elseif ($type == 1){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已收藏不能重复收藏']);
                }else{
                    $data = array(
                        'app_id' => $AppId,
                        'member_id' => $userId,
                        'favorite_id' => $conId,
                        'favorite_table' => $con,
                        'create_time' =>time()
                    );
                    $favorite->strict(false)->insert($data);
                    Db::name('product')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    return json(['code' => 200, 'data' => [], 'msg' => '收藏成功']);
                }
            }elseif($type == 2){
                if($info){
                    $favorite->where($map)->delete();
                    Db::name('product')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    return json(['code' => 200, 'data' => [], 'msg' => '取消收藏成功']);
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂时没有收藏不能取消收藏']);
                }
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-点赞
     */
    public function addLike(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conId = input('param.con_id');
        $type = input('param.type');//0-请求1-点赞 2-取消
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            $con = 'product';
            //处理点赞逻辑
            $map[] = ['like_table','=',$con];
            $map[] = ['like_id','=',$conId];
            $map[] = ['member_id','=',$userId];

            $like = new LikeModel();
            $info = $like->where($map)->find();
            if($type == 0){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已点赞']);
                } else{
                    return json(['code' => 100, 'data' => [], 'msg' => '未点赞']);
                }
            }elseif ($type == 1){
                if($info){
                    return json(['code' => 200, 'data' => [], 'msg' => '已点赞不能重复点赞']);
                }else{
                    $data = array(
                        'member_id' => $userId,
                        'like_id' => $conId,
                        'like_table' => $con,
                        'create_time' =>time()
                    );
                    $like->strict(false)->insert($data);
                    Db::name('product')->where('id', '=', $conId)->setInc('like_num', 1);

                    return json(['code' => 200, 'data' => [], 'msg' => '点赞成功']);
                }

            }elseif($type == 2){
                if($info){
                    $like->where($map)->delete();
                    Db::name('product')->where('id', '=', $conId)->setDec('like_num', 1);

                    return json(['code' => 200, 'data' => [], 'msg' => '取消点赞']);
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂时没有点赞不能取消点赞']);
                }
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
        }
        $this->push($res);
    }
    /**
     * 首页-喝水
     */
    public function drinkWater(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $type = input('param.type');//1-请求数据，2-提交数据
        try{
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            $userWater = new UserWaterModel();
            $userWaterDate = $userWater->where('user_id','=',$userId)->find();
            if($type == 1){
                if(!empty($userWaterDate)){
                    return json(['code' => 200, 'data' => $userWaterDate, 'msg' => '']);
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂无数据']);
                }
            }elseif ($type == 2){
                $conData = input('param.con_data');
                $waterData = [];
                $baseData = 33;
                $waterNum = $baseData * $conData;
                $waterData['num'] = $waterNum;
                $waterData['aa'] = ceil($waterNum / 471);
                $waterData['ba'] = ceil($waterNum * 0.15);
                $waterData['ca'] = ceil($waterNum * 0.14);
                $waterData['da'] = ceil($waterNum * 0.15);
                $waterData['ea'] = ceil($waterNum * 0.14);
                $waterData['fa'] = ceil($waterNum * 0.15);
                $waterData['ga'] = ceil($waterNum * 0.15);
                $waterData['ha'] = ceil($waterNum * 0.14);
                $waterData['user_id'] = $userId;
                $waterData['weight'] = $conData;
                if(!empty($userWaterDate)){
                    $waterData['id'] = $userWaterDate['id'];
                    $flag =$userWater->editUserWater($waterData);;
                }else{
                    $flag =$userWater->insertUserWater($waterData);
                }
                return json(['code' => $flag['code'], 'data' => $waterData, 'msg' => $flag['msg']]);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 喝水提醒-
     */
    public function getUserWater(){
        $userId = input('param.token');
        try{
            //根据当前时间判断当前的时辰
            $mytime = getdate();
            if(!empty($userId)){
                $userWater = new UserWaterModel();
                $userWaterDate = $userWater->where('user_id','=',$userId)->find();
                if(!empty($userWaterDate)){
                    if($mytime['hours'] >= 0 && $mytime['hours'] < 6){
                        $con = 'AM6:30您需要喝'.$userWaterDate['ba'].'CC的水';
                    }elseif ($mytime['hours'] >= 6 && $mytime['hours'] < 9){
                        $con = 'AM8:30您需要喝'.$userWaterDate['ca'].'CC的水';
                    }elseif ($mytime['hours'] >= 9 && $mytime['hours'] < 11){
                        $con = 'AM11:00您需要喝'.$userWaterDate['da'].'CC的水';
                    }elseif ($mytime['hours'] >= 11 && $mytime['hours'] < 13){
                        $con = 'PM12:50您需要喝'.$userWaterDate['ea'].'CC的水';
                    }elseif ($mytime['hours'] >= 13 && $mytime['hours'] < 15){
                        $con = 'PM3:00您需要喝'.$userWaterDate['fa'].'CC的水';
                    }elseif ($mytime['hours'] >= 15 && $mytime['hours'] < 18){
                        $con = 'PM5:30您需要喝'.$userWaterDate['ga'].'CC的水';
                    }elseif ($mytime['hours'] >= 18 && $mytime['hours'] < 22){
                        $con = 'PM10:30您需要喝'.$userWaterDate['ha'].'CC的水';
                    }else{
                        $con = '科学健康饮水可以帮助肾脏户肝脏解毒';
                    }
                }else{
                    $con = '尚无数据,立即测试';
                    return json(['code' => 100, 'data' => $con, 'msg' => '尚无测试数据']);
                }
            }else{
                $con = '点击登录测试';
                return json(['code' => 101, 'data' => $con, 'msg' => '点击登录测试']);
            }
            return json(['code' => 200, 'data' => $con, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}