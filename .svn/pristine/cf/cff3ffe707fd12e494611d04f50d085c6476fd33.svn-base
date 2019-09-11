<?php
namespace Order\Controller;

use Order\Model\EbayOrderModel;
use Package\Model\OrderslogModel;
use Think\Controller;

/**
 * 长期逗留再系统中的订单
 */
class StayOrderController extends Controller
{

    /**
     * @desc  24小时之前的异常订单
     * @Author leo
     */

    //TODO: /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Order/StayOrder/index
    public function index()
    {

        if(!IS_CLI){
            echo "订单异常! \n ";
            return ;
        }
        $startTime     = date("Y-m-d H:i:s", strtotime('-1 days'));
        $erporderModel = new EbayOrderModel();

        $where    = [
            'ebay_status'   => ['in', '1723,1724,1745,2009'],
            'w_update_time' => ['lt', $startTime],
        ];
        $count    = $erporderModel->where($where)->count();
        $pageshow = 300;
        $pCount   = ceil($count / $pageshow);
        p("Task start count {$count}, Total {$pCount} batch,every {$pageshow} strip");

        for ($page = 0; $page <= $pCount; $page++) {
            if ($page <= 0) {
                $page = 1;
            }
            $pagesize  = ($page - 1) * $pageshow;
            $orderData = $erporderModel->field('ebay_id,ebay_status')->where($where)->limit($pagesize, $pageshow)->select();
            $this->saveStatus($orderData);

            p("The {$page} batch complete,limit($pagesize,$pageshow)");
        }

        p('End of the task');
    }


    /**
     * @desc 根据api查找修改订单状态
     * @Author leo
     * @param
     * @return
     */
    public function saveStatus($orderData)
    {
        if (empty($orderData)) {
            return false;
        }
        $logMolde      = new OrderslogModel();
        $orderService  = new \Mid\Service\OrderService();
        $erporderModel = new EbayOrderModel();
        foreach ($orderData as $val) {
            $requestData = ['ebay_id' => $val['ebay_id'], 's_time' => time()];
            $data        = $orderService->getOrderByEbayId($requestData);
            $apiStatus   = $data['data']['ebay_status'];

            if(empty($apiStatus)){
                continue;
            }

            if(!in_array($apiStatus,['2','1731'])){
                continue;
            }


            echo $val['ebay_id']."\n";
            //return true;

            if ($apiStatus != $val['ebay_status']) {
                $save = ['ebay_status' => $apiStatus];
                $re   = $erporderModel->where(['ebay_id' => $val['ebay_id']])->save($save);
                if ($re) {
                    $note = "WMS API自动校验：订单[{$val['ebay_id']}]状态由[{$val['ebay_status']}]修改为[{$apiStatus}]";
                    $logMolde->addordernote($val['ebay_id'],$note,5);
                }
            }
        }
        return true;
    }


}
