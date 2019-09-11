<?php

namespace Api\Controller;
use Api\Model\OrderWeightModel;
use Think\Controller;

// 验证一下订单 有没有称重
class CancelOrderController extends Controller{

    //验证订单 是不是可以取消 终止的状态
    function checkOrder(){
        $ebay_id=(int)$_POST['ebay_id'];
        if(!$ebay_id){
            echo json_encode(['status'=>0,'msg'=>'参数错误']);return;
        }
        $EbayWeightModel=new OrderWeightModel();
        $rr=$EbayWeightModel->isExsit($ebay_id);
        if($rr){
            echo json_encode(['status'=>0,'msg'=>'最后一枪已经扫描']);
        }else{
            echo json_encode(['status'=>1,'msg'=>'最后一枪没扫描']);
        }
    }

}