<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/29
 * Time: 23:18
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class BlogDayModel extends BaseModel
{
    protected $name = 'blog_day';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}