<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\EbayUserModel;
use Package\Service\ConfirmService;
use Think\Log;

/**
 * Class CreatePickController
 * @package Package\Controller
 *  确认捡货单
 */
class ConfirmController extends CommonController{

    /**
    *测试人员谭 2017-05-25 16:01:44
    *说明:// 从拣货单子中删除一个订单  应用场景：当库存不够，或者某写时候 订单 不能去发货。
    */
    function deleteOnOrder(){
        $ordersn=I('ordersn');
        $ebay_id=(int)I('ebay_id');

        if($ordersn==''||$ebay_id==0){
            echo json_encode(['status'=>0,'msg'=>'参数错误!']); return;
        }

        if(!can('del_pick_detail')){
            echo json_encode(['status'=>0,'msg'=>'您没有权限移除订单']); return;
        }

        $ConFirmSes=new ConfirmService();

        $RR=$ConFirmSes->deleteOneOrder($ordersn,$ebay_id);

        echo json_encode($RR); return;

    }


    /**
    *测试人员谭 2017-05-25 21:00:04
    *说明: 删掉整个 捡货单
    */
    function deleteOnePkOrder(){
        $ordersn=I('ordersn');

        if($ordersn==''){
            echo json_encode(['status'=>0,'msg'=>'参数错误!']);return;
        }

        if(!can('del_pick_order')){
            echo json_encode(['status'=>0,'msg'=>'您没有权限移除订单']); return;
        }

        $ConFirmSes=new ConfirmService();

        $RR=$ConFirmSes->deleteOnePickOrder($ordersn);

        echo json_encode($RR); return;

    }


    /**
    *测试人员谭 2017-05-25 21:02:48
    *说明: 分配库存 仅仅 是分配库存，刷新捡货单，可以手工 删掉 你认为 不该
    */

    // TODO 预确认
    function AllocationInventory(){
        $ordersn=I('ordersn');
        $ConFirmSes=new ConfirmService();
        $RR=$ConFirmSes->allocationInventoryBYConfirm($ordersn);
        echo json_encode($RR);return;
    }


    /**
    *测试人员谭 2017-05-25 21:09:54
    *说明: 确定捡货单
     *
     * 非常
    */
    //TODO 【真的确认】
    //TODO 非常重要的方法! 操作前必须要提示
    //TODO  1. 会分配一遍库存
    //TODO  2. 会移除分配不到 库存的订单
    //TODO  3. 操作完之后 要运行一遍 getBackSKU 确定有没有结余，有结余 一定要 立刻 马上 还回去！！！

    function DoConFirmPickOrder(){
        $ordersn=I('ordersn');

        if($ordersn==''){
            echo json_encode(['status'=>0,'msg'=>'参数错误!']); return;
        }

        if(!can('del_pick_confirm')){
            echo json_encode(['status'=>0,'msg'=>'您没有权限确认拣货单,确认拣货单 必须要有删除订单权限']);return;
        }

        $piuserid=$_REQUEST['userid'];
        $User=new EbayUserModel();
        $UserName=$User->getusername($piuserid);

        if($UserName==''){
            echo json_encode(['status'=>0,'msg'=>'工号有误!系统中不存在哦!']); return;
        }

        $ConFirmSes=new ConfirmService();

        $RR=$ConFirmSes->ConFirmPickOrder($ordersn);

        if(!$RR['status']){ // 失败了
            echo json_encode($RR); return;
        }

        $data['status'] = 1;
        $data['msg']    = '';
        $data['data']    = [];
        //操作成功了
        $Backsku=$ConFirmSes->getBackSKU($ordersn);

        if(!$Backsku['status']){
            $data['msg']='确认拣货单成了,但是获取退回SKU失败了:'.$Backsku['msg'].',请点击查看需退回SKU!';
        }else{
            if(count($Backsku['data'])>0){
                $data['msg']='确认拣货单成了,请注意有需要退回的sku!请立刻清点退回库位！';
                $data['data']=$Backsku['data'];
            } else {
                $data['msg'] = '确认拣货单成功, 没有需要退回的SKU.';
            }
        }

        echo json_encode($data); return;

    }





    /**
    *测试人员谭 2017-05-25 21:03:11
    *说明:  一张捡货单 不能完整拣出来的时候，需要分配库存需要 查看 需要还回去的 库存信息
    */
    function ViewBackSKU(){
        $ordersn=I('get.ordersn');
        $ConFirmSes=new ConfirmService();

        $RR=$ConFirmSes->getBackSKU($ordersn);
        $this -> assign('SKUArr', $RR);
        $this -> assign('ordersn', $ordersn);
        $this->display();
       // echo json_encode($RR); return;

        /**
        *测试人员谭 2017-05-25 21:05:00
        *说明:
         * array(
              'sku'=>$sku,
              'backqty'=>$backqty,
              'goods_name'=>$goods_name,
              'location'=>$location,
              'pic'=>$pic,
         * ),
        */
    }



}