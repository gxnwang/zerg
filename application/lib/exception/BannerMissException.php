<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/7
 * Time: 23:59
 */

namespace app\lib\exception;


class BannerMissException extends BaseException {
    public $code = 404;
    public $msg = '请求的Banner不存在';
    public $error_code = 40000;

}