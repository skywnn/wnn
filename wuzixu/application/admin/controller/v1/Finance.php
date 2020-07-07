<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\FinanceModel;
use app\common\upload\Uplaod;
use think\Db;

class Finance extends Base
{
    public function financeIndex(){
        //$cateId = input('cate_id');
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['way','=',$keyword];
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
            $article = new FinanceModel();
            //$map[] = ['z_cate_id','=',input('cate_id')];
            $lists = $article->getFinanceByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);die();
        }
        //$this->assign('cateId',$cateId);
        return $this->fetch('finance_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addFinance()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new FinanceModel();
            $flag = $article->insertFinance($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_finance');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editFinance()
    {
        $article = new FinanceModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editFinance($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneFinance($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_finance');
    }

    /**
     * [del_article 删除文章]
     */
    public function delFinance()
    {
        $id = input('param.id');
        $article = new FinanceModel();
        $flag = $article->delFinance($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}