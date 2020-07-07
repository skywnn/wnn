<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\AimModel;
use think\Db;

class Aim extends Base
{
    public function aimIndex(){
        //$cateId = input('cate_id');
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="".$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('genealogy_address')->where($map)->count();//计算总页面
            $article = new AimModel();
            //$map[] = ['z_cate_id','=',input('cate_id')];
            $lists = $article->getAimByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        //$this->assign('cateId',$cateId);
        return $this->fetch('aim_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addAim()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new AimModel();
            $flag = $article->insertAim($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_aim');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editAim()
    {
        $article = new AimModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editAim($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneAim($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_aim');
    }
    /**
     * [del_article 删除文章]
     */
    public function delAim()
    {
        $id = input('param.id');
        $article = new AimModel();
        $flag = $article->delAim($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [article_state 文章状态]
     */
    public function aimState()
    {
        extract(input());
        $article = new AimModel();
        $flag = $article->aimState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}