<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/18
 * Time: 11:11
 */

namespace app\api\model;


use think\Model;

class Image extends BaseModel {
    protected $hidden = ['id','update_time','delete_time','from'];

    public function getUrlAttr($value ,$data){
        return $this -> prefixImgUrl($value,$data);
    }
}