<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2019/11/4
 * Time: 22:10
 */

namespace app\common\upload;

use think\Controller;
use think\File;
use think\Db;
use OSS\OssClient;
use OSS\Core\OssException;
use think\facade\Config;
use think\Image;
class Uplaod
{
    /**
     * [baseUplaod 客户端单一图片上传]
     */
    public function baseUplaod($file='file', $pathNewName='goods_icon',$isThumb=false,$width, $height)
    {

        $file = request()->file($file);
        $file_info = $file->getInfo();

        $fileConfig = Config::get('app.upload_image');//上传的图片配置
        //判断是否提交文件
        if(empty($file_info['tmp_name'])){
            return ['code' => 100, 'data' => '', 'msg' => '上传文件出错，请重新上传'];
        }
        //获取图片格式并判断
        $type = explode('/',$file_info['type']);
        //md5加密临时文件名，取前两位字符作为目录名
        $tmpName = $pathNewName.time().$file_info['tmp_name'];
        $tmpNewName = md5($tmpName);
        $dir =  substr($tmpNewName,0,2);
        $savePath = Config::get('app.upload_image.rootNewPath');
        $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        if(true == $isThumb){
            $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/thumb_'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        }
        //上传图片
        $info = $file->move($savePath,$saveName);
        if($info){
            if(true == $isThumb){
                $imgFile = $savePath.$saveName;
                $thumbImg = Image::open($imgFile);
                $thumbImg->thumb($width,$height,Image::THUMB_FIXED)->save($imgFile);
            }
            $pic = $saveName;
            return ['code' => 200, 'data' => $pic, 'msg' => '上传文件成功'];
        }else{
            $file->getError();
            return ['code' => 100, 'data' => '', 'msg' => '上传失败'];
        }
    }
    /**
     * [defaultUplaod 单一图片上传到本地]
     */
    public function defaultUplaod($file='image', $pathNewName='goods_icon', $isThumb=false,$width, $height)
    {
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
//        if($file_info['size'] > $fileConfig['maxSize']){
//            return ['code' => 100, 'data' => '', 'msg' => '上传的文件过大，请重新上传'];
//        }
        //md5加密临时文件名，取前两位字符作为目录名
        $tmpName = $pathNewName.time().$file_info['tmp_name'];
        $tmpNewName = md5($tmpName);
        $dir =  substr($tmpNewName,0,2);
        $savePath = Config::get('app.upload_image.rootNewPath');
        $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        if(true == $isThumb){
            $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/thumb_'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        }
        //上传图片
        $info = $file->move($savePath,$saveName);
        if($info){
            if(true == $isThumb){
                $imgFile = $savePath.$saveName;
                $thumbImg = Image::open($imgFile);
                $thumbImg->thumb($width,$height,Image::THUMB_FIXED)->save($imgFile);
            }
            $pic = $saveName;
            return ['code' => 200, 'data' => $pic, 'msg' => '上传文件成功'];
        }else{
            $file->getError();
            return ['code' => 100, 'data' => '', 'msg' => '上传失败'];
        }
    }
    /**
     * [defaultUplaodPic 单一图片上传到本地--不压缩]
     */
    public function defaultUplaodPic($file='image', $pathNewName='goods_icon',$id)
    {
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
        //md5加密临时文件名，取前两位字符作为目录名
        $tmpName = $pathNewName.$id.$file_info['tmp_name'];
        $tmpNewName = md5($tmpName);
        $dir =  substr($tmpNewName,0,2);
        $savePath = Config::get('app.upload_image.rootNewPath');
        $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称

        //上传图片
        $info = $file->move($savePath,$saveName);
        if($info){
            $pic = $saveName;
            return ['code' => 200, 'data' => $pic, 'msg' => '上传文件成功'];
        }else{
            $file->getError();
            return ['code' => 100, 'data' => '', 'msg' => '上传失败'];
        }
    }
    /**
     * [picsUplaod 多图上传]
     */
    public function picsUplaod($file='image', $pathNewName='goods_icon', $isThumb=false, $id, $width, $height)
    {
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
        //md5加密临时文件名，取前两位字符作为目录名
        $tmpName = $pathNewName.$id.$file_info['tmp_name'];
        $tmpNewName = md5($tmpName);
        $dir =  substr($tmpNewName,0,2);
        $savePath = Config::get('app.upload_image.rootNewPath');
        $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        if(true == $isThumb){
            $saveName = '/uploads/'.$pathNewName.'/'.$dir.'/thumb_'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        }
        //上传图片
        $info = $file->move($savePath,$saveName);
        if($info){
            if(true == $isThumb){
                $imgFile = $savePath.$saveName;
                $thumbImg = \think\Image::open($imgFile);
                $thumbImg->thumb($width,$height,\think\Image::THUMB_FIXED)->save($imgFile);
            }
            $pic = $saveName;
            return ['code' => 200, 'data' => $pic, 'msg' => '上传文件成功'];
        }else{
            $file->getError();
            return ['code' => 100, 'data' => '', 'msg' => '上传失败'];
        }
    }
    /**
     * [defaultUplaod 单一图片上传---阿里云OSS]
     */
    public function defaultUplaodOss($file='image', $pathNewName='goods_icon', $isThumb=false, $id, $width, $height)
    {
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
        //md5加密临时文件名，取前两位字符作为目录名
        $tmpName = $pathNewName.$id.$file_info['tmp_name'];
        $tmpNewName = md5($tmpName);
        $dir =  substr($tmpNewName,0,2);

        $saveName = 'hongyun/uplaods/'.$pathNewName.'/'.$dir.'/'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        if(true == $isThumb){
            $saveName = 'hongyun/uplaods/'.$pathNewName.'/'.$dir.'/thumb'.$tmpNewName.'.'.$type[1];//上传到服务器的新的文件名称
        }
        //上传图片
        $config = Config::get('app.aliyun_oss');
        $ossClient = new OssClient($config['AccessKeyID'], $config['AccessKeySecret'], $config['Endpoint']);
        //执行阿里云上传
        $result = $ossClient->uploadFile($config['Bucket'], $saveName, $file_info['tmp_name']);
        /* 组合返回数据 */
        $arr = [
            'oss_url' => $result['info']['url'], //上传资源地址
            'relative_path' => $saveName  //数据库保存名称(相对路径)
        ];
        if($result){
            //此处做图片的处理
//            if(true == $isThumb){
//                $imgFile = $ossSavePath.$saveName;
//                $thumbImg = \think\Image::open($imgFile);
//                $thumbImg->thumb($width,$height,\think\Image::THUMB_FIXED)->save($imgFile);
//            }
            $pic = $arr['relative_path'];
            return ['code' => 200, 'data' => $pic, 'msg' => '上传文件成功'];
        }else{
            $file->getError();
            return ['code' => 100, 'data' => '', 'msg' => '上传失败'];
        }
    }

}