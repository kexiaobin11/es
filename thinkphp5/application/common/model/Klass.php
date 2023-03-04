<?php
namespace app\common\model;

use think\Model;

class Klass extends Model{
    public function Teacher(){
		return $this->belongsTo('Teacher');
	}
    //Klass->Teacher->name
}