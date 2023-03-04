<?php
namespace app\index\controller;
use app\common\model\Course; 
use app\common\model\Teacher;
use think\Request;
use app\common\model\KlassCourse;
use app\index\controller;   

class CourseController extends IndexController
{
    private function saveCourse(Course $Course,$isUpdate = false){
        $Course->name = Request::instance()->post('name');
        return $Course->validate(true)->save();
    }
    public function index()
    {
        try{
            $pageSize = 5;
            $name = Request::instance()->get('name');
            if(!Teacher::isLogin()){
                return $this->error('plz login first', url('Login_controller/index'));
            }
            $Course = new Course;
            if(!empty($name)){
                $Course->where('name', 'like', '%' . $name . '%');
            }
            $Courses = $Course->paginate($pageSize, false, [
                'query' => [
                    'name' => $name,
                ]
            ]);
            $this->assign('Courses', $Courses);
            return $this->fetch();
        }catch (\think\Exception\HttpResponseException $e) {
            throw $e;
        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }

    public function add()
    {
        $Course = new Course;
        $Course->name = '';
        $Course->id = 0;
        $this->assign('Course', $Course);
        return $this->fetch('edit');
    }
    public function save(){
        $Course = new Course();
        $Course->name = Request::instance()->post('name');
        if(!$Course->validate(true)->save()){
            return $this->error('保存错误' . $Course->getError());
        }
         // 接收klass_id这个数组
        $klassIds = Request::instance()->post('klass_id/a');
         // 利用klass_id这个数组，拼接为包括klass_id和course_id的二维数组。
        if(!is_null($klassIds)){
            if(!$Course->Klasses()->saveAll($klassIds)){
                return $this->error('课程-班级信息保留错误' . $Course->Klasses()->getError());
            }
        }
        return $this->success('操作成功', url('index'));
    }
    public function edit(){
        $id = Request::instance()->param('id/d');
        if (is_null($id) || 0 === $id) {
            throw new \Exception('未获取到ID信息', 1);
        }
        $Course = Course::get($id);
        if(is_null($Course)){
            return $this->error('不存在' . $id . '的记录');
        }

        $this->assign('Course', $Course);
        return $this->fetch();
    }
    public function update(){
        $id = Request::instance()->param('id/d');
        $Course = Course::get($id);
        if (is_null($Course)) {
            return $this->error('不存在ID为' . $id . '的记录');
        }
         // 更新课程名
        if(!$this->saveCourse($Course)){
            return $this->error('课程信息更新失败：' . $Course->getError());
        }
        // 删除原有信息
        $map = ['course_id' => $id];
        //把原有的Id 删除，查询班级课程表进行删除
        if(false===$Course->KlassCourse()->where($map)->delete){
            return $this->error('删除班级课程关联发生错误' . $Course->KlassCourses()->getError());
        }
        $klassIds = Request::instance()->post('klass_id/a');//a表示为数组
        if (!is_null($klassIds)) {
            if (!$Course->Klasses()->saveAll($klassIds)) {
                return $this->error('课程-班级信息保存错误：' . $Course->Klasses()->getError());
            }
        }
        return $this->success('更新成功', url('index'));
    }
    public function delete(){
        $Request = Request::instance();
        $id = Request::instance()->param('id/d');
        $Course = Course::get($id);
        if(is_null($Course)){
            throw new \Exception('不存在id为' . $id . '的班级，删除失败', 1);
        }
        if(!$Course->delete()){
            return $this->error('删除班级失败：' . $Course->getError());
        }else{
            return $this->success('删除成功' . $Request->header('referer'));
        }
    }
 
}