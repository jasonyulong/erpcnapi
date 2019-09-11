<?php
namespace Order\Model;

use Think\Model;

class EbayOrderDetailModel extends Model{

    protected $tableName = 'erp_ebay_order_detail';

    /**
     * 根据订单sn获取订单详情
     */
    public function getDetailByEbayOrderSn($sn){
    	$where['ebay_ordersn'] = $sn;
    	return $this->where($where)->order('ebay_id asc')->select();
    }

    /**
    *测试人员谭 2017-01-16 17:07:27
    *说明: Joom 订单验证 是否存在 子信息
    */
    public function isExsit($ordersn,$recordnumber){
        $where['ebay_ordersn'] = $ordersn;
        $where['recordnumber'] = $recordnumber;
        $rr=$this->where($where)->limit(1)->select();
        if($rr){
            return true;
        }
        return false;
    }
}
