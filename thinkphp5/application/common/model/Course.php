<?php
namespace app\common\model;
use think\Model;
class Course extends Model{
    public function Klasses(){
    return $this->belongsToMany('Klass', config('database.prefix') . 'klass_course');
    //表示 Klass 和 klass_course
    //有了这个多对多关联的Klasses()，在进行查找操作时，它会自动的对klass表进行操作；
    //在进行数据插入、更新操作时，它又会自动对中间表klass_course进行操作。
    }
    public function getIsChecked(Klass $klass){
        $courseId = (int) $this->id;//Id  = courseId;
        $klassId = (int) $klass->id;
        $map = array();
        $map['klass_id'] = $klassId;
        $map['course_id'] = $courseId;
        //判断有没有这个课程班级信息
        $KlassCourse = KlassCourse::get($map);
        if(is_null($KlassCourse)){
            return false;
        }else{
            return true;
        }
    }
    public function KlassCourse(){
        //一个课程有很多课程班级
       // 数据之间的关系一对多，比如：一个课程有多个课程班级。
        return $this->hasMany('KlassCourse');
    }
}