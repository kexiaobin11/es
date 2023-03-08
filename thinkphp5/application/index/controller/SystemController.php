<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;

class SystemController extends Controller{
    public function index()
    {
        if(User::isLogin()){
            return $this->fetch();
        }
        else{
            return $this->error('请登录后在访问',url('login_controller/index'));      
        }
    }
}