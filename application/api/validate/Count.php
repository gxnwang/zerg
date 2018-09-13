<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 18:01
 */

namespace app\api\validate;


class Count extends BaseValidate {
    protected $rule = [
        'count' => 'isPositiveInt|between:1,15',
    ];
    protected $message = [
      'count' => '数量必须是1到15的正整数'
    ];
}