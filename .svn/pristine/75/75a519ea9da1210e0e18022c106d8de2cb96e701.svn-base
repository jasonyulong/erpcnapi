<?php
namespace Package\Service;
use Common\Model\EbayUserModel;
use Common\Model\OrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderLogModel;
use Package\Model\PickOrderModel;

/**
 * Class CreatePickService
 * @package Package\Service
 *  检测并结束拣货单子 这里不需要修改任何 订单 数据  订单数据必须全部 由主页的 操作去修改成 is_delete
 */
class OverOrderService {

    private $PickOrderModel=null;
    private $PickOrderDetailModel=null;
    private $OredrModel=null;
    private $orderType=null;
    private $PickOrderLogModel=null;


    public function __construct(){
        $this->OredrModel           = new OrderModel();
        $this->PickOrderModel       = new PickOrderModel();
        $this->PickOrderDetailModel = new PickOrderDetailModel();
        $this->orderType            = new OrderTypeModel();
        $this->PickOrderLogModel    = new PickOrderLogModel();
    }

    public function OverPickOrder($ordersn){


        $pickOrderData = $this->PickOrderModel->where("ordersn='$ordersn'")->field('isprint,id,type')->find();
        if(empty($pickOrderData)){
            echo $ordersn."拣货单不存在\n";
            return ;
        }

        $isprint=$pickOrderData['isprint'];

        if($isprint!=1 && $isprint!=2){
            echo $ordersn."拣货单不是已打印待确认禁止操作\n";
        }


        $Arr=[
            1=>'overOneSkuOnePcs',
            2=>'overOneSkuOnePcs',
            3=>'overMoreSku',
        ];

        $type=$pickOrderData['type'];

        if(!in_array($type,[1,2,3])){
            echo "神奇的拣货单{$ordersn}不知是什么类型\n\n";
            return ;
        }

        $func=$Arr[$type];

        echo $ordersn."================================={$type}\n";
        //echo $func."-------\n";
        $this->$func($ordersn);

    }
    /**
     *测试人员谭 2018-07-20 21:18:16
     *说明:结束单品单货
     * TODO:
     */
    private function overOneSkuOnePcs($ordersn){

        $OrdersLog=new OrderslogModel();
        $file            = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.checkover.txt';
        $filestatus      = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.checkover.status.txt';
        //查出没有打包的订单，转入异常处理
        $map['is_baled'] = 0;
        //$map['is_delete'] = 1;
        $map['ordersn'] = $ordersn;


        $isprint=$this->PickOrderModel->where(compact('ordersn'))->getField('isprint');

        if($isprint!=1 && $isprint!=2){
            echo $ordersn.',拣货单状态 不允许操作'."\n\n";
            return false;
        }



        $pickDetailData = $this->PickOrderDetailModel
            ->field("ebay_id,is_delete")
            ->where($map)->select();

        $ebayids=[];

        foreach($pickDetailData as $List){
            $ebay_id=$List['ebay_id'];
            $is_delete=$List['is_delete'];

            if(array_key_exists($ebay_id,$ebayids)){
                continue;
            }

            $ebayids[$ebay_id]=1;

            if($is_delete==0){

                $rs=$this->OredrModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');

                if(in_array($rs,[2,1731,2009])){
                    $log='拣货单:'.$ordersn.",订单 {$ebay_id} 状态是 {$rs} 视为已经结束!";
                    //echo $log."\n";
                    writeFile($filestatus, $log . '------' . date('Y-m-d H:i:s') . "\n\n\n");
                    continue;
                }




                $log='拣货单:'.$ordersn.",有一个订单 {$ebay_id} 还是正常的 所以不予结束!";
                echo $log."\n";
                writeFile($file, $log . '------' . date('Y-m-d H:i:s') . "\n\n\n");
                return false;
            }

        }


        $log=$ordersn.' 系统检测到订单全部都包装完毕了 或者 已经移除了，拣货单自动结束';
        echo $log."\n";

        $save=[];
        $save['isprint']=3;
        $rs=$this->PickOrderModel->where(compact('ordersn'))->limit(1)->save($save);

        if(false!==$rs){
            $this->PickOrderLogModel->addOneLog($ordersn,$log);
            echo $log."\n";
            return true;
        }

        return false;

    }



    /**
    *测试人员谭 2018-07-20 21:18:16
    *说明:结束多品的
    */
    private function overMoreSku($ordersn){
        $OrdersLog=new OrderslogModel();
        $file            = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.checkover.txt';
        $filestatus      = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.checkover.status.txt';
        //查出没有打包的订单，转入异常处理
        $map['is_baled'] = 0;
        //$map['is_delete'] = 1;
        $map['ordersn'] = $ordersn;
        $isprint=$this->PickOrderModel->where(compact('ordersn'))->getField('isprint');

        if($isprint!=1 && $isprint!=2){
            echo $ordersn.',拣货单状态 不允许操作'."\n\n";
            return false;
        }




        $pickDetailData = $this->PickOrderDetailModel
            ->field("ebay_id,is_delete")
            ->where($map)->select();


        foreach($pickDetailData as $List){
            $ebay_id=$List['ebay_id'];
            $is_delete=$List['is_delete'];

            if($is_delete==0){

                $rs=$this->OredrModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');

                if(in_array($rs,[2,1731,2009])){
                    $log='[拣货单自动结束程序]--拣货单:'.$ordersn.",订单 {$ebay_id} 状态是 {$rs} 视为已经结束!";
                    //echo $log."\n";
                    writeFile($filestatus, $log . '------' . date('Y-m-d H:i:s') . "\n\n\n");
                    $OrdersLog->addordernote($ebay_id,$log);
                    continue;
                }




                $log='拣货单:'.$ordersn.",有一个订单 {$ebay_id} 还是正常的 所以不予结束!";
                echo $log."\n";
                writeFile($file, $log . '------' . date('Y-m-d H:i:s') . "\n\n\n");
                return false;
            }

        }


        $log=$ordersn.' 系统检测到订单全部都包装完毕了 或者 已经移除了，拣货单自动结束';
        echo $log."\n";
        //return false;

        $save=[];
        $save['isprint']=3;
        $rs=$this->PickOrderModel->where(compact('ordersn'))->limit(1)->save($save);

        if(false!==$rs){
            $this->PickOrderLogModel->addOneLog($ordersn,$log);
            echo $log."\n";
            return true;
        }

        return false;

    }

}
