<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/6
 * Time: 23:42
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ContractCateModel;
use app\admin\model\ContractModel;
use think\db;
class Contract extends Base
{
//*********************************************协议管理*********************************************//
    /**
     * [contractIndex 协议列表]
     */
    public function contractIndex()
    {
        if (request()->isAjax()) {
            extract(input());
            $map = [];
            if (isset($keyword) && $keyword != "") {
                $map[] = ['title', 'like', '%' . $keyword . '%'];
            }
            $field = input('field');//字段
            $order = input('order');//排序方式
            if ($field && $order) {
                $od = $field . " " . $order;
            } else {
                $od = "create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page') : 1;
            $limits = input("limit") ? input("limit") : 10;
            $count = Db::name('contract')->where($map)->count();//计算总页面
            $contract = new ContractModel();
            $lists = $contract->getContractByWhere($map, $Nowpage, $limits, $od);
            return json(['code' => 220, 'msg' => '', 'count' => $count, 'data' => $lists]);
        }
        return $this->fetch('contract_index');
    }

    /**
     * [add_contract 添加协议]
     */
    public function addContract()
    {
        if (request()->isPost()) {
            extract(input());
            $param = input('post.');
            $contract = new ContractModel();
            $flag = $contract->insertContract($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_contract');
    }

    /**
     * [edit_contract 编辑协议]
     */
    public function editContract()
    {
        $contract = new ContractModel();
        if (request()->isPost()) {
            $param = input('post.');
            $flag = $contract->editContract($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $contract->getOneContract($id);
        $this->assign('item', $data);
        return $this->fetch('edit_contract');
    }

    /**
     * [del_contract 删除协议]
     */
    public function delContract()
    {
        $id = input('param.id');
        $contract = new ContractModel();
        $flag = $contract->delContract($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [contract_state 协议状态]
     */
    public function contractState()
    {
        extract(input());
        $contract = new ContractModel();
        $flag = $contract->contractState($id, $num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}
