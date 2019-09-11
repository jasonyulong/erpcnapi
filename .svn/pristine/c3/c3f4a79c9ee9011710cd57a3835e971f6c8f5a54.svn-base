<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 * 跨仓异常看板
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ErrorLocationModel;
use Common\Model\OrderModel;
use Package\Model\TopMenuModel;
use Package\Service\OrderCrossStockListService;

/**
 *测试人员谭 2017-12-12 18:38:31
 *说明: 分拣分区 跨仓的异常 要扫描到 一个一个 的格子这中去
 */
class OrderCrossStockListController extends CommonController {



    /**
    *测试人员谭 2017-12-21 14:39:05
    *说明: 格子看板
    */
    function index() {
        $OrderCrossStockListService=new OrderCrossStockListService();

        // 显示所有location 的组状况 !
        $Locations=$OrderCrossStockListService->StockListView();

        $ebay_id=trim(I('ebay_id'));

        if($ebay_id){
            $map['ebay_id']=$ebay_id;
            $map['ebay_status']=['gt',1];
            $OrderModel=new OrderModel();
            $RR=$OrderModel->where($map)->field('ebay_id,ebay_carrier')->find();
            if(empty($RR)){
                $error='找不到订单!';
                $this->assign('error', $error);
            }else{
                $ebay_carrier = $RR['ebay_carrier'];
                $CONFIGD=include(ROOT_PATH.'/newerp/Application/Transport/Conf/config.php');
                $TEMP15=$CONFIGD['CARRIER_TEMPT_15'];
                if(array_key_exists($ebay_carrier,$TEMP15)){
                    $mod=2;
                }else{
                    $mod=1;
                }

                $OrderModel->where([
                    'ebay_status'=>['in',[1723,1745,1724]],
                    'ebay_id'=>$ebay_id
                ])->limit(1)->save(['ebay_status'=>1724]);

                $url='t.php?s=/Transport/Print/PrintAllCarrier&bill='.$ebay_id.'&mod='.$mod;
                $this->assign('error', '');
                $this->assign('reprint',$url);
            }

        }
        $this -> assign('storeNameArr', C("STORE_NAMES"));
        $this -> assign('currStoreId', C("CURRENT_STORE_ID"));
        $this -> assign('otherStoreId', C("MERGE_STORE_ID"));
        $this -> assign('Locations', $Locations);

        $this->display();
    }



    // 拣货单异常扫入sku
    function scanskuintoListByordersn(){

        $this->display();
    }

    //盲扫SKU
    function scanskuintoListBysku(){

        $this->display();
    }

    //根据erp 的
    // 找到看板中的共同sku
    function getSKUByCarNumber(){
        $carnumber=trim($_GET['carnumber']);

    }



