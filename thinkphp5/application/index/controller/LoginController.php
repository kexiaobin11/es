<?php
namespace app\index\controller;
use think\Controller;   
use think\Request;
use app\common\model\User;
class LoginController extends Controller{
    public function index(){    
        return $this->fetch();
    }
    public function login(){
        $postData = Request::instance()->post();
        var_dump($postData);
        $map = array('username' => $postData['username']);
        $User = User::get($map);
        if(!is_null($User) &&$User->getData('password')===$postData['password'] ){
            session('userId', $User->getData('id'));
            return $this->success('login success',url('homepage_controller/index'));
        }else{
            return $this->error('username not exist');
        }
    }
    public function logOut(){
        if(User::logOut()){
            return $this->success('exit success',url('index'));
        }
    }

}