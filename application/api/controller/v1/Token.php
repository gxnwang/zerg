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
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;

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

    /**
     * 验证token
     * @param $token
     * @return array
     * @throws ParameterException
     */
    public function verifyToken($token){
        if(!$token){
            throw new ParameterException([
                'msg' => 'token不能为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];
    }
}