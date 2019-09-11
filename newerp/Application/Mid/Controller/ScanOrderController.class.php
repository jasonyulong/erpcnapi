<?php
 namespace Mid\Controller;
 use Mid\Model\MidEbayOrderModel;
 use Think\Controller;
/**
*测试人员谭 2017-11-07 21:41:40
*说明: 等待扫描 全他妈干道 wms
*/
 class ScanOrderController extends Controller {

     function checkOrdersExsit(){
         $action      = 'Apiwms/ScanOrder/giveAllOrder1724';
         $url=C('ONLINE_PIC_URL').'/t.php?s='.$action;
         $txt=file_get_contents($url);
         $Orders=explode(',',$txt);

         $MidOrder=new MidEbayOrderModel();

         $str=[];
         foreach($Orders as $ebay_id){
             if(empty($ebay_id)||!is_numeric($ebay_id)){
                 continue;
             }
             $rr=$MidOrder->where(compact('ebay_id'))->getField('id');
             if(empty($rr)){
                 $str[]=$ebay_id;
             }
         }

         // 线上所有等待扫描，线下不存在的订单 全部干进来 别罗嗦！
         echo 'WMS 不存在的订单是'."\n";
         echo 'count:'.count($str)."\n";
         $this->getOrderFromErp($str);
         //return $str;
     }

     function getOrderFromErp($arr){
         $orderService = new \Mid\Service\OrderService();
         /*if (isset($request['ebay_id']) && (int)$request['ebay_id'] < 1) {
             unset($request['ebay_id']);
         }*/
         foreach($arr as $ebay_id){
             $orderService->getWaitScanOrderList($ebay_id);
             echo $ebay_id."\n";//die();
         }

         echo '<br/>End'."\n";
     }
 }