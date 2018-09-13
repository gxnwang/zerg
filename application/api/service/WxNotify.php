<?php
/**
 * Created by PhpStorm.
 * Created_at: 2018-9-5 17:46
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify {

    public function NotifyProcess($data, &$msg) {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try {
                $order = OrderModel::where('order_no', '=', $orderNo)->lock(true)->find();
                if ($order->status == 1) {
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']) {
                        // 库存量检测通过
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($stockStatus);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }else{
            // 微信支付失败
            return true;
        }
    }

    /**
     * 减库存
     * @param $stockStatus
     * @throws Exception
     */
    private function reduceStock($stockStatus) {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            // $singlePStatus['count']
            Product::where('id', '=' . $singlePStatus['id'])->setDec('stock', $singlePStatus['count']);
        }
    }

    /**
     * 更新订单状态
     * @param $orderID
     * @param $success
     */
    private function updateOrderStatus($orderID, $success) {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;

        OrderModel::where('id', '=', $orderID)->update(['status' => $status]);
    }

}