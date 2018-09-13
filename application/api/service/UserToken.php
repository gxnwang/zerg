<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 21:20
 */

namespace app\api\service;

use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token {
    // 客户端（小程序）传递过来的code
    protected $code = '';
    // 小程序app_id
    protected $wx_app_id = '';
    // 小程序secret
    protected $wx_app_secret = '';
    // 小程序登陆接口地址
    protected $wx_login_url = '';

    public function __construct($code) {
        // 设置code
        $this->code = $code;
        // 设置app_id
        $this->wx_app_id = config('wx.app_id');
        // 设置app_secret
        $this->wx_app_secret = config('wx.app_secret');
        // 设置小程序登陆接口地址  sprintf(地址，app_id,secret,code)
        $this->wx_login_url = sprintf(config('wx.login_url'), $this->wx_app_id, $this->wx_app_secret, $this->code);
    }

    // 获取用户Token值
    public function get() {
        // 获取微信接口内容
        $result = curl_get($this->wx_login_url);
        $wx_result = json_decode($result, true);
        if (empty($wx_result)) {
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $wx_result);
            if ($loginFail) {
                // 处理微信登陆错误
                $this->processLoginError($wx_result);
            } else {
                return $this->grantToken($wx_result);
            }
        }

    }

    /**
     * 处理微信登陆错误
     * @param $wx_result
     * @throws WeChatException
     */
    private function processLoginError($wx_result) {
        throw new WeChatException([
            'msg'        => $wx_result['errmsg'],
            'error_code' => $wx_result['errcode'],
        ]);
    }

    // 授权Token
    private function grantToken($wx_result) {
        //  拿到openid
        //  查找数据库 这个openid 是否已经存在
        //  如果存在，不处理；如果不存在，新增一条记录
        //  生成令牌，准备缓存数据，写入缓存
        //  把令牌返回客户端
        //  key: Token令牌
        //  value: wx_result,uid,scope(用户身份)

        // 拿到openid
        $openid = $wx_result['openid'];
        // 根据openid查找用户
        $user = UserModel::getByOpenID($openid);
        //如果用户存在，直接获取id；如果用户不存在，则新增用户后再获取id
        $uid = $user ? $user->id : $this->newUser($openid);
        // 获取缓存value
        $cache_value = $this->prepareCachedValue($wx_result, $uid);
        return $this -> saveToCache($cache_value);
    }

    /**
     * 新增用户
     * @param $openid
     * @return mixed
     */
    private function newUser($openid) {
        $user = UserModel::create([
            'openid' => $openid
        ]);
        // 返回用户的ID
        return $user->id;
    }

    //

    /**
     * 准备缓存内容
     * 缓存内容包括微信返回的openid 用户id 权限级别scope
     * @param $wx_result
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue($wx_result, $uid) {
        $cache_value = $wx_result;
        $cache_value['uid'] = $uid;
        // scope=16 代表用户的权限数值
        $cache_value['scope'] = ScopeEnum::User; // 暂时使用16这个值
        //$cache_value['scope'] = ScopeEnum::User; // 暂时使用16这个值
        return $cache_value;
    }

    // 保存缓存
    private function saveToCache($cache_value) {
        // 生成Token
        $key = self::generateToken();
        $value = json_encode($cache_value);
        $expire_in = config('setting.token_expire_in');
        //  利用ThinkPHP5缓存机制生成缓存
        $request = cache($key, $value, $expire_in);
        if (!$request){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'error_code' => 10005
            ]);
        }
        // 返回Token
        return $key;
    }
}