<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\MyinfoModel;

class Myinfo extends Base
{

    /**
     * [nodeIndex 权限节点列表]
     */
    public function myinfoIndex(){
        $m = new MyinfoModel();
        $flag = $m->getOneMyinfo(1);
        $this->assign('my',$flag);
        return $this->fetch('myinfo_index');
    }

    /**
     * [editCompanyContacts 编辑公司联系人信息]
     */
    public function editMyinfo()
    {
        $company = new MyinfoModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editMyinfo($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$company->getOneMyinfo($id));
        return $this->fetch("edit_myinfo");
    }


}