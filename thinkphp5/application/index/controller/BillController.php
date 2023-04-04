<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Account;
use app\common\model\Stream;
use app\index\controller\StreamController;
use app\common\model\User;
use think\Request;
class BillController extends Controller {
    public function index() {
        try
        {
            // 获取支付类型中的所有数据
            $Account = new Account;
            $account = $Account->select();
            $this->assign('Bill',$account);
            $role = User:: role(); //role：角色
            $this->assign('role',$role);
                      
            if(User::isLogin())
            {  
                // 获取查询信息
                $remark = Request::instance()->get('remark');
                //获取获取select选中的值 
                $accountTest = Request::instance()->get('accounts');
                $this->assign('accountTest', $accountTest);   
                //通过select的值，返回指定行
                $accounts = $Account->where('name', '=', $accountTest)->select();
                // 获取指定字段的id,默认值，不管在什么时候后面会用到都account_id
                $account_id = 0;
                foreach ($accounts as $row) {
                    $account_id = $row['id'] ;
                }
                          
                $pageSize = 10; // 每页显示10条数据
                // 实例化Stream
                $Stream = new Stream;   
                $income = $Stream->where('account_id', '=', $account_id)->where('inandex', '=', 1)->sum('money');
                $pay = $Stream->where('inandex','=','0')->where('account_id', '=', $account_id)->sum('money');
                $income = number_format( $income, 2, '.', ',');
                $pay= number_format( $pay, 2, '.', ',');
                $this->assign('IncomeSum', $income);
                $this->assign('PaySum', $pay);
                
                //获取今天的收入和支出
                $incomeToday = $Stream->whereTime('create_time', 'today')->where('inandex','=','1')->where('account_id', '=' , $account_id)->sum('money');
                $payToday = $Stream->whereTime('create_time', 'today')->where('inandex','=','0')->where('account_id', '=' , $account_id)->sum('money');
                $sumToday = $incomeToday - $payToday;
                $this->assign('sumToday', $sumToday);
                $this->assign('IncomeToday', $incomeToday);
                $this->assign('PayToday', $payToday);

                // 定制查询信息
                if (!empty($remark))
                {
                    $Stream->where('remark', 'like', '%' . $remark . '%');
                }
                if (!empty($account_id))
                {
                    $Stream->where('account_id', '=' , $account_id);
                }
                
                // 调用分页
                $stream = $Stream->order('id DESC')->paginate($pageSize,false,[
                            'query' => [
                                'remark' => $remark,
                                'account_id' => $account_id
                            ]
                ]);
                
                $this->assign('streams',$stream);
                $this->assign('account_id',$account_id);
                // 取回打包后的数据
                $htmls = $this->fetch();
                // 将数据返回给用户
                return $htmls;
            }
            else
            {
                return $this->error('请登录后在访问', url('login_controller/index'));      
            }
            
        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
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

}