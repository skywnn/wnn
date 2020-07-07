<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/3/29
 * Time: 22:41
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\BlogDayModel;
use think\db;
class BlogDay extends Base
{
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

//            if($field && $order){
//                $od=$field." ".$order;
//            }else{
//                $od="create_time desc";
//            }
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