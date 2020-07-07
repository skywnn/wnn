<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 16:02
 */

namespace app\api\controller\v1;

use app\admin\controller\v1\Contract;
use app\admin\model\ContractModel;
use app\api\controller\Base;
use app\lib\exception\ApiErrorException;
use app\api\model\MemberModel;
use app\api\service\Member as MemberService;
use app\api\model\FavoriteModel;
use app\api\model\LikeModel;
use app\api\model\NoticeModel;
use app\api\model\OpinionModel;
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
                    $arr = substr($data['icon'],0,3);
                    if($arr != 'htt'){
                        $data['icon'] = Config::get('app.baseUrl').$data['icon'];
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
            $date['phone'] = input('param.phone');
            $date['weixin'] = input('param.weixin');
            $date['email'] = input('param.email');
            $date['desc_words'] = input('param.desc_words');
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
            if($conName =='icon'){
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
            $map[] = ['member_id','=',$userId];
            $favorite = new FavoriteModel();
            $lists = $favorite->where($map)->field('id,favorite_id,member_id,create_time')->order('create_time desc')->select();
            if(!empty($lists)){
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
     * [获取我的点赞]
     */
    public function getLike(){
        $userId = input('param.token');
        $AppId = input('param.app_id');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //收藏夹中收藏列表
            $map[] = ['member_id','=',$userId];
            $like = new LikeModel();
            $lists = $like->where($map)->field('id,like_id,member_id,create_time')->order('create_time desc')->select();
            if(!empty($lists)){
                foreach ($lists as &$v){
                    $arra = Db::name('product')->field('id,title,pic,m_price')->where('id', '=', $v['like_id'])->find();
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
            foreach($data as $k=>&$v){
                if(!empty($v['pic'])){
                    $v['pic'] = Config::get('app.baseUrl').$v['pic'];
                }
            }
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 我的 数据
     */
    public function getNum(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取消息列表

            $map[] = ['member_id','=',$userId];
            $favorite = new FavoriteModel();
            $data['favorite_num'] = $favorite->where($map)->count();
            $like = new LikeModel();
            $data['like_num'] = $like->where($map)->count();
            $member = new MemberModel();
            $arra = $member->where('id','=',$userId)->find();
            $data['share_num'] = $arra['zhi_num'];
            return json(['code' => 200, 'data' => $data, 'msg' => '']);
        }catch (ApiErrorException $e){
            $res = $e->toArray();
            $this->push($res);
        }
    }
    /**
     * 我的 消息详情
     */
    public function getNoticeDetail(){
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $noticeId = input('param.notice_id');
        try{
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            $notice = new NoticeModel();
            $data = $notice->getOneNotice($noticeId);
            if(!empty($data)){
                $data['pic'] = Config::get('app.baseUrl').$data['pic'];
                $stra = '"/upload/ueditor/';
                $strb = "\"".Config::get('app.ueditorUrl')."/upload/ueditor/";
                if(!empty($data['content'])){
                    if (strpos($data['content'], $stra) !== false) {
                        $data['content'] = str_replace($stra,$strb,$data['content']);
                    }
                }
            }else{
                return json(['code' => 100, 'data' => [], 'msg' => '参数异常，请确认！']);
            }
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
     * [意见与建议列表]
     */
    public function opinionList()
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
            $map[] = ['member_id','=',$userId];
            $map[] = ['is_delete','=',0];

            $opinion = new OpinionModel();
            $list = $opinion->where($map)->order('create_time')->select();
            return json(['code' => 200, 'data' => $list, 'msg' => '']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [意见与建议列表]
     */
    public function getOneOpinion()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $opinionId = input('param.opinion_id');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //修改资料
            $map[] = ['member_id','=',$userId];
            $map[] = ['is_delete','=',0];

            $opinion = new OpinionModel();
            $opinionDate = $opinion->getOneOpinion($opinionId);
            return json(['code' => 200, 'data' => $opinionDate, 'msg' => '']);
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }
    /**
     * [关于我们/协议]
     */
    public function setAbout()
    {
        $AppId = input('param.app_id');
        $userId = input('param.token');
        $conName = input('param.con_name');
        try {
            //验证用户合法性
            $userInfo = MemberService::checkUserID($userId,$AppId);
            if($userInfo === false ){
                return json(['code' => 100, 'data' => [], 'msg' => '用户信息丢失，请重新登录']);
            }
            //获取数据
            if($conName =='关于我们'){
                $map[] = ['title','=','关于我们'];
            }elseif ($conName =='用户协议'){
                $map[] = ['title','=','用户信息管理协议'];
            }
            $contact = new ContractModel();
            $data = $contact->where($map)->find();
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
        } catch (ApiErrorException $e) {
            $res = $e->toArray();
            return $this->push($res);
        }
    }

}