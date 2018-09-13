<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/19
 * Time: 15:02
 */

namespace app\api\controller\v1;
use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

/**
 * @url /theme?ids=id1,id2,id3,...
 * @return 一组Theme模型
 */
class Theme {
    public function getSimpleList($ids = ''){
        (new IDCollection()) -> goCheck();
        $ids = explode(',',$ids);
        $result = ThemeModel::with('topicImg,headImg') -> select($ids);
        if($result-> isEmpty()) {
            throw new ThemeException();
        }
        return $result;
    }

    /**
     * @url /theme/:id
     */
    public function getComplexOne($id){
        (new IDMustBePositiveInt()) -> goCheck();
        $result = ThemeModel::getThemeWithProducts($id);
        if(!$result){
            throw new ThemeException();
        }
        return $result;
    }
}