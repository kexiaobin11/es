<?php
namespace app\index\controller;
use think\Controller;
class SystemController extends Controller{
    public function index()
    {
        $perId =  session('perId');
        if( $perId ===1){  

        return $this->fetch();
       }
       else{
        return $this->error('权限不够',url('homepage_controller/index'));
        
       }
    }
}