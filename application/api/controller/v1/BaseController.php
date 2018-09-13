<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/28
 * Time: 22:42
 */

namespace app\api\controller\v1;


use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller {
    // 基本权限
    protected function checkPrimaryScope() {
        TokenService::needPrimaryScope();
    }
    // 只有用户可以访问的权限
    protected function checkExclusiveScope() {
        TokenService::needExclusiveScope();
    }
}