<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:12
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\model\SpaceModel;
use app\api\model\UserModel;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\MerchantModel;
use app\api\model\MerchantUserModel;
use app\api\model\OrderModel;
use app\api\model\GoodsModel;
use app\api\model\ProductModel;
use app\api\model\ActivityModel;
use app\api\model\SpaceOrderModel;
use app\api\model\PaymentSlipModel;
use app\api\model\ContractModel;
use app\api\model\MerchantPicsModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\File;
use think\Db;
class Member extends Base
{
    /**
     * 我的 获取当前用户的基本信息
     */
    public function getUser()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $field = 'id,icon,nick_name,real_name,username,phone,email,is_auth,weixin,recommend_code,desc_words,create_time';
            $data = User::getUserInfo($userId, $field);
            if (empty($data)) {
                $this->getFail([],'用户信息丢失，请重新登录',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 完善当前用户的基本信息
     */
    public function setUserInfo()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $user = new UserModel();
            $username = input('param.username');

            if(!empty($username)){
                $userDate = $user->where('username','=',$username)->field('id,username')->find();
                if(!empty($userDate)){
                    if($userDate['id'] != $userId){
                        return json(['code' => 100, 'data' => '', 'msg' => '用户名已存在']);
                    }
                }
            }
            $date['real_name'] = input('param.real_name');
            $date['username'] = input('param.username');
            $date['email'] = input('param.email');
            $date['phone'] = input('param.phone');
            $date['weixin'] = input('param.weixin');
            $date['desc_words'] = input('param.desc_words');
            $date['id'] = $userId;

            $flag = $user->editUser($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 用户的实名认证
     */
    public function realUser()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $date['real_name'] = input('param.real_name');
            $date['phone'] = input('param.phone');
            $date['shen_num'] = input('param.shen_num');
            $date['start_time'] = input('param.start_time');
            $date['end_time'] = input('param.end_time');
            $date['id'] = $userId;
            $date['is_auth'] = 2;
            $user = new UserModel();
            $flag = $user->editUser($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 获取需要搜索的公司
     */
    public function searchMerchant()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $keyword = input('param.keyword');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['r.title','like','%'.$keyword.'%'];
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['r.company_id','=',$company_id];
            $merchant = new MerchantModel();
            $od = "r.create_time desc";
            $data = $merchant->getMerchantByWhere($map,10,$od);
            if(!empty($data)){
                foreach ($data as &$v){
                    $v['value'] = $v['title'];
                }
            }
            $data =$data->toArray();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);

        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 用户绑定公司
     */
    public function bindMerchant()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $merchant_id = input('param.merchant_id');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($merchant_id);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            if(empty($merchant_id)){
                return json(['code' => 100, 'data' => [], 'msg' => '请选择商户']);
            }
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['user_id','=',$userId];
            $map[] = ['company_id','=',$company_id];
            $map[] = ['merchant_id','=',$merchant_id];
            $merchantUser = new MerchantUserModel();
            if(!empty($merchantUser->where($map)->find())){
                return json(['code' => 100, 'data' => [], 'msg' => '不能重复绑定']);
            }else{
                $date['user_id'] = $userId;
                $date['merchant_id'] = $merchant_id;
                $date['company_id'] = $company_id;
                $merchantUser->save($date);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '成功绑定']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 创建订单
     */
    public function createOrder(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conId = input('param.con_id');
        $conName = input('param.con_name');
        $isAgree = input('param.is_agree');
        try {
            if($isAgree != 1){
                return json(['code' => 100, 'data' => [], 'msg' => '此服务须同意相关协议']);
            }
            AppId::checkAppID($AppId);
            $this->isPositiveInt($conId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }

            $company_id = $this->getCompanyId($AppId);
            $map[] = ['user_id','=',$userId];
            $map[] = ['company_id','=',$company_id];
            $map[] = ['con_id','=',$conId];
            $map[] = ['con_name','=',$conName];
            $map[] = ['create_time','egt',time()-5];
            $order = new OrderModel();
            if(!empty($order->where($map)->find())){
                return json(['code' => 100, 'data' => [], 'msg' => '不能重复操作']);
            }else{
                if($conName =='招商'){
                    $goods = new GoodsModel();
                    $mapg[] = ['r.id','=',$conId];
                    $goodsDate = $goods->getGoodsInfo($mapg);
                    if(!empty($goodsDate)){
                        $date['title'] = $goodsDate['title'];
                        $date['price'] = $goodsDate['price'];
                        $date['name'] = $goodsDate['name'];
                        $date['pic'] = $goodsDate['pic'];
                    }else{
                        return json(['code' => 100, 'data' => [], 'msg' => '数据异常']);
                    }
                }elseif ($conName =='企业服务'){
                    $product = new ProductModel();
                    $mapp[] = ['r.id','=',$conId];
                    $productDate = $product->getProductInfo($mapp);
                    if(!empty($productDate)){
                        $date['title'] = $productDate['title'];
                        $date['price'] = $productDate['v_price'];
                        $date['name'] = $productDate['name'];
                        $date['pic'] = $productDate['pic'];
                    }else{
                        return json(['code' => 100, 'data' => [], 'msg' => '数据异常']);
                    }
                }elseif ($conName =='活动'){
                    $activity = new ActivityModel();
                    $mapa[] = ['r.id','=',$conId];
                    $activityDate = $activity->getActivityInfo($mapa);
                    if(!empty($activityDate)){
                        $date['title'] = $activityDate['title'];
                        $date['price'] = $activityDate['price'];
                        $date['name'] = $activityDate['name'];
                        $date['pic'] = $activityDate['pic'];
                    }else{
                        return json(['code' => 100, 'data' => [], 'msg' => '数据异常']);
                    }
                }
                $date['user_id'] = $userId;
                $date['company_id'] = $company_id;
                $date['con_id'] = $conId;
                $date['con_name'] = $conName;
                $date['contacts'] = input('param.contacts');
                $date['phone'] = input('param.phone');
                $date['remarks'] = input('param.remarks');
                $date['is_agree'] = $isAgree;
                $date['status'] = 2;
                $date['company'] = input('param.company');
                $order->save($date);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '提交成功，请保持手机畅通']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 获取订单
     */
    public function getOrder(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }

            $company_id = $this->getCompanyId($AppId);
            $map[] = ['user_id','=',$userId];
            $map[] = ['company_id','=',$company_id];
            $map[] = ['con_name','=',$conName];
            $order = new OrderModel();
            $data = $order->where($map)->order('create_time desc')->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }else{
                return json(['code' => 200, 'data' => [], 'msg' => '暂无相关数据']);
            }

        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 创建会议室订单
     */
    public function createSpaceOrder(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conId = input('param.con_id');
        $conName = input('param.con_name');
        $isAgree = input('param.is_agree');
        try {
            if($isAgree != 1){
                return json(['code' => 100, 'data' => [], 'msg' => '此服务须同意相关协议']);
            }
            AppId::checkAppID($AppId);
            $this->isPositiveInt($conId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }

            $company_id = $this->getCompanyId($AppId);
            $map[] = ['user_id','=',$userId];
            $map[] = ['company_id','=',$company_id];
            $map[] = ['con_id','=',$conId];
            $map[] = ['con_name','=',$conName];
            $map[] = ['create_time','egt',time()-5];
            $order = new SpaceOrderModel();
            if(!empty($order->where($map)->find())){
                return json(['code' => 100, 'data' => [], 'msg' => '不能重复操作']);
            }else{
                if($conName =='会议室预定'){
                    $goods = new SpaceModel();
                    $mapg[] = ['r.id','=',$conId];
                    $goodsDate = $goods->getSpaceInfo($mapg);
                    if(!empty($goodsDate)){
                        $date['title'] = $goodsDate['title'];
                        $date['price'] = $goodsDate['price'];
                        $date['name'] = $goodsDate['name'];
                        $date['address'] = $goodsDate['address'];
                        $date['pic'] = $goodsDate['pic'];
                    }else{
                        return json(['code' => 100, 'data' => [], 'msg' => '数据异常']);
                    }
                }else{
                    return json(['code' => 100, 'data' => [], 'msg' => '内容类型错误']);
                }
                $date['user_id'] = $userId;
                $date['company_id'] = $company_id;
                $date['con_id'] = $conId;
                $date['con_name'] = $conName;
                $date['contacts'] = input('param.contacts');
                $date['phone'] = input('param.phone');
                $date['remarks'] = input('param.remarks');
                $date['is_agree'] = $isAgree;
                $date['status'] = 2;
                $date['company'] = input('param.company');
                $date['end_time'] = input('param.end_time');
                $date['start_time'] = input('param.start_time');
                $order->save($date);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '提交成功，请保持手机畅通']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 获取订单
     */
    public function getSpaceOrder(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }

            $company_id = $this->getCompanyId($AppId);
            $map[] = ['user_id','=',$userId];
            $map[] = ['company_id','=',$company_id];
            $order = new SpaceOrderModel();
            $data = $order->where($map)->order('create_time desc')->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }else{
                return json(['code' => 200, 'data' => [], 'msg' => '暂无相关数据']);
            }

        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 首页-根据用户id获取自己绑定的商户及身份
     */
    public function getMerUser($user_id,$company_id){
        $merchantUser = new MerchantUserModel();
        $map[] = ['user_id','=',$user_id];
        $map[] = ['company_id','=',$company_id];
        $map[] = ['role_type','in','1,2,3'];
        $merUser = $merchantUser->where($map)->find();
        return $merUser;
    }
    /**
     * 我的 公司信息完善
     */
    public function setMerchantInfo(){
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
            $date['id'] =  $merUser['merchant_id'];
            $date['user_id'] =  $userId;
            $date['title'] =  input('param.title','');
            $date['address'] =  input('param.address','');
            $date['email'] =  input('param.email','');
            $date['tel'] =  input('param.tel','');
            $date['summary'] =  input('param.summary','');
            $date['expert'] =  input('param.expert','');
            $date['require'] =  input('param.require','');
            $date['parent_id'] =  0;
            $work = new MerchantModel();
            $flag = $work->save($date);
            if($flag == false ){
                return json(['code' => 101, 'data' => [], 'msg' => '信息保存失败']);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '信息保存成功']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 我的 上传图片--缴费
     */
    public function uplaodPayPic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=false,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='pay'){
                //删除原有图片保存新图片
                $payment = new PaymentSlipModel();
                if(!empty($conId)){
                    $bra = $payment->getOnePaymentSlip($conId);
                    $oldurl=$bra['pic'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'pic' => $pic['data'],
                    'id' => $conId,
                );
                $flag = $payment->editPaymentSlip($date);
            }
            //$flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 上传图片--缴费
     */
    public function uplaodIcon(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='company'){
                //删除原有图片保存新图片
                $merchant = new MerchantModel();
                if(!empty($conId)){
                    $bra = $merchant->getOneMerchant($conId);
                    $oldurl=$bra['logo'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'logo' => $pic['data'],
                    'id' => $conId,
                );
                $flag = $merchant->editMerchant($date);
            }
            //$flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
 * 我的 上传图片--工单
 */
    public function uplaodWorkPic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=false,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            return $pic['data'];

        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }

    }
    /**
     * 我的 协议
     */
    public function getContract(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $map[] = ['title','=',$conName];
            $contract = new ContractModel();
            $arr = $contract->where($map)->find();
            if(!empty($arr)){
                return json(['code' => 200, 'data' => $arr, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '暂未找到相关协议']);

        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }

    }/**
 * 我的 企业信息--上传封头图
 */
    public function uplaodPic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,750, 562);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='company'){
                //删除原有图片保存新图片
                $merchant = new MerchantModel();
                if(!empty($conId)){
                    $bra = $merchant->getOneMerchant($conId);
                    $oldurl=$bra['pic'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'pic' => $pic['data'],
                    'id' => $conId,
                );
                $flag = $merchant->editMerchant($date);
            }
            $flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 企业信息--上传图片
     */
    public function uplaodMerchantPic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,750, 562);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='merchant'){
                $merchant = new MerchantPicsModel();
                //保存当前数据对象
                $date = array(
                    'pic' => $pic['data'],
                    'merchant_id' => $conId,
                    'member_id' => $userId,
                    'sort' => time(),
                );
                $flag = $merchant->insertMerchantPics($date);
            }
            $flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * 我的 企业信息--删除图片
     */
    public function delMerchantPics()
    {
        $id = input('param.id');
        $merchant = new MerchantPicsModel();
        if(!empty($id)){
            $bra = $merchant->getOneMerchantPics($id);
            $oldurl=$bra['pic'];
            @unlink('static'.$oldurl);
        }
        $flag = $merchant->where('id', $id)->delete();

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * 我的 企业信息--获取图片
     */
    public function getMerchantPics()
    {
        $merchant_id = input('param.merchant_id');
        $merchant = new MerchantPicsModel();
        $data = $merchant->where('merchant_id', $merchant_id)->order('sort')->select();
        if(!empty($flag)){
            $data['pic'] = Config::get('app.baseUrl').$data['pic'];
        }
        return json(['code' => 200, 'data' => $data, 'msg' => '']);
    }
    /**
     * 我的 企业信息--图片向上
     */
    public function siteMerchantPics()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $picsId = input('param.id');
        $merchant_id = input('param.merchant_id');
        $type = input('param.type');
        try {
            AppId::checkAppID($AppId);
            $this->isPositiveInt($userId);
            $userInfo = User::checkUserID($userId);
            if($userInfo === false ){
                $this->res([],'用户信息丢失，请重新登录',100);
            }
            //获取商户图片
            $merchant = new MerchantPicsModel();
            $data = $merchant->where('merchant_id', $merchant_id)->order('sort')->select();
            $count = $merchant->where('merchant_id', $merchant_id)->count();
            $ar= 999;
            //找到当前图片位置
            if(!empty($data)) {
                foreach ($data as $key=>$v) {
                    if($v['id'] == $picsId) {
                        $ar = $key;
                    }
                }
                $data["a"]=$ar;
                $downa = 0;
                $upa =0;

                if($ar != 999){
                    if ($type == 'up') {
                        if($ar == 0){
                            return json(['code' => 100, 'data' => $data, 'msg' => '已经是第一个，不能向前操作']);
                        }else{
                            $downa = $ar-1;
                            $aSort = $data[$ar]['sort'];
                            $downaSort = $data[$downa]['sort'];
                            $merchant->where('id', '=', $data[$downa]['id'])->setField(['sort' => $aSort]);
                            $merchant->where('id', '=', $data[$ar]['id'])->setField(['sort' => $downaSort]);
                            return json(['code' => 200, 'data' => $data, 'msg' => '成功']);
                        }
                    } elseif ($type == 'down') {
                        if($ar == $count - 1){
                            return json(['code' => 100, 'data' => $data, 'msg' => '已经是最后一个，不能向后操作']);
                        }else{
                            $upa = $ar+1;
                            $aSort = $data[$ar]['sort'];
                            $upaSort = $data[$upa]['sort'];
                            $merchant->where('id', '=', $data[$upa]['id'])->setField(['sort' => $aSort]);
                            $merchant->where('id', '=', $data[$ar]['id'])->setField(['sort' => $upaSort]);
                            return json(['code' => 200, 'data' => $data, 'msg' => '成功']);
                        }
                    }else{
                        return json(['code' => 101, 'data' => $data, 'msg' => '类型异常']);
                    }
                }else{
                    return json(['code' => 101, 'data' => $data, 'msg' => '数据异常']);
                }
            }else{
                return json(['code' => 100, 'data' => '', 'msg' => '数据异常']);
            }
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
}