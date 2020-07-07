<?php


namespace app\home\model;


class LawModel extends BaseModel
{
    protected $name = 'law';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取法律列表信息]
     */
    public function getLawByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有法律数据数量]
     */
    public function getCountLaw($map)
    {
        return $this->where($map)->count();
    }

    /**
     * [getOneArticle 根据法律id获取一条信息]
     */
    public function getOneLaw($id)
    {
        return $this->where('id', $id)->find();
    }
}