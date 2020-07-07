<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/8
 * Time: 16:26
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\Db;
class LikeModel extends BaseModel
{
    protected $name = 'like';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}