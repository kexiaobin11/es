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
             //return $this->fetch(); 
           
            if(User::isLogin())
            {
                if($role === 1)//如果是1，则是管理员；0就是用户，不可访问
                {   
                    // 获取查询信息
                    $remark = Request::instance()->get('remark');
                    $account_id = Request::instance()->get('account_id/d');

                    var_dump($account_id);
                    $pageSize = 10; // 每页显示10条数据
                    // 实例化Income
                    $Stream = new Stream; 
                    $stream = $Stream->select();
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
                    $stream = $Stream->paginate($pageSize,false,[
                                'query' => [
                                    'remark' => $remark,
                                    'account_id' => $account_id
                                ]
                    ]);
                    // 向V层传数据
                    $this->assign('streams',$stream);
                    $this->assign('account_id',$account_id);
                    // 取回打包后的数据
                    $htmls = $this->fetch();
                    // 将数据返回给用户
                    return $htmls;
                }
                else
                {
                    return $this->error('你的权限不够', url('homepage_controller/index')); 
                }
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