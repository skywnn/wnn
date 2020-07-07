<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/5/27
 * Time: 11:11
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\OpinionModel;
class Opinion extends Base
{
    /**
     * [index_opinion 分类列表]
     */
    public function opinionIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $opinion = new OpinionModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $opinion->getCountOpinion($map);//计算总条数
            $lists = $opinion->getOpinionByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('opinion_index');
    }
    /**
     * [edit_opinion 编辑分类]
     */
    public function editOpinion()
    {
        $opinion = new OpinionModel();
        $id = input('param.id');
        $this->assign('item',$opinion->getOneOpinion($id));
        return $this->fetch();
    }

    /**
     * [del_opinion 删除分类]
     */
    public function delOpinion()
    {
        $id = input('param.id');
        $opinion = new OpinionModel();
        $flag = $opinion->delOpinion($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [oOpinion_state 分类状态]
     */
    public function opinionState()
    {
        extract(input());
        $opinion = new OpinionModel();
        $flag = $opinion->opinionState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}