<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 11:06
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class HelpModel extends BaseModel
{
    protected $name = 'help';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getCateByWhere 分页获取全部]
     */
    public function getHelpByWhere($map, $limits, $od)
    {
        return $this->where($map)->limit($limits)->order($od)->select();
    }
    /**
     * [getOneNotice 根据id获取一条信息]
     */
    public function getOneHelp($map)
    {
        return $this->where($map)->find();
    }
}