<?php
namespace app\index\controller;
use app\common\model\Stream;
use app\common\model\Account;
use app\common\model\Income;
use app\common\model\Pay;
use app\common\model\User;
use think\Controller;
use think\Request;
use think\Db;
class StreamController extends Controller{

    public function saveStream(Stream $Stream) {
        $Stream->inandex = Request::instance()->post('inandex/d');
        $Stream->income_id = Request::instance()->post('income_id/d');
        $Stream->pay_id = Request::instance()->post('pay_id/d');
        $Stream->account_id = Request::instance()->post('account_id/d');
        $Stream->money = Request::instance()->post('money/f');
        $Stream->remark = Request::instance()->post('remark');        
        return $Stream->validate()->save();
    }

    public function index()
    {
        if (User::isLogin()) {
            //表单传值
            $role = User::role();
            $this->assign('role',$role);
            $tid = Request::instance()->param('tid/d');
            $date = Request::instance()->param('date');
            if (!isset($tid)) {
                $this->error('error',url('homepage_controller/index'));
            }

            if($tid === 0) {           
                if (!isset($date)) {
                    $date = 'yesterday';
                }
                if ( $date === 'yesterday') {
                        $start_time=date('Y-m-d', strtotime('-1 day'));
                        $end_time=date('Y-m-d', strtotime('-1 day'));
                 } else {
                    $start_time=date('Y-m-d', strtotime(date('Y-m-d')));
                    $end_time=date('Y-m-d', strtotime(date('Y-m-d')));
                 }
              }  elseif ($tid === 1) {
                if(!isset($date)) {
                    $date = 'week';
                } 
                if ($date === 'week') {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")+6,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);
                } else {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")-6,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")-1,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);      
                } 
            } elseif ($tid === 2) {
                if(!isset($date)) {
                    $date = 'month';
                }
                if ($date === 'month') {
                    $start_time = date('Y-m-1');   
                    $end_time  = date('Y-m-d',strtotime(date('Y-m-1',strtotime('next month')).'-1 day'));
                } else {
                    $start_time = date('Y-m-1',strtotime('last month'));        
                    $end_time = date('Y-m-d',strtotime(date('Y-m-1').'-1 day'));
                }
              }
                elseif ($tid === 3) {
                    if (!isset($date)) {
                        $date = 'year';
                    } 
                    if ($date === 'year') {
                        $start_time = date('Y-m-d',strtotime(date('Y-1-1 00:00:00',time())));
                        $end_time = date('Y-12-31');
                    } 
                     else {
                        $start_time =date('Y-m-d',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                        $end_time = date('Y-12-31',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                    }
                  }
                  //传入时间的值，是 0 显示当天和昨天、1 显示本周和上周、2 显示本周和上周 3 显示本月和上月  4 显示本年和去年
                    $this->assign('tid',$tid);
                    $Stream = new Stream;
                    //页面数
                    $pageSize = 10;
                    $income = $Stream->whereTime('create_time', $date)->where('inandex','=','1')->sum('money');
                    $pay = $Stream->whereTime('create_time', $date)->where('inandex','=','0')->sum('money');
                 
                    /**
                     * 
                     *
                     * @var 金额千位分割符
                     */
                    $income = number_format( $income, 2, '.', ',');
                    $pay= number_format( $pay, 2, '.', ',');     

                    $remark = Request::instance()->get('remark');
                     
                if (!empty($remark)) {
                    $Stream->whereTime('create_time',$date)->where('remark','like','%' . $remark .'%');
                }
                $Streams = $Stream->order('id DESC')->whereTime('create_time',$date)
                             ->paginate($pageSize,false,[   
                                 'query' => [
                                            'remark' => $remark,
                                            'date' => $date     
                                            ],
                                   ]); 
                $this->assign('incomes', $income);
                $this->assign('pays',  $pay);
                $this->assign('streams', $Streams);
                $this->assign('date', $date);
                $this->assign('start_time', $start_time);
                $this->assign('end_time', $end_time); 
                return $this->fetch(); 
        } 
        else {
            return $this->error('请登录后在访问', url('login_controller/index')); 
        }
       
    }


