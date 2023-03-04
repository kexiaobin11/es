<?php
namespace app\index\controller;
use app\common\model\Student;
use Random\RandomError;
use think\Request;
use app\common\model\Klass;
use app\common\model\Teacher;
class StudentController extends IndexController
{
    private function saveStudent(Student $Student,$isUpdate = false){
        $Student->name = Request::instance()->post('name');
        $Student->num  = Request::instance()->post('num');
        $Student->sex = Request::instance()->post('sex');
        $Student->klass_id = Request::instance()->post('klass_id');
        $Student->email = Request::instance()->post('email');
        return $Student->validate()->save();
    }
    public function index()
    {
        
        try {
            $pageSize = 5; // 每页显示5条数据
            // 获取查询信息
            // 验证用户是否登录
            $name = Request::instance()->get('name');
            if (!Teacher::isLogin()) {
                return $this->error('plz login first', url('Login_controller/index'));
            }
            $name = input('get.name');
            $size = (int) $pageSize;
            $Student = new Student; 
            trace($Student, 'debug');
            if(!empty($name)){
                $Student->where('name', 'like', '%' . $name . '%');
            }
            // 调用分页
            $students = $Student->paginate($size, false, [
                'query' => [
                    'name' => $name,
                ],
            ]);
            // 向V层传数据
            $this->assign('students', $students);
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
    public function edit(){
        $id = Request::instance()->param('id/d');
        if(is_null($Student = Student::get($id))){
            return $this->error('未找到ID为' . $id . '的记录');
        }
         // 取出班级列表
         $this->assign('Student', $Student);
         return $this->fetch();
    }
    public function save(){
        $Request = Request::instance();
        // 实例化班级并赋值
        $Student = new Student();
      
        if (!$this->saveStudent($Student)) {
            return $this->error('数据添加错误：' . $Student->getError());
        }
        return $this->success('操作成功', url('index'));
    }
    public function update(){
        $id = Request::instance()->param('id/d');
        $Student = Student::get($id);
        if(is_null($Student)){
            return $this->error('系统未找到ID为' . $id . '的记录');
        }
       
        if(!$this->saveStudent($Student)){
            return $this->error('更新错误：' . $Student->getError());
        }else{
            return $this->success('更新成功', url('index'));
        }
    }
    public function add(){
        $Student =new Student;
        $Student->id = 0;
        $Student->name = '';
        $Student->num = '';
        $Student->sex = 0;
        $Student->klass_id = 1;
        $Student->email = '';
        $this->assign('Student', $Student);
        return $this->fetch('edit');
    }
    public function delete(){
        $Request = Request::instance();
        $id = Request::instance()->param('id/d');
        if (0 === $id) {
            throw new \Exception('未获取到ID信息', 1);
        }
        $Student = Student::get($id);
        if(is_null($Student)){
            throw new \Exception('不存在id为' . $id . '的辅导员，删除失败', 1);
        }
        if (!$Student->delete()) {
            return $this->error('删除失败:' . $Student->getError());
        }else{
            return $this->success('删除成功', url('index'));
        }
    }
}