<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/29
 * Time: 22:48
 */

namespace app\lib\exception;


class OrderException extends BaseException {
//HTTP状态码
    public $code =404;
    //错误具体信息
    public $msg = '订单不存在，请检查ID';
    //自定义的错误
    public $error_code = 80000;
}