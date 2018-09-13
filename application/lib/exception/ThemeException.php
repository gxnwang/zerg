<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 16:06
 */

namespace app\lib\exception;


class ThemeException extends BaseException {
    //HTTP状态码
    public $code =404;
    //错误具体信息
    public $msg = '指定主题不存在，请检查主题ID';
    //自定义的错误
    public $error_code = 30000;
}