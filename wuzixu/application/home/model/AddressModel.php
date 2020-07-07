<?php


namespace app\home\model;


class AddressModel extends BaseModel
{
    protected $name = 'genealogy_address';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取地址列表信息]
     */
    public function getAddressByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有地址数据数量]
     */
    public function getCountAddress($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [getOneArticle 根据地址id获取一条信息]
     */
    public function getOneAddress($id)
    {
        return $this->where('id', $id)->find();
    }
}