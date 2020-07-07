<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/16
 * Time: 11:51
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class NoticeModel extends BaseModel
{
    protected $name = 'notice';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getActivityByWhere 根据搜索条件获取活动列表信息]
     */
    public function getNotice($id)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.views,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('notice_cate rc', 'r.cate_id = rc.id')
            ->where('r.id','=',$id)
            ->find();
    }
}