<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/25
 * Time: 11:26
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class ProductModel extends BaseModel
{
    protected $name = 'product';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getProductByWhere 根据搜索条件获取企业服务列表信息]
     */
    public function getProductByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.thum_pic,r.pic,r.keywords,r.like_num,r.favorite_num,r.content,r.views,r.is_rec,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('product_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountProduct 根据条件获取所有数据数量]
     */
    public function getCountProduct($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertProduct 添加企业服务]
     */
    public function insertProduct($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'添加失败'];
        }
    }

    /**
     * [updateProduct 编辑企业服务]
     */
    public function editProduct($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑失败'];
        }
    }

    /**
     * [getOneProduct 根据企业服务id获取一条信息]
     */
    public function getOneProduct($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delProduct 删除企业服务]
     */
    public function delProduct($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '删除失败'];
        }
    }

    /**
     * [productState 企业服务状态]
     */
    public function productState($id,$num){
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