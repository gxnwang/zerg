<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 18:52
 */

namespace app\lib\exception;


class CategoryException extends BaseException {
    //HTTP状态码
    public $code =404;
    //错误具体信息
    public $msg = '指定的类目不存在';
    //自定义的错误
    public $error_code = 50000;
}