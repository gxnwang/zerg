<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/29
 * Time: 0:13
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order {
    // 客户端传递过来的订单product参数
    protected $oProducts;
    // 存储从数据库查询出来的product
    protected $products;
    // 用户ID
    protected $uid;

    /**
     * 下单
     * 1、检测库存量
     * 2、创建订单
     * @param $uid  用户ID
     * @param $oProducts  客户端传递过来的订单product参数
     */
    public function place($uid, $oProducts) {
        // oProducts 和 products 做对比

        $this->oProducts = $oProducts;
        // 根据订单查询products
        $this->products = $this->getProductsByOrder($oProducts);

        $this->uid = $uid;
        // 库存量状态检测
        $status = $this->getOrderStatus();
        if ($status['pass'] == false) {
            $status['order_id'] = -1;
            return $status;
        }
        // 开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    private function createOrder($snap) {
        Db::startTrans();  // 启动事务
        try {
            $orderNo = self::makeOrderNo();
            $order = new \app\api\model\Order();
            // 对订单模型赋值
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();
            $orderId = $order->id;
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$oProduct) {
                $oProduct['order_id'] = $orderId;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();   // 提交事务
            return [
                'order_no'    => $orderNo,
                'order_id'    => $orderId,
                'create_time' => $create_time
            ];
        } catch (Exception $e) {
            Db::rollback();     // 回滚事务
            throw $e;
        }
    }

    /**
     * 生成订单号
     * @return string
     */
    public static function makeOrderNo() {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    //  生成订单快照
    private function snapOrder($status) {
        $snap = [
            'orderPrice'  => 0,
            'totalCount'  => 0,
            'pStatus'     => [],
            'snapAddress' => null,
            'snapName'    => '',
            'snapImg'     => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    private function getUserAddress() {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg'        => '用户收货地址不存在，下单失败',
                'error_code' => 60001,
            ]);
        }
        return $userAddress->toArray();
    }

    /**
     * 订单库存量检测
     * @return array    订单详细数据
     * @throws OrderException
     */
    private function getOrderStatus() {
        $status = [
            'pass'         => true, // 库存量检测标识
            'orderPrice'   => 0,    // 订单里所有商品的总价格
            'totalCount'   => 0,
            'pStatusArray' => []    //  保存订单中所有商品的详细信息，用于历史订单
        ];
        // 遍历订单商品，对订单商品中的数量(count)进行检测
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
            // 只要订单内的一个商品验证不通过，则整个订单就不通过
            if ($pStatus['haveStock'] === false) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }

    /**
     * 对订单内单个商品的数量进行检测，并返回历史订单数据
     * @param $oProduct_id  订单商品id
     * @param $oCount       订单商品数量
     * @param $products     查询出来的商品
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oProduct_id, $oCount, $products) {
        // 商品序号
        $pIndex = -1;
        // 保存单个订单商品的详细信息
        $pStatus = [
            'id'         => null,   // 商品ID
            'haveStock'  => false,   // 是否有库存
            'count'      => 0,           // 单个商品购买数量
            'name'       => '',           // 单个商品名称
            'totalPrice' => 0,      // 单个商品的总价
        ];
        // 循环，判断数据库中是否存在该商品
        for ($i = 0; $i < count($products); $i++) {
            if ($oProduct_id == $products[$i]['id']) {
                // 如果存在，则赋值下标
                $pIndex = $i;
            }
        }
        /*
         * 如果pIndex依然为-1，则认为数据库中没有该商品
         */
        if ($pIndex == -1) {
            // 客户端传递的product_id有可能不存在
            throw new OrderException([
                'msg' => 'id为' . $oProduct_id . '的商品不存在,创建订单失败'
            ]);
        } else {
            $product = $products[$pIndex];  // 获取下标为pIndex的商品
            // 设置pStatus数据
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            // 库存量判断
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }


    /**
     * 根据订单信息查找真实的商品信息
     * @param $oProducts    订单product
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function getProductsByOrder($oProducts) {
        // 存储所有的订单商品id
        $product_ids = [];
        foreach ($oProducts as $oProduct) {
            array_push($product_ids, $oProduct['product_id']);
        }
        // 根据一组id查询商品信息
        $products = Product::all($product_ids)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();
        return $products;
    }


    /**
     * 对外部开放的根据订单ID检测库存量方法
     * @param $orderId  订单Id
     * @return array
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkOrderStock($orderId){
        // 根据订单ID查询订单信息，并将信息存储到成员变量中
        $oProducts = OrderProduct::where('order_id','=',$orderId)->select();
        $this -> oProducts = $oProducts;
        // 根据订单信息，获取商品信息，并将信息存储到成员变量中
        $this -> products = $this -> getProductsByOrder($oProducts);
        // 检测库存量
        $status = $this -> getOrderStatus();
        return $status;
    }

}