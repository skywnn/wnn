<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 18:18
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class CompanyModel extends BaseModel
{
    protected $name = 'company';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getCompanyByWhere 根据搜索条件获取公司列表信息]
     */
    public function getCompanyByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('company_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCompanyInfo 获取公司详细信息]
     */
    public function getCompanyInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.views,r.add_like,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('company_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }

}