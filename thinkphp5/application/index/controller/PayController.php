<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Pay;
use think\Request;
use app\common\model\User;

class PayController extends Controller{
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
                    $pageSize = 3; // 每页显示5条数据
                    // 实例化Income
                    $Pay = new Pay; 
                    // 定制查询信息
                    if (!empty($name))
                    {
                        $Pay->where('name', 'like', '%' . $name . '%');
                    }
                    // 调用分页
                    $pay = $Pay->paginate($pageSize);
                    // 向V层传数据
                    $this->assign('Pay', $pay);
                    // 取回打包后的数据
                    $htmls = $this->fetch();

                    // 将数据返回给用户
                    return $htmls;
                }
                else
                {
                    return $this->error('你的权限不够',url('homepage_controller/index')); 
                }
            }
            else
            {
                return $this->error('请登录后在访问',url('login_controller/index'));      
            }
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } 
        catch (\think\Exception\HttpResponseException $e)
        {
            throw $e;
        // 获取到正常的异常时，输出异常
        } 
        catch (\Exception $e) 
        {
            return $e->getMessage();
        } 
    }
    
    public function add()
    {  
        return $this->fetch();
    }
    
    public function insert()
    {
        $postData = $this->request->post();//接受传入的数据       
        $Pay = new Pay();//空对象     
        $Pay->name = $postData['name'];
        $Pay->create_time = $postData['create_time'];
        $Pay->save();
        // 反馈结果
        return $this->success('添加成功', url('pay_controller/index'));
    }

    public function edit()
    {
        // 获取传入ID
        $id = Request::instance()->param('id/d');
        // 在Income表模型中获取当前记录
        $Pay = Pay::get($id);
        // 将数据传给V层
        $this->assign('Pay', $Pay);
        // 获取封装好的V层内容
        $htmls = $this->fetch();
        // 将封装好的V层内容返回给用户
        return $htmls;
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
        $Pay = Pay::get($id);
        // 要删除的对象不存在
        if (is_null($Pay))
        {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }
        // 删除对象
        if (!$Pay->delete())
        {
            return $this->error('删除失败:' . $Pay->getError());
        }
        // 进行跳转
        return $this->success('删除成功', url('index'));
    }

    public function update()
    {
        // 接收数据
        $pay = Request::instance()->post();
        // 将数据存入Pay表
        $Pay = new Pay();
        // 依据状态定制提示信息
        if (false === $Pay->validate(true)->isUpdate(true)->save($pay)) {
            return $this->error('更新失败' . $Pay->getError());
        }
        return $this->success('操作成功', url('index'));
    }
}