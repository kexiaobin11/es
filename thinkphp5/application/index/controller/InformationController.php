<?php
namespace app\index\controller;
use app\common\validate;
use think\Controller;
use app\common\model\User;
use think\Request;
class InformationController extends Controller
{
    public function commonSession()
	  {
        $id = session('userId');
         return User::get($id);
    }

    /**
     * 登录页面
     */
    public function index()
	  {
      if(User::isLogin())
	    {
        $User = $this->commonSession();
       
        if(!isset($User)) {
          $this->error('用户不存在');
        }
        //表单传值
        $role = User::role();
        $this->assign('role', $role);
        $this->assign('user', $User);
        return $this->fetch();
      }
      else
      {
          return $this->error('请登录后在访问', url('login_controller/index'));      
      }
    }

    public function edit()
    {
         $role = User::role();
         
          $User = $this->commonSession();
          $this->assign('user', $User);
          $this->assign('role',  $role);
          return $this->fetch();
    }

    public function updata()
    {
          $id = Request::instance()->post('id/d');
          $User =User::get($id);
          $User->name = input('name');
          $User->permissions=input('permissions/d');
        if ($User->validate(true)->save()) {
          $this->success('更新成功', url('index'));
        }
        else  {
          $this->error('更新失败', url('edit'));
         } 
    }   

    public function updatapassword()
	   {
        $User = $this->commonSession();
        if(!isset($User)) {
          $this->error('用户不存在');
        }
        
        $role = User:: role();
        $this->assign('role', $role);
        $this->assign('user', $User);
        return $this->fetch();
    }
    /**
     * 密码进行更新
     * @param $oldpossword 原始密码
     * @param $password1 新密码
     * @param $password2 新密码（数据校验）
     */
    public function upsave()
	  {
       $id = Request::instance()->post('id/d');
       $User =User::get($id);
       $oldpossword = Request::instance()->post('oldpassword');
       $password1 = Request::instance()->post('password1');
       $password2 = Request::instance()->post('password2');
       
        //判断两次密码输入是否一则
        if ($User->password === $oldpossword) {
            if ($password2 === $password1) {
                $User->password = $password1;
                if ($this->validate($User, 'User') === true) {
                    $User->save();
                    $this->success(' 密码更改成功 ', url('index'));
                }
                else {
                    $this->error(' 输入的密码不合法,请重新输入 ', url('index'));
                }
            }	
            else {
                $this->error('两次密码不一致 ', url('updatapassword'));
            }  
       	}
      	else {
			    $this->error('原密码输入错误 ', url('updatapassword'));			        
        }
    }
}