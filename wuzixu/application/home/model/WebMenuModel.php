<?php


namespace app\home\model;


use think\Db;

class WebMenuModel extends BaseModel
{
    protected  $name = 'web_menu';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getAllMenu 根据条件获取所有菜单信息]
     */
    public function getAllWebMenu($where)
    {
        return $this->where($where)->order('sort')->select();
    }

    public function getWebMenuInfo($id)
    {
        return $this->where('id', $id)->find();
    }

}