<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/12
 * Time: 14:54
 */

namespace app\api\controller;

use think\Controller;
use app\lib\exception\ApiErrorException;
use think\facade\Config;
use think\db;

class Base extends Controller
{
    /**
     * 检查并获取页参数
     */
    public function checkPagingParam(){

        $listRows = input('listRows');
        $page = input('page');
        if(empty($listRows)){
            $listRows = Config::get('app.LIST_ROWS');
        }
        if(empty($page)){
            $page = Config::get('app.PAGE');
        }
        $intArr = compact('listRows','page');
        $this->isPositiveInteger($intArr);
        return $intArr;
    }
    /**
     * 根据平台应用ID获取平台公司ID
     */
    public function getCompanyId($appid){
        if(empty($appid)){
            return '参数异常';
        }
        $map[] = ['xcx_appid','=',$appid];
        $third_devconfig = Db::table('hy_third_devconfig')->where($map)->find();
        if($third_devconfig){
            return $third_devconfig['company_id'];
        } else {
            return '参数异常';
        }
    }
    /**
     * 检查参数是否为正整数
     */
    public function isPositiveInt($int,$msg='数据格式错误'){

        if( !is_numeric($int) || !is_int($int + 0 ) || ($int+0) <= 0){
            $this->getFail([],$msg);
        }
        return true;
    }
    /**
     * 验证id是否为正整数,数组格式
     */
    public function isPositiveInteger(array $intArr,$msg='参数异常'){

        if(!empty($intArr)){
            foreach ($intArr as $key => $id){
                if( !is_numeric($id) || !is_int($id + 0 ) || ($id+0) <= 0){
                    $this->getFail([],$msg);
                }
            }
        }
        return true;
    }
    /**
     * Q根据用户Id生成用户推荐码
     */
    public function setIdCode($id)
    {
        $dir = $id * 65;
        $idCode = 'HR'.time().substr($dir,0,2);
        return $idCode;
    }
    /**
     * 成功返回
     */
    public function res($arr, $msg='', $code=''){
        $info['code'] = $code;
        $info['msg'] =($msg) ? $msg :"success";
        $info['data'] =$arr;
        return json(['code' => $info['code'], 'data' => $info['data'], 'msg' => $info['msg']]);
    }
    public function getSuccess($arr, $msg=''){
        $info['code'] = 200;
        $info['msg'] =($msg) ? $msg :"success";
        $info['data'] =$arr;
        return json(['code' => 200, 'data' => $info['data'], 'msg' => $info['msg']]);
    }

    public function getFail($arr, $msg='',$code = 1){
        $info['code'] = $code;
        $info['msg']  =($msg) ? $msg :"error";
        $info['data'] =$arr;
        return json(['code' => $info['code'], 'data' => $info['data'], 'msg' => $info['msg']]);
    }
    /**
     * 返回数据 json字符串
     */
    protected function push($arr){
        return json($arr);
    }

}