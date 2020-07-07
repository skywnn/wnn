<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\JobModel;
use think\Db;

class Job extends Base
{
    public function jobIndex(){
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
            $article = new JobModel();
            //$map[] = ['z_cate_id','=',input('cate_id')];
            $lists = $article->getJobByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        //$this->assign('cateId',$cateId);
        return $this->fetch('job_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addJob()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new JobModel();
            $flag = $article->insertJob($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_job');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editJob()
    {
        $article = new JobModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editJob($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneJob($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_job');
    }

    /**
     * [del_article 删除文章]
     */
    public function delJob()
    {
        $id = input('param.id');
        $article = new JobModel();
        $flag = $article->delJob($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [article_state 文章状态]
     */
    public function jobState()
    {
        extract(input());
        $article = new JobModel();
        $flag = $article->jobState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}