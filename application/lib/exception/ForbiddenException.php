<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/27
 * Time: 22:34
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException {
    //HTTP状态码
    public $code =403;
    //错误具体信息
    public $msg = '权限不够';
    //自定义的错误
    public $error_code = 10001;
}