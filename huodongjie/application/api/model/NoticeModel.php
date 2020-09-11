<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/2
 * Time: 15:46
 */

namespace app\api\model;

use app\admin\model\BaseModel;
use think\Db;
class NoticeModel extends BaseModel
{
    protected $name = 'notice';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getNoticeByWhere 根据搜索条件获取通知列表信息]
     */
    public function getNoticeByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.content,r.is_rec,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('notice_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountNotice 根据条件获取所有数据数量]
     */
    public function getCountNotice($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertNotice 添加通知]
     */
    public function insertNotice($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '通知添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'通知添加失败'];
        }
    }

    /**
     * [updateNotice 编辑通知]
     */
    public function editNotice($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '通知编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '通知编辑失败'];
        }
    }

    /**
     * [getOneNotice 根据通知id获取一条信息]
     */
    public function getOneNotice($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delNotice 删除通知]
     */
    public function delNotice($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '通知删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '通知删除失败'];
        }
    }

    /**
     * [noticeState 通知状态]
     */
    public function noticeState($id,$num){
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