<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\ServiceWorkModel;
use app\admin\service\Message;
use app\common\upload\Uplaod;
use think\Db;

class Work extends Base
{
    /**
     * [workIndex 公司列表]
     */
    public function serviceWorkIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的公司需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $serviceWork = new ServiceWorkModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $serviceWork->where($map)->count();//计算总条数
            $lists = $serviceWork->getServiceWorkByWhere($map, $nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("service_work_index");
    }

    /**
     * 添加新的业务
     */
    public function addServiceWork(){
        if(request()->isPost()){
            $param = input('post.');
            $data = [];
            if (!empty($_FILES['pic']['name'])) {
                $uplaod = new Uplaod();
                $pic = $uplaod
                    ->picsUplaod('pic','Service_work_pic',true,uniqid(),400,400);
                $param['pic'] = $pic['data'];
            }
            $serviceWork = new ServiceWorkModel();
            $flag = $serviceWork->insertServiceWork($param);
            if ($flag['code']==200) {
                echo "<script>history.go(-2)</script>";die();
            } else {
                $message = new Message();
                exit($message->alert_errors($flag['msg']));
            }
        }
        return $this->fetch("add_service_work");
    }

    /**
     * 编辑业务
     */
    public function editServiceWork(){
        $serviceWork = new ServiceWorkModel();
        if(request()->isPost()){
            $param = input('post.');
            if (!empty($_FILES['pic']['name'])) {
                $uplaod = new Uplaod();
                $pic = $uplaod
                    ->defaultUplaodPic('pic','Service_work_pic',true,$param['id'],400,400);
                $param['pic'] = $pic['data'];
                if (!empty($param['id'])) {
                    $item = Db::name('service_work')->where('id',$param['id'])->find();
                    $oldurl=$item['pic'];
                    @unlink('static'.$oldurl);
                }
            }
            $flag = $serviceWork->editServiceWork($param);
            if ($flag['code']==200) {
                echo "<script>history.go(-2)</script>";die();
            } else {
                $message = new Message();
                exit($message->alert_errors($flag['msg']));
            }
        }
        $id = input('param.id');
        $item = $serviceWork->getOneServiceWork($id);
        $this->assign('item',$item);
        return $this->fetch("edit_service_work");
    }

    /**
     * [serviceWorkState 公司状态]
     */
    public function serviceWorkState()
    {
        extract(input());
        $serviceWork = new ServiceWorkModel();
        $flag = $serviceWork->serviceWorkState($id, $num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [delServiceWork 删除业务]
     */
    public function delServiceWork()
    {
        $id = input('param.id');
        $serviceWork = new ServiceWorkModel();
        $flag = $serviceWork->delServiceWork($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}