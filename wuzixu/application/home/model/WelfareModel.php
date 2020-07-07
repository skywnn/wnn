<?php


namespace app\home\model;


class WelfareModel extends BaseModel
{
    protected $name = 'welfare';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取公益列表信息]
     */
    public function getWelfareByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有公益数据数量]
     */
    public function getCountWelfare($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [getOneArticle 根据公益id获取一条信息]
     */
    public function getOneWelfare($id)
    {
        return $this->where('id', $id)->find();
    }
}