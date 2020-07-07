<?php


namespace app\admin\service;


class Tree
{

    public function getTree($arr,$id){
        $newArr = [];
        foreach ($arr as $v){
            if ($v['parent_id'] == $id){
                $newArr[] = array(
                    "title"=>$v['title'],
                    "id"=>$v['id'],
                    'children'=>$this->getTree($arr,$v['id'])
                );
            }

        }
        return $newArr;
    }
}