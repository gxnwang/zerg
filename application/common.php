<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0) {
    //初始化连接句柄
    $ch = curl_init();
    // 设置连接url
    curl_setopt($ch, CURLOPT_URL, $url);
    // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。return transfer
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验，部署在Linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 设置连接超时时间 connect timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $file_contents = curl_exec($ch);
    // 获取HTTP状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

/**
 * 获取随机字符串
 * @param $length   随机字符串长度
 * @return null|string
 */
function getRandChar($length) {
    $str = null;
    $strPol = 'abcdefghigjklmopqrstuvwxyzABCDEFGHIGJKLMOPQRSTUVWXYZ0123456789';
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0,$max)];
    }
    return $str;
}
