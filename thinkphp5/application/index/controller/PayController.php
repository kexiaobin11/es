<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Pay;
use think\Request;

class PayController extends Controller{
    public function index()
    {
        try {
             // 获取查询信息
             $name = Request::instance()->get('name');
             echo $name;

            $pageSize = 3; // 每页显示5条数据

            // 实例化Income
            $Pay = new Pay; 

             // 定制查询信息
             if (!empty($name)) {
                $Income->where('name', 'like', '%' . $name . '%');
            }


            // 调用分页
            $pay = $Pay->paginate($pageSize);

            // 向V层传数据
            $this->assign('Pay', $pay);

            // 取回打包后的数据
            $htmls = $this->fetch();

            // 将数据返回给用户
            return $htmls;

        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;

        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }
    
}