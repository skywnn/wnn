<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/11
 * Time: 10:16
 */

namespace app\common\chuanglan;
use org\ChuanglanSmsApi;
use think\db;
header('Access-Control-Allow-Origin: *');   // 响应类型，解决前后端的分离的跨域问题
header('Access-Control-Allow-Methods: *');   // 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class Sms
{
    private $Msg=array();//短信信息

    /**
     * 发送验证码---客户端/PC端
     */
    public function SendSign($phone,$type){
        if (!$this->isPhone($phone)) {
            return json(['code' => 100, 'data' => [], 'msg' => '手机号码格式错误！']);
        }
        //创建验证码信息
        $this->Msg['phone'] = $phone;
        $this->Msg['code'] = rand(100000,999999);
        $this->Msg['msg_type'] = $type;
        $this->Msg['msg']='【泓沄科技】您的验证码为：'. $this->Msg['code'];
        //验证码频次限制
        if (!$this->SingLimit()) {
            return json(['code' => 100, 'data' => [], 'msg' => '验证码获取太频繁，请稍后再试！']);
        }
        //发送短信
        $res= $this->sendPhoneMsg($phone, $this->Msg['msg']);

        if($res['code']!==0){
            return json(['code' => 100, 'data' => [], 'msg' => $res['msg']]);
        }else{
            return json(['code' => 200, 'data' => [], 'msg' => '发送成功']);
        }

    }

    /**
     * 发送短信通知
     */
    public function sendMsg($phone,$type,$msg){

        $this->Msg['phone'] = $phone;
        $this->Msg['msg_type'] = $type;
        $this->Msg['msg']= '【泓沄科技】'.$msg;

        //发送短信
        $res= $this->sendPhoneMsg($phone, $this->Msg['msg']);

        if($res['code']!==0){
            return json(['code' => 100, 'data' => [], 'msg' => $res['msg']]);
        }else{
            return json(['code' => 200, 'data' => [], 'msg' => '发送成功']);
        }

    }
    /**
     * 获取短信验证码
     */
    public function getMsg($phone){
        if (!$this->isPhone($phone)) {
            return json(['code' => 100, 'data' => [], 'msg' => '手机号码格式错误！']);
        }
        $where[]=['phone','=',$phone];
        $where[]=['msg_type','=','验证码'];
        $where[]=['status','=',0];
        $where[]=['add_time','egt',time()-600];//验证码有效期10分钟
        $msg=Db::table('mobile_msg_log')->where($where)->order('id desc')->getField('code');
        return $msg;
    }

    /**
     * 保存短信发送记录
     */
    private function saveLog(){
        $this->Msg['add_time'] = time();
        Db::table('mobile_msg_log')->save($this->Msg);
    }

    /**
     * 验证码频次限制
     */
    private function singLimit()
    {
        $minutelimit = 1;                     //1分钟内允许的短信条数
        $daylimit = 60;                       //每天允许的验证短信接收条数
        $where[]=['phone','=',$this->Msg['phone']];
        $where[]=['msg_type','=','验证码'];
        $where[]=['status','=',0];
        $where[]=['add_time','egt',time() - 60]; //1分钟最大验证短信条数限制
        $count = Db::table('mobile_msg_log')->where($where)->count();
        if($count >= $minutelimit){
            return false;
        }
        //1天最大验证短信条数限制
        $where[]=['add_time','egt',time() - 3600*24]; //1分钟最大验证短信条数限制
        $count = Db::table('mobile_msg_log')->where($where)->count();
        if ($count >= $daylimit) {
            return false;
        }
        return true;
    }
    /**
     * 验证是否是手机号
     */
    private function isPhone($phone)
    {
        return preg_match("/^1[3456789]{1}\d{9}$/", $phone);
    }
    /**
     * 向用户发送短信
     */
    private function sendPhoneMsg($phones, $msg){
        if(!$msg){
            return json(['code' => 100, 'data' => [], 'msg' => '发送内容不可为空！']);
        }
        //判断是否是批量发送
        if(count(explode(',', $phones))==1){
            if(!$this->isPhone($phones)){
                return json(['code' => 100, 'data' => [], 'msg' => '手机号码格式错误！']);
            }
        }
        //发送短信
        $api = new ChuanglanSmsApi();
        $res=$api->sendSMS($phones, $msg);
        list($outtime,$status)=explode(',',$res);

        $this->Msg['back_text']= $res;
        $this->Msg['status'] = is_null($status)?'-1': $status;
        $this->SaveLog();

        if (!$res || !$outtime) {
            return json(['code' => 100, 'data' => [], 'msg' => '短信获取失败！']);
        }else{
            return json(['code' => 200, 'data' => [], 'msg' => '发送成功']);
        }
    }
}