<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 15:55
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
use think\Exception;

class ProductDocModel extends BaseModel
{
    protected $name = 'product_doc';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getProductDocByWhere 根据搜索条件获取企业服务列表信息]
     */
    public function getProductDocByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.keywords,r.content,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,rc.name')
            ->join('product_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountProductDoc 根据条件获取所有数据数量]
     */
    public function getCountProductDoc($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertProductDoc 添加企业服务]
     */
    public function insertProductDoc($param)
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
     * [updateProductDoc 编辑企业服务]
     */
    public function editProductDoc($param)
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
     * [getOneProductDoc 根据企业服务id获取一条信息]
     */
    public function getOneProductDoc($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delProductDoc 删除企业服务]
     */
    public function delProductDoc($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '企业服务删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '企业服务删除失败'];
        }
    }

    /**
     * [productDocState 企业服务状态]
     */
    public function productDocState($id,$num){
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