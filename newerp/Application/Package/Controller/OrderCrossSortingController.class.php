<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Package\Model\PickOrderModel;
use Package\Service\OrderCrossSortingService;

/**
 *测试人员谭 2017-12-12 18:38:31
 *说明: 分拣分区 跨仓的
 */
class OrderCrossSortingController extends CommonController {



    function index() {
        #echo 'SecondPickController';
        $this->display();
    }


    //跨仓单----- 初始化掉一次
    //
    function  initCrossOrder() {
        $ordersn = trim(I("ordersn"));

        if (empty($ordersn)) {
            echo '<div style="color:red;">请先扫描拣货单</div>';
            exit;
        }

        $pickOrderModel       = new PickOrderModel();



        $pickInfo = $pickOrderModel->where(['ordersn' => $ordersn])->find();

        if (empty($pickInfo)) {
            echo '<div style="color:red;">拣货单：' . $ordersn . '不存在。</div>';
            exit;
        }

        if($pickInfo['is_cross']!=1){
            echo '<div style="color:red;">拣货单：' . $ordersn . '不是跨仓类型,你逗我？</div>';
            exit;
        }

        $OrderSortingService=new OrderCrossSortingService();

        $dd=$OrderSortingService->isInit($ordersn);

        if($dd['status']!=1){
            echo '<div style="color:red;">包裹：' . $ordersn . '：'.$dd['msg'].'</div>';
            exit;
        }


        $OrderSortingService->InitOrderConfirm($ordersn);
        $OrderSortingService->InitOrderDetailSkustr($ordersn);

        echo '<div style="color:green;">包裹：' . $ordersn . '初始化成功。</div>';
    }

    /**
    *测试人员谭 2017-12-13 19:55:51
    *说明: 扫描一个sku
    */
    function ScanOneSKU(){

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

        $OrderSortingService=new OrderCrossSortingService();
        $RR=$OrderSortingService->ScanOneSKU($ordersn,$sku);

        if(!$RR['status']){
            $Data['msg']= '<div class="SecondPick_error" style="color:#911">'.$RR['msg'].'!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }


        $number=$RR['data'];

        $Data['msg']= '<div class="show_location" sku="'.$sku.'" date="'.date('H:i:s').'">'.$number.'</div>';
        $Data['status']=1;
        $Data['data']=$number;
        echo json_encode($Data);
        return ;

    }


    /**
     *测试人员谭 2017-12-13 19:55:11
     *说明: 1次确认
     */
    function ConfirmCurrentStore(){
        $ordersn=$_GET['ordersn'];
        $OrderSortingService=new OrderCrossSortingService();

        if($ordersn==''){
            echo '<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }

        $RR=$OrderSortingService->ConfirmCurrentStore($ordersn);

        if($RR['status']==1){
            echo '<div style="color:#1a1;font-size:30px;">'.$RR['msg'].'</div>';
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg1'].'</div>';
        }else{
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg1'].'</div>';
        }

    }

    function ConfirmCurrentStoreBefore(){
        $this->ConfirmOtherStoreBefore(1);
    }

    /**
    *测试人员谭 2017-12-18 20:22:00
    *说明: 确认的时候看一下! 返回的HTML 有一个 button 来 触发ConfirmOtherStore  预览
    */
    function ConfirmOtherStoreBefore($type=0){
        $currStoreId    = C("CURRENT_STORE_ID");
        $mergeStoreId   = C("MERGE_STORE_ID");
        $storeArrName   = C("STORE_NAMES");
        $ordersn=$_GET['ordersn'];
        $HeadSuc='<!--SUCCESS-->';
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }
        $OrderSortingService=new OrderCrossSortingService();
        $RR=$OrderSortingService->getOrderSortStatus($ordersn,true);
        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }
        $MainArr=$RR['data'];// 这里将所有的没有捡到或的

        $SKU_196=[];
        $SKU_234=[];
        foreach($MainArr as $ebay_id=>$Order){
            foreach($Order as $List){
                $pic        = $List['pic'];
                $qty        = $List['qty']; // 一共需要多少个
                $sku        = $List['sku'];
                $storeid    = $List['storeid'];
                $qty_com    = $List['qty_com']; // 分配到多少个
                $shenyu=$qty-$qty_com;

                if($storeid == $currStoreId){
                    if(!isset($SKU_196[$sku])){
                        $SKU_196[$sku]['qty']=0;
                        $SKU_196[$sku]['pic']=$pic;
                    }
                    $SKU_196[$sku]['qty']+=$shenyu;
                }else{
                    if(!isset($SKU_234[$sku])){
                        $SKU_234[$sku]['qty']=0;
                        $SKU_234[$sku]['pic']=$pic;
                    }
                    $SKU_234[$sku]['qty']+=$shenyu;
                }
            }
        }

//        debug($SKU_196);
//        debug($SKU_234);
        echo '<table class="ebay_sku_table" style="width:80%;margin:15px;">';

        $url=C('ONLINE_PIC_URL');
        $storeName = $storeArrName[$currStoreId];
        $html='';
        foreach($SKU_196 as $sku=>$skuInfo){
            if($skuInfo['qty']==0){continue;}
            $html.='<tr><td><b>'.$sku.'*<span style="color:#911">'.$skuInfo['qty'].'</span></b>&nbsp;&nbsp;('.$storeName.')</td>';
            $html.= '<td><img style="height:48px;width:48px;" src="'.$url.'/images/'.$skuInfo['pic'].'"/></td></tr>';
        }

        if($type==0){
            $storeName='<b style="color:#911">'.$storeArrName[$mergeStoreId].'</b>';
            foreach($SKU_234 as $sku=>$skuInfo){
                if($skuInfo['qty']==0){continue;}
                $html.='<tr><td><b>'.$sku.'*<span style="color:#911">'.$skuInfo['qty'].'</span></b>&nbsp;&nbsp;('.$storeName.')</td>';
                $html.= '<td><img style="height:48px;width:48px;" src="'.$url.'/images/'.$skuInfo['pic'].'"/></td></tr>';
            }
        }


        echo $html;
        echo '</table>';


        if($type==0){
            echo '<div style="margin:10px;"><input class="btn btn-sm btn-danger" value="确认集货仓的所有物品" onclick="TrueOther_to_end()"/></div>';
        }else{
            echo '<div style="margin:10px;"><input class="btn btn-sm btn-danger" value="确认本仓的所有物品"  onclick="TrueForced_to_end()"/></div>';
        }
    }

