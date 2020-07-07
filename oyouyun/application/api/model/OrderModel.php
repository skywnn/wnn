<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/21
 * Time: 11:06
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class OrderModel extends BaseModel
{
    protected $name = 'order';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

}