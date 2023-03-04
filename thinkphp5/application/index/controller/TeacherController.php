<?php
namespace app\index\controller; 
use think\Controller;   
use app\common\model\Teacher;
use think\Exception;
use think\Request;
// 教师模型
/**
 * 教师管理
 */
class TeacherController extends  IndexController
{
    private function saveTeacher(Teacher $Teacher,$isUpdate = false){
        $Teacher->name = Request::instance()->post('name');
        if(!$isUpdate){
            $Teacher->username = Request::instance()->post('username');
        }   
        $Teacher->sex = input('post.sex/d');
        $Teacher->email = input('post.email');
        // 更新或保存
        return $Teacher->validate(true)->save();
    }
    public function index()
    {
        try {
            $pageSize = 5; // 每页显示5条数据
            // 获取查询信息
            // 验证用户是否登录
            $name = Request::instance()->get('name');
//            if (!Teacher::isLogin()) {
//                return $this->error('plz login first', url('Login_controller/index'));
//            }
            $name = input('get.name');
            $size = (int) $pageSize;
            $Teacher = new Teacher;
            trace($Teacher, 'debug');
            if(!empty($name)){
                $Teacher->where('name', 'like', '%' . $name . '%');
            }
            // 调用分页
            $teachers = $Teacher->paginate($size, false, [
                'query' => [
                    'name' => $name,
                ],
            ]);
            // 向V层传数据
            $this->assign('teachers', $teachers);
            // 取回打包后的数据
            $htmls = $this->fetch();
            // 将数据返回给用户
            return $htmls;
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;
        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
     
    }
   
    public function add()
    {
        // 实例化
        $Teacher = new Teacher;
        // 传入默认值设置默认值
        $Teacher->id = 0;
        $Teacher->name = '';
        $Teacher->username = '';
        $Teacher->sex = 0;
        $Teacher->email = '';
        $this->assign('Teacher', $Teacher);
        // 调用edit模板
        return $this->fetch('edit');
    }
    public function edit(){
        $id = Request::instance()->param('id/d');
        if(is_null($Teacher = Teacher::get($id))){
            return $this->error('未找到ID为' . $id . '的记录');
        }
         // 取出班级列表
         $this->assign('Teacher', $Teacher);
         return $this->fetch();
    }

    public function delete()
    {
        try {
            // 实例化请求类
            $Request = Request::instance();
            // 获取get数据
            $id = Request::instance()->param('id/d');
            // 判断是否成功接收
            if (0 === $id) {
                throw new \Exception('未获取到ID信息', 1);
            }
            // 获取要删除的对象
            $Teacher = Teacher::get($id);
            // 要删除的对象存在
            if (is_null($Teacher)) {
                throw new \Exception('不存在id为' . $id . '的教师，删除失败', 1);
            }
            // 删除对象
            if (!$Teacher->delete()) {
                return $this->error('删除失败:' . $Teacher->getError());
            }
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;
        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
        // 进行跳转 ,跳转回原来的页面
        return $this->success('删除成功', $Request->header('referer')); 
    }

    public function update()
    {
        // 接收数据，取要更新的关键字信息
        $id = Request::instance()->post('id/d');

        // 获取当前对象
        $Teacher = Teacher::get($id);
       
        if (!is_null($Teacher)) {
            if(!$this->saveTeacher($Teacher,true)){
                return $this->error('操作失败' . $Teacher->getError());
            }
        } else {
            return $this->error('当前操作的记录不存在');
        }
    
        // 成功跳转至index触发器
        return $this->success('操作成功', url('index'));
    }
    public function save(){
        $Teacher = new Teacher;
        if (!$this->saveTeacher($Teacher)) {
            return $this->error('操作失败' . $Teacher->getError());
        }
        return $this->success('操作成功', url('index'));
    
    }
    public function atrr(){
        $id = Request::instance()->param('id/d');
       if(0===$id||is_null($id)){
        throw new \Exception('未获取到ID信息', 1);
       }
       if (null === $Teacher = Teacher::get($id))
       {
           $this->error('系统未找到ID为' . $id . '的记录');
       }
        $this->assign('Teacher', $Teacher);
        $htms =  $this->fetch();
        return $htms;   
    }
   
    
    public function test(){
    }
}