        public function add()
         {
            if (User::isLogin()) {
                //表单传值
                $role = User::role();
                $this->assign('role', $role);
                $Account = Account::select();
                $aid = Request::instance()->param('aid/d');
                $this->assign('aid',  $aid);

                if (!isset($aid) && is_null( $Account)) {
                    $this->error('添加失败', url('homepage_controller/index'));
                }
                $this->assign('Account', $Account);
                $this->assign('aid',  $aid);

                if($aid === 1) {
                    $Income =Income::select();
                    if (is_null($Income)) {
                        return $this->error('添加失败', url('homepage_controller/index'));
                    }
                    $this->assign('Income',  $Income);
                  }
                     else {
                          $Pay = Pay::select();
                          if (is_null($Pay)) {
                             return $this->error('添加失败', url('homepage_controller/index'));
                          }
                          $this->assign('Pay',$Pay);
                      }
                   return $this->fetch();

                }
                 else {
                return $this->error('请登录后在访问', url('login_controller/index')); 
                }    
        }

    public function save()
    {
        $Stream = new Stream;
        if (!$this->saveStream($Stream)) {
            return $this->error('数据添加错误：' . $Stream->getError());
        }
        return $this->success('操作成功', url('bill_controller/index'));
    }


    
    /**
     * 获取支付的信息
     * @param tid 周期（天、星期、月、年）
     * @param date 天（今天、昨天、本周、上周、本月、上月、本年、去年）
     * @param sid 判断今天还是昨天
     */
    public function indexpay() {
        if (User::isLogin()) {
            //表单传值
            $role = User::role();
            $this->assign('role',$role);
            $tid = Request::instance()->param('tid/d');
            $date = Request::instance()->param('date');
            $sid = Request::instance()->param('sid/d');
            
            //如果tid没有接收到，后面也就不需要执行了
            if (!isset($tid)) {
                $this->error('访问出错',url('homepage_controller/index'));
            }
            //判断$date是否有数据,第一次访问不会有数据
            if(isset($date)) {
                $sid = 0;
            }
            //判断时候是不是从今天收入访问的
            if($sid === 1 ) {
              $date = 'today';   
            }

            if($tid === 0) {           
                if (!isset($date)) {
                    $date = 'yesterday';
                }
                 if ( $date === 'yesterday') {
                        $start_time=date('Y-m-d', strtotime('-1 day'));
                        $end_time=date('Y-m-d', strtotime('-1 day'));
                 }
                  else {
                        $start_time=date('Y-m-d', strtotime(date('Y-m-d')));
                        $end_time=date('Y-m-d', strtotime(date('Y-m-d')));
                 }
              } 
              elseif ($tid === 1) {
                if(!isset($date)) {
                    $date = 'week';
                } 
                if ($date === 'week') {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")+6,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);
                }
                 else {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")-6,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")-1,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);      
                } 
            }
             elseif ($tid === 2) {
                if(!isset($date)) {
                        $date = 'month';
                    }
                if ($date === 'month') {
                        $start_time = date('Y-m-1');   
                        $end_time  = date('Y-m-d',strtotime(date('Y-m-1',strtotime('next month')).'-1 day'));
                } else {
                      $start_time = date('Y-m-1',strtotime('last month'));        
                      $end_time = date('Y-m-d',strtotime(date('Y-m-1').'-1 day'));
                   }
              }
                elseif ($tid === 3) {
                    if (!isset($date)) {
                        $date = 'year';
                    } 
                    if ($date === 'year') {
                        $start_time = date('Y-m-d',strtotime(date('Y-1-1 00:00:00',time())));
                        $end_time = date('Y-12-31');
                    } 
                     else {
                        $start_time =date('Y-m-d',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                        $end_time = date('Y-12-31',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                    }
                  }
                  //传入时间的值，是 0 显示当天和昨天、1 显示本周和上周、2 显示本周和上周 3 显示本月和上月  4 显示本年和去年
                    $this->assign('tid',$tid);
                    $Stream = new Stream;
                    //页面数
                    $pageSize = 10;
                   
                    $pay = $Stream->whereTime('create_time', $date)->where('inandex','=','0')->sum('money');

                    /**
                     * 
                     *
                     * @var 金额千位分割符
                     */
                    $pay= number_format( $pay, 2, '.', ',');     

                    $remark = Request::instance()->get('remark');
                if (!empty($remark)) {
                    $Stream->whereTime('create_time',$date)->where('remark','like','%' . $remark .'%');
                }
                $Streams = $Stream->order('id DESC')->whereTime('create_time',$date)->where('inandex','=','0')
                             ->paginate($pageSize,false,[   
                                 'query' => [
                                            'remark' => $remark,
                                            'date' => $date     
                                            ],
                                   ]); 
             
                $this->assign('pays',  $pay);
                $this->assign('streams', $Streams);
                $this->assign('date', $date);
                $this->assign('start_time', $start_time);
                $this->assign('end_time', $end_time);
                return $this->fetch();  
        } 
        else {
            return $this->error('请登录后在访问', url('login_controller/index')); 
        }
    }

