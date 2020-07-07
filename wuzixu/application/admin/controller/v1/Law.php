<?php


namespace app\admin\controller\v1;


use app\admin\controller\Base;
use app\admin\model\LawModel;
use app\common\upload\Uplaod;
use think\Db;

class Law extends Base
{
    public function lawIndex(){
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
            $count = Db::name('law')->where($map)->count();//计算总页面
            $article = new LawModel();
            //$map[] = ['z_cate_id','=',input('cate_id')];
            $lists = $article->getLawByWhere($map, $Nowpage, $limits,$od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        //$this->assign('cateId',$cateId);
        return $this->fetch('law_index');
    }

    /**
     * [add_article 添加文章]
     */
    public function addLaw()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $article = new LawModel();
            $flag = $article->insertLaw($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $map[] = ['status','=',1];
        $cateId = input('cate_id');
        $this->assign('cateId',$cateId);
        return $this->fetch('add_law');
    }

    /**
     * [edit_article 编辑文章]
     */
    public function editLaw()
    {
        $article = new LawModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editLaw($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $data = $article->getOneLaw($id);
        $map[] = ['status','=',1];
        $this->assign('item',$data);
        return $this->fetch('edit_law');
    }
    /**
     * [articlePic 模板图片]
     */
    public function lawPic($id){
        $article = new LawModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editLaw($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneLaw($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 410;
        $this->assign('pic',$pic);
        return $this->fetch('law_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodPic($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Law_pic';
            $width = 750;
            $height = 410;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new LawModel();
            if(!empty($id)){
                $bra = $article->getOneLaw($id);
                $oldurl=$bra['pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editLaw($date);
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
    public function lawIcon($id){
        $article = new LawModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $article->editLaw($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '上传文件成功']);
        }
        $id = input('param.id');
        $data = $article->getOneLaw($id);
        $this->assign('item',$data);
        $pic['width'] = 750;
        $pic['height'] = 310;
        $this->assign('pic',$pic);
        return $this->fetch('law_thum_pic');
    }
    /**
     * [uplaodPic 上传图片]
     */
    public function uplaodIcon($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Law_icon';
            $width = 750;
            $height = 310;
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $article = new LawModel();
            if(!empty($id)){
                $bra = $article->getOneLaw($id);
                $oldurl=$bra['thum_pic'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'thum_pic' => $pic['data'],
                'id' => $id,
            );
            $flag = $article->editLaw($date);
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
    public function delLaw()
    {
        $id = input('param.id');
        $article = new LawModel();
        $flag = $article->delLaw($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [article_state 文章状态]
     */
    public function lawState()
    {
        extract(input());
        $article = new LawModel();
        $flag = $article->LawState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}