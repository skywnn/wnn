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
use app\api\model\MemberModel;
use app\api\service\Member as MemberService;
use app\api\model\FavoriteModel;
use app\api\model\DynamicModel;
use app\api\model\NoticeModel;
use app\api\model\OpinionModel;
use app\api\model\SponsorModel;
use app\common\upload\Uplaod;
use think\facade\Config;
use think\db;
class Member extends Base
{
    /**
     * [获取个人信息]
     */
    public function getMemberInfo(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取圈子列表
            $map[] = ['id','=',$userId];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $member = new MemberModel();
            $data = $member->where($map)->find();
            if(!empty($data)){
                if(!empty($data['icon'])){
                    $data['icon'] = Config::get('app.baseUrl').$data['icon'];
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * [个人资料修改]
     */
    public function setMemberInfo()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['real_name'] = input('param.real_name');
            $date['sex'] = input('param.sex');
            $date['phone'] = input('param.phone');
            $date['weixin'] = input('param.weixin');
            $date['email'] = input('param.email');
            $date['province'] = input('param.province');
            $date['city'] = input('param.city');
            $date['district'] = input('param.district');
            $date['birthday'] = input('param.birthday');
            $date['id'] = $userId;
            $user = new MemberModel();
            $flag = $user->editMember($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }

    /**
     * [个人头像修改]
     */
    public function uplaodIcon(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='company'){
                //删除原有图片保存新图片
                $member = new MemberModel();
                if(!empty($userId)){
                    $bra = $member->getOneMember($userId);
                    $oldurl=$bra['icon'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'icon' => $pic['data'],
                    'id' => $userId,
                );
                $flag = $member->editMember($date);
            }
            $flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }

    /**
     * [获取我的收藏]
     */
    public function getFavorite(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //收藏夹中收藏列表
            $map[] = ['app_id','=',$AppId];
            $map[] = ['user_id','=',$userId];
            $favorite = new FavoriteModel();
            $lists = $favorite->where($map)->field('id,favorite_id,user_id,create_time')->order('create_time desc')->select();
            if(!empty($lists)){
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
     * [获取我的动态]
     */
    public function getDynamic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取自己发布的动态
            $map[] = ['member_id','=',$userId];
            $map[] = ['status','=',1];
            $map[] = ['is_delete','=',0];
            $dynamic = new DynamicModel();
            $data = $dynamic->where($map)->order('create_time desc')->select();
            if(!empty($data)){
                foreach ($data as &$v){
                    if(!empty($v['pic'])){
                        $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                    }
                }
                return json(['code' => 200, 'data' => $data, 'msg' => '']);
            }
            return json(['code' => 100, 'data' => [], 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * [查出我报名的活动]
     */


    /**
     * [获取消息]
     */
    public function getNotice(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取消息列表
            $map[] = ['r.status','=',1];
            $map[] = ['r.is_delete','=',0];
            $Nowpage = 1;
            $limits = 10;
            $od = 'r.create_time desc';
            $notice = new NoticeModel();
            $data = $notice->getNoticeByWhere($map, $Nowpage, $limits,$od);
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * [提交意见与建议]
     */
    public function setOpinion()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['content'] = input('param.content');
            $date['member_id'] = $userId;
            $opinion = new OpinionModel();
            $flag = $opinion->insertOpinion($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }

    /**
     * [主办方认证]
     */

    /**
     * [主办方资料修改完善]
     */
    public function setSponsorInfo()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $date['name'] = input('param.name');
            $date['short_name'] = input('param.short_name');
            $date['summary'] = input('param.summary');
            $date['tel'] = input('param.tel');
            $date['email'] = input('param.email');
            $date['id'] = MemberService::getSponsorId($userId);
            $sponsor = new SponsorModel();
            $flag = $sponsor->editSponsor($date);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }

    /**
     * [主办方LOGO]
     */
    public function uplaodLogo(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='company'){
                //删除原有图片保存新图片
                $sponsor = new SponsorModel();
                if(!empty($conId)){
                    $bra = $sponsor->getOneSponsor($conId);
                    $oldurl=$bra['logo'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'logo' => $pic['data'],
                    'id' => $conId,
                );
                $flag = $sponsor->editSponsor($date);
            }
            $flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [主办方封面图]
     */
    public function uplaodPic(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        $conId = input('param.con_id');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $pathNewName = $conName.'_pic';
            $uplaodData = new Uplaod();
            $pic = $uplaodData->baseUplaod('file',$pathNewName,$isThumb=true,150, 150);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            if($conName =='company'){
                //删除原有图片保存新图片
                $sponsor = new SponsorModel();
                if(!empty($conId)){
                    $bra = $sponsor->getOneSponsor($conId);
                    $oldurl=$bra['pic'];
                    @unlink('static'.$oldurl);
                }
                //保存当前数据对象
                $date = array(
                    'pic' => $pic['data'],
                    'id' => $conId,
                );
                $flag = $sponsor->editSponsor($date);
            }
            $flag['data']['newpic'] = Config::get('app.baseUrl').$pic['data'];
            return Config::get('app.baseUrl').$pic['data'];
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
}