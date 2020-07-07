<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 16:49
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\model\LikeModel;
use app\api\model\MerchantModel;
use app\api\model\WorkCateModel;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\NoticeModel;
use app\api\model\ArticleModel;
use app\api\model\AdverModel;
use app\api\model\AdverCateModel;
use app\api\model\MerchantUserModel;
use app\api\model\PaymentSlipModel;
use app\api\model\WorkOrderModel;
use app\api\model\SpaceModel;
use app\api\model\FavoriteModel;
use think\facade\Config;
use think\db;
class Home extends Base
{
    /**
     * 首页 获取轮播图
     */
    public function getAdver(){
        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);
            $company_id = $this->getCompanyId($AppId);
            $whe[] = ['name','=','首页'];
            $whe[] = ['company_id','=',$company_id];
            $whe[] = ['status','=',1];
            $whe[] = ['is_delete','=',0];
            $adverCate = new AdverCateModel();
            $cate_id = $adverCate->getAdverCate($whe);

            $map[] = ['cate_id','=',$cate_id];
            $map[] = ['company_id','=',$company_id];
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
     * 首页 获取公告数据
     */
    public function getNotice(){

        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['company_id','=',$company_id];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $notice = new NoticeModel();
            $data = $notice->order('create_time desc')->field('id,title,create_time')->limit(3)->where($map)->select();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 公告详情
     */
    public function getNoticeDetail(){
        $AppID = input('param.app_id');
        $noticeId = input('param.notice_id');
        try{
            AppId::checkAppID($AppID);
            $notice = new NoticeModel();
            $data = $notice->getNotice($noticeId);
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
                $notice->where('id','=',$noticeId)->setInc('views',1);
            }else{
                $this->getFail([],'参数异常，请确认！',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页 获取新闻列表
     */
    public function getArticleList(){
        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['r.company_id','=',$company_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $article = new ArticleModel();
            $od = "r.create_time desc";
            $data = $article->getArticleByWhere($map,18,$od);
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
    * 首页 获取新闻列表(更多)
    */
    public function getAllArticleList(){
        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);

            $company_id = $this->getCompanyId($AppId);
            $map[] = ['r.company_id','=',$company_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $article = new ArticleModel();
            $od = "r.create_time desc";
            $data = $article->getArticleByWhere($map,18,$od);
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
     * 首页 新闻详情（详情）
     */
    public function getArticleDetail(){
        $AppID = input('param.app_id');
        $article_id = input('param.article_id');
        try{
            AppId::checkAppID($AppID);
            $map[] = ['r.id','=',$article_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $article = new ArticleModel();
            $data = $article->getArticleInfo($map);
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
                $article->where('id','=',$article_id)->setInc('views',1);
            } else {
                $this->getFail([],'参数异常，请确认！',100);
            }
            $data =$data->toArray();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-验证身份
     */
    public function compareUser(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            //确定自己绑定的商户及在这个商户中的身份
            $merUser = $this->getMerUser($userId,$company_id);

            if(empty($merUser)){
                return json(['code' => 100, 'data' => [], 'msg' => '您暂无访问权限']);
            }else{
                $role_type = $merUser['role_type'];
                $merchant= new MerchantModel();
                $arra = $merchant->where('id','=',$merUser['merchant_id'])->field('id,title,logo')->find();
                $merUser['title'] = $arra['title'];
                $merUser['logo'] = Config::get('app.baseUrl').$arra['logo'];

                //如果是员工则提示身份不正确
                if( $role_type != 2){
                    return json(['code' => 101, 'data' => $merUser, 'msg' => '您暂无访问权限']);
                }
                return json(['code' => 200, 'data' => $merUser, 'msg' => '可以访问']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-充值缴费
     */
    public function getPaymentInfo(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        $cate_name = input('param.cate_name') ? input('param.cate_name'):'';
        $status = input('param.status') ? input('param.status'):0;
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            //确定自己绑定的商户及在这个商户中的身份
            $merUser = $this->getMerUser($userId,$company_id);
            if(empty($merUser)){
                return json(['code' => 100, 'data' => [], 'msg' => '您暂无访问权限']);
            }else{
                $merchant_id = $merUser['merchant_id'];
                $role_type = $merUser['role_type'];
            }
            //如果是员工则提示身份不正确
            if( $role_type != 2){
                return json(['code' => 101, 'data' => [], 'msg' => '您暂无访问权限']);
            }
            //如果是财务人员，查看账单，账单是这个商户的未缴清单数组，
            $paymentDate = $this->getPaymentSlip($merchant_id,$cate_name,$status);
            if(empty($paymentDate)){
                return json(['code' => 102, 'data' => [], 'msg' => '您暂时没有待付费']);
            }
            $data =$paymentDate->toArray();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-充值缴费-根据用户id获取自己绑定的商户及身份
     */
    public function getMerUser($user_id,$company_id){
        $merchantUser = new MerchantUserModel();
        $map[] = ['user_id','=',$user_id];
        $map[] = ['company_id','=',$company_id];
        $merUser = $merchantUser->where($map)->find();
        return $merUser;
    }
    /**
     * 首页-充值缴费-获取商户缴费清单
     */
    public function getPaymentSlip($merchant_id,$cate_name,$status){
        $payment = new PaymentSlipModel();
        $where[] = ['r.merchant_id','=',$merchant_id];
        if(!empty($cate_name)){
            $where[] = ['r.cate_name','=',$cate_name];
        }
        $where[] = ['r.status','in',$status];
        if($status == 0){
            $od = 'r.create_time';
        }else{
            $od = 'r.update_time desc';
        }

        $paymentDate = $payment->getPaymentSlipByWhere($where,$od);
        if(!empty($paymentDate)){
            foreach ($paymentDate as &$v){
                $v['logo'] = Config::get('app.baseUrl').$v['logo'];
            }
        }
        return $paymentDate;
    }
    /**
     * 首页-充值缴费-缴费单详情
     */
    public function getOnePayment(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        $payment_id = input('param.payment_id');
        try{
            AppId::checkAppID($AppID);
            $this->isPositiveInt($payment_id);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }

            $payment = new PaymentSlipModel();
            $map[] = ['r.id','=',$payment_id];
            $arr = $payment->getOnePaymentSlipByWhere($map);
            if(!empty($arr)){
                return json(['code' => 200, 'data' => $arr, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => $arr, 'msg' => '数据异常']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-充值缴费-完成缴费
     */
    public function setPayment(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        $payment_id = input('param.payment_id');
        try{
            AppId::checkAppID($AppID);
            $this->isPositiveInt($payment_id);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            //
//            $date['merchant_id'] =  input('param.merchant_id');
//            $date['cate_name'] =  input('param.cate_name');
//            $date['cost'] =  input('param.cost');
            $date['type'] =  input('param.type');
            $date['status'] =  2;
            $payment = new PaymentSlipModel();
            $flag = $payment->save($date, ['id' => $payment_id]);
            if($flag == false ){
                return json(['code' => 102, 'data' => [], 'msg' => '您暂无未付费']);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '您的缴费提交成功']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-物业保修-创建新工单
     */
    public function setWorkOrder(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            $merUser = $this->getMerUser($userId,$company_id);
            if(empty($merUser)){
                return json(['code' => 101, 'data' => [], 'msg' => '您暂无访问权限']);
            }
            $date['company_id'] =  $company_id;
            $date['user_id'] =  $userId;
            $date['title'] =  input('param.title');
            $date['cate_id'] =  input('param.cate_id');
            $date['content'] =  input('param.content');
            $date['contacts'] =  input('param.contacts');
            $date['tel'] =  input('param.tel');
            $date['pic'] =  input('param.pic');
            $date['parent_id'] =  0;
            $work = new WorkOrderModel();
            $flag = $work->save($date);
            if($flag == false ){
                return json(['code' => 101, 'data' => [], 'msg' => '工单提交失败']);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '工单提交成功']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-物业保修-工单f分类列表
     */
    public function workCateList(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            $map[] = ['company_id','=',$company_id];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $work = new WorkCateModel();
            $data = $work->where($map)->select();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-物业保修-工单列表
     */
    public function workList(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            $merUser = $this->getMerUser($userId,$company_id);
            if(empty($merUser)){
                return json(['code' => 100, 'data' => [], 'msg' => '您暂无发布权限']);
            }
            $map['r.company_id'] =  $company_id;
            $map['r.user_id'] =  $userId;
            $map['r.parent_id'] =  0;
            $od = 'r.create_time desc';
            $work = new WorkOrderModel();
            $data = $work->getWorkOrderByWhere($map,18,$od);
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
     * 首页-物业保修-接龙工单列表
     */

    public function workOrderList(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        $work_id = input('param.work_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            $merUser = $this->getMerUser($userId,$company_id);
            if(empty($merUser)){
                return json(['code' => 100, 'data' => [], 'msg' => '您暂无查看权限']);
            }
            $work = new WorkOrderModel();
            $map[] = ['id','=',$work_id];
            $map[] = ['parent_id','=',$work_id];
            $workDate = $work->whereOr($map)->select();
            if(empty($workDate)){
                return json(['code' => 100, 'data' => [], 'msg' => '工单参数异常']);
            }
            $data = $workDate;
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-物业保修-添加接龙工单
     */
    public function addWorkOrder(){
        $userId = input('param.token');
        $AppID = input('param.app_id');
        $work_id = input('param.work_id');
        try{
            AppId::checkAppID($AppID);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $company_id = $this->getCompanyId($AppID);
            $merUser = $this->getMerUser($userId,$company_id);
            if(empty($merUser)){
                return json(['code' => 100, 'data' => [], 'msg' => '您暂无发布权限']);
            }
            $work = new WorkOrderModel();
            $workDate = $work->where('id','=',$work_id)->find();
            if(empty($workDate)){
                return json(['code' => 100, 'data' => [], 'msg' => '工单参数异常']);
            }
            $date['company_id'] = $company_id;
            $date['user_id'] = $userId;
            $date['title'] = $workDate['title'];
            $date['cate_id'] = $workDate['cate_id'];
            $date['content'] = input('param.content');
            $date['contacts'] = input('param.contacts');
            $date['tel'] =  input('param.tel');
            $date['pic'] =  input('param.pic');
            $date['parent_id'] =  $work_id;
            $flag = $work->save($date);
            if($flag == false ){
                return json(['code' => 100, 'data' => [], 'msg' => '工单提交失败']);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '工单提交成功']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }

    /**
     * 首页-会议室预定-会议室列表
     */
    public function spaceList(){
        $AppID = input('param.app_id');
        try{
            AppId::checkAppID($AppID);

            $company_id = $this->getCompanyId($AppID);
//            $merUser = $this->getMerUser($userId,$company_id);
//            if(empty($merUser)){
//                return json(['code' => 100, 'data' => [], 'msg' => '您暂无发布权限']);
//            }
            $map['r.company_id'] =  $company_id;
            $map['r.status'] =  1;
            $map['r.is_delete'] =  0;
            $od = 'r.create_time desc';
            $work = new SpaceModel();
            $data = $work->getSpaceByWhere($map,$od);
            if(!empty($data)){
                foreach($data as $k=>&$v){
                    $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '暂无相关数据']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-会议室预定-会议室详情
     */
    public function spaceiInfo(){
        $AppID = input('param.app_id');
        $spaceId = input('param.space_id');
        try{
            AppId::checkAppID($AppID);

            $map[] = ['r.id','=',$spaceId] ;
            $work = new SpaceModel();
            $data = $work->getSpaceInfo($map);
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
                $work->where('id','=',$spaceId)->setInc('views',1);
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '暂无相关数据']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 首页-收藏 【活动、房源、企业服务，会议室预定】
     */
    public function favorite(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conId = input('param.con_id');
        $type = input('param.type');//0-请求1-收藏 2-取消
        $conName = input('param.con_name');
        try{
            AppId::checkAppID($AppId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            if($conName =='活动'){
                $con = 'activity';
            }elseif ($conName =='房源'){
                $con = 'goods';
            }elseif ($conName =='企业服务'){
                $con = 'product';
            }elseif ($conName =='会议室预定'){
                $con = 'space';
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '内容类型错误']);
            }
            $map[] = ['favorite_table','=',$con];
            $map[] = ['app_id','=',$AppId];
            $map[] = ['favorite_id','=',$conId];
            $map[] = ['user_id','=',$userId];

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
                        'user_id' => $userId,
                        'favorite_id' => $conId,
                        'favorite_table' => $con,
                        'create_time' =>time()
                    );
                    $favorite->strict(false)->insert($data);
                    if($conName =='活动'){
                        Db::name('activity')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    }elseif ($conName =='房源'){
                        Db::name('goods')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    }elseif ($conName =='企业服务'){
                        Db::name('product')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    }elseif ($conName =='会议室预定'){
                        Db::name('space')->where('id', '=', $conId)->setInc('favorite_num', 1);
                    }
                    return json(['code' => 200, 'data' => [], 'msg' => '收藏成功']);
                }

            }elseif($type == 2){
                if($info){
                    $favorite->where($map)->delete();
                    if($conName =='活动'){
                        Db::name('activity')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    }elseif ($conName =='房源'){
                        Db::name('goods')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    }elseif ($conName =='企业服务'){
                        Db::name('product')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    }elseif ($conName =='会议室预定'){
                        Db::name('space')->where('id', '=', $conId)->setDec('favorite_num', 1);
                    }
                    return json(['code' => 200, 'data' => [], 'msg' => '取消收藏成功']);
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '暂时没有收藏不能取消收藏']);
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
     * 我的-收藏统计 【活动、房源、企业服务，会议室预定】
     */
    public function getFavorite(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conName = input('param.con_name');
        try{
            AppId::checkAppID($AppId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            if($conName =='活动'){
                $con = 'activity';
            }elseif ($conName =='房源'){
                $con = 'goods';
            }elseif ($conName =='企业服务'){
                $con = 'product';
            }elseif ($conName =='会议室预定'){
                $con = 'space';
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '内容类型错误']);
            }
            $map[] = ['favorite_table','=',$con];
            $map[] = ['app_id','=',$AppId];
            $map[] = ['user_id','=',$userId];

            $favorite = new FavoriteModel();
            $lists = $favorite->where($map)->field('id,favorite_id,user_id,create_time')->order('create_time desc')->select();
            if(!empty($lists)){
                if($conName =='活动'){
                    foreach ($lists as &$v){
                        $arra = Db::name('activity')->field('id,title,pic,price')->where('id', '=', $v['favorite_id'])->find();
                        if(!empty($arra)){
                            $v['con_title'] = $arra['title'];
                            $v['con_pic'] = Config::get('app.baseUrl').$arra['pic'];
                            $v['con_price'] = $arra['price'];
                        }else{
                            $v['con_title'] = '';
                            $v['con_pic'] = '';
                            $v['con_price'] = '';
                        }
                    }
                }elseif ($conName =='房源'){
                    foreach ($lists as &$v){
                        $arra = Db::name('goods')->field('id,title,pic,price')->where('id', '=', $v['favorite_id'])->find();
                        if(!empty($arra)){
                            $v['con_title'] = $arra['title'];
                            $v['con_pic'] = Config::get('app.baseUrl').$arra['pic'];
                            $v['con_price'] = $arra['price'];
                        }else{
                            $v['con_title'] = '';
                            $v['con_pic'] = '';
                            $v['con_price'] = '';
                        }
                    }
                }elseif ($conName =='企业服务'){
                    foreach ($lists as &$v){
                        $arra = Db::name('product')->field('id,title,pic,m_price')->where('id', '=', $v['favorite_id'])->find();
                        if(!empty($arra)){
                            $v['con_title'] = $arra['title'];
                            $v['con_pic'] = Config::get('app.baseUrl').$arra['pic'];
                            $v['con_price'] = $arra['m_price'];
                        }else{
                            $v['con_title'] = '';
                            $v['con_pic'] = '';
                            $v['con_price'] = '';
                        }
                    }
                }elseif ($conName =='会议室预定'){
                    foreach ($lists as &$v){
                        $arra = Db::name('space')->field('id,title,pic,price')->where('id', '=', $v['favorite_id'])->find();
                        if(!empty($arra)){
                            $v['con_title'] = $arra['title'];
                            $v['con_pic'] = Config::get('app.baseUrl').$arra['pic'];
                            $v['con_price'] = $arra['price'];
                        }else{
                            $v['con_title'] = '';
                            $v['con_pic'] = '';
                            $v['con_price'] = '';
                        }
                    }
                }
                return json(['code' => 200, 'data' => $lists, 'msg' => '']);
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '暂无收藏内容']);
            }
        }catch (ApiErrorException $e){
            $res = $e->toArray();
        }
        $this->push($res);
    }

    /**
     * 首页-点赞 【活动、房源、企业服务，会议室预定】
     */
    public function addLike(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        $conId = input('param.con_id');
        $type = input('param.type');//0-请求1-收藏 2-取消
        $conName = input('param.con_name');
        try{
            AppId::checkAppID($AppId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            if($conName =='活动'){
                $con = 'activity';
            }elseif ($conName =='房源'){
                $con = 'goods';
            }elseif ($conName =='企业服务'){
                $con = 'product';
            }elseif ($conName =='会议室预定'){
                $con = 'space';
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '内容类型错误']);
            }
            $map[] = ['like_table','=',$con];
            $map[] = ['app_id','=',$AppId];
            $map[] = ['like_id','=',$conId];
            $map[] = ['user_id','=',$userId];

            $like = new LikeModel();
            $info = $like->where($map)->find();
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
                        'user_id' => $userId,
                        'like_id' => $conId,
                        'like_table' => $con,
                        'create_time' =>time()
                    );
                    $like->strict(false)->insert($data);
                    if($conName =='活动'){
                        Db::name('activity')->where('id', '=', $conId)->setInc('add_like', 1);
                    }elseif ($conName =='房源'){
                        Db::name('goods')->where('id', '=', $conId)->setInc('add_like', 1);
                    }elseif ($conName =='企业服务'){
                        Db::name('product')->where('id', '=', $conId)->setInc('add_like', 1);
                    }elseif ($conName =='会议室预定'){
                        Db::name('space')->where('id', '=', $conId)->setInc('add_like', 1);
                    }
                    return json(['code' => 200, 'data' => [], 'msg' => '点赞成功']);
                }

            }elseif($type == 2){
                if($info){
                    $like->where($map)->delete();
                    if($conName =='活动'){
                        Db::name('activity')->where('id', '=', $conId)->setDec('add_like', 1);
                    }elseif ($conName =='房源'){
                        Db::name('goods')->where('id', '=', $conId)->setDec('add_like', 1);
                    }elseif ($conName =='企业服务'){
                        Db::name('product')->where('id', '=', $conId)->setDec('add_like', 1);
                    }elseif ($conName =='会议室预定'){
                        Db::name('space')->where('id', '=', $conId)->setDec('add_like', 1);
                    }
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
}