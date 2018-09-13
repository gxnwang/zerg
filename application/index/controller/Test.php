<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/16
 * Time: 22:28
 */

namespace app\index\controller;


use think\Controller;

class Test extends Controller {
    public function index(){
        $b = 6;
        $this -> test($b);
        echo  $b;
        //return $this -> fetch();
    }


    public function test(&$a){
        $a = $a + 100;
    }
}