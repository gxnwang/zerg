<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 21:08
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token {
    /**
     * 获取Token
     * @param string $code
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getToken($code = '') {
        // 验证微信code值
        (new TokenGet()) -> goCheck();
        // 获取Token
        $ut = new UserToken($code);
        $token =  $ut -> get();
        // 以键值对的方式返回token
        return ['token'=>$token];
    }
}