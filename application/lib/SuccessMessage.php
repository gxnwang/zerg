<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 20:47
 */

namespace app\lib;


class SuccessMessage {
    //HTTP状态码
    public $code = 201;
    //错误具体信息
    public $msg = 'ok';
    //自定义的错误
    public $error_code = 0;
}