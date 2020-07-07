<?php


namespace app\home\model;

class ProjectModel extends BaseModel
{
    protected $name = 'project';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取地址列表信息]
     */
    public function getProjectByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有地址数据数量]
     */
    public function getCountProject($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [getOneArticle 根据地址id获取一条信息]
     */
    public function getOneProject($id)
    {
        return $this->where('id', $id)->find();
    }
}