<?php
/**
 * @copyright Copyright (c) 2018
 * @version   Beta 1.0
 * @author    leo
 * @date      2018年8月21日17:42:20
 */
namespace Report\Controller;

use Common\Model\OrderModel;
use Think\Controller;
/**
 * 计算仓库连续7天 指定的 小时内 进入的订单量
 * Class ShowOrderCountsController
 * @package Statistics\Controller
 */
class ComingWmsOrderQtyController extends Controller
{
    public function index()
    {
        $params = $_REQUEST;
        # 组织时间搜索条件
        if (trim($params['start_time'])) {
            if($params['st'] == '2'){
                $params['start_time'] = date('H:i:s', strtotime($params['start_time'].'+1 hour'));
            }elseif($params['st'] == '1'){
                $params['start_time'] = date('H:i:s', strtotime($params['start_time'].'-1 hour'));
            }
        } else {
            $params['start_time'] = date('H:00:00', strtotime('-1 hour'));
        }
        if (trim($params['end_time'])) {
            if($params['st'] == '2'){
                $params['end_time'] = date('H:i:s', strtotime($params['end_time'].'+1 hour'));
            }elseif($params['st'] == '1'){
                $params['end_time'] = date('H:i:s', strtotime($params['end_time'].'-1 hour'));
            }
        } else {
            $params['end_time'] = date('H:00:00');
        }

        $start = strtotime(date('Y-m-d', strtotime('-7 day')) . ' 00:00:00');
        $end = strtotime(date('Y-m-d') . ' 00:00:00');
        for ($i = $start; $i <= $end; $i = $i + 60 * 60 * 24) {
            $arr[] = date("Y-m-d", $i);
        }
        $fordate = json_encode($arr);

        $orderModel = new OrderModel();
        $resultarr = [];
        foreach($arr as $val){
            $betweenStart = $val.' '.$params['start_time'];
            $betweenEnd   = $val.' '.$params['end_time'];
            $map['a.w_update_time']= ['between', [$betweenStart,$betweenEnd]];
            $resultarr['erp'][] = (int)$orderModel->where($map)->alias('a')
                ->join('inner join erp_order_type as b on a.ebay_id = b.ebay_id')
                ->count();
        }

        $result = json_encode($resultarr);
        $this->assign('result', $result);
        $this->assign('params', $params);
        $this->assign('fordate', $fordate);
        $this->display();
    }
}