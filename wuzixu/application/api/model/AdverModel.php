<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/26
 * Time: 10:51
 */

namespace app\api\model;
use app\api\model\BaseModel;
use think\db;
class AdverModel extends BaseModel
{
    protected $name = 'adver';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getAdverByWhere 根据搜索条件获取图片信息]
     */
    public function getAdverByWhere($map,$field,$type)
    {
        if($type ==2 ){
            return $this->where($map)->order('sort')->field($field)->select();
        }elseif ($type ==1){
            return $this->where($map)->field($field)->find();
        }

    }
    /**
     * [getOneAdver 根据广告id获取一条信息]
     */
    public function getOneAdver($id)
    {
        return $this->where('id', $id)->find();
    }
}