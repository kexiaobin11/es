<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;
use think\Request;
class InformationController extends Controller{
   
    public function commonSession(){
        $id = session('userId');
         return User::get($id);
    }

    /**
     * 登录页面
     */
    public function index(){

      if(User::isLogin()){
        $User = $this->commonSession();
        $this->assign('user',$User);
        return $this->fetch();
      }
      else{
        return $this->error('请登录后在访问',url('login_controller/index'));      
      }
    }


    public function edit(){
        $User = $this->commonSession();
        $this->assign('user',$User);
        return $this->fetch();
    }
    public function updata(){
        $id = Request::instance()->post('id/d');
        $User =User::get($id);
        $User->name = input('name');
        $User->permissions=input('permissions/d');
      if($User->validate(true)->save()){
        $this->success('updata success',url('index'));
      }else{
        $this->error('updata error',url('edit'));
      } 
    }   

    public function updatapassword(){
        $id = session('userId');
        $User = User::get($id);
        $this->assign('user',$User);
        return $this->fetch();
    }

    public function upsave(){
       $id = Request::instance()->post('id/d');
       $User =User::get($id);
       $oldpossword = Request::instance()->post('oldpassword');
       $password1 = Request::instance()->post('password1');
       $password2 = Request::instance()->post('password2');
       if($User->password === $oldpossword){
            if($password2=== $password1){
              $User->password = $password1;
              $User->validate(true)->save();  
              $this->success(' password updata success ',url('index'));
            }else{
                $this->error('twice
                Different password ',url('updatapassword'));
            }  
       }else{
        $this->error('ord password error ',url('updatapassword'));
       }
      

    }

}