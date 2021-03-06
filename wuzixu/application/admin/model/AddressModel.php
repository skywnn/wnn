<?php


namespace app\admin\model;


use think\Db;

class AddressModel extends BaseModel
{

    protected $name = 'genealogy_address';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getGenealogyByWhere 根据搜索条件获取家族列表信息]
     */
    public function getAddressByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }

    /**
     * [getCountArticle 根据条件获取所有数据数量]
     */
    public function getCountAddress($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertArticle 添加文章]
     */
    public function insertAddress($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '文章添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'文章添加失败'];
        }
    }

    /**
     * [updateArticle 编辑文章]
     */
    public function editAddress($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '文章编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [getOneArticle 根据文章id获取一条信息]
     */
    public function getOneAddress($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delArticle 删除文章]
     */
    public function delAddress($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '文章删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '文章删除失败'];
        }
    }
    /**
     * [articleState 文章状态]
     */
    public function addressState($id,$num){
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