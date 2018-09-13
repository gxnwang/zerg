<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/8
 * Time: 22:29
 */

namespace app\lib\exception;


class ParameterException extends BaseException {
    public $code = 400;
    public $message = '参数错误';
    public $error_code = 10000;
}