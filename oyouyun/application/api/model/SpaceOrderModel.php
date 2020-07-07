<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/27
 * Time: 11:14
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class SpaceOrderModel extends BaseModel
{
    protected $name = 'space_order';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}