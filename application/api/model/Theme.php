<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 15:04
 */

namespace app\api\model;


class Theme extends BaseModel {

    protected $hidden = [
        'delete_time','update_time','topic_img_id','head_img_id'
    ];

    /**
     * 定义topic_img 关联关系
     * @return \think\model\relation\BelongsTo
     */
    public function topicImg(){
        return $this -> belongsTo('Image','topic_img_id','id');
    }
    /**
     * 定义head_img 关联关系
     * @return \think\model\relation\BelongsTo
     */
    public function headImg(){
        return $this -> belongsTo('Image','head_img_id','id');
    }
    public function products(){
        return $this -> belongsToMany('Product','theme_product','product_id','theme_id');
    }

    public static function getThemeWithProducts($id){
        $theme = self::with('topicImg,headImg,products') -> find($id);
        return $theme;
    }
}