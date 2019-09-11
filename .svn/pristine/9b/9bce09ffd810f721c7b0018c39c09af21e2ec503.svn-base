<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:24
 */
include "include/RequestErp.php";

class GetOrder extends RequestErp
{
    public function getOrderByEbayId($ebay_id) {
        $action      = 'Order/getOrderByEbayId/wid/196';
        $requestData = ['ebay_id' => $ebay_id, 's_time' => time()];
        $data        = $this->getErpData($requestData, $action);
        if ($data['ret'] != 100 || empty($data['data'])) {
            return ['status' => 0, 'msg' => 'no data'];
        } else {
            return ['status' => 1, 'data' => $data['data']];
        }
    }
}