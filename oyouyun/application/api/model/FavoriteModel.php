<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/28
 * Time: 11:03
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class FavoriteModel extends BaseModel
{
    protected $name = 'favorite';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}