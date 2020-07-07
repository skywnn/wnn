<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:15
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class ActivityModel extends BaseModel
{
    protected $name = 'activity';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getActivityByWhere 根据搜索条件获取活动列表信息]
     */
    public function getActivityByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.price,r.favorite_num,r.start_time,r.end_time,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('activity_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getActivityInfo 获取活动详细信息]
     */
    public function getActivityInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.price,r.start_time,r.favorite_num,r.end_time,r.address,r.content,r.keywords,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('activity_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }
}