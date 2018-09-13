<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 15:50
 */

namespace app\api\model;


class ProductProperty extends BaseModel {
    protected $hidden = ['product_id','delete_time','id'];
}