<?php
namespace app\common\model;
use think\Model;

class Stream extends Model
{
    /**
     * 输出性别的属性
     * @return string 0支出，1收入
     * 
     */
    public function getInandExAttr($value)
    {
        $status = array('0'=>'支', '1'=>'收');
        $inandex = $status[$value];
        if (isset($inandex))
        {
            return $inandex;
        } 
        else
        {
            return $status[0];
        }
    }

    // 收入类型
    public function Income()
    {
        return $this->belongsTo('Income');
    }

    // 支出类型
    public function Pay()
    {
        return $this->belongsTo('Pay');
    }

    public function Account()
    {
        return $this->belongsTo('Account');
    }
}