<?php
namespace Order\Model;

use Think\Model;

class EbayOrderModel extends Model
{
    protected $tableName = 'erp_ebay_order';

    /**
     * 创建人:朱诗萌 创建时间 2017-10-30 16:21:32
     * 说明
     */
    public function cancelOrder($orderSn) {
    }

    /**
     * get wait print order list
     * @author 	Rex
     * @since 	2017-12-04 14:20:00
     */
    public function getWaitPrintOrderList() {
    	$endTime = date('Y-m-d H:i:s', time()-3600*36);
    	$where['w_update_time'] = ['elt', $endTime];
    	$where['ebay_status'] = '1745';
    	$list = $this->where($where)->field('ebay_id,ebay_status')->select();		//ebay_addtime,w_add_time,w_update_time
    	return $list;
    }

}
