<?php
namespace app\common\validate;
use think\Validate; 

class User extends Validate
{
    protected $rule = [
        'username' => 'require|unique:user|length:4,25',
        'name' => 'require|length:2,25',
        'password'=>'require|length:6,18'
    ];
}