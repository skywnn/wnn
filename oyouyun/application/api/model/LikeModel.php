<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/4
 * Time: 22:53
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class LikeModel extends BaseModel
{
    protected $name = 'like';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}