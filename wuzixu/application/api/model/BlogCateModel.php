<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 11:05
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class BlogCateModel extends BaseModel
{
    protected $name = 'blog_cate';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}