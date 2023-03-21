<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\User;
use think\Request;
use app\index\controller\IndexController;

class UserController extends Controller{
    public function index()
    {
        try
        {
            $role = User::role(); //role：角色
            if (User::isLogin()) {
                if ($role === 1) {
                    $name = Request::instance()->get('name');
                    $pageSize = 10;            
                    $User = new User;            
                    if (!empty($name)) {
                        $User->where('name','like','%' . $name .'%');
                    }
                    $Users = $User->paginate($pageSize,false,[
                        'query'=>[
                        'name'=>$name
                        ],
                        ]);
                    $this->assign('Users', $Users);
                    $this->assign('role', $role);

                    return $this->fetch();   
                } else {
                    return $this->error('你的权限不够',url('homepage_controller/index')); 
                }
            } else {
                return $this->error('请登录后在访问',url('login_controller/index'));      
            }
        } catch (\think\Exception\HttpResponseException $e)
        {
            throw $e;
        // 获取到正常的异常时，输出异常
        }  catch (\Exception $e) 
        {
            return $e->getMessage();
        } 
    }

    public function add()
    {   
       $role =  User::role();
       $this->assign('role',$role);
        return $this->fetch();
    }

    public function save()
    {
        $User = new User;
        $User->permissions= Request::instance()->post('permissions/d');
        $username = Request::instance()->post('username');
        if ($User->where('username', '=' , $username)->select()) 
        {
            $this->error('用户名已存在，请重新输入');
        }
        $User->username = $username;
        $User->name = Request::instance()->post('name');
        $User->password='123456';

        if($User->validate()->save())
        {
            return $this->success('add succuss',url('index'));
        }
        else
        {
            return $this->error('add error',url('add'));
        }
    }

    public function edit()
    {
        $id = Request::instance()->param('id/d');
        $role = User::role(); //role：角色
        $User = User::get($id);

        if(!isset($id)) {
            $this->error('未获得ID信息');
        }
        if(!isset($User)) {
            $this->error('查询的ID不存在');
        }
        $this->assign('User', $User);
        $this->assign('role', $role);
        $htmls = $this->fetch(); 
        return $htmls;
    }

    public function update()
    {
        $id =  Request::instance()->post('id/d');
        $User = User::get($id);
        $User->permissions= Request::instance()->post('permissions/d');
        $User->name = Request::instance()->post('name');
        $password1 =  Request::instance()->post('password1');
        $password2 =  Request::instance()->post('password2');

        if($password1===$password2)
        {
            $User->password =$password1;
            if($User->validate()->save())
            {
                return $this->success('updata success', url('index'));
            }
            else
            {
                return $this->error('updata success', url('edit')); 
            }  
        }
        else
        {
            return $this->error('password error', url('edit')); 
        }
    }

    public function delete()
    {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形”
        if (is_null($id) || 0 === $id)
        {
            return $this->error('未获取到ID信息');
        }
        // 获取要删除的对象
        $User = User::get($id);
        // 要删除的对象不存在
        if (is_null($User)) 
        {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }
        // 删除对象
        if (!$User->delete()) 
        {
            return $this->error('删除失败:' . $User->getError());
        }
        // 进行跳转
        return $this->success('删除成功', url('index'));
    }
}