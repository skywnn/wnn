<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/23
 * Time: 15:42
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class ProductCateModel extends BaseModel
{
    protected $name = 'product_cate';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}