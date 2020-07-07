<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 10:51
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class AdverCateModel extends BaseModel
{
    protected $name = 'adver_cate';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getAdverCate 获取广告位信息]
     */
    public function getAdverCate($map)
    {
        $cate = $this->where($map)->field('id')->find();
        if($cate){
            return $cate['id'];
        }else{
            return '参数异常';
        }

    }
}