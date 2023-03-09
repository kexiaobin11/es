<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;

class SystemController extends Controller{
    public function index()
    {
        $id = session('userId');
        $user = User::get($id);
        //var_dump($user->getData('permissions'));
        $role = $user->getData('permissions'); //role：权限
        if(User::isLogin()){
            if($role){//如果是1，则是管理员；0就是用户，不可访问
              
                return $this->fetch();
            }
            else{
                return $this->error('你的权限不够',url('homepage_controller/index')); 
            }
        }
        else{
            return $this->error('请登录后在访问',url('login_controller/index'));      
        }
    }
}