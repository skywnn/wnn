<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/4
 * Time: 23:50
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\Db;
class CompanyModel extends BaseModel
{
    protected $name = 'company';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}