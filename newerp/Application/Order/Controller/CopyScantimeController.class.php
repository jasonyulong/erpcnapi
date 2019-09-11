<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 13:50
 */
namespace Order\Controller;
use  Common\Model\CarrierModel;
use Common\Model\OrderDetailModel;
use Common\Model\OrderModel;

use Package\Model\ApiOrderWeightModel;
use Package\Model\OrderslogModel;
use Package\Model\TopMenuModel;
use Think\Controller;
use Think\Page;

class CopyScantimeController extends Controller
{

    //TODO: /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Order/CopyScantime/SyncScantime
    public function SyncScantime(){
        ini_set('memory_limit', '1024M');
        $hours=I('h');

        $end=I('end');


        $map=[];
        $map['scantime']=['gt',strtotime("-{$hours} hours")];

        if($end){
            $s=strtotime("-{$hours} hours");
            $e=strtotime("-{$end} hours");
            $map['scantime']=['between',[$s,$e]];
        }

        $ApiWeight=new ApiOrderWeightModel();
        $OrderModel=new OrderModel();

        $RS=$ApiWeight->where($map)->field('ebay_id,scantime')->select();
        echo $ApiWeight->_sql()."\n\n";

        foreach($RS as $List){
            $ebay_id=$List['ebay_id'];
            $scantime=$List['scantime'];
            $OrderModel->where(['ebay_id'=>$ebay_id,'scantime'=>0])->limit(1)->save(['scantime'=>$scantime]);
        }

    }



    //TODO　/usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Order/CopyScantime/fixedOrderScantime/d/15
    public function fixedOrderScantime(){
        ini_set('memory_limit', '1024M');

        $OrderModel=new OrderModel();

        $days=(int)I('d');


        if(!$days){
            $days=15;
        }

        $map['ebay_addtime'] = ['gt', strtotime("-{$days} days")];
        $map['ebay_status']  = 2;
        $map['scantime']     = 0;

        $ApiWeight=new ApiOrderWeightModel();

        $RR=$OrderModel->where($map)->field('ebay_id')->select();

        echo "need fixed orders:".count($RR)."\n";

        foreach($RR as $List){
            $ebay_id=$List['ebay_id'];
            $scantime=$ApiWeight->where(['ebay_id'=>$ebay_id])->getField('scantime');

            if($scantime>0){
                $OrderModel->where(['ebay_id'=>$ebay_id,'scantime'=>0])->limit(1)->save(['scantime'=>$scantime]);
            }
        }

    }



    //测试环境干进去
    public function testlocal(){
        ini_set('memory_limit', '8024M');
        $ApiWeight=new ApiOrderWeightModel();
        $OrderModel=new OrderModel();

        $RS=$ApiWeight->field('ebay_id,scantime')->order('id desc')->limit(9999999)->select();
        echo $ApiWeight->_sql()."\n\n";

        echo count($RS)."\n";

        foreach($RS as $List){
            $ebay_id=$List['ebay_id'];
            $scantime=$List['scantime'];
            $OrderModel->where(['ebay_id'=>$ebay_id,'scantime'=>0])->limit(1)->save(['scantime'=>$scantime]);
        }

    }

}



