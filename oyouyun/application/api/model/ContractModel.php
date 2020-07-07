<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 16:45
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class ContractModel extends BaseModel
{
    protected $name = 'contract';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}