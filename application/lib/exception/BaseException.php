<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/7
 * Time: 23:57
 */

namespace app\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception {
    //HTTP状态码
    public $code =400;
    //错误具体信息
    public $msg = '参数错误';
    //自定义的错误
    public $error_code = 10000;

    public function __construct($params = []) {
        if(!is_array($params)){
            return ;
        }
        if(array_key_exists('code',$params)){
            $this ->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this ->msg = $params['msg'];
        }
        if(array_key_exists('error_code',$params)){
            $this ->error_code = $params['error_code'];
        }
    }
}