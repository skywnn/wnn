<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\YuanQuModel;

class YuanQu extends Base
{
    /**
     * [add_activity 添加活动]
     */
    public function addYuanQu()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $activity = new YuanQuModel();
            $flag = $activity->insertYuanQu($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $area = new \app\common\place\Area;
        $map[] = ['company_id','=',session('company_id')];
        return $this->fetch('add_yuanqu',['province'=>$area->province()]);
    }
}