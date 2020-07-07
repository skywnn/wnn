<?php


namespace app\home\model;


class GenealogyModel extends BaseModel
{
    protected $name = 'genealogy';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取家族列表信息]
     */
    public function getGenealogyByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有数据数量]
     */
    public function getCountGenealogy($map)
    {
        return $this->where($map)->count();
    }

    public function getOneGenealogy($id)
    {
        return $this->where('id', $id)->find();
    }
}