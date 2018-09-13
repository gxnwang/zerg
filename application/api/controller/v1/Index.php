<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/6
 * Time: 21:40
 */

namespace app\api\controller\v1;


use think\Request;

class Index {
    public function index(){
       // $arr = Request::instance() -> param();
        $all  = input('param.');

        print_r($all);
    }
}