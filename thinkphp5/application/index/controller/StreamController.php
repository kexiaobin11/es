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

    public function saveStream(Stream $Stream){

        $Stream->inandex = Request::instance()->post('inandex/d');

        $Stream->income_id = Request::instance()->post('income_id/d');

        $Stream->pay_id = Request::instance()->post('pay_id/d');
        
        $Stream->account_id = Request::instance()->post('account_id/d');

        $Stream->money = Request::instance()->post('money/f');

        $Stream->remark = Request::instance()->post('remark');
        
        return $Stream->validate()->save();
    }

    public function index(){
        
        if(User::isLogin()){

            //表单传值
            $perId = User::role();
            $this->assign('perId',$perId);

            $tid = Request::instance()->param('tid/d');

            $date = Request::instance()->get('date');

            if(!isset($tid))
            {
                $this->error('error',url('homepage_controller/index'));
            }

            if($tid === 0)
            {
                    
            if(!isset($date))
                {
                $date = 'yesterday';
                } 

            }
            elseif($tid === 1)
            {
                if(!isset($date))
                {
                    $date = 'week';
                } 
            }
            elseif($tid === 2)
            {
                if(!isset($date))
                {
                    $date = 'month';
                } 
            }
            elseif($tid === 3)
            {
                if(!isset($date))
                {
                    $date = 'year';
                } 
            }

            $this->assign('tid',$tid);

            $Stream = new Stream;

            $pageSize = 6;
            
            $income = $Stream->whereTime('update_time', $date)->where('inandex','=','1')->sum('money');

            $pay= $Stream->whereTime('update_time', $date)->where('inandex','=','0')->sum('money');
            
            
            $remark = Request::instance()->get('remark');

            if(!empty($remark))
            {

                $Stream->whereTime('update_time',$date)->where('remark','like','%' . $remark .'%');
                
            }

            $Streams = $Stream->whereTime('update_time',$date)->paginate($pageSize,false,[
                'query'=>[
                    'remark'=>$remark,
                    'date'=>$date
                    
                ],
                ]);

            $this->assign('incomes', $income);

            $this->assign('pays',  $pay);

            $this->assign('streams', $Streams);

            $this->assign('date', $date);

            return $this->fetch();  

        }
        else{
            
            return $this->error('请登录后在访问',url('login_controller/index')); 

        }
    }




public function add()
    {
        if(User::isLogin()){

            //表单传值
            $perId = User::role();
            $this->assign('perId',$perId);

            $Account = Account::select();

            $aid = Request::instance()->param('aid/d');

            $this->assign('aid',  $aid);
        
            if(!isset($aid) && is_null( $Account))
            {
                $this->error('error',url('homepage_controller/index'));
            }

            $this->assign('Account', $Account);

            $this->assign('aid',  $aid);

            if($aid === 1)
            {

                $Income =Income::select();
                if(is_null($Income))
                {
                    return $this->error('error ',url('homepage_controller/index'));
                }

                $this->assign('Income',  $Income);
        
            }
            else
            {
                $Pay = Pay::select();

                if(is_null($Pay))
                {
                    return $this->error('error ',url('homepage_controller/index'));
                
                }
                $this->assign('Pay',$Pay);

                }
                return $this->fetch();

            }
        else{
            return $this->error('请登录后在访问',url('login_controller/index')); 
        }
         
    }
       


    public function save()
    {
         $Stream = new Stream;

        if(!$this->saveStream($Stream))
        {
            return $this->error('数据添加错误：' . $Stream->getError());
        }
        return $this->success('操作成功', url('homepage_controller/index'));
    }


    public function delete()
    {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形”


        if (is_null($id) || 0 === $id) 
        {
            return $this->error('未获取到ID信息');
        }

        // 获取要删除的对象
        $Stream= Stream::get($id);

        // 要删除的对象不存在
        if (is_null($Stream))
         {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }

        // 删除对象
        if (!$Stream->delete()) 
        {
            return $this->error('删除失败:' . $Stream->getError());
        }
        
        return $this->success('删除成功');
    }

    public function edit()
    {
        
        $id = Request::instance()->param('id/d');
        if(is_null($id))
        {
            $this->error('未获取到Id',url('index'));
        }

        $Stream = Stream::get($id);

        if(isset($Stream))
        {

        }

        $this->assign('Stream',$Stream);

        return $this->fetch();



    }

    public function update()
    {
      $id = Request::instance()->post('id/d');

       $Stream = Stream::get($id);


      if($this->saveStream($Stream))
      {
        return $this->success('修改成功',url('index'));
      }
      else
      {
        return $this->success('修改失败',url('edit'));
      }

      
    }

}