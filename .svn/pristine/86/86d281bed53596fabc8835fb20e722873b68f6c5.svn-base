<?php
/**
 * 获取订单进入某个状态的时间
 * @author Simon 2017/12/6
 */
namespace Order\Service;

use Package\Model\ApiCheckskuModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;

class GetOrderToStatusTimeService
{
    public function __construct() {
        $this->pickOrderDetail = new PickOrderDetailModel();
        $this->pickOrder       = new PickOrderModel();
        $this->apiCheckSku     = new ApiCheckskuModel();
    }

    public function getToTime($order) {
        $funcName = 'getToTime' . $order['ebay_status'];
        $time     = $this->$funcName($order);
        return $time ? : null;
    }

    public function getToTime1723($order) {
        return $order['w_add_time'];
    }

    /**
     * 进入等待打印的时间
     * @author Simon 2017/12/6
     */
    public function getToTime1745($order) {
        $pickOrderDetailInfo = $this->pickOrderDetail->where(['ebay_id' => $order['ebay_id'], 'is_normal' => 1, 'is_delete' => 0])->find();
        if (empty($pickOrderDetailInfo)) {
            return false;
        }
        $pickOrderInfo = $this->pickOrder->where(['ordersn' => $pickOrderDetailInfo['ordersn'], 'isprint' => 1])->find();
        if (empty($pickOrderInfo)) {
            return false;
        }
        return $pickOrderInfo['addtime']?date('Y-m-d H:i:s',$pickOrderInfo['addtime']):false;
    }

    /**
     * 进入等待扫描的时间
     * @author Simon 2017/12/6
     */
    public function getToTime1724($order) {
        $pickOrderDetailInfo = $this->pickOrderDetail->where(['ebay_id' => $order['ebay_id'], 'is_normal' => 1, 'is_delete' => 0])->find();
        if (empty($pickOrderDetailInfo)) {
            return false;
        }
        $pickOrderInfo = $this->pickOrder->where(['ordersn' => $pickOrderDetailInfo['ordersn'], 'isprint' => 2])->find();
        if (empty($pickOrderInfo)) {
            return false;
        }
        return $pickOrderInfo['pick_end']?date('Y-m-d H:i:s',$pickOrderInfo['pick_end']):false;
    }

    /**
     * 进入已出库待称重的时间
     * @author Simon 2017/12/6
     */
    public function getToTime2009($order) {
        $data = $this->apiCheckSku->where(['ebay_id' => ['eq', $order['ebay_id']], 'status' => ['in', [1, 2]]])->find();
        return $data['addtime']?date('Y-m-d H:i:s',$data['addtime']):false;
    }
}