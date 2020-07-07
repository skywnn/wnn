<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:17
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class GoodsModel extends BaseModel
{
    protected $name = 'goods';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGoodsByWhere 根据搜索条件获取房源列表信息]
     */
    public function getGoodsByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.price,r.favorite_num,r.address,r.views,r.add_like,r.is_rec,r.source,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('goods_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getGoodsInfo 获取房源详细信息]
     */
    public function getGoodsInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.price,r.favorite_num,r.address,r.keywords,r.content,r.views,r.add_like,r.is_rec,r.source,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('goods_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }
}