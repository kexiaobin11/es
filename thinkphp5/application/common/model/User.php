<?php
namespace app\common\model;
use think\Model;
use think\Request;
class User extends Model{

    //public static $sessionKey = "yunzhi_user";
    
    static public function logOut()
    {
        session('userId',null);
        
        return true;
    }


    static public function login($username,$password){
        $map = array('username' => $username);

        $User = self::get($map);

        if(!is_null($User))
        {
            if($User->checkPassword($password))
            {

                session('userId', $User->getData('id'));

                return true;
            }
        }
    }

    public function checkPassword($password){

        if($this->getData('password') === $this::encryptPassword($password))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    static public function encryptPassword($password)
    {
        
        if(!is_string($password))
        {
           throw new \RuntimeException("传入变量类型非字符串，错误码2", 2);
        }

      return sha1(md5($password) . 'mengyunzhi');
     }

  
    static public function isLogin()
    {
        $userId = session('userId');     
        if(isset($userId)) 
        {
            return true;
        }
        else
        {
            return false;

        }

    }
    
    /**
     * 判断用户角色
     * @return 1 管理员 
     * @return 0 用户 
     */
    static public function role(){

         //在数据库中第几个用户
         $id = session('userId');
         //获取$id的a全部数据
         $User = User::get($id);
         session('perId',$User->getData('permissions'));
         $perId = session('perId');
         return $perId;
    }


}