<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/12/20
 * Time: 23:08
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\upload\Uplaod as FileUUplaod;
use think\facade\Config;
use OSS\OssClient;
use OSS\Core\OssException;
use think\db;
class Uplaod extends Base
{
    /**
     * [cateIcon 上传到本地并形成缩略图]
     */
    public function iconUpload($id,$pathNewName){
        $width = Config::get('app.upload_image.iconWidth');
        $height = Config::get('app.upload_image.iconHeight');
        $uplaodData = new FileUUplaod();
        $pic = $uplaodData->defaultUplaod('image',$pathNewName,true,$id,$width,$height);
        return json(['code' => $pic['code'], 'data' => $pic['data'], 'msg' => $pic['msg']]);
    }

    public function jsonReturn($code, $message = 'success', $data=[]) {
        return json(['code'=>$code, 'message'=>$message, 'data'=>$data]);
    }

    /* OSS图片上传
         * liwei
         * */
    public function oss_uploadImage() {
        /* 获取到上传的文件 */
        $file = 'image';
        $file = request()->file($file);
        $file_info = $file->getInfo();
        $fileConfig = Config::get('app.upload_image');//上传的图片配置
        //判断是否提交文件
        if(empty($file_info['tmp_name'])){
            return ['code' => 100, 'data' => '', 'msg' => '上传文件出错，请重新上传'];
        }
        //获取图片格式并判断
        $type = explode('/',$file_info['type']);
        if(!in_array($type[1],$fileConfig['imgType'])){
            return ['code' => 100, 'data' => '', 'msg' => '上传的文件格式不正确，请重新上传'];
        }
        //判断图片大小
        if($file_info['size'] > $fileConfig['maxSize']){
            return ['code' => 100, 'data' => '', 'msg' => '上传的文件过大，请重新上传'];
        }

        $pathNewName='goods_icon';
        //md5加密临时文件名，取前两位字符作为目录名
        //$tmpName = $pathNewName.$id.$file_info['tmp_name'];
        //$fileName = 'hongyun/uplaod/image/' . date("Ymd") . '/' . sha1(date('YmdHis', time()) . uniqid()) . $format;

        $format = strrchr($file_info['name'], '.'); //截取文件后缀名如 (.jpg)
        // 尝试执行
        try {
            $config = Config::get('app.aliyun_oss');
            //实例化对象 将配置传入
            $ossClient = new OssClient($config['AccessKeyID'], $config['AccessKeySecret'], $config['Endpoint']);

            //这里可以按需改成商户id的什么目录 这里是有sha1加密 生成文件名 之后连接上后缀
            $fileName = 'hongyun/uplaod/image/' . date("Ymd") . '/' . sha1(date('YmdHis', time()) . uniqid()) . $format;
            //执行阿里云上传
            $result = $ossClient->uploadFile($config['Bucket'], $fileName, $file_info['tmp_name']);
            /* 组合返回数据 */
            $arr = [
                'oss_url' => $result['info']['url'], //上传资源地址
                'relative_path' => $fileName  //数据库保存名称(相对路径)
            ];
        } catch (OssException $e) {
            return $this->jsonReturn(400, $e->getMessage());
        }

        //将结果返回
        return $this->jsonReturn(0, '成功上传到oss', array('file' => $arr['oss_url']));
    }

    /* OSS图片上传
         * liwei
         * */
    public function oss_uploadImage2() {
        /* 获取到上传的文件 */
        //$file = request()->file('file');
        if (!$_FILES) {
            return $this->jsonReturn(400, "文件不存在");
        }

        $file = $_FILES['file'];
        $name = $file['name'];
        $format = strrchr($name, '.'); //截取文件后缀名如 (.jpg)
        /* 判断图片格式 */
        $allow_type = ['.jpg', '.jpeg', '.gif', '.bmp', '.png'];
        if (!in_array($format, $allow_type)) {
            return $this->jsonReturn(400, "文件格式不在允许范围内哦");
        }


        // 尝试执行
        try {
            $config = Config::get('app.aliyun_oss');
            //实例化对象 将配置传入
            $ossClient = new OssClient($config['AccessKeyID'], $config['AccessKeySecret'], $config['Endpoint']);

            //这里可以按需改成商户id的什么目录 这里是有sha1加密 生成文件名 之后连接上后缀
            $fileName = 'hongyun/uplaod/image/' . date("Ymd") . '/' . sha1(date('YmdHis', time()) . uniqid()) . $format;
            //执行阿里云上传
            $result = $ossClient->uploadFile($config['Bucket'], $fileName, $file['tmp_name']);
            /* 组合返回数据 */
            $arr = [
                'oss_url' => $result['info']['url'], //上传资源地址
                'relative_path' => $fileName  //数据库保存名称(相对路径)
            ];
        } catch (OssException $e) {
            return $this->jsonReturn(400, $e->getMessage());
        }

        //将结果返回
        return $this->jsonReturn(0, '成功上传到oss', array('file' => $arr['oss_url']));
    }


}