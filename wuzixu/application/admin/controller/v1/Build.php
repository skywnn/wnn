<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\BuildModel;
use app\admin\model\OrganizeModel;
use app\common\upload\Uplaod;
use think\Db;

class Build extends Base
{
    /**
     * [add_article 组织架构]
     */
    public function buildIndex(){
        $cateId = input('cate_id');
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
            $count = Db::name('organize')->where($map)->count();//计算总页面
            $article = new OrganizeModel();
            //$map[] = ['z_cate_id','=',input('cate_id')];
            $lists = $article->getOrganizeByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('build_index');
    }

    public function buildIndex2(){
        if ($this->request->isAjax()) {
            $build = new BuildModel();
            $map[] = ['organize_id','=',input('get.id')];
            $flag = $build->getBuildByWhere($map);
            return json($flag);
        }
        $id = input('id');
        $this->assign('id',$id);
        return $this->fetch('build_index2');
    }

    /**
     * [add_article 添加文章]
     */
    public function addBuild()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new BuildModel();
            $flag = $article->insertBuild($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $pId = input('parent_id');
        $oId = input('organize_id');
        $this->assign('oId',$oId);
        $this->assign('pId',$pId);
        return $this->fetch('add_build');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editBuild()
    {
        $article = new BuildModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editBuild($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneBuild($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_build');
    }

    /**
     * [del_article 删除文章]
     */
    public function delBuild()
    {
        $id = input('param.id');
        $article = new BuildModel();
        $flag = $article->delBuild($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}