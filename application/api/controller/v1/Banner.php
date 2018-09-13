<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/6
 * Time: 22:24
 */

namespace app\api\controller\v1;


use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;

class Banner {

    /**
     * 获取指定ID的Banner
     * @url /banner/:id
     * @http GET
     * @param $id
     */
    public function getBanner($id) {
        (new IDMustBePositiveInt())->goCheck();

        $banner = BannerModel::getBannerById($id);
        if (!$banner) {
            throw new BannerMissException();
        }
        return $banner;
    }
}