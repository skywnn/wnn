<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/11/3
 * Time: 15:28
 */

namespace app\common\place;

use think\Db;
class Area
{
    /**
     * province 省
     */
    public function province(){
        $province = Db::name('area')
            ->where('parent_id',1)
            ->field('id,parent_id,name')
            ->order('id asc')
            ->select();
        return $province;
    }
    /**
     * place 地区三级联动
     */
    public function area(){
        $id = input('param.id');
        $area = Db::name('area')
            ->where('parent_id',$id)
            ->field('id,parent_id,name')
            ->order('id asc')
            ->select();
        return ['code'=>200,'msg'=>$area];
    }
}