    /**
     * 获取收入信息
     * @param tid 周期（天、星期、月、年）
     * @param date 天（今天、昨天、本周、上周、本月、上月、本年、去年）
     * @param sid 判断今天还是昨天
     */
    public function indexincome() {
        if (User::isLogin()) {
            //表单传值
            $role = User::role();
            $this->assign('role',$role);
            $tid = Request::instance()->param('tid/d');
            $sid = Request::instance()->param('sid/d');
            $date = Request::instance()->param('date');

            //如果tid没有接收到，后面也就不需要执行了
            if (!isset($tid)) {
                $this->error('访问出错',url('homepage_controller/index'));
            }
            //判断$date是否有数据,第一次访问不会有数据
            if(isset($date)) {
                $sid = 0;
            }
            //判断时候是不是从今天收入访问的
            if($sid === 1 ) {
              $date = 'today';   
            }

            if($tid === 0) {           
                if (!isset($date) ) {
                    $date = 'yesterday';
                }
                 if ( $date === 'yesterday') {
                        $start_time=date('Y-m-d', strtotime('-1 day'));
                        $end_time=date('Y-m-d', strtotime('-1 day'));
                 }
                  else {
                        $date = 'today';  
                        $start_time=date('Y-m-d', strtotime(date('Y-m-d')));
                        $end_time=date('Y-m-d', strtotime(date('Y-m-d')));
                 }
              } 
              elseif ($tid === 1) {
                if(!isset($date)) {
                    $date = 'week';
                } 
                if ($date === 'week') {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")+6,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);
                }
                 else {
                    $start_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")-6,date("Y"));
                    $end_time   = mktime(23,59,60,date("m"),date("d")-date("w")-1,date("Y"));
                    $start_time = date('Y-m-d',$start_time);
                    $end_time = date('Y-m-d', $end_time);      
                } 
            }
             elseif ($tid === 2) {
                if(!isset($date)) {
                        $date = 'month';
                    }
                if ($date === 'month') {
                        $start_time = date('Y-m-1');   
                        $end_time  = date('Y-m-d',strtotime(date('Y-m-1',strtotime('next month')).'-1 day'));
                } else {
                      $start_time = date('Y-m-1',strtotime('last month'));        
                      $end_time = date('Y-m-d',strtotime(date('Y-m-1').'-1 day'));
                   }
              }
                elseif ($tid === 3) {
                    if (!isset($date)) {
                        $date = 'year';
                    } 
                    if ($date === 'year') {
                        $start_time = date('Y-m-d',strtotime(date('Y-1-1 00:00:00',time())));
                        $end_time = date('Y-12-31');
                    } 
                     else {
                        $start_time =date('Y-m-d',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                        $end_time = date('Y-12-31',strtotime(date('Y-1-1 00:00:00',strtotime('-1 year'))));
                    }
                }
                  //传入时间的值，是 0 显示当天和昨天、1 显示本周和上周、2 显示本周和上周 3 显示本月和上月  4 显示本年和去年
                    $this->assign('tid',$tid);
                    $Stream = new Stream;
                    //页面数
                    $pageSize = 10;
                   
                    $income = $Stream->whereTime('create_time', $date)->where('inandex','=','1')->sum('money');
                   

                    /**
                     * 
                     *
                     * @var 金额千位分割符
                     */
                    $remark = Request::instance()->get('remark');
                if (!empty($remark)) {
                    $Stream->whereTime('create_time',$date)->where('remark','like','%' . $remark .'%');
                }
                $Streams = $Stream->order('id DESC')->whereTime('create_time',$date)->where('inandex','=','1')
                             ->paginate($pageSize,false,[   
                                 'query' => [
                                            'remark' => $remark,
                                            'date' => $date     
                                            ],
                                   ]); 
             
                $this->assign('incomes',  $income);
                $this->assign('streams', $Streams);
                $this->assign('date', $date);
                $this->assign('start_time', $start_time);
                $this->assign('end_time', $end_time);
                return $this->fetch();  
        } 
        else {
            return $this->error('请登录后在访问', url('login_controller/index')); 
        }
    }
}