<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 16:02
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\model\ActivityCateModel;
use app\api\model\ActivityModel;
use app\api\model\TicketModel;
use app\api\service\Member;
class Activity extends Base
{
    /**
     * [创建活动]
     */
    public function setActivity()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['phone'] = input('param.phone');
            $date['email'] = input('param.email');
            $activity = new ActivityModel();
            $flag = $activity->insertActivity($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [完善活动]
     */
    public function editActivity()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['phone'] = input('param.phone');
            $date['email'] = input('param.email');
            $activity = new ActivityModel();
            $flag = $activity->editActivity($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [创建票种]
     */
    public function setTicket()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['phone'] = input('param.phone');
            $date['email'] = input('param.email');
            $activity = new TicketModel();
            $flag = $activity->insertTicket($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [修改票种]
     */
    public function editTicket()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['phone'] = input('param.phone');
            $date['email'] = input('param.email');
            $activity = new TicketModel();
            $flag = $activity->editTicket($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [删除票种]
     */
    public function delTicket()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = Member::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['phone'] = input('param.phone');
            $date['email'] = input('param.email');
            $activity = new TicketModel();
            $flag = $activity->delTicket($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [创建报名要求]
     */
}