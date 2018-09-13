<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/6
 * Time: 23:03
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

/**
 * 公共验证器
 * Class BaseValidate
 * @package app\api\validate
 */
class BaseValidate extends Validate {

    public function goCheck() {
        $params = Request::instance()->param();
        $result = $this->batch()->check($params);
        if (!$result) {
            throw new ParameterException([
                'msg' => $this->getError(),
            ]);
        } else {
            return true;
        }
    }

    protected function isPositiveInt($value, $rule = '', $data = '', $field = '') {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判定非空
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isNotEmpty($value, $rule = '', $data = '', $field = '') {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }


    public function getDataByRule($arrays) {
        if (array_key_exists('user_id', $arrays) || array_key_exists('id', $arrays)) {
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

    public function isMobile($value) {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}