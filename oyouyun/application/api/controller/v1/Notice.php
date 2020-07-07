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
use app\admin\model\NoticeModel;
use think\facade\Config;
use think\db;
class Notice extends Base
{
    /**
     * 消息 获取系统消息数据
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
            $data = $notice->order('create_time desc')->field('id,title,create_time')->limit(18)->where($map)->select();
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
}