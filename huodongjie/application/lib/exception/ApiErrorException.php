<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/1
 * Time: 20:43
 */

namespace app\lib\exception;

use think\Exception;
class ApiErrorException extends Exception
{
    /**
     * @return array
     */
    public function toArray(){
        $code = $this->getCode();
        $errorResult = [
            'code'  => !empty($code) ? $code : 1 ,
            'msg'   => $this->getMessage(),
            'data'  => []
        ];
        return $errorResult;
    }
}