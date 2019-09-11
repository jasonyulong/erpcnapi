<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/30
 * Time: 15:36
 */
namespace Mid\Controller;

use Api\Model\OrderWeightModel;
use Order\Service\OrderInterceptService;
use Package\Model\TopMenuModel;

class TestController
{

    function fixedOrder(){
        $ebayOrderModel = new \Order\Model\EbayOrderModel();
        $MidebayOrderModel = new \Mid\Model\MidEbayOrderModel();
        $ebay_ids=$MidebayOrderModel->where("wms_flag=0")->field('ebay_id')->select();

        foreach($ebay_ids as $list){
            $ebay_id=$list['ebay_id'];

            $ebay_id=$ebayOrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_id');

            if($ebay_id){
                echo $ebay_id."\n";
                $MidebayOrderModel->where(['ebay_id'=>$ebay_id])->limit(1)->save(['wms_flag'=>1]);
            }
        }
    }

}