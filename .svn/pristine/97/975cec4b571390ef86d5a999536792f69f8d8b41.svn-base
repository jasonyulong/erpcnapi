<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 14:09
 */
namespace Common\Model;

use Think\Model;

class CurrentLocationOrderDetailModel extends Model
{
    protected $tableName = 'current_location_order_detail';

    /**
     * 说明:是否完成
     * @param string $location  库位
     * @param int $ebay_id 订单号
     * @Author 测试人员杨 2018-01-03
     * @return bool true or false
     */
    function isComplete($location,$ebay_id){
        $map['status']   = 1;
        $map['location'] = $location;
        $map['ebay_id']  = $ebay_id;

        $RR = $this->where($map)->field('qty,real_qty')->select();
        foreach($RR as $List){
            if($List['qty']-$List['real_qty']>0){
                return false;
            }
        }
        return true;
    }

    /**
     * 说明:停止集货
     * @param string $location  库位
     * @param int $ebay_id 订单号
     * @Author 测试人员杨 2018-01-03
     * @return int $rr
     */
    function StopSortOrder($location,$ebay_id){
        $map['location'] = $location;
        $map['ebay_id']  = $ebay_id;
        $rr = $this->where($map)->save(['status'=>2]);
        return $rr;
    }


}