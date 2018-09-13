<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 18:46
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category {
    public function getAllCategories(){
        $result = CategoryModel::all([],'img');
        //$result = CategoryModel::with('img') -> select();
        if($result -> isEmpty()){
            throw new CategoryException();
        }
        return $result;
    }
}