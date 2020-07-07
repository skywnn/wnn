<?php

namespace app\admin\controller\v1;
use app\admin\controller\Base;
use app\admin\model\CustomerModel;
use think\Db;

class Customer extends Base
{

    /**
     * [customer_index 客户信息]
     */
    public function customerIndex()
    {
        if (request()->isAjax()) {
            extract(input());
            $map = [];
            if (isset($keyword) && $keyword != "") {
                $map[] = ['c.name', 'like', '%' . $keyword . '%'];
            }
            $map[] = ['c.company_id', '=', session('company_id')];
            $field = input('field');//字段
            $order = input('order');//排序方式
            if ($field && $order) {
                $od = "c." . $field . " " . $order;
            } else {
                $od = "c.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page') : 1;
            $limits = input("limit") ? input("limit") : 10;
            $count = Db::name('crm_customer')->alias('c')->where($map)->count();//计算总页面
            $customer = new CustomerModel();
            $lists = $customer->getCustomers($map, $Nowpage, $limits, $od);
            return json(['code' => 220, 'msg' => '', 'count' => $count, 'data' => $lists]);
        }
        return $this->fetch('customer_index');
    }

    /**
     * [edit_Customer 编辑客户]
     */
    public function editCustomer()
    {
        {
            $cus = new CustomerModel();
            if (request()->isPost()) {
                $param = input('post.');
                $param['birthday'] = strtotime($param['birthday']);
                $flag = $cus->editCustomer($param);
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
            $id = input();
            $data = $cus->getCustomerBy($id);
            $this->assign('item', $data);
            $area = new \app\common\place\Area;
            $this->assign('province', $area->province());
            return $this->fetch('edit_customer');
        }
    }

    /**
     * [del_customer 删除客户]
     */
    public function delCustomer()
    {
        $id = input('param.id');
        $cus = new CustomerModel();
        $flag = $cus->delCustomer($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [customer_com 客户公海]
     */
    public function customerCom()
    {
        if (request()->isAjax()) {
            extract(input());
            $map = [];
            if (isset($keyword) && $keyword != "") {
                $map[] = ['name', 'like', '%' . $keyword . '%'];
            }
            $map[] = ['company_id', '=', session('company_id')];
            $map[] = ['user_id', '=', 0];
            $field = input('field');//字段
            $order = input('order');//排序方式
            if ($field && $order) {
                $od = "" . $field . " " . $order;
            } else {
                $od = "create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page') : 1;
            $limits = input("limit") ? input("limit") : 10;
            $count = Db::name('crm_customer')->where($map)->count();//计算总页面
            $customer = new CustomerModel();
            $lists = $customer->getCustomers($map, $Nowpage, $limits, $od);
            return json(['code' => 220, 'msg' => '', 'count' => $count, 'data' => $lists]);
        }
        return $this->fetch('customer_com');
    }

    /**
     * [add_customer 添加客户]
     */
    public function addCustomer()
    {
        if (request()->isPost()) {
            extract(input());
            $param = input('post.');
            $param['birthday'] = strtotime($param['birthday']);
            $activity = new CustomerModel();
            $flag = $activity->insertCustomer($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $area = new \app\common\place\Area;
        $map[] = ['company_id', '=', session('company_id')];
        return $this->fetch('add_customer', ['province' => $area->province()]);
        }
    }