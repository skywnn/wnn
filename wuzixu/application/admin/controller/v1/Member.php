<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/5
 * Time: 10:11
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\MemberModel;
use app\admin\model\SponsorModel;
class Member extends Base
{
    /**
     * [index 用户列表]
     */
    public function memberIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['username','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $role = new MemberModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $role ->getCountMember($map);  //总数据数目
            $lists = $role ->getMemberByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("member_index");
    }

    /**
     * [edit 编辑菜单]
     */
    public function editMember()
    {
        $menu = new MemberModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $menu->editMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $oneMenu = $menu->getOneMember($id);
        $this->assign('item',$oneMenu);
        return $this->fetch('edit_member');
    }

    /**
     * [认证管理]
     */
    public function realMember(){
        if(request()->isAjax ()){
            extract(input());
            $map = array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['username','like','%'.$keyword.'%'];
            }
            $map[] = ['is_auth','in','1,2,3'];
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $member = new MemberModel();
            $Nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;// 获取总条数;
            $count = $member ->getCountMember($map);  //总数据数目
            $lists = $member ->getMemberByWhere($map, $Nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("real_member");
    }



    /**
     * [index 认证审核处理]
     */
    public function realMemberInfo()
    {
        $member = new MemberModel();
        $id = input('param.id');
        $data = $member->getOneMember($id);
        $this->assign('item',$data);
        return $this->fetch('real_member_info');
    }
    public function adoptMember(){
        $member = new MemberModel();
        $id = input('param.id');
        $member->where('id',$id)->setField(['is_auth'=>1]);
        $sponsor = new SponsorModel();
        $sponsor->where('member_id',$id)->setField(['is_check'=>1]);
        return json(['code' => 200, 'data' => [], 'msg' => '审核通过']);
    }
    public function rejectMember(){
        $member = new MemberModel();
        $id = input('param.id');
        $member->where('id',$id)->setField(['is_auth'=>3]);
        return json(['code' => 200, 'data' => [], 'msg' => '驳回成功']);
    }
}