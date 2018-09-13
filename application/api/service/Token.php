<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 22:49
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token {
    /**
     * 生成Token
     * @return string Token
     */
    public static function generateToken(){
        //  选取32个字符组成一组随机字符串
        $randChars = getRandChar(32);
        //  用三组字符串，进行md5加密
        //  当前时间戳
        $timestamp =  time();
        //  盐值 salt
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }

    /**
     * 获取token缓存中指定key的值
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key){
        // 获取客户端HTTP请求中头部token信息
        $token = Request::instance()-> header('token');
        //  根据token查找缓存内容
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量不存在');
            }
        }
    }

    /**
     * 获取uid
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUID(){
        // Token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 需要需要用户和CMS管理员都可以访问的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    /**
     * 只有用户才能访问的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needExclusiveScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    // 是否是合法的操作
    public static function isValidOperate($checkedUId){
        if(!$checkedUId){
            throw new Exception('检测UID时必须传入一个被检测的UID');
        }
        $currentOperateUID = self::getCurrentUID();
        if($checkedUId == $currentOperateUID){
            return  true;
        }else{
            return false;
        }
    }
}