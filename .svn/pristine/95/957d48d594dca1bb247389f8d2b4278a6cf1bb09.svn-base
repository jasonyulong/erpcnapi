<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 */
namespace Package\Controller;

use Order\Model\OrderTypeModel;
use Package\Model\OrderslogModel;
use Think\Controller;

/**
*测试人员谭 2017-11-18 14:32:32
*说明: 自动清理掉 pick_status = 3 的数据  眼不见为净
*/
class PickBackClearController extends Controller
{


    // 这些状态的 订单,就修改掉 order_type.pick_status =1
    private  $auto_over_status=[1731,2];

    function autoClearPickStatus(){

        if(!IS_CLI){
            echo 'you need run in cli'."\n\n";
        }
        $field='b.ebay_addtime,b.ebay_id,b.ebay_noteb,b.w_add_time,b.ebay_carrier,b.ebay_status ';
        $field='b.ebay_id,b.ebay_status ';

        $OrderType=new OrderTypeModel();
        $map['a.pick_status']=3;
        $map['b.ebay_status']=['in',$this->auto_over_status];

        $orders=$OrderType->alias('a')->join('inner join erp_ebay_order b using(ebay_id)')
            ->where($map)->field($field)->select();

        $Orderslog=new OrderslogModel();


        foreach($orders as $List){
            $ebay_id=$List['ebay_id'];
            $ebay_status=$List['ebay_status'];
            echo $ebay_id.",".$ebay_status."\n";
            $OrderType->where(compact('ebay_id'))->limit(1)->save(['pick_status'=>1]);
            $Orderslog->addordernote($ebay_id,'订单已经在回收站或者已完成,捡货状态自动设置为1');
        }

    }




}