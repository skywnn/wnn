<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/6/5
 * Time: 13:42
 */

namespace app\home\controller;
use app\home\model\WebMenuModel;
use think\Controller;

class Index extends Controller
{
    public function index(){
        return $this->fetch('v1/index');
    }
}