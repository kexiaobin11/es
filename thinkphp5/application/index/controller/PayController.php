<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Pay;
use think\Request;
use app\common\model\User;
use app\common\model\Stream;

class PayController extends Controller{
    public function index()
    {
        try 
        {
            $role = User::role(); //role：角色
            if(User::isLogin())
            {
                //如果是1，则是管理员；0就是用户，不可访问
                if($role === 1) {
                    // 获取查询信息
                    $name = Request::instance()->get('name');
                    $pageSize = 10; // 每页显示10条数据
                    // 实例化Income
                    $Pay = new Pay; 
                    // 定制查询信息
                    if (!empty($name)) {
                        $Pay->where('name', 'like', '%' . $name . '%');
                    }
                    // 调用分页
                    $pay = $Pay->paginate($pageSize);
                    // 向V层传数据
                    $this->assign('Pay', $pay); 
                    $this->assign('role',$role);
                    // 取回打包后的数据
                    $htmls = $this->fetch();
                    // 将数据返回给用户
                    return $htmls;
                }  
                return $this->error('你的权限不够',url('homepage_controller/index')); 
            }  
            return $this->error('请登录后在访问',url('login_controller/index'));     
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } 
        catch (\think\Exception\HttpResponseException $e)  {
            // 获取到正常的异常时，输出异常
            throw $e;
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }
    
    public function add() {  
        $eid = 0; 
        $Pay = new Pay;
        $Pay->id = '';
        $Pay->name = '';
        $role = User::role();

        if(is_null($eid) && is_null($role)) {
            $this->error('访问失败');
        }
        $this->assign('role',$role);
        $this->assign('eid',$eid);  
        $this->assign('Pay',$Pay);

        return $this->fetch('edit');
    }
    
    public function insert() {
        $Pay = new Pay;
        $name = Request::instance()->post('name');
        if ($Pay->where('name', '=' , $name)->select()) {
            $this->error('此类型已存在，请重新输入');
        }
       
        $Pay->name = $name;
        if($this->validate($Pay, 'Pay') === true) {
            $Pay->save();
            return $this->success('添加成功',url('index'));
        } else {
            return $this->error('添加失败，输入的数据不合法',url('add'));
        }
    }

    public function edit() {
        // 获取传入ID
          $id = Request::instance()->param('id/d');
          $role = User::role();
          $eid = 0;
          if (!isset($id)) {
            $this->error('未传入ID');
          }
          if(is_null($eid) && is_null($role)) {
            $this->error('访问失败');
          }
          $Pay = Pay::get($id);
          
          if(is_null ($Pay)) {
              $this->error('不存在ID的值');
          }
         $this->assign('eid',$eid);
         $this->assign('Pay', $Pay);
         $this->assign('role',$role);
         $role = User::role();
         $htmls = $this->fetch();
         return $htmls;
    }

    public function delete() {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形”
        $Stream = new Stream;
       
       if(!empty($Stream->where('pay_id','=',$id)->select())) {
          $this->error('删除失败，有支出选择这个类型');
       }

        if (is_null($id) || 0 === $id)  {
            return $this->error('未获取到ID信息');
        }
        $Pay = Pay::get($id);
        if (is_null($Pay))  {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }
        if (!$Pay->delete()) {
            return $this->error('删除失败:' . $Pay->getError());
        }
        return $this->success('删除成功', url('index'));
    }

    public function update()
    {
        $Pay = new Pay;
        $name = Request::instance()->post('name');
        if ($Pay->where('name', '=' , $name)->select()) 
        {
            $this->error('此类型已存在，请重新输入');
        }
        // 将数据存入Pay表
        $pay = Request::instance()->post();
        // 依据状态定制提示信息
        if (false === $Pay->validate(true)->isUpdate(true)->save($pay)) {
            return $this->error('更新失败' . $Pay->getError());
        }
        return $this->success('操作成功', url('index'));
    }
}