<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 22:03
 */

namespace app\api\model;


class UserAddress extends BaseModel {
    protected $hidden = ['id','delete_time','user_id'];
}