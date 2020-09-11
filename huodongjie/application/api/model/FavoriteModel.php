<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:47
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class FavoriteModel extends BaseModel
{
    protected $name = 'favorite';
    protected $hidden = ['update_time', 'is_delete',];

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
}