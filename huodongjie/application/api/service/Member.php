<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:56
 */

namespace app\api\service;

use app\api\model\MemberModel;
use app\lib\exception\ApiErrorException;
use app\api\model\SponsorModel;
class Member
{
    /**
     * 验证用户ID是否有效并存在
     */
    public static  function checkUserId($uid,$appId){

        $map[] = ['id','=',$uid];
        $map[] = ['app_id','=',$appId];
        $member = new MemberModel();
        $memberData = $member->field('id')->where($map)->find();
        if(empty($memberData)){
            return false;
        }
        return true;
    }
    /**
     * 根据用户ID活动关联主办方的id
     */
    public static  function getSponsorId($uid){

        $map[] = ['member_id','=',$uid];
        $sponsor = new SponsorModel();
        $sponsorData = $sponsor->where($map)->find();
        if(!empty($sponsorData)){
            return $sponsorData['id'];
        }
        return $sponsorData;
    }
}