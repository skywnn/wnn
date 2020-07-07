<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/4/20
 * Time: 11:31
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\Db;
class UserWaterModel extends BaseModel
{
    protected $name = 'user_water';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [insertBlogDay 添加分类]
     */
    public function insertUserWater($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '添加成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '添加失败'];
        }
    }

    /**
     * [editBlogDay 编辑分类]
     * @author
     */
    public function editUserWater($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '编辑失败'];
        }
    }
}