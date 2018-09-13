<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 20:31
 */

namespace app\lib\exception;


class UserException extends BaseException {
    //HTTP状态码
    public $code =404;
    //错误具体信息
    public $msg = '用户不存在';
    //自定义的错误
    public $error_code = 60000;
}