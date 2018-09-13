<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 15:03
 */

namespace app\api\model;


class Product extends BaseModel {
    protected $hidden = [
        'update_time','delete_time','create_time','from','category_id','main_img_id','pivot'
    ];

    protected function getMainImgUrlAttr($value,$data){
        return $this -> prefixImgUrl($value,$data);
    }
    // 关联product_image
    public function imgs(){
        return $this -> hasMany('ProductImage','product_id','id');
    }
    // 关联product_property
    public function properties(){
        return $this -> hasMany('ProductProperty','product_id','id');
    }

    public static function getMostRecent($count){
        $products = self::limit($count) -> order('create_time desc') -> select();
        return $products;
    }

    public static function getProductByCategoryID($id){
        $products = self::where('category_id','=',$id) -> select();
        return $products;
    }

    public static function getProductDetail($id){
        return self::with([
            'imgs' => function($query){
                $query->with('imgUrl')
                ->order('order','asc');
            },
            'properties'
        ])->find($id);
    }
}