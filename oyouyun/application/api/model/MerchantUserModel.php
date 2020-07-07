<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/16
 * Time: 22:49
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class MerchantUserModel extends BaseModel
{
    protected $name = 'merchant_user';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

}