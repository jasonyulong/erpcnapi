<?php

namespace Order\Controller;


use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Order\Model\CheckSkuMonitorModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\OrderslogModel;
use Package\Model\TopMenuModel;
use Think\Controller;

/**
*测试人员谭 2018-08-14 14:53:39
*说明: 同步包装人员
*/

class RsyncBaleUserController extends Controller{



    /**
    *测试人员谭 2018-08-14 15:10:48
    *说明: 如果有异常就清理掉
    */
    public function clearCheckSku(){
        $ApiCheck   = new ApiCheckskuModel();
        $Orderslog  = new OrderslogModel();
        $OrderModel = new OrderModel();

        $where['status']=1;
        $where['addtime']=['lt',strtotime('-5 hours')];

        $Rs=$ApiCheck->where($where)->field('id,ebay_id')->select();

        foreach($Rs as $List){

            $ebay_id=$List['ebay_id'];
            $id=$List['id'];

            $ebay_status=$OrderModel->where(compact('ebay_id'))->getField('ebay_status');

            if(1731!=$ebay_status){
                continue;
            }


            $ApiCheck->where(compact('id'))->limit(1)->delete();

            $log= $ebay_id.'--系统检查到订单在回收站,删除掉验货记录!';

            echo $log."\n";

            $Orderslog->addordernote($ebay_id,$log);

        }


    }



    public function monitor(){

        $monitorModel=new CheckSkuMonitorModel();

        $ApiCheck   = new ApiCheckskuModel('','',C('DB_CONFIG_READ'));
        $where['status']=1;

        $count=$ApiCheck->where($where)->count();

        $log=$count.'--已包装或者已验货数量--'.date('Y-m-d H:i:s');

        $add['note']=$log;
        $add['addtime']=time();

        $monitorModel->add($add);

    }

}