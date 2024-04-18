<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;

class SystemController extends Controller{
    public function index() {
        $role = User:: role(); 
        if (User::isLogin()) {
            if($role) {
                $this->assign('role', $role);
                return $this->fetch();
            } else {
                return $this->error('你的权限不够',url('homepage_controller/index')); 
            }
        }  else {
            return $this->error('请登录后在访问',url('login_controller/index'));      
        }
    }
}