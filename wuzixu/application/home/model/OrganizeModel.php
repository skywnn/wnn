<?php


namespace app\home\model;


class OrganizeModel extends BaseModel
{
    protected $name = 'organize';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取组织列表信息]
     */
    public function getOrganizeByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有组织数据数量]
     */
    public function getCountOrganize($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [getOneArticle 根据组织id获取一条信息]
     */
    public function getOneOrganize($id)
    {
        return $this->where('id', $id)->find();
    }
}