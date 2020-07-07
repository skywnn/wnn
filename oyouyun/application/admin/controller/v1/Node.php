<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/20
 * Time: 16:46
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\NodeModel;
use think\db;
class Node extends Base
{
    /**
     * [nodeIndex 权限节点列表]
     */
    public function nodeIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['company_id','=',session('company_id')];
            $map[] = ['parent_id','=',0];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $node = new NodeModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $node ->getCountNode($map);  //总数据数目
            $lists = $node ->getNodeByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("node_index");
    }
    /**
     * [add 添加权限节点]
     */
    public function addNode()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $node = new NodeModel();
            $flag = $node->insertNode($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_node');
    }

    /**
     * [edit 编辑权限节点]
     */
    public function editNode()
    {
        $node = new NodeModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $node->editNode($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneNode = $node->getOneNode($id);
        $this->assign('item',$oneNode);
        return $this->fetch('edit_node');
    }
    /**
     * [delete 删除权限节点]
     */
    public function delNode()
    {
        $id = input('param.id');
        $node = new NodeModel();
        $flag = $node->delNode($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [node_state 权限节点状态]
     */
    public function nodeState()
    {
        extract(input());
        $node = new NodeModel();
        $flag = $node->nodeState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [subnodeIndex 子权限节点列表]
     */
    public function subnodeIndex(){
        $id = input('param.id');
        $node = new NodeModel();
        $oneNode = $node->getOneNode($id);
        $oneNode['level'] += 1;
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $map[] = ['parent_id','=',$id];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="sort";
            }
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $node ->getCountNode($map);  //总数据数目
            $lists = $node ->getNodeByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('item',$oneNode);
        return $this->fetch("subnode_index");
    }
    /**
     * [addSubnode 添加子权限节点]
     */
    public function addSubnode()
    {
        $node = new NodeModel();
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $flag = $node->insertNode($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $level = input('param.level');
        $oneNode = $node->getOneNode($id);
        $oneNode['level'] = $level;
        $this->assign('item',$oneNode);
        return $this->fetch('add_subnode');
    }
}