    // 扫描一下这个鬼
    function  ScanOneSKUWithOrdersn(){

        $ordersn=$_POST['ordersn'];
        $sku=strtoupper($_POST['sku']);
        $Data['status']=1;
        $Data['msg']='';
        if($ordersn==''||$sku==''){
            $Data['msg']= '<div style="color:#911">拣货单号 或者 SKU 有误!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }

        $OrderCrossStockListService=new OrderCrossStockListService();

        $RR=$OrderCrossStockListService->ScanOneSKUWithOrdersn($ordersn,$sku);

        echo json_encode($RR);

    }


    /**
    *测试人员谭 2017-12-27 22:08:15
    *说明:扫描一个sku 看看当前的异常里面有没有
    */
    function ScanOneSKU(){
        $sku=strtoupper($_POST['sku']);
        if($sku==''){
            $Data['msg']= '<div style="color:#911">SKU 有误!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }

        $OrderCrossStockListService=new OrderCrossStockListService();

        $RR=$OrderCrossStockListService->ScanOneSKU($sku);

        echo json_encode($RR);


    }


    // 放弃集货
    function GiveUPOrder(){
        $ebay_id=intval($_POST['ebay_id']);

        if($ebay_id==0){
            $Data['msg']= '<div style="color:#911">您提交的货物有误!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }
        $OrderCrossStockListService=new OrderCrossStockListService();

        $RR=$OrderCrossStockListService->GiveUPOrder($ebay_id);

        echo json_encode($RR);
    }




    function UseLocationView(){
        $location=trim($_GET['location']);
        if($location==''){
            echo '<div style="color:#191;font-size: 20px;">库位输入错误</div>';
            return;
        }
        $ErrorLocation=new ErrorLocationModel();

        $RR=$ErrorLocation->where(['location'=>$location])->find();

        if($RR['status']!=1){
            echo '<div style="color:#191;font-size: 20px;">库位'.$location.',当前不是空库位</div>';
            return;
        }

        $this->assign('location',$location);
        $this->display();
    }


    /**
    *测试人员谭 2017-12-23 17:18:30
    *说明: 确认占用这个库位
    */
    function DoUseLocation(){
        $location=$_POST['location'];
        $ebay_id=$_POST['ebay_id'];

        if($ebay_id==''){
            echo json_encode(['status'=>0,'msg'=>'订单号有误!']);return;
        }

        if($location==''){
            echo json_encode(['status'=>0,'msg'=>'库位有误!']);return;
        }

        $OrderCrossStockService=new OrderCrossStockListService();
        $RR=$OrderCrossStockService->DoUseLocation($location,$ebay_id);
        echo json_encode($RR);
    }

    // 根据跟踪号 转为 ebay_id
    // 系统始终 只有 根据ebay_id 初始化这个
    function getebayIdByTracknumber(){
        $tracknumber=strtoupper(trim($_POST['tracknumber']));
        if($tracknumber==''){
            echo json_encode(['status'=>0,'msg'=>'跟踪号输入有误!']);return;
        }


        $OrderModel=new OrderModel();
        $RR=$OrderModel->where(['ebay_tracknumber'=>$tracknumber])
            ->field('ebay_status,ebay_id')->limit(1)->find();

        if(empty($RR)){
            $RR=$OrderModel->where(['pxorderid'=>$tracknumber])
                ->field('ebay_status,ebay_id')->limit(1)->find();

            if(empty($RR)){
                echo json_encode(['status'=>0,'msg'=>'跟踪号'.$tracknumber.',找不到订单!']);return;
            }
        }
        /*
         *
          $OrderMenu = new TopMenuModel();
          $TopMenu   = $OrderMenu->getMenuName();
          $ebay_status = $RR['ebay_status'];
         if(!in_array($ebay_status,[1723,1724,1724])){
             echo json_encode(['status'=>0,'msg'=>'跟踪号'.$tracknumber.',订单：'.$ebay_id.' 状态 【'.$TopMenu[$ebay_status].'】不允许发货!']);
             return;
         }
        **/

        $ebay_id     = $RR['ebay_id'];


        echo json_encode(['status'=>1,'msg'=>'跟踪号'.$tracknumber.',找不到订单!','data'=>$ebay_id]);return;


    }


    /**
     * hank
     * 显示订单的集货进度
     */
    function viewOrderSku() {
        $ebay_id                = trim(I('ebay_id'));
        $orderCrossStockService = new OrderCrossStockListService();
        $SKU_info               = $orderCrossStockService->getOrderSteps($ebay_id);

        if ($SKU_info === false) {
            echo '订单：' . $ebay_id . '不存在。';
            exit;
        }
        $this -> assign('storeNameArr', C("STORE_NAMES"));
        $this -> assign('currStoreId', C("CURRENT_STORE_ID"));
        $this->assign('skuInfo', $SKU_info);
        $this->display();
    }

    /**
     * hank
     * 查询看板中所有的异常订单还需要集多少
     */
    function viewNote() {
        $storeid            = trim(I('storeid'));
        $orderCrossStockService = new OrderCrossStockListService();
        $orderInfo               = $orderCrossStockService->getOrderInfo($storeid);

        if ($orderInfo === false) {
            echo '没有异常订单';
            exit;
        }


        //获取可用库存
        $skuArr = array_column($orderInfo,'sku'); $storeId = $orderInfo[0]['storeid'];
        $ErpInStoreApi = new ErpInstoreApiController();
        $skuArray = $ErpInStoreApi->getUsableSkuByApi($storeId,$skuArr);
        $result = json_decode($skuArray,true);
        if($result['status'] == 1){
            foreach($orderInfo as $key=>$val){
                foreach($result['data'] as $k=>$v){
                    if($v['sku'] == $val['sku']){
                        $orderInfo[$key]['count'] = $v['count'];
                    }
                }
                $orderInfo[$key]['count']  = ($orderInfo[$key]['count'] != '')  ? $orderInfo[$key]['count']  : '';
            }
        }

        $this->assign('orderInfo', $orderInfo);
        $this->assign('storeid', $storeid);
        $this->display();
    }
}