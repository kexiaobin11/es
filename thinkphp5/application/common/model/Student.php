<?php
namespace app\common\model;

use think\Model;


class Student extends Model{
    public function getSexAttr($value){
        $status = array('0' => '男', '1' => '女');
        $sex = $status[$value];
        if(isset($sex)){
            return $sex;
        }else{
            return $status[0];
        }
    }
    protected $dateFormat = 'Y年m月d日';

    protected $type = [
        'create_time' => 'datetime',
    ];
    public function Klass()
    {
        return $this->belongsTo('Klass');
    }
    static public function logout(){
        session('teacherId', null);
        return true;
    }
 
}