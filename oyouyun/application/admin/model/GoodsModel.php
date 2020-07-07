<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 11:05
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class GoodsModel extends BaseModel
{
    protected $name = 'goods';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGoodsByWhere 根据搜索条件获取商品业务列表信息]
     */
    public function getGoodsByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.favorite_num,r.keywords,r.content,r.views,r.add_like,r.is_rec,r.source,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('goods_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountGoods 根据条件获取所有数据数量]
     */
    public function getCountGoods($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertGoods 添加商品业务]
     */
    public function insertGoods($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '商品业务添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'商品业务添加失败'];
        }
    }

    /**
     * [updateGoods 编辑商品业务]
     */
    public function editGoods($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商品业务编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '商品业务编辑失败'];
        }
    }

    /**
     * [getOneGoods 根据商品业务id获取一条信息]
     */
    public function getOneGoods($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delGoods 删除商品业务]
     */
    public function delGoods($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商品业务删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '商品业务删除失败'];
        }
    }

    /**
     * [goodsState 商品业务状态]
     */
    public function goodsState($id,$num){
        if($num == 2){
            $msg = '禁用';
        }else{
            $msg = '启用';
        }
        Db::startTrans();// 启动事务
        try{
            $this->where('id',$id)->setField(['status'=>$num]);
            Db::commit();// 提交事务
            return ['code' => 200 , 'data' => '' , 'msg' => '已'.$msg];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $msg.'失败'];
        }
    }
}