<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 11:03
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class BlogModel extends BaseModel
{
    protected $name = 'blog';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}