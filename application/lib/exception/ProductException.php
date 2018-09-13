<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 18:10
 */

namespace app\lib\exception;


class ProductException extends BaseException {
    //HTTP状态码
    public $code =404;
    //错误具体信息
    public $msg = '指定的商品不存在';
    //自定义的错误
    public $error_code = 20000;
}