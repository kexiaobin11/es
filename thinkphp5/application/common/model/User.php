<?php
namespace app\common\model;
use think\Model;
use think\Request;
class User extends Model{
    static public function login($username,$password){
        $map = array('username' => $username);
        $User = self::get($map);
        if(!is_null($User)){
            if($User->checkPassword($password)){
                session('teacherId', $User->getData('id'));
                return true;
            }
        }
    }
    public function checkPassword($password){
        if($this->getData('password') === $this::encryptPassword($password)){
            return true;
        }else{
            return false;
        }
    }
    static public function encryptPassword($password){
        // 实际的过程中，我还还可以借助其它字符串算法，来实现不同的加密。
        if(!is_string($password)){
           throw new \RuntimeException("传入变量类型非字符串，错误码2", 2);
        }
      return sha1(md5($password) . 'mengyunzhi');
    }
        static public function logOut(){
            session('userId',null);
            return true;
        }

}