<?php


namespace app\admin\model;

class RoleUserModel extends BaseModel
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $datetime_format = "m-d H:i";

    protected $name = 'role_user';
    //select  r.id,u.username,r.`name` from hy_role_user ur, hy_role r, hy_user u where ur.user_id=u.id and ur.role_id=r.id AND r.id=30
    public function getUserByUId($where,$page,$limit){
         $uList = [];
         $userList = $this->alias('ur')->leftJoin('user u','ur.user_id=u.id')
             ->leftJoin('role r','ur.role_id=r.id')
             ->where($where)->page($page,$limit)->field('u.*')->select();

         foreach ($userList as $u) {
             $u['login_time'] = date('m-d h:i:s');
             array_push($uList,$u);
         }
         return $uList;
    }
}