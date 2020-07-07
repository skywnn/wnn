<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:40
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class AdverModel extends BaseModel
{
    protected $name = 'adver';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getAdverByWhere 根据搜索条件获取广告列表信息]
     */
    public function getAdverByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.content,r.views,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('adver_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountAdver 根据条件获取所有数据数量]
     */
    public function getCountAdver($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertAdver 添加广告]
     */
    public function insertAdver($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '广告添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'广告添加失败'];
        }
    }

    /**
     * [updateAdver 编辑广告]
     */
    public function editAdver($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '广告编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '广告编辑失败'];
        }
    }

    /**
     * [getOneAdver 根据广告id获取一条信息]
     */
    public function getOneAdver($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delAdver 删除广告]
     */
    public function delAdver($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '广告删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '广告删除失败'];
        }
    }

    /**
     * [adverState 广告状态]
     */
    public function adverState($id,$num){
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