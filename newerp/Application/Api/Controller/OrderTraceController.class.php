<?php
/**
*测试人员谭 2017-11-17 15:56:51
*说明: 订单轨迹API
*/
namespace Api\Controller;

use Api\Service\OrderTraceService;
use Think\Controller;
class OrderTraceController extends  BaseController{

    function _initialize(){
        parent::checkAuth();
    }

    // 显 示 轨 迹
    function showTrace(){
        $OrderTrace=new OrderTraceService();
        $OrderTrace->show();
    }
}