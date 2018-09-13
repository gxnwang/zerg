<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/7
 * Time: 23:55
 */

namespace app\lib\exception;


use Exception;
use think\Config;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle {
    // HTTP状态码
    private $code;
    // 错误具体描述
    private $msg;
    // 自定义错误码
    private $error_code;
    // 客户端当前访问路径
    private $url;

    public function render(Exception $e) {
        if ($e instanceof BaseException) {
            //如果是自定义的异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->error_code = $e->error_code;
            $this->url = '';
        } else {
            $switch = Config::get('app_debug');
            if($switch){
                return parent::render($e);
            }else{
                $this->code = 500;
                $this->msg = 'Server internal error';
                $this->error_code = 999;
                //将异常信息记录到日志中
                $this -> recordErrorLog($e);
            }

        }
        $request = Request::instance();
        $result = [
            'msg'         => $this->msg,
            'error_code'  => $this->error_code,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
    }

    /**
     * 定义错误日志
     * @param Exception $e
     */
    private function recordErrorLog(Exception $e) {
        // 日志初始化
        Log::init([
            // 日志记录方式，内置 file socket 支持扩展
            'type'  => 'File',
            // 日志保存目录
            'path'  => LOG_PATH,
            // 日志记录级别
            'level' => ['error'],
        ]);
        Log::record($e->getMessage(), 'error');
    }
}