<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\Stream;
use app\common\model\User;
class HomepageController extends Controller{
    public function index(){

       $Stream =new Stream;

       $income = $Stream->where('inandex','=','1')->sum('money');//收入

       $pay = $Stream->where('inandex','=','0')->sum('money');//支出

       $sum =  $income - $pay;


       $incomey = $Stream->whereTime('update_time', 'yesterday')->where('inandex','=','1')->sum('money');
   
       $payy = $Stream->whereTime('update_time', 'yesterday')->where('inandex','=','0')->sum('money');


       
       $incomew = $Stream->whereTime('update_time', 'week')->where('inandex','=','1')->sum('money');
   
       $payw = $Stream->whereTime('update_time', 'week')->where('inandex','=','0')->sum('money');

       $incomem = $Stream->whereTime('update_time', 'month')->where('inandex','=','1')->sum('money');
   
       $paym = $Stream->whereTime('update_time', 'month')->where('inandex','=','0')->sum('money');

       $incomeyear = $Stream->whereTime('update_time', 'year')->where('inandex','=','1')->sum('money');
   
       $payyear = $Stream->whereTime('update_time', 'year')->where('inandex','=','0')->sum('money');


       if(User::isLogin())
       
       {
        $id = session('userId');

        $User = User::get($id);
        
 
        session('perId',$User->getData('permissions'));
 
        $perId = session('perId');
        
        
        $this->assign('perId',$perId);

        $this->assign('incomew', $incomew);

        $this->assign('payw',$payw);

        $this->assign('incomey', $incomey);

        $this->assign('payy',$payy);

        $this->assign('incomeyear', $incomeyear);

        $this->assign('payyear',$payyear);

        $this->assign('incomem', $incomem);

        $this->assign('paym',$paym);

        $this->assign('sum',$sum);

        return $this->fetch();
        
       }
       else
       {
        return $this->error('未登录',url('login_controller/index'));
       }

      
    }
}