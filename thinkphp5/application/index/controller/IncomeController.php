<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Income;


class IncomeController extends Controller{
    public function index(){
       $Income =new Income;
      $income =  $Income->select();
        $this->assign('Income',$income);
        return $this->fetch();     
    }
    public function add(){
        $Income =new Income;
    }
    public function save(){

    }
}