<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/9/2
 * Time: 22:15
 */

namespace app\api\controller\v1;


use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController {
    // 权限控制，只有用户可以支付
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    //请求预订单
    public function getPreOrder($id = ''){
        (new IDMustBePositiveInt()) ->goCheck();
        $pay =  new PayService($id);
        return $pay -> pay();
    }
    // 接收微信通知
    public function receiveNotify(){
        // 1、检测库存量，防止超卖
        // 2、更新订单状态 status
        // 3、减库存
        // 如果成功处理，我们返回微信成功处理的信息。否则，我们需要返回没有成功处理

        // 特点：post; xml格式; 不会携带参数
        $notify = new WxNotify();
        $notify -> Handle();
    }
}