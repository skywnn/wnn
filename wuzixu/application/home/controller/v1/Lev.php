<?php


namespace app\home\controller\v1;


use app\home\controller\Base;

class Lev extends Base
{
    public function levIndex(){
        return $this->fetch('lev');
    }
}