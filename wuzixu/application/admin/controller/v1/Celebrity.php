<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\CelebrityModel;
use app\common\upload\Uplaod;
use think\Db;

class Celebrity extends Base
{
    /**
     * [articleIndex 文章列表]
     */
    public function celebrityIndex(){
        $h = input('history');
        if(request()->isAjax ()){
            extract(input());
            $map = [];
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            if ($h>0) {
                $birthday = strtotime('1949');
                $map[] = ['birthday','>',$birthday];
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
            $count = Db::name('genealogy')->where($map)->count();//计算总页面
            $article = new CelebrityModel();
            //$map[] = ['gen_cate','=',input('cate_id')];
            $lists = $article->getCelebrityByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        $this->assign('h',$h);
        return $this->fetch('celebrity_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addCelebrity()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new CelebrityModel();
            $flag = $article->insertCelebrity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_celebrity');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editCelebrity()
    {
        $article = new CelebrityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editCelebrity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneCelebrity($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_celebrity');
    }
    /**
     * [articlePic 模板图片]
     */
    public function celebrityPic($id){
        $article = new CelebrityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editCelebrity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneCelebrity($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 410;
        $this->assign('pic',$pic);
        return $this->fetch('celebrity_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Celebrity_pic';
            $width = 750;
            $height = 410;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new CelebrityModel();
            if(!empty($id)){
                $bra = $article->getOneCelebrity($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editCelebrity($date);
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
    public function celebrityIcon($id){
        $article = new CelebrityModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editCelebrity($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneCelebrity($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 310;
        $this->assign('pic',$pic);
        return $this->fetch('celebrity_thum_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Address_icon';
            $width = 750;
            $height = 310;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new CelebrityModel();
            if(!empty($id)){
                $bra = $article->getOneCelebrity($id);
                $oldurl=$bra['thum_pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'thum_pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editCelebrity($date);
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
    public function delCelebrity()
    {
        $id = input('param.id');
        $article = new CelebrityModel();
        $flag = $article->delCelebrity($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [article_state 文章状态]
     */
    public function celebrityState()
    {
        extract(input());
        $article = new CelebrityModel();
        $flag = $article->celebrityState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}