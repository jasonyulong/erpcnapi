<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 */
namespace Package\Controller;
use Package\Service\OrderCrossStockListService;
use Package\Service\OrderCurrentStockListService;
use Think\Controller;

/**
 *测试人员谭 2017-12-12 18:38:31
 *说明: 分拣分区 跨仓的
 */
class AbnormalLocationController extends Controller {


    //php tcli.php Package/AbnormalLocation/CheckAbnormalLocation
    function CheckAbnormalLocation(){
        $OrderCrossStockListServivce=new OrderCrossStockListService();

        $OrderCrossStockListServivce->OrderAbnormalUpdate();
        $OrderCrossStockListServivce->OrderSuccessUpdate();


    }

    function CheckCurrentAbnormalLocation(){
        $OrderCurrentStockListServivce = new OrderCurrentStockListService();

        $OrderCurrentStockListServivce->OrderAbnormalUpdate();
        $OrderCurrentStockListServivce->OrderSuccessUpdate();
    }
}