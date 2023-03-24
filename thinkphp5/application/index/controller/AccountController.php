<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Account;
use app\common\model\User;
use think\Request;
use app\common\model\Stream;

class AccountController extends Controller
{
    public function index()
    {
        try
        {
            $role = User:: role(); //role：角色
            if(User::isLogin())
            {
                if($role === 1) //如果是1，则是管理员；0就是用户，不可访问
                {                
                    // 获取查询信息
                    $name = Request::instance()->get('name');
                    echo $name;
                    $pageSize = 10; // 每页显示10条数据
                    // 实例化Account
                    $Account = new Account; 
                    // 定制查询信息
                    if (!empty($name))
                    {
                        $Account->where('name', 'like', '%' . $name . '%');
                    }
                    // 调用分页
                    $account = $Account->paginate($pageSize);
                    // 向V层传数据
                    $this->assign('Account', $account);
                    $this->assign('role', $role);
                    // 取回打包后的数据
                    $htmls = $this->fetch();
                    // 将数据返回给用户
                    return $htmls;         
                }
                else
                {
                    return $this->error('你的权限不够', url('homepage_controller/index')); 
                }
            }
            else
            {
                return $this->error('请登录后在访问', url('login_controller/index'));      
            }
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        }
        catch (\think\Exception\HttpResponseException $e)
        {
            throw $e;
            // 获取到正常的异常时，输出异常
        } catch (\Exception $e)
        {
            return $e->getMessage();
        } 
    }


    public function add()
    {
        $eid = 1;
        $role = User:: role();
        if(is_null($eid) && is_null($role)) {
            $this->error('访问失败');
        }
        $Account = new Account;
        $Account->id = '';
        $Account->name = '';
        $this->assign('role', $role);
        $this->assign('Account', $Account);
        $this->assign('eid', $eid);
        return $this->fetch('edit');      
    }

    public function edit()
    {
        $role = User:: role();
        $eid = 1;
        // 获取传入ID
        $id = Request::instance()->param('id/d');
        // 在Income表模型中获取当前记录
        $Account = Account::get($id);
        // 将数据传给V层
        $this->assign('Account', $Account);
        $this->assign('role', $role);
        $this->assign('eid', $eid);
        // 获取封装好的V层内容
        $htmls = $this->fetch();
        // 将封装好的V层内容返回给用户
        return $htmls;
    }
    
    public function insert()
    {
        $Account = new Account;
        $name = Request::instance()->post('name');
        if ($Account->where('name', '=' , $name)->select()) 
        {
            $this->error('此类型已存在，请重新输入');
        }
        $Account->name = $name;
        if($Account->validate()->save())
        {
            return $this->success('添加成功',url('index'));
        }
        else
        {
            return $this->error('添加失败',url('add'));
        }
    }

    public function delete()
    {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); 
        $Stream = new Stream;

        if(!is_null($Stream->where('account_id','=',$id)->select())) 
        {
           $this->error('删除失败，有选择账户的类型');
        }
        if (is_null($id) || 0 === $id)
        {
            return $this->error('未获取到ID信息');
        }
        // 获取要删除的对象
        $Account = Account::get($id);
        // 要删除的对象不存在
        if (is_null($Account))
        {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }
        // 删除对象
        if (!$Account->delete())
        {
            return $this->error('删除失败:' . $Account->getError());
        }
        // 进行跳转
        return $this->success('删除成功', url('index'));
    }
    
    /**
     * 更新数据
     */
    public function update()
    {
        $Account = new Account;
        $name = Request::instance()->post('name');//接收数据
        if ($Account->where('name', '=' , $name)->select()) 
        {
            $this->error('此类型已存在，请重新输入');
        }
        $account = Request::instance()->post();
        if (false === $Account->validate(true)->isUpdate(true)->save($account))
        {
            return $this->error('更新失败' . $Account->getError());
        }
        return $this->success('操作成功', url('index'));
    }
}