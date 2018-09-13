<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/9/2
 * Time: 22:27
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pay {
    private $orderId;
    private $orderNo;

    public function __construct($orderId) {
        if (!$orderId) {
            throw new Exception('订单号不允许为空');
        }
        $this->orderId = $orderId;
    }

    public function pay() {
        // 订单号可能根本不存在
        // 订单号存在，但是订单号和用户不匹配
        // 订单有可能已经被支付过
        $this->checkOrderValid();
        // 检测库存量
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderId);
        if ($status['pass'] == false) {
            // 如果检测不通过
            return $status;
        }

        // 向微信发送请求，创建预订单
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    // 创建微信预订单
    private function makeWxPreOrder($totalPrice) {
        // 用户的openid
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        // 定义相关参数
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        // 获取预订单；
        return $this->getPaySignature($wxOrderData);
    }

    // 调用微信预订单接口
    private function getPaySignature($wxOrderData) {
        $wxOrder = \wxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        $this->recordPreOrder($wxOrder);

        $signature = $this->sign($wxOrder);

        // 返回 微信小程序支付所需参数
        return $signature;
    }

    /**
     * 生成sign
     * @param $wxOrder
     * @return array
     */
    private function sign($wxOrder) {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage("prepay_id=" . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();

        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);

        return $rawValues;
    }


    /**
     * 将prepay_id 存储到数据库中
     * @param $wxOrder  微信预订单接口返回的数据
     */
    private function recordPreOrder($wxOrder) {
        OrderModel::where('id', '=', $this->orderId)->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    /**
     * 检测订单有效性： 是否存在，是否是当前用户的订单，是否已经支付过
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkOrderValid() {
        // 检测订单是否存在
        $order = OrderModel::where('id', '=', $this->orderId)->find();
        if (!$order) {
            throw new OrderException();
        }
        // 验证用户和订单是否匹配
        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'error_code' => 10003
            ]);
        }
        // 验证订单是否已经支付过
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'error_code' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;
    }
}