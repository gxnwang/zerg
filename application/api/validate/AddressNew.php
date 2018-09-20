<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 18:38
 */

namespace app\api\validate;


class AddressNew extends BaseValidate {
    protected $rule = [
        'name' => 'require|isNotEmpty',
        //'mobile' => 'require|isMobile',
        'mobile' => 'require',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'county' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty',
    ];
}