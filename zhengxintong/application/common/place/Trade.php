<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/24
 * Time: 12:00
 */

namespace app\common\place;

use think\Db;
class Trade
{
    /**
     * province 一级行业
     */
    public function oneTrade(){
        $trade_parent = Db::name('trade')
            ->where('parent_id',0)
            ->field('id,parent_id,name')
            ->order('id asc')
            ->select();
        return $trade_parent;
    }
    /**
     * place 二级行业
     */
    public function trade(){
        $id = input('param.id');
        $trade = Db::name('trade')
            ->where('parent_id',$id)
            ->field('id,parent_id,name')
            ->order('id asc')
            ->select();
        return ['code'=>200,'msg'=>$trade];
    }
}