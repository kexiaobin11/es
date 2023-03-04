<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;
use think\Request;
use app\index\controller\IndexController;

class UserController extends Controller{
    public function index(){
        $name = Request::instance()->get('name');
        var_dump($name);
        $User = new User;
        $users = $User->select();
        $this->assign('Users',$users);
        return $this->fetch();   
    }

    public function add(){
        return $this->fetch();
    }



}