<?php
namespace Test\Controller;

use Think\Controller;

class ModifyOrderController extends Controller
{

    /**
     * 更新订单状态
     *
     * @return void
     */
    public function updateOrderStatus()
    {
        $request  = $_REQUEST;
        $ebay_ids = $request['ebay_ids'];
        if (empty($ebay_ids)) {
            echo json_encode(['status' => 101, 'msg' => '订单id不能为空'], JSON_UNESCAPED_UNICODE);
            die;
        }
        $ebay_ids    = explode(',', $ebay_ids);
        $apiCheckSku = new \Package\Model\ApiCheckskuModel();
        $orderModel  = new \Common\Model\OrderModel();

        $status = $orderModel->where(['ebay_id' => ['in', $ebay_ids]])->setField('ebay_status', 1731);
        if (false !== $status) {
            echo json_encode(['status' => 100, 'ebay_ids' => implode(',', $ebay_ids)], JSON_UNESCAPED_UNICODE);
            die;
        } else {
            echo json_encode(['status' => 102, 'msg' => '修改失败'], JSON_UNESCAPED_UNICODE);
            die;
        }
    }

}
