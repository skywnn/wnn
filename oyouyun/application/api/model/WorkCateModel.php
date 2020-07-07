<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/24
 * Time: 17:04
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class WorkCateModel extends BaseModel
{
    protected $name = 'work_cate';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}