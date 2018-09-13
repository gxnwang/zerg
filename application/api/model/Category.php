<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 18:46
 */

namespace app\api\model;


class Category extends BaseModel {
    protected $hidden = [
        'update_time','delete_time','create_time'
    ];

    public function img(){
        return $this -> belongsTo('Image','topic_img_id','id');
    }
}