    /**
    *测试人员谭 2017-12-13 19:55:11
    *说明: 真的2次确认
    */
    function ConfirmOtherStore(){
        $ordersn=$_GET['ordersn'];
        $OrderSortingService=new OrderCrossSortingService();

        if($ordersn==''){
            echo '<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }

        $RR=$OrderSortingService->ConfirmOtherStore($ordersn);

        if($RR['status']==1){
            echo '<div style="color:#1a1;font-size:30px;">'.$RR['msg'].'</div>';
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg1'].'</div>';
        }else{
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg1'].'</div>';
        }
    }


    /**
    *测试人员谭 2017-12-13 19:54:00
    *说明: 本仓库应退回
    */
    function showCurrentStoreBackSKUS(){
        $ordersn=$_GET['ordersn'];
        $OrderSortingService=new OrderCrossSortingService();
    }


    /**
    *测试人员谭 2017-12-13 19:54:10
    *说明: 其他仓库应退回
    */
    function showOtherStoreBackSKUS(){
        $ordersn=$_POST['ordersn'];
        $OrderSortingService=new OrderCrossSortingService();
    }



    /**
    *测试人员谭 2017-12-18 18:28:34
    *说明: viewBadOrder 显示应该退回的sku
    */
    function viewBadOrder(){
        $ordersn=$_GET['ordersn'];

        if($ordersn==''){
            echo '<div style="color:#911">单号为空</div>'; return;
        }

        $OrderSortingService=new OrderCrossSortingService();

        $RR=$OrderSortingService->viewBadOrder($ordersn);

        if(!$RR['status']){
            echo '<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $MainArr=$RR['data'];
        $i=0;
        $index=[];
        foreach($MainArr as $Order){
            $i++;
            $combineid=$Order['combineid'];
            $index[]=$combineid;
        }


        for($i=1;$i<=70;$i++){
            if($i%10==1){
                echo '<div style="height:80px;border:#119">';
            }
            if(in_array($i,$index)){
                echo '<div style="color:#FFF;background:#911;font-size:40px;height:50px;width:50px;margin:15px;float:left;border:2px solid #33a;">'.$i.'</div>';
            }else{
                echo '<div style="color:#000;font-size:40px;height:50px;width:50px;margin:15px;float:left;border:2px solid #33a;">'.$i.'</div>';
            }
            if($i%10==0){
                echo '<div style="clear: both;"></div>';
            }

        }

    }


    //手动创建 调拨单子
    function createTransferOrder(){
        $ordersn=$_GET['ordersn'];

        if($ordersn==''){
            echo '<div style="color:#911">单号为空</div>'; return;
        }

        $OrderSortingService=new OrderCrossSortingService();
        $RR=$OrderSortingService->CreateTransferOrder($ordersn);

        if($RR['status']==1){
            echo '<div style="color:#191">'.$RR['msg'].'</div>';return;
        }
        echo '<div style="color:#911">'.$RR['msg'].'</div>';return;
    }



    function checkTransferOrder(){
        $ordersn=$_GET['ordersn'];

        if($ordersn==''){
            echo '<div style="color:#911">单号为空</div>'; return;
        }

        $OrderSortingService=new OrderCrossSortingService();
        $RR=$OrderSortingService->CheckTransferOrder($ordersn);

        if($RR['status']==1){
            echo '<div style="color:#191">'.$RR['msg'].'</div>';return;
        }
        echo '<div style="color:#911">'.$RR['msg'].'</div>';return;
    }

    // 分拣到什么进度鸟
    function getPickStstus(){
        $ordersn=$_GET['ordersn'];
        $HeadSuc='<!--SUCCESS-->';
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }


        $OrderSortingService=new OrderCrossSortingService();

        $RR=$OrderSortingService->getOrderSortStatus($ordersn);
        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }
        $MainArr=$RR['data'];

        $url=C('ONLINE_PIC_URL');
        $storeArrName   = C('STORE_NAMES');
        $currStoreId    = C("CURRENT_STORE_ID");
        $mergeStoreId   = C("MERGE_STORE_ID");

        $btn1='<input type="checkbox" checked="checked" onclick="showHide196(this)">显示'.$storeArrName[$currStoreId];
        $btn2='<input type="checkbox" checked="checked" onclick="showHide234(this)">显示'.$storeArrName[$mergeStoreId];
        echo '<table class="table table-responsive table-condensed table-hover ebay_id_table" cellpadding="0" cellspacing="0">';
        echo '<tr><th>分拣位&nbsp;/&nbsp;订单号'.$btn1.$btn2.'</th><th>SKU&nbsp;/&nbsp;还差多少&nbsp;/&nbsp;是否完成</th></tr>';
        $i=1;
        foreach($MainArr as $ebay_id=>$Order){
            $i++;
            $ebay_id=preg_replace("/\w+\./",'',$ebay_id);
            $combineid=$Order[0]['combineid'];
            if($combineid==0){
                $str='<b>无分拣位</b>';
            }else{
                $str='<b class="fenjian_location">'.$combineid.' 号</b>';
            }

            if($i%2==0){
                $trbg='tr_second_bg';
            }else{
                $trbg='';
            }

            echo '<tr class="'.$trbg.'"><td>'.$str.'<br>'.$ebay_id.'</td><td>';
            echo '<table class="ebay_sku_table">';
            foreach($Order as $List){
                $pic        = $List['pic'];
                $qty        = $List['qty'];
                $sku        = $List['sku'];
                $storeid    = $List['storeid'];
                $qty_com    = $List['qty_com'];

                $storeid_name = $storeArrName[$storeid];

                $shenyu=$qty-$qty_com;

                if($shenyu==0){
                    $str='<b style="color:#191">OK</b>';
                    $shenyuhtml='<b style="color:#000">0</b>';
                }else{
                    $str='<b style="color:#911">NO</b>';
                    $shenyuhtml='<b style="color:#911">'.$shenyu.'</b>';
                }
                $html= '<tr><td class="showhide_'.$storeid.'"><img style="height:48px;width:48px;" src="'.$url.'/images/'.$pic.'"/><br>';
                $html.='<span><b>'.$sku.'</b> * '.$qty.'('.$storeid_name.')</span></td>';

                $html.='<td class="showhide_ok_'.$storeid.'"><span>'.$shenyuhtml.'</span></td>';
                $html.='<td class="showhide_ok_'.$storeid.'"><span>'.$str.'</span></td>';
                $html.='</tr>';
                echo $html;
            }
            echo '</table>';
            echo '</td></tr>';
        }
        echo '</table>';


    }

}