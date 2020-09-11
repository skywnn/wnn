<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/7
 * Time: 14:55
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class MemberModel extends BaseModel
{
    protected $name = 'member';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getUserByWhere 根据条件获取用户列表信息]
     */
    public function getMemberByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }
    /**
     * [getCountRole 根据条件获取所有的数量]
     */
    public function getCountMember($where)
    {
        return $this->where($where)->count();
    }


    /**
     * 根据搜索条件获取所有的用户数量
     */
    public function getAllMember($where)
    {
        return $this->where($where)->count();
    }
}