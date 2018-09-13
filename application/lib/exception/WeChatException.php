<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 22:03
 */

namespace app\lib\exception;


class WeChatException extends BaseException {
    //HTTP状态码
    public $code =400;
    //错误具体信息
    public $msg = '微信服务器接口调用失败';
    //自定义的错误
    public $error_code = 999;
}