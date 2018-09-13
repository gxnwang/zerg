<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/27
 * Time: 23:07
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController {

    // 用户在选择商品后，向API提交包含所选商品的相关信息
    // API在接收到信息后，需要检查订单相关商品的库存量
    // 有库存，把订单数据存入数据库中，并返回客户端消息 可以支付了
    // 调用支付接口，进行支付
    // 还需要再次进行库存量检测
    // 小程序根据服务器返回的结果拉起微信支付
    // 微信返回支付结果
    // 成功：也需要进行库存量的检测
    // 支付成功，扣除库存量

    // 权限控制，只有用户可以下单
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser']
    ];

    public function getSummaryByUser($page = 1, $size = 15) {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUID();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders -> isEmpty()){
            return [
                'data' => [],
                'current_page' => $pagingOrders -> getCurrentPage(),
            ];
        }
        $data = $pagingOrders ->hidden(['prepay_id','snap_items','snap_address'])-> toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders -> getCurrentPage(),
        ];
    }

    public function getDetail($id){
        (new IDMustBePositiveInt()) -> goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail -> hidden(['prepay_id']);
    }

    /**
     * 下订单
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function placeOrder() {
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        // 获取用户id;
        $uid = TokenService::getCurrentUID();
        $order = new OrderService();
        $status = $order->place($uid, $products);
        return $status;
    }
}