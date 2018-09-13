<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 12:20
 */

namespace app\lib\exception;


class TokenException extends BaseException {
    //HTTP状态码
    public $code =401;
    //错误具体信息
    public $msg = 'Token已过期或无效Token';
    //自定义的错误
    public $error_code = 10001;
}