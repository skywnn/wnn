<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/25
 * Time: 22:43
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\BlogModel;
use app\admin\model\BlogCateModel;
use app\admin\model\BlogDayModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\db;
class Blog extends Base
{
//*********************************************时辰养生管理*********************************************//
    /**
     * [blogIndex 时辰养生列表]
     */
    public function blogIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['r.title','like','%'.$keyword.'%'];
            }
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od="r.".$field." ".$order;
            }else{
                $od="r.create_time desc";
            }
            $Nowpage = input('get.page') ? input('get.page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = Db::name('blog')->alias('r')->where($map)->count();//计算总页面
            $blog = new BlogModel();
            $lists = $blog->getBlogByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('blog_index');
    }

    /**
     * [add_blog 添加时辰养生]
     */
    public function addBlog()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $blog = new BlogModel();
            $flag = $blog->insertBlog($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $bolgCate= new BlogCateModel();
        $bolgDate = $bolgCate->where('status','=',1)->select();
        $this->assign('cate',$bolgDate);
        return $this->fetch('add_blog');
    }

    /**
     * [edit_blog 编辑时辰养生]
     */
    public function editBlog()
    {
        $blog = new BlogModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $blog->editBlog($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $blog->getOneBlog($id);
        $this->assign('item',$data);
        $bolgCate= new BlogCateModel();
        $bolgDate = $bolgCate->where('status','=',1)->select();
        $this->assign('cate',$bolgDate);
        return $this->fetch('edit_blog');
    }

    /**
     * [del_blog 删除时辰养生]
     */
    public function delBlog()
    {
        $id = input('param.id');
        $blog = new BlogModel();
        $flag = $blog->delBlog($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [blog_state 时辰养生状态]
     */
    public function blogState()
    {
        extract(input());
        $blog = new BlogModel();
        $flag = $blog->blogState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    //*********************************************分类管理*********************************************//

    /**
     * [index_cate 分类列表]
     */
    public function cateIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time";
            }
            $cate = new BlogCateModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):12;
            $count = $cate->getCountCate($map);//计算总条数
            $lists = $cate->getCateByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('cate_index');
    }

    /**
     * [add_cate 添加分类]
     */
    public function addCate()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new BlogCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_cate');
    }

    /**
     * [edit_cate 编辑分类]
     */
    public function editCate()
    {
        $cate = new BlogCateModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneCate($id));
        return $this->fetch();
    }

    /**
     * [del_cate 删除分类]
     */
    public function delCate()
    {
        $id = input('param.id');
        $cate = new BlogCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new BlogCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    //*********************************************分类管理*********************************************//
    /**
     * [index_cate 分类列表]
     */
    public function blogDayIndex(){

        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的分类需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time";
            }
            $cate = new BlogDayModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):12;
            $count = $cate->getCountBlogDay($map);//计算总条数
            $lists = $cate->getBlogDayByWhere($map, $nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch('blog_day_index');
    }

    /**
     * [add_cate 添加分类]
     */
    public function addBlogDay()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $cate = new BlogDayModel();
            $flag = $cate->insertBlogDay($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch('add_blog_day');
    }

    /**
     * [edit_cate 编辑分类]
     */
    public function editBlogDay()
    {
        $cate = new BlogDayModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editBlogDay($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneBlogDay($id));
        return $this->fetch('edit_blog_day');
    }

    /**
     * [del_cate 删除分类]
     */
    public function delBlogDay()
    {
        $id = input('param.id');
        $cate = new BlogDayModel();
        $flag = $cate->delBlogDay($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function blogDayState()
    {
        extract(input());
        $cate = new BlogDayModel();
        $flag = $cate->blogDayState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}