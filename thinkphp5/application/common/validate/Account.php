<?php
namespace app\common\validate;
use think\Validate;     // 内置验证类

class Account extends Validate
{
    protected $rule = [
        'name'  => 'require|length:1,10',    
    ];
}