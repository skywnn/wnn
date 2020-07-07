<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/19
 * Time: 11:33
 */

namespace app\admin\model;

use app\admin\model\BaseModel;
use think\Db;
class HelpModel extends BaseModel
{
    protected $name = 'help';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    /**
     * [getHelpByWhere 根据搜索条件获取帮助文档列表信息]
     */
    public function getHelpByWhere($map, $Nowpage, $limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.pic,r.keywords,r.content,r.views,r.is_rec,r.editor,r.company_id,r.operate_id,r.create_time,r.update_time,r.status,r.sort,rc.name')
            ->join('help_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->page($Nowpage, $limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCountHelp 根据条件获取所有数据数量]
     */
    public function getCountHelp($map)
    {
        return $this->where($map)->count();
    }
    /**
     * [insertHelp 添加帮助文档]
     */
    public function insertHelp($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => $param, 'msg' => '帮助文档添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>'帮助文档添加失败'];
        }
    }

    /**
     * [updateHelp 编辑帮助文档]
     */
    public function editHelp($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '帮助文档编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '帮助文档编辑失败'];
        }
    }

    /**
     * [getOneHelp 根据帮助文档id获取一条信息]
     */
    public function getOneHelp($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * [delHelp 删除帮助文档]
     */
    public function delHelp($id)
    {
        Db::startTrans();// 启动事务
        try{
            $this->where('id', $id)->delete();
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '帮助文档删除成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' =>  '帮助文档删除失败'];
        }
    }

    /**
     * [helpState 帮助文档状态]
     */
    public function helpState($id,$num){
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