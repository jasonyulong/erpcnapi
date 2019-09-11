<?php
/**
*测试人员谭 2017-11-21 09:39:38
*说明: 订单状态
*/
namespace Api\Controller;


use Common\Model\OrderModel;
use Package\Model\OrderslogModel;
use Think\Controller;
class OrderStstusController extends  Controller{

    // 给线上的 打印提供一个 接口 修改 线下的 为
    function changeStstus(){

        $user     = $_POST['username'];
        $ebay_ids = $_POST['ebay_ids'];
        $from_wms = $_POST['from_wms'];


        $ArrStatus=[
            1723=>'可打印',
            1745=>'等待打印',
            1724=>'等待扫描',
        ];

        if($from_wms==''){
            echo '<div style="color:#911">需要参数为空(wms)</div>';return;
        }

        if($ebay_ids==''){
            echo '<div style="color:#911">需要参数为空(订单id)</div>';return;
        }

        if($user==''){
            echo '<div style="color:#911">需要参数为空(u)</div>';return;
        }

        $ebay_ids=explode(',',$ebay_ids);
        $file              = dirname(dirname(THINK_PATH)) . '/log/order/' . date('YmdH') . '.to1724.txt';
        $msg='';
        $OrderModel=new OrderModel();
        $Orderslog=new OrderslogModel();
        foreach($ebay_ids as $List){
            $ebay_id=$List;
            $ebay_status=$OrderModel->where(compact('ebay_id'))
                ->getField('ebay_status');
            if($ebay_status==''){
                $msg.='<div style="color:#911;">'.$ebay_id.",订单状态异常!</div>";
                continue;
            }

            if($ebay_status!=1723 && 1745!=$ebay_status){
                $msg.='<div style="color:#911;">'.$ebay_id.",订单WMS中不在【可打印 或者等待打印】({$ebay_status})!</div>";
                continue;
            }
            $rr=$OrderModel->where(compact('ebay_id'))->limit(1)->save(['ebay_status'=>1724]);

            if($rr){
                $log="订单在ERP指令下从【{$ArrStatus[$ebay_status]}】转到【等待扫描】操作人:".$user;
                writeFile($file,$log);
                $Orderslog->addordernote($ebay_id,$log,5);
                $msg.='<div style="color:#191;">'.$ebay_id.",订单WMS中，转到【等待扫描】!</div>";
            }

        }
        echo $msg;
    }
}