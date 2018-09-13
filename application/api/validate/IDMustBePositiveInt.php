<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/6
 * Time: 22:55
 */

namespace app\api\validate;


use think\Exception;

/**
 * 验证器 ： 验证ID必须是整数
 * Class IDMustBePositiveInt
 * @package app\api\validate
 */
class IDMustBePositiveInt extends BaseValidate {
    protected $rule = [
        'id' => 'require|isPositiveInt',
    ];

    protected $message = [
        'id' => 'ID必须是正整数'
    ];
}