<?php
namespace Report\Service;

class OrderCountService {

	/**
	 * start create report_order_board data
	 * @author Rex
	 * @since  2017-11-11 11:20
	 */
	public function createReportBoardData($to_date) {
        $map['ebay_addtime'][] = ['egt',strtotime($to_date.' 00:00:00')];
        $map['ebay_addtime'][] = ['elt',strtotime($to_date.' 23:59:59')];

        //echo $to_date.'<br/>';

        $reportOrderBoardModel = new \Report\Model\ReportOrderBoardModel();

        $orderModel = new \Common\Model\OrderModel();
        $total_order_count = $orderModel->where($map)->count('DISTINCT ebay_id');
        $midOrderModel = new \Mid\Model\MidEbayOrderModel();
        $total_mid_order_count = $midOrderModel-> where($map)->count('DISTINCT ebay_id');
        //echo $midOrderModel->_sql();
        //var_dump($total_mid_order_count);
        //echo '<br/>';

        $map['ebay_status'] = 1723;
        $total_order_count_1723 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1745;
        $total_order_count_1745 = $orderModel->where($map)->count('DISTINCT ebay_id');
        
        $map['ebay_status'] = 1724;
        $total_order_count_1724 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 2009;
        $total_order_count_2009 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1733;
        $total_order_count_1733 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1731;
        $total_order_count_1731 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 2;
        $total_order_count_2 = $orderModel->where($map)->count('DISTINCT ebay_id');


        $where['ebay_status']=2;
        $where['scantime']=$map['ebay_addtime'];

        $to_day_total= $orderModel->where($where)->count('DISTINCT ebay_id');


        $date = date('Y-m-d H:i:s');
        $saveData = array(
        	'to_date'=>$to_date,
        	'total_mid_order_count'	=> $total_mid_order_count,
        	'total_order_count'		=> $total_order_count,
        	'total_order_count_1723'=> $total_order_count_1723,
        	'total_order_count_1745'=> $total_order_count_1745,
        	'total_order_count_1724'=> $total_order_count_1724,
        	'total_order_count_2009'=> $total_order_count_2009,
        	'total_order_count_1733'=> $total_order_count_1733,
        	'total_order_count_1731'=> $total_order_count_1731,
            'total_order_count_2'   => $total_order_count_2,
            'to_day_total'   => $to_day_total,
        	'add_time'				=> $date,
        	'update_time'			=> $date,
        );

/*        echo '<pre>';
        var_dump($saveData);*/

        $row = $res = $reportOrderBoardModel->where(['to_date'=>$to_date])->limit(1)->find();
        if ($row) {
        	unset($saveData['add_time']);
        	$ret = $reportOrderBoardModel->where("id = {$row['id']}")->save($saveData);
        } else {
        	$ret = $reportOrderBoardModel->add($saveData);
        }
        return $ret;
	}

    /**
     * start create report_order_board data
     * @author Rex
     * @since  2017-11-11 11:20
     */
    public function createReportBoardData2($to_date) {
        $map['w_update_time'][] = ['egt',$to_date.' 00:00:00'];
        $map['w_update_time'][] = ['elt',$to_date.' 23:59:59'];

        //echo $to_date.'<br/>';
        $map2['wms_update_time'][] = ['egt',$to_date.' 00:00:00'];
        $map2['wms_update_time'][] = ['elt',$to_date.' 23:59:59'];

        $reportOrderBoard2Model = new \Report\Model\ReportOrderBoard2Model();

        $orderModel = new \Common\Model\OrderModel();
        $total_order_count = $orderModel->where($map)->count('DISTINCT ebay_id');
        $midOrderModel = new \Mid\Model\MidEbayOrderModel();
        $total_mid_order_count = $midOrderModel-> where($map2)->count('DISTINCT ebay_id');
        //echo $midOrderModel->_sql();exit;
        //var_dump($total_mid_order_count);
        //echo '<br/>';

        $map['ebay_status'] = 1723;
        $total_order_count_1723 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1745;
        $total_order_count_1745 = $orderModel->where($map)->count('DISTINCT ebay_id');
        
        $map['ebay_status'] = 1724;
        $total_order_count_1724 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 2009;
        $total_order_count_2009 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1733;
        $total_order_count_1733 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 1731;
        $total_order_count_1731 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $map['ebay_status'] = 2;
        $total_order_count_2 = $orderModel->where($map)->count('DISTINCT ebay_id');

        $date = date('Y-m-d H:i:s');
        $saveData = array(
            'to_date'=>$to_date,
            'total_mid_order_count' => $total_mid_order_count,
            'total_order_count'     => $total_order_count,
            'total_order_count_1723'=> $total_order_count_1723,
            'total_order_count_1745'=> $total_order_count_1745,
            'total_order_count_1724'=> $total_order_count_1724,
            'total_order_count_2009'=> $total_order_count_2009,
            'total_order_count_1733'=> $total_order_count_1733,
            'total_order_count_1731'=> $total_order_count_1731,
            'total_order_count_2'   => $total_order_count_2,
            'add_time'              => $date,
            'update_time'           => $date,
        );

/*        echo '<pre>';
        var_dump($saveData);*/

        $row = $res = $reportOrderBoard2Model->where(['to_date'=>$to_date])->limit(1)->find();
        if ($row) {
            unset($saveData['add_time']);
            $ret = $reportOrderBoard2Model->where("id = {$row['id']}")->save($saveData);
        } else {
            $ret = $reportOrderBoard2Model->add($saveData);
        }
        return $ret;
    }


    /**
    * 显示数据
    * @author 王模刚
    * @since 2017 11 11
    */
    public function getData(){
            $ReportOrderBoardModel = new \Report\Model\ReportOrderBoardModel();
            $date = date('Y-m-d',strtotime('-1 month'));
            if($date < '2017-11-04'){
                $date = '2017-11-04';
            }
            $where['to_date'] = ['egt',$date];
            return $ReportOrderBoardModel->where($where)->order('to_date desc')->select();
    }


}