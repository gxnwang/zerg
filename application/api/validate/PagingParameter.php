<?php
/**
 * Created by PhpStorm.
 * Created_at: 2018-9-6 11:37
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate {
    protected $rule = [
        'page' =>'isPositiveInt',
        'size' =>'isPositiveInt',
    ];
    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数',
    ];
}