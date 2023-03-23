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
            $perId = User::role();
            $this->assign('role',$perId);
            $tid = Request::instance()->param('tid/d');
            $date = Request::instance()->get('date');

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
                $Streams = $Stream->whereTime('create_time',$date)
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
        return $this->success('操作成功', url('homepage_controller/index'));
    }

    public function delete()
    {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形”
        if (is_null($id) || 0 === $id) {
            return $this->error('未获取到ID信息');
        }
        // 获取要删除的对象
        $Stream= Stream::get($id);
        // 要删除的对象不存在
        if (is_null($Stream)) {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }

        // 删除对象
        if (!$Stream->delete()) {
            return $this->error('删除失败:' . $Stream->getError());
        }
         return $this->success('删除成功');
    }


    public function edit() { 
        $id = Request::instance()->param('id/d');
        if (is_null($id)) {
            $this->error('未获取到Id', url('index'));
        }

        $Stream = Stream::get($id);  

        if (is_null(User::role()) && isset($Stream)) {
           $this->error('错误');
        }
        $this->assign('role',User::role());
        $this->assign('Stream',$Stream);
        return $this->fetch();

    }


    public function update()
    {
      $id = Request::instance()->post('id/d');
      $Stream = Stream::get($id);

      if ($this->saveStream($Stream)) {
        return $this->success('修改成功',url('index'));
      }
      else {
        return $this->success('修改失败',url('edit'));
      } 
    }    

    public function indexpay() {
        if (User::isLogin()) {
            //表单传值
            $perId = User::role();
            $this->assign('role',$perId);
            $tid = Request::instance()->param('tid/d');
            $date = Request::instance()->get('date');

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
                $Streams = $Stream->whereTime('create_time',$date)->where('inandex','=','0')
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


    public function indexincome() {
        if (User::isLogin()) {
            //表单传值
            $perId = User::role();
            $this->assign('role',$perId);
            $tid = Request::instance()->param('tid/d');
            $date = Request::instance()->get('date');

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
                $Streams = $Stream->whereTime('create_time',$date)->where('inandex','=','1')
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