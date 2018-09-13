<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 21:10
 */

namespace app\api\validate;


class TokenGet extends BaseValidate {
    protected $rule = [
        'code' => 'require|isNotEmpty',
    ];
    protected $message = [
        'code' => 'code值不能为空'
    ];
}