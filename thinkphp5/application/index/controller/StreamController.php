<?php
namespace app\index\controller;
use app\common\model\Stream;
use app\common\model\Account;
use app\common\model\Income;
use app\common\model\Pay;
use think\Controller;
use think\Request;
class StreamController extends Controller{

    public function saveStream(Stream $Stream){

        $Stream->inandex = Request::instance()->post('inandex/d');

        $Stream->income_id = Request::instance()->post('income_id/d');

        $Stream->pay_id = Request::instance()->post('pay_id/d');
        
        $Stream->account_id = Request::instance()->post('account_id/d');

        $Stream->money = Request::instance()->post('money/d');

        $Stream->remark = Request::instance()->post('remark');
        
        return $Stream->validate()->save();
    }

    public function index(){
        try {

        $remark = Request::instance()->get('remark');


        $pageSize = 5;

        $Stream = new Stream;

        if(!empty($remark))
        {

            $Stream->where('remark','like','%' . $remark .'%');
            
        }
        $Streams = $Stream->paginate($pageSize,false,[
            'query'=>[
                'remark'=>$remark
            ],
            ]);

            $this->assign('streams', $Streams);
       
            return $this->fetch();  
            }

    catch (\think\Exception\HttpResponseException $e) 
    {
        throw $e;
    // 获取到正常的异常时，输出异常
    } 
    catch (\Exception $e) 
    {
        return $e->getMessage();
    } 
}


    public function add()
    {
        $Account = Account::select();

        $Income =Income::select();

        $this->assign('Account', $Account);

        $this->assign('Income', $Income);

        return $this->fetch();

    }


    public function save()
    {
        $Stream = new Stream;

        if(!$this->saveStream($Stream))
        {
            return $this->error('数据添加错误：' . $Stream->getError());
        }
        return $this->success('操作成功', url('index'));
    }

    public function addpay()
    {

        $Account = Account::select();

        $Pay = Pay::select();

        if(is_null($Pay) && is_null( $Account))
        {
            return $this->error('error ',url('homepage_controller/index'));
        }
        else
        { 
            $this->assign('Account', $Account);

            $this->assign('Pay',  $Pay);
            
            return $this->fetch();

        }

       

      

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

        // 进行跳转
        return $this->success('删除成功', url('index'));
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

}