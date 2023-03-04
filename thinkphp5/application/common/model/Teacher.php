<?php
// 简单的原理重复记： namespace说明了该文件位于application\common\model 文件夹中
namespace app\common\model;
use think\Model;    
use think\Request;//  导入think\Model类
class Teacher extends Model
{

    static public function login($username,$password){
        $map = array('username' => $username);
        $Teacher = self::get($map);
        if(!is_null($Teacher)){
            if($Teacher->checkPassword($password)){
                session('teacherId', $Teacher->getData('id'));
                return true;
            }
        }
    }
    /**
	 * 验证密码是否正确
	 * @param  string $password 密码
	 * @return bool           
	 */
    public function checkPassword($password){
        if($this->getData('password') === $this::encryptPassword($password)){
            return true;
        }else{
            return false;
        }
    }
      /**
     * 密码加密算法
     */

    static public function encryptPassword($password){
         // 实际的过程中，我还还可以借助其它字符串算法，来实现不同的加密。
         if(!is_string($password)){
            throw new \RuntimeException("传入变量类型非字符串，错误码2", 2);
         }
       return sha1(md5($password) . 'mengyunzhi');
    }

    static public function logOut(){
        session('teacherId',null);
        return true;
    }
    
    static public function isLogin(){
        $teacherId = session('teacherId');
        if (isset($teacherId)) {
            return true;
        } else {
            return false;
        }
    }
    

}