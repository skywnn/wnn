<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/18
 * Time: 19:09
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\ArticleModel;
use app\admin\model\ArticleCateModel;
use app\admin\model\BowenModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
class Article extends Base
{
//*********************************************文章管理*********************************************//
    /**
     * [articleIndex 文章列表]
     */
    public function articleIndex(){
        $cateId = input('cate_id');
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
            $count = Db::name('article')->alias('r')->where($map)->count();//计算总页面
            $article = new ArticleModel();
            $map[] = ['r.cate_id','=',input('cate_id')];
            $lists = $article->getArticleByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('cateId',$cateId);
        return $this->fetch('article_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addArticle()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new ArticleModel();
            $flag = $article->insertArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_article');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editArticle()
    {
        $article = new ArticleModel();
        if(request()->isPost()){
            $param = input('post.');
//            if($param['start_time']){
//                $param['start_time'] =strtotime($param['start_time']);
//            }
//            if($param['end_time']){
//                $param['end_time'] =strtotime($param['end_time']);
//            }
            $flag = $article->editArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $cate = new ArticleCateModel();
        $data = $article->getOneArticle($id);
        if(!empty($data)){
            if(!empty($data['start_time'])){
                $data['start_time'] = date("Y-m-d H:i:s",$data['start_time']);
            }
            if(!empty($data['end_time'])){
                $data['end_time'] = date("Y-m-d H:i:s",$data['end_time']);
            }
        }
        $map[] = ['status','=',1];
        $this->assign('cate',$cate->getAllCate($map));
        $this->assign('item',$data);
        return $this->fetch('edit_article');
    }
    /**
     * [articlePic 模板图片]
     */
    public function articlePic($id){
        $article = new ArticleModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneArticle($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 410;
        $this->assign('pic',$pic);
        return $this->fetch('article_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Article_pic';
            $width = 750;
            $height = 410;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new ArticleModel();
            if(!empty($id)){
                $bra = $article->getOneArticle($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editArticle($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
    /**
     * [articlePic 模板图片]
     */
    public function articleIcon($id){
        $article = new ArticleModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneArticle($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 310;
        $this->assign('pic',$pic);
        return $this->fetch('article_thum_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Article_icon';
            $width = 750;
            $height = 310;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new ArticleModel();
            if(!empty($id)){
                $bra = $article->getOneArticle($id);
                $oldurl=$bra['thum_pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'thum_pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editArticle($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
    /**
     * [del_article 删除文章]
     */
    public function delArticle()
    {
        $id = input('param.id');
        $article = new ArticleModel();
        $flag = $article->delArticle($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [article_state 文章状态]
     */
    public function articleState()
    {
        extract(input());
        $article = new ArticleModel();
        $flag = $article->articleState($id,$num);
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
                $od="create_time desc";
            }
            $cate = new ArticleCateModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
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
            $cate = new ArticleCateModel();
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
        $cate = new ArticleCateModel();
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
        $cate = new ArticleCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [cate_state 分类状态]
     */
    public function cateState()
    {
        extract(input());
        $cate = new ArticleCateModel();
        $flag = $cate->cateState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}