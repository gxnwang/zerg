<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/18
 * Time: 13:36
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model {
    protected function prefixImgUrl($value,$data){
        if($data['from'] == 1){
            return config('setting.img_prefix').$value;
        }else{
            return $value;
        }
    }
}