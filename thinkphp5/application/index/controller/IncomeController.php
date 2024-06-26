<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Income;
use think\Request;
use app\common\model\User;
use app\common\model\Stream;

class IncomeController extends Controller
{
    public function index()
    {
        try
        {
            $role = User:: role(); //role：角色
            if(User::isLogin()) {
                //如果是1，则是管理员；0就是用户，不可访问
                if($role === 1) {   
                    // 获取查询信息
                    $name = Request::instance()->get('name');
                    echo $name;
                    $pageSize = 10; // 每页显示10条数据
                    // 实例化Income
                    $Income = new Income; 
                    // 定制查询信息
                    if (!empty($name)) {
                        $Income->where('name', 'like', '%' . $name . '%');
                    }
                    // 调用分页
                    $income = $Income->paginate($pageSize);
                    // 向V层传数据
                    $this->assign('Income', $income);
                    $this->assign('role', $role);
                    // 取回打包后的数据
                    $htmls = $this->fetch();
                    // 将数据返回给用户
                    return $htmls;
                }
                return $this->error('你的权限不够', url('homepage_controller/index')); 
            } 
            return $this->error('请登录后在访问', url('login_controller/index'));     
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
            } catch (\think\Exception\HttpResponseException $e) {
              // 获取到正常的异常时，输出异常
            throw $e;
         } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }

    public function add() {
        $Income = new Income;
        $Income->id = 0;
        $Income->name = '';
        $eid = 0;
        $role = User::role();
        if(is_null($eid) && is_null($role)) {
            $this->error('访问失败');
        }
        $this->assign('role', $role);
        $this->assign('eid', $eid);
        $this->assign('Income',  $Income);
        return $this->fetch('edit');
    }

    public function edit() {
        $eid = 1;
        $role = User::role();
        // 获取传入ID
        $id = Request::instance()->param('id/d');

        if (!isset($id)) {
          $this->error('未传入ID');
        }
        if(is_null($eid) && is_null($role)) {
            $this->error('访问失败');
        }
        // 在Income表模型中获取当前记录
        $Income = Income::get($id);
        if(is_null($Income)) {
            $this->error('不存在ID的值');
        }
        // 将数据传给V层
        $this->assign('Income', $Income);
        // 获取封装好的V层内容
        $this->assign('eid', $eid);
        $this->assign('role', $role);
        $htmls = $this->fetch();
        // 将封装好的V层内容返回给用户
        return $htmls;
    }

    public function insert()
    {
        $Income = new Income;
        $name = Request::instance()->post('name');
        if ($Income->where('name', '=' , $name)->select()) 
        {
            $this->error('此类型已存在，请重新输入');
        }
        $Income->name = $name;
        if($this->validate($Income, 'Income') === true) {
            $Income->save();
            return $this->success('添加成功',url('index'));
        } else {
            return $this->error('添加失败,输入的类型不合法',url('add'));
        }
    }

    public function delete() {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形” 
        $Stream = new Stream;
        // var_dump();
        // die();
        if(!empty($Stream->where('income_id','=',$id)->select()))  {
           $this->error('删除失败，有收入选择这个类型');
        }
        if (is_null($id) || 0 === $id) {
            return $this->error('未获取到ID信息');
        }
        // 获取要删除的对象
        $Income = Income::get($id);
        // 要删除的对象不存在
        if (is_null($Income)) {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }
        // 删除对象
        if (!$Income->delete()) {
            return $this->error('删除失败:' . $Income->getError());
        }
        // 进行跳转
        return $this->success('删除成功', url('index'));
    }
    
    public function update() {
        $Income = new Income;
        $name = Request::instance()->post('name');
        if ($Income->where('name', '=' , $name)->select())  {
            $this->error('此类型已存在，请重新输入');
        }
        // 将数据存入Income表
        $income = Request::instance()->post();
        // 依据状态定制提示信息
        
        if (false === $Income->validate(true)->isUpdate(true)->save($income))  {
            return $this->error('更新失败' . $Income->getError());
        }
        return $this->success('操作成功', url('index'));
    }
}