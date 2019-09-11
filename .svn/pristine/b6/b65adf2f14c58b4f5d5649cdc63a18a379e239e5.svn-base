<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 10:39
 */
namespace Common\Model;
use Think\Model;

class ErrorLocationOrderDetailModel extends Model
{
    protected $tableName = 'error_location_order_detail';

    /**
    *测试人员谭 2018-01-02 16:07:43
    *说明: 是否完成 ？
    */
    function isComplete($location,$ebay_id){
        $map['status']   = 1;
        $map['location'] = $location;
        $map['ebay_id']  = $ebay_id;

        $RR=$this->where($map)->field('qty,real_qty')->select();
        foreach($RR as $List){
            if($List['qty']-$List['real_qty']>0){
                return false;
            }
        }

        return true;
    }


    // 停止集货
    function StopSortOrder($location,$ebay_id){
        $map['location'] = $location;
        $map['ebay_id']  = $ebay_id;
        $rr=$this->where($map)->save(['status'=>2]);
        return $rr;
    }
}