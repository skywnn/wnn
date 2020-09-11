<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:37
 */

namespace app\api\controller;

use think\Controller;

class Base extends Controller
{
    /**
     * 检查参数是否为正整数
     */
    public function isPositiveInt($int,$msg='数据格式错误'){

        if( !is_numeric($int) || !is_int($int + 0 ) || ($int+0) <= 0){
            return json(['code' => 100, 'data' => [], 'msg' => $msg]);
        }
        return true;
    }
    /**
     * Q返回数据 json字符串
     */
    protected function push($arr){
        return json($arr);
    }
    /**
     * Q根据用户Id生成用户推荐码
     */
    public function setIdCode($id)
    {
        $dir = $id * 17;
        $idCode = 'HD'.time().substr($dir,0,2);
        return $idCode;
    }
}