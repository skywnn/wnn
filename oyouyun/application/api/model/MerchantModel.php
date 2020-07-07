<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/2/16
 * Time: 13:26
 */

namespace app\api\model;

use app\api\model\BaseModel;
use think\db;
class MerchantModel extends BaseModel
{
    protected $name = 'merchant';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";
    /**
     * [getCompanyByWhere 根据搜索条件获取公司列表信息]
     */
    public function getMerchantByWhere($map,$limits,$od)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.logo,r.summary,r.expert,r.require,r.address,r.email,r.tel,r.abbreviation,r.views,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('merchant_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->limit($limits)
            ->order($od)
            ->select();
    }
    /**
     * [getCompanyInfo 获取公司详细信息]
     */
    public function getMerchantInfo($map)
    {
        return $this->alias ('r')
            ->field('r.id,r.title,r.cate_id,r.logo,r.pic,r.summary,r.expert,r.require,r.address,r.email,r.tel,r.abbreviation,r.views,r.is_rec,r.create_time,r.update_time,r.status,rc.name')
            ->join('merchant_cate rc', 'r.cate_id = rc.id')
            ->where($map)
            ->find();
    }
    /**
     * [getOneMerchant 根据商户id获取一条信息]
     */
    public function getOneMerchant($id)
    {
        return $this->where('id', $id)->find();
    }
    /**
     * [updateMerchant 编辑商户]
     */
    public function editMerchant($param)
    {
        Db::startTrans();// 启动事务
        try{
            $this->allowField(true)->save($param, ['id' => $param['id']]);
            Db::commit();// 提交事务
            return ['code' => 200, 'data' => '', 'msg' => '商户编辑成功'];
        }catch( \Exception $e){
            Db::rollback();// 回滚事务
            return ['code' => 100, 'data' => '', 'msg' => '商户编辑失败'];
        }
    }
}