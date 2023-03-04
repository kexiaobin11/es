<?php
namespace app\index\controller;

use app\common\model\Klass;
use think\Request;
use app\common\model\Teacher;

class KlassController extends IndexController{
    private function saveKlass(Klass $Klass,$isUpdate = false){
        $Klass->name = Request::instance()->post('name');
        $Klass->teacher_id = Request::instance()->post('teacher_id/d');
        return $Klass->validate(true)->save();
    }
    public function index(){
        try {
            $pageSize = 5; 
            // 获取查询信息
            // 验证用户是否登录
            $name = Request::instance()->get('name');
            if (!Teacher::isLogin()) {
                return $this->error('plz login first', url('Login_controller/index'));
            }
            $Klass = new Klass; 
            if(!empty($name)){
                $Klass->where('name', 'like', '%' . $name . '%');
            }
            $klasses = $Klass->paginate(5, false, [
                'query' => [
                    'name' => $name,
                ],
            ]);
            $this->assign('klasses', $klasses);
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
    public function add(){  
        $teachers = Teacher::all();
        $Klass = new Klass;
        $Klass->name = '';
        $Klass->teacher_id = 1;
        $this->assign('Klass', $Klass);
        $this->assign('teachers', $teachers);
        return $this->fetch('edit');
    }
    public function delete(){
        $Request = Request::instance();
        $id = Request::instance()->param('id/d');
        if (0 === $id) {
            throw new \Exception('未获取到ID信息', 1);
        }
        $Klass = Klass::get($id);
        if(is_null($Klass)){
            throw new \Exception('不存在id为' . $id . '的辅导员，删除失败', 1);
        }
        if(!$Klass->delete()){
            return $this->error('删除失败', $Klass->getError());
        }else{
            return $this->success('删除成功', $Request->header('referer')); 
        }
    }
    public function edit(){
        // 获取传入ID
        $id = Request::instance()->param('id/d');

        // 获取所有的教师信息
        $teachers = Teacher::all();
        $this->assign('teachers', $teachers);

        // 获取用户操作的班级信息
        if (false === $Klass = Klass::get($id))
        {
            return $this->error('系统未找到ID为' . $id . '的记录');
        }

        $this->assign('Klass', $Klass);
        return $this->fetch();
    }
    public function save(){
        $Request = Request::instance();
        // 实例化班级并赋值
        $Klass = new Klass();
        if(!$this->saveKlass($Klass)){
            return $this->error('更新错误：' . $Klass->getError());
        }else{
            return $this->success('操作成功', url('index'));
        }
    }
    public function update(){
        $id = Request::instance()->post('id/d');
        $Klass = Klass::get($id);
        if (is_null($Klass)) {
            return $this->error('系统未找到ID为' . $id . '的记录');
        }
        if(!$this->saveKlass($Klass)){
            return $this->error('更新错误：' . $Klass->getError());
        }else{
            return $this->success('操作成功', url('index'));
        }
      
    }
   
    public function insert(){
        $postData = Request::instance()->post();    

        // 实例化Teacher空对象
        $Klass = new Klass();

        // 为对象赋值
        $Klass->name = $postData['name'];
        $Klass->teacher_id = $postData['teacher_id'];
        // 新增对象至数据表
        $result = $Klass->validate(true)->save($Klass->getData());

        // 反馈结果
        if (false === $result)
        {
            return '新增失败:' . $Klass->getError();
        } else {
            return  $this->success('添加成功成功',url('index'),1);
        }
    }
}