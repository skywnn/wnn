<?php


namespace app\admin\model;


class HelpModel extends BaseModel
{
    protected $name = 'service_work';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getHelpByWhere 根据条件获取常见问题列表信息]
     */
    public function getHelpByWhere($map, $Nowpage, $limits, $od)
    {
        return $this->where($map)->page($Nowpage, $limits)->order($od)->select();
    }

    /**
     * [getCountHelp 根据条件获取常见问题总数]
     */
    public function getCountHelp($map){
        return $this->where($map)->count();
    }
}