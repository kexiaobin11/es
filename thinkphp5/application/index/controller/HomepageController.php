<?php
namespace app\index\controller;
use think\Controller;
use app\common\model\Stream;
use app\common\model\User;
class HomepageController extends Controller
{
    public function index()
    {
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

       $TodayIncomeSum = $Stream->whereTime('create_time', 'today')->where('inandex','=','1')->sum('money');//今天的总收入
       $TodayPaySum = $Stream->whereTime('create_time', 'today')->where('inandex','=','0')->sum('money');//今天的总支出
       $TodayIncomeSum = number_format($TodayIncomeSum, 2, '.', ',');
       $TodayPaySum = number_format($TodayPaySum, 2, '.', ',');

       $income = number_format($income, 2, '.', ',');
       $pay = number_format($pay, 2, '.', ',');
       $sum =  number_format( $sum, 2, '.', ',');
       $incomey =  number_format($incomey, 2, '.', ',');
       $payy =  number_format($payy, 2, '.', ',');
       $incomew  =  number_format($incomew , 2, '.', ',');
       $payw =  number_format($payw, 2, '.', ',');
       $incomem =  number_format($incomem, 2, '.', ',');
       $paym =  number_format($paym, 2, '.', ',');
       $incomeyear = number_format( $incomeyear, 2, '.', ',');
       $payyear =  number_format($payyear, 2, '.', ',');
       if(User::isLogin())
       {
        $role = User::role();    
        $this->assign('role',$role);
        $this->assign('incomew', $incomew);
        $this->assign('payw',$payw);
        $this->assign('incomey', $incomey);
        $this->assign('payy',$payy);
        $this->assign('incomeyear', $incomeyear);
        $this->assign('payyear',$payyear);
        $this->assign('incomem', $incomem);
        $this->assign('paym',$paym);
        $this->assign('sum',$sum);
        $this->assign('TodayIncomeSum',$TodayIncomeSum);
        $this->assign('TodayPaySum',$TodayPaySum);
        return $this->fetch();       
       }
       else
       {
        return $this->error('请登录在访问',url('login_controller/index'));
       }
    }


}