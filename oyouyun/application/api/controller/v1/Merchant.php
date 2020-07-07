<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:11
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\service\AppId;
use app\api\service\User;
use app\api\model\MerchantModel;
use app\api\model\MerchantUserModel;
use think\facade\Config;
use think\db;
class Merchant extends Base
{
    /**
     * 园区企业 企业列表
     */
    public function getAllMerchantList(){
        $AppId = input('param.app_id');
        try{
            AppId::checkAppID($AppId);
            $company_id = $this->getCompanyId($AppId);
            $map[] = ['r.company_id','=',$company_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $merchant = new MerchantModel();
            $od = "r.create_time desc";
            $data = $merchant->getMerchantByWhere($map,18,$od);
            foreach($data as $k=>&$v){
                $v['logo'] = Config::get('app.baseUrl').$v['logo'];
            }
            $data =$data->toArray();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 园区企业 企业详情
     */
    public function getMerchantDetail(){
        $AppID = input('param.app_id');
        $merchant_id = input('param.merchant_id');
        try{
            AppId::checkAppID($AppID);

            $map[] = ['r.id','=',$merchant_id];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $merchant = new MerchantModel();
            $data = $merchant->getMerchantInfo($map);
            if(!empty($data)){
                $data['logo'] = Config::get('app.baseUrl').$data['logo'];
                $merchant->where('id','=',$merchant_id)->setInc('views',1);
            } else {
                $this->getFail([],'当前企业不存在，请确认！',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 园区企业 企业详情
     */
    public function getMerchantAllDetail(){
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

            $map[] = ['r.id','=',$merUser['merchant_id']];
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $merchant = new MerchantModel();
            $data = $merchant->getMerchantInfo($map);
            if(!empty($data)){
                $data['logo'] = Config::get('app.baseUrl').$data['logo'];
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $merchant->where('id','=',$merUser['merchant_id'])->setInc('views',1);
            } else {
                $this->getFail([],'当前企业不存在，请确认！',100);
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
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
            $date['user_id'] =  $userId;
            if(!empty(input('param.abbreviation'))){
                $date['abbreviation'] =  input('param.abbreviation');
            }
            if(!empty(input('param.address'))){
                $date['address'] =  input('param.address');
            }
            if(!empty(input('param.email'))){
                $date['email'] =  input('param.email');
            }
            if(!empty(input('param.tel'))){
                $date['tel'] =  input('param.tel');
            }
            if(!empty(input('param.summary'))){
                $date['summary'] =  input('param.summary');
            }
            if(!empty(input('param.expert'))){
                $date['expert'] =  input('param.expert');
            }
            if(!empty(input('param.require'))){
                $date['require'] =  input('param.require');
            }

            $work = new MerchantModel();
            $flag = $work->save($date,['id'=> $merUser['merchant_id']]);
            if($flag == false ){
                return json(['code' => 101, 'data' => [], 'msg' => '信息保存失败']);
            }
            return json(['code' => 200, 'data' => [], 'msg' => '信息保存成功']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}