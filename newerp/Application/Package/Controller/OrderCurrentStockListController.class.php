<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 11:57
 */

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\OrderModel;
use Package\Service\OrderCurrentStockListService;

/**
 *测试人员杨 2018-1-2
 *说明: 分拣分区 本仓的异常 扫描到库位中去(库位:每一格为一个库位)
 */
class OrderCurrentStockListController extends CommonController
{
    /**
     * 本仓异常看板
     */
    public function index()
    {
        // 显示所有location的组状况
        $OrderCrossStockListService = new OrderCurrentStockListService();
        $Locations = $OrderCrossStockListService->StockListView();
        $this -> assign('Locations', $Locations);
        $this -> assign('storeNameArr', C("STORE_NAMES"));
        $this -> assign('currStoreId', C("CURRENT_STORE_ID"));

        $ebay_id=trim(I('ebay_id'));
        if($ebay_id){
            $map['ebay_id'] = $ebay_id;
            $map['ebay_status'] = ['gt',1];
            $OrderModel = new OrderModel();
            $RR = $OrderModel->where($map)->field('ebay_id,ebay_carrier')->find();
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

        $this->display();
    }



    /**
     * 库位列表页面
     */
    function UseLocationView(){
        $location = trim(I('get.location'));
        if($location == ''){
            echo '<div style="color:#191;font-size: 20px;">库位输入错误</div>';
            return;
        }
        $RR = D('CurrentLocation')->where(['location'=>$location])->find();

        if($RR['status']!=1){
            echo '<div style="color:#191;font-size: 20px;">库位'.$location.',当前不是空库位</div>';
            return;
        }
        $this->assign('location',$location);
        $this->display();
    }

    /**
     * 根据跟踪号获取ebay_id
     */
    function getebayIdByTracknumber(){
        $trackNumber = strtoupper(trim(I('post.tracknumber')));
        if($trackNumber == ''){
            echo json_encode(['status'=>0,'msg'=>'跟踪号输入有误!']);return;
        }

        $OrderModel = new OrderModel();
        $RR = $OrderModel->where(['ebay_tracknumber' => $trackNumber])
            ->field('ebay_status,ebay_id')->limit(1)->find();

        if(empty($RR)){
            $RR = $OrderModel->where(['pxorderid' => $trackNumber])
                ->field('ebay_status,ebay_id')->limit(1)->find();

            if(empty($RR)){
                echo json_encode(['status'=>0,'msg'=>'跟踪号'.$trackNumber.',找不到订单!']);return;
            }
        }

        $ebay_id     = $RR['ebay_id'];
        echo json_encode(['status'=>1,'msg'=>'跟踪号'.$trackNumber.',找不到订单!','data'=>$ebay_id]);return;
    }

    /**
     * 说明: 确认占用这个库位
     */
    function DoUseLocation(){
        $location = trim(I('post.location'));
        $ebay_id = trim(I('post.ebay_id'));

        if($ebay_id==''){
            echo json_encode(['status'=>0,'msg'=>'订单号有误!']);return;
        }

        if($location==''){
            echo json_encode(['status'=>0,'msg'=>'库位有误!']);return;
        }

        $OrderCurrentStockService = new OrderCurrentStockListService();
        $RR = $OrderCurrentStockService->DoUseLocation($location,$ebay_id);
        echo json_encode($RR);
    }

    /**
     * 查询看板中所有的异常订单还需要集多少
     */
    function viewNote() {
        $storeid = trim(I('get.storeid'));
        $orderCurrentStockService = new OrderCurrentStockListService();
        $orderInfo = $orderCurrentStockService->getOrderInfo($storeid);

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
                $orderInfo[$key]['count']  = ($orderInfo[$key]['count'] !='')  ? $orderInfo[$key]['count']  : '';
            }
        }

        $this->assign('orderInfo', $orderInfo);
        $this->assign('storeid', $storeid);
        $this->display();
    }

    // 拣货单异常扫入sku
    function scanSkuIntoListByOrderSn(){
        $this->display();
    }

    //盲扫SKU
    function scanSkuIntoListBySku(){
        $this->display();
    }

    // 扫描一个SKU 相关订单编号
    function  ScanOneSKUWithOrdersn(){
        $orderSn = I('post.ordersn');
        $sku = strtoupper(I('post.sku'));
        $Data['status'] = 1;
        $Data['msg']='';
        if($orderSn == ''|| $sku == ''){
            $Data['msg'] = '<div style="color:#911">拣货单号 或者 SKU 有误!</div>';
            $Data['status'] = 0;
            echo json_encode($Data);
            return ;
        }

        $OrderCurrentStockListService=new OrderCurrentStockListService();
        $RR = $OrderCurrentStockListService->ScanOneSKUWithOrdersn($orderSn,$sku);
        echo json_encode($RR);
    }

    /**
     *测试人员杨 2017-01-03
     *说明:扫描一个sku 看看在当前的异常里是否存在
     */
    function ScanOneSKU(){
        $sku = strtoupper(I('post.sku'));
        if($sku == ''){
            $Data['msg']= '<div style="color:#911">SKU 有误!</div>';
            $Data['status'] = 0;
            echo json_encode($Data);
            return ;
        }

        $OrderCurrentStockListService = new OrderCurrentStockListService();
        $RR = $OrderCurrentStockListService->ScanOneSKU($sku);
        echo json_encode($RR);
    }

    /**
     * 显示订单的集货进度
     */
    function viewOrderSku() {
        $ebay_id                = trim(I('ebay_id'));
        $orderCurrentStockService = new OrderCurrentStockListService();
        $SKU_info               = $orderCurrentStockService->getOrderSteps($ebay_id);
        $this -> assign('storeNameArr', C("STORE_NAMES"));
        $this -> assign('currStoreId', C("CURRENT_STORE_ID"));
        if ($SKU_info === false) {
            echo '订单：' . $ebay_id . '不存在。';
            exit;
        }

        $this->assign('skuInfo', $SKU_info);
        $this->display();
    }

    /**
     *  放弃集货
     */
    function GiveUPOrder(){
        $ebay_id = intval($_POST['ebay_id']);

        if($ebay_id==0){
            $Data['msg']= '<div style="color:#911">您提交的货物有误!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }
        $OrderCurrentStockListService = new OrderCurrentStockListService();

        $RR = $OrderCurrentStockListService->GiveUPOrder($ebay_id);

        echo json_encode($RR);
    }
}