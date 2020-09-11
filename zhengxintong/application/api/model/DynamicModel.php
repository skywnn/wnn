<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 21:26
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class DynamicModel extends BaseModel
{
    protected $name = 'dynamic';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}