<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ErpEbayGoodsModel;
use Common\Model\OrderModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\EbayGoodsModel;
use Package\Model\OrderPackageModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;
use Package\Service\MakeBaleService;
use Think\Cache\Driver\Redis;
use Think\Controller;
use Think\Page;

/**
 * Class CreatePickController
 * @package Package\Controller
 * 订单
 */
class OrderStatusController extends Controller
{


    public function _initialize()
    {

        if(!IS_CGI){
            echo 'Must Run in Cli'."\n\n";
            die();
        }

    }


    public $OrderStstus = array(
        '0'   => '等待打印',
        '1'   => '已打印未确认',
        '2'   => '已经确认',
        '3'   => '已经完成',
        '100' => '废除',
    );

    public function OverOrder(){
        $TopMenu=new TopMenuModel('','',C('DB_CONFIG_READ'));


        $PickOrder=new PickOrderModel('','',C('DB_CONFIG_READ'));

        $PickOrderDetail=new PickOrderDetailModel('','',C('DB_CONFIG_READ'));

        /**
        *测试人员谭 2018-07-24 08:52:33
        *说明: $Arr['ebay_status'];
        */
        $Arr=$TopMenu->getMenuName();


        $map['isprint']=['lt',3];









    }
}