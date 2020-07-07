<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:33
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\SconfigModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Sconfig extends Base
{
    /**
     * [index_sconfig 站点参数列表]
     */
    public function sconfigIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的站点参数需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="id";
            }
            $sconfig = new SconfigModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $sconfig->getCountSconfig($map);//计算总条数
            $lists = $sconfig->getSconfigByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('sconfig_index');
    }

    /**
     * [add_sconfig 添加站点参数]
     */
    public function addSconfig()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $sconfig = new SconfigModel();
            $flag = $sconfig->insertSconfig($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_sconfig');
    }

    /**
     * [edit_sconfig 编辑站点参数]
     */
    public function editSconfig()
    {
        $sconfig = new SconfigModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $sconfig->editSconfig($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$sconfig->getOneSconfig($id));
        return $this->fetch();
    }

    /**
     * [del_sconfig 删除站点参数]
     */
    public function delSconfig()
    {
        $id = input('param.id');
        $sconfig = new SconfigModel();
        $flag = $sconfig->delSconfig($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [sconfig_state 站点参数状态]
     */
    public function sconfigState()
    {
        extract(input());
        $sconfig = new SconfigModel();
        $flag = $sconfig->sconfigState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [setSconfig 设置站点参数值]
     */
    public function setSconfig()
    {
        $sconfig = new SconfigModel();
        if(request()->isPost()){
            $param = input('post.');
            if($param['config']){
                foreach ($param['config'] as $k => $v){
                    $sconfig->where('id='.$k)->setField('value', $v);
                }
                return json(['code' => 200, 'data' => '', 'msg' => '参数配置保存成功']);
            }
            return json(['code' => 100, 'data' => '', 'msg' => '未选中需要赋值的参数']);
        }
        $sconfigDate = $sconfig->select();
        $this->assign('lists',$sconfigDate);
        return $this->fetch('set_sconfig');
    }
}