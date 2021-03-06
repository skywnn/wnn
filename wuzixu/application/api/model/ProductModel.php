<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 10:58
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class ProductModel extends BaseModel
{
    protected $name = 'product';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getProductByWhere 根据搜索条件获取房源列表信息]
     */
    public function getProductByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.like_num,r.favorite_num,r.thum_pic,r.pic,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('product_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getProductInfo 获取房源详细信息]
     */
    public function getProductInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.thum_pic,r.pic,r.like_num,r.m_price,r.favorite_num,r.v_price,r.sales,r.keywords,r.content,r.views,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('product_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }
}