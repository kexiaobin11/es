<?php
namespace app\index\controller;
use think\Controller;   
use think\Request;
use app\common\model\User;
class LoginController extends Controller
{
    public function index() {    
        return $this->fetch();
    }

    public function login() {
        $postData = Request::instance()->post();
        $map = array('username' => $postData['username']);
        $User = User::get($map);
        if (!is_null($User) && $User->getData('password') === $postData['password'] ) {
            session('userId', $User->getData('id'));
            return $this->success('登录成功',url('homepage_controller/index'));
        } else {
            return $this->error('密码错误或用户名错误');
        }
    }

    public function logOut() {
        if(User::logOut()) { 
            return $this->success('退出登录',url('index'));
        }
    }
}