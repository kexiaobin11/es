<?php
namespace app\common\validate;
use think\Validate; // 内置验证类

class User extends Validate{
    protected $rule = ['password'  => 'require|length:6,25'];   
}
     