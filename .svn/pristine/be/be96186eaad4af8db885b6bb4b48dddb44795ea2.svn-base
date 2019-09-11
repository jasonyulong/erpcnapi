<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 20:15
 */
namespace Mid\Controller;

use Think\Controller;
use Think\Exception;

class SyncEbayOrderController extends Controller
{
    /**
     * mid_ebay_order 同步到 erp_ebay_order
     * @author 王模刚
     * @since  2017 10 26
     * @link   http://local.erpanapi.com/t.php?s=/Mid/SyncEbayOrder/syncEbayOrder
     */
    public function syncEbayOrder() {
        $orderService = new \Mid\Service\OrderService();
        $orderService->syncOrder();
        die('mid_ebay_order 同步到 erp_ebay_order，成功！');
    }

    /**
     * mid_ebay_order_type 同步到 erp_ebay_order_type
     * @author  王模刚
     * @since   2017 10 27
     * @link    http://local.erpanapi.com/t.php?s=/Mid/SyncEbayOrder/syncEbayOrderType
     */
    public function syncEbayOrderType() {

        $orderService = new \Mid\Service\OrderService();
        $orderService->syncOrderType();
        die('mid_ebay_order_type 同步到 erp_ebay_order_type，成功！');

    }
}