<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/17
 * Time: 20:38
 */

namespace app\admin\controller\v1;

use app\admin\controller\Base;
use app\admin\model\CompanyModel;
use app\admin\model\TradeModel;
use app\admin\model\ThirdDevconfigModel;
use think\facade\Config;
use app\common\upload\Uplaod;
use think\Db;
use org\Verify;
class Company extends Base
{
    /**
     * [companyIndex 公司列表]
     */
    public function companyIndex(){
        if(request()->isAjax ()){
            extract(input());
            $map = Array();
            if(isset($keyword)&&$keyword!=""){
                $map[] = ['name','like','%'.$keyword.'%'];
            }
            //属于商家编辑的公司需设定公司条件
            $field=input('field');//字段
            $order=input('order');//排序方式
            if($field && $order){
                $od=$field." ".$order;
            }else{
                $od="create_time desc";
            }
            $company = new CompanyModel();
            $nowpage = input('page') ? input('page'):1;
            $limits = input("limit")?input("limit"):10;
            $count = $company->getCountCompany($map);//计算总条数
            $lists = $company->getCompanyByWhere($map, $nowpage, $limits, $od);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
        return $this->fetch("company_index");
    }
    /**
     * [companyReal 公司基本信息]
     */
    public function companyInfo()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('company',$company->getOneCompany($id));
        return $this->fetch("company_info");
    }
    /**
     * [editCompanyBase 编辑公司信息]
     */
    public function editCompanyBase()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $trade = new \app\common\place\Trade;
        $this->assign('trade_parent',$trade->oneTrade());
        $id = input('param.id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("edit_company_base");
    }
    /**
     * [editCompanyContacts 编辑公司联系人信息]
     */
    public function editCompanyContacts()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("edit_company_contacts");
    }
    /**
     * [companyReal 公司实名认证]companySafety
     */
    public function companyReal()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('company',$company->getOneCompany($id));
        return $this->fetch("company_real");
    }
    /**
     * [editCompanyReal 公司实名认证信息]companySafety
     */
    public function editCompanyReal()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $area = new \app\common\place\Area();
        $this->assign('province',$area->province());
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("edit_company_real");
    }
    /**
     * [uplaodLicense 上传营业执照]
     */
    public function uplaodLicense($id){
        if(request()->isPost()){
            $id = input('param.id');
            $pathNewName = 'Company_license';
            $width = Config::get('app.upload_image.picWidth');
            $height = Config::get('app.upload_image.picHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,false,$id,$width,$height);
            if(200 != $pic['code']){
                return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
            }
            //删除原有图片保存新图片
            $column = new CompanyModel();
            if(!empty($id)){
                $bra = $column->getOneCompany($id);
                $oldurl=$bra['license'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'license' => $pic['data'],
                'id' => $id,
            );
            $flag = $column->editCompany($date);
            if(200 == $flag['code']){
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }else{
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
    }
    /**
     * [companySafety 公司安全机制]
     */
    public function companySafety()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("company_safety");
    }
    /**
     * [editCompanysafetyEmail 公司安全机制--修改邮箱]
     */
    public function editCompanysafetyEmail()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("edit_companysafety_email");
    }
    /**
     * [editCompanysafetyPhone 公司安全机制--修改手机]
     */
    public function editCompanysafetyPhone()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("edit_companysafety_phone");
    }
    /**
     * [companyDeveloper 公司开发者中心]
     */
    public function companyDeveloper()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        $third = new ThirdDevconfigModel();
        $thirdDate = $third->where('company_id','=',$id)->select();
        $this->assign('thirdDate',$thirdDate);
        return $this->fetch("company_developer");
    }
    /**
     * [companyThirdList 公司第三方开发平台配置列表]
     */
    public function companyThirdList()
    {
        if(request()->isAjax ()){
            $id = session('company_id');
            $third = new ThirdDevconfigModel();
            $lists = $third->where('company_id','=',$id)->select();
            $count= count($lists);
            return json(['code'=>220,'msg'=>'','count'=>$count,'data'=>$lists]);
        }
    }
    /**
     * [editCompanyThird 编辑公司第三方开发平台配置]
     */
    public function editCompanyThird()
    {
        $cate = new ThirdDevconfigModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $cate->editThirdDevconfig($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('item',$cate->getOneThirdDevconfig($id));
        return $this->fetch('edit_company_third');
    }
    /**
     * [delCompanyThird 删除公司第三方开发平台配置]
     */
    public function delCompanyThird()
    {
        $id = input('param.id');
        $cate = new ThirdDevconfigModel();
        $flag = $cate->delThirdDevconfig($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [companyThirdState 公司第三方开发平台配置状态]
     */
    public function companyThirdState()
    {
        extract(input());
        $cate = new ThirdDevconfigModel();
        $flag = $cate->thirdDevconfigState($id,$num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * [bindCompanyThird 绑定公司第三方开发平台配置]
     */
    public function bindCompanyThird()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $third = new ThirdDevconfigModel();
            $flag = $third->insertThirdDevconfig($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch("bind_company_third");
    }
    /**
     * [createCompanyDeveloperKey 生成公司开发者APPID]
     */
    public function createCompanyDeveloperKey()
    {
        $time = time();
        $ao = rand(1,100);
        $id = session('company_id');
        $company = new CompanyModel();
        $comDate = $company->getOneCompany($id);
        if(empty($comDate['appid'])){
            if(!empty($comDate['xinyongma'])){
                $tmpdate = $comDate['id'].$comDate['xinyongma'].$time.$ao;
                $param['appid'] = 'hy'.md5($tmpdate);
                $param['id'] = $comDate['id'];
                $flag = $company->editCompany($param);
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '已成功创建开发者AppID']);
            }else{
                return json(['code' => 100, 'data' => '', 'msg' => '没有开发者权限，企业需实名认证']);
            }
        }else{
            return json(['code' => 100, 'data' => '', 'msg' => '已有开发者权限，不能重复创建']);
        }
    }
    /**
     * [resetCompanyDeveloperSecret 重置开发者密码]
     */
    public function resetCompanyDeveloperSecret()
    {
        $time = time();
        $ao = rand(1,100);
        $id = session('company_id');
        $company = new CompanyModel();
        $comDate = $company->getOneCompany($id);
        if(!empty($comDate)){
            $tmpdate = $time.$ao;
            $param['appsecret'] = md5($ao).md5($tmpdate);
            $param['id'] = $comDate['id'];
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '已重置开发者密码']);
        }else{
            return json(['code' => 100, 'data' => '', 'msg' => '企业状态异常']);
        }
    }
    /**
     * [companyDeveloperDoc 公司开发者文档]
     */
    public function companyDeveloperDoc()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("company_developer_doc");
    }
    /**
     * [companyLog 公司日志]
     */
    public function companyLog()
    {
        $company = new CompanyModel();
        $id = session('company_id');
        $this->assign('item',$company->getOneCompany($id));
        return $this->fetch("company_log");
    }

    /**
     * [addCompany 添加公司]developer
     */
    public function addCompany()
    {
        if(request()->isPost()){
            extract(input());
            $param = input('post.');
            $company = new CompanyModel();
            $flag = $company->insertCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch("add_company");
    }
    /**
     * [editCompany 编辑公司]
     */
    public function editCompany()
    {
        $company = new CompanyModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $company->editCompany($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('company',$company->getOneCompany($id));
        return $this->fetch("edit_company");
    }

    /**
     * [companyIcon 设置公司图标]
     */
    public function companyIcon($id){

        $company = new CompanyModel();
        if(request()->isPost()){
            $pathNewName = 'ArticleCompany_icon';
            $width = Config::get('app.upload_image.iconWidth');
            $height = Config::get('app.upload_image.iconHeight');
            $uplaodData = new Uplaod();
            $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
            if(200 != $pic['code']){
                $this->error($pic['msg'], 'boss/Article/companyIndex');
            }
            //删除原有图片保存新图片
            if(!empty($id)){
                $bra = $company->getOneCompany($id);
                $oldurl=$bra['icon'];
                @unlink('static'.$oldurl);
            }
            //保存当前数据对象
            $date = array(
                'icon' => $pic['data'],
                'id' => $id,
            );
            $flag = $company->editCompany($date);
            if(200 == $flag['code']){
                $this->success('上传图片成功！', 'boss/Article/companyIndex');
            }else{
                $this->error('上传图片失败!', 'boss/Article/companyIndex');
            }
        }
        $oneCompany = $company->getOneCompany($id);
        $this->assign('item',$oneCompany);
        return $this->fetch("company_icon");
    }
    /**
     * [delCompany 删除公司]
     */
    public function delCompany()
    {
        $id = input('param.id');
        $company = new CompanyModel();
        $flag = $company->delCompany($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [companyState 公司状态]
     */
    public function companyState()
    {
        extract(input());
        $company = new CompanyModel();
        $flag = $company->companyState($id, $num);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [checkVerify 验证码]
     */
    public function checkVerify(){
        $config =    [
            'imageH' => 36,// 验证码图片高度
            'imageW' => 120,// 验证码图片宽度
            'codeSet' => '02345689',// 验证码字符集合
            'useZh' => false,//使用中文验证码
            'length' => 4,// 验证码位数
            'useNoise' => true,//是否添加杂点
            'useCurve' => false,//是否画混淆曲线
            'useImgBg' => false,//使用背景图片
            'fontSize' => 16// 验证码字体大小(px)
        ];
        $verify = new Verify($config);
        return $verify->entry();
    }
}