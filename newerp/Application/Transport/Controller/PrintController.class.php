<?php
/**
 * 打印控制器
 */
namespace Transport\Controller;

use Common\Model\OrderModel;
use Think\Controller;
use Transport\Service\PrintService;
use Transport\Model\CarrierModel;

class PrintController extends Controller{
    //打印所有的 运输方式面单
    function PrintAllCarrier(){
        ini_set('memory_limit','500M');
      //debug(C());
        $bill=$_GET['bill'];
        $printMod=(int)$_GET['mod'];
        if(preg_match('/[^0-9,]/',$bill)){
            echo '<meta charset="utf-8"><div style="color:#911;font-size:20px">订单参数异常</div>';
            return ;
        }

        $PrintService=new PrintService();

        $bill=explode(',',trim($bill,','));


        $BillArr=$PrintService->Splite2Label($bill);



        if($printMod==1){
            $bill=$BillArr['bill10'];
        }
        else if($printMod == 3)
        {
            $bill = $BillArr['bill20'];
        }
        else
        {
            $bill=$BillArr['bill15'];
        }


        /**
        *测试人员谭 2017-09-14 17:30:29
        *说明: 仓库 不需要排序
        */
        //$bill=$PrintService->SortOrder($bill); //库位 排序的 干活
        //仓库需要知道运输方式
        $carrier='';

        $Html='';

        $BePrintOrder=[];
        $FailurePrint=[];
        //用于拣货区分代码
        $CountriesModel = new CarrierModel();
        $CountriesList = $CountriesModel->getField('name,sorting_code');

        foreach($bill as $ebay_id){

            // 订单中需要在 面单中搞定的 信息
            $Order=$PrintService->getOrderInfo($ebay_id);
//            dump($Order);
            if($Order===false){
                $FailurePrint[]=$ebay_id;
                continue;
            }
            // 模板是什么
            $tempte=$Order['template'];
            $this->assign('countriest',$CountriesList);
            $BePrintOrder[]=$ebay_id;

            $this->assign('Order',$Order);

            // 渲染 但是 不输出模板
            $Html.=$this->fetch($tempte);


        }

        if($Html==''&&!empty($ebay_id)){
            $OrderModel=new OrderModel();
            $carrier=$OrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_carrier');
        }

        if($_GET['debug']==1){
            $Html='';
            $BePrintOrder=[];
            $FailurePrint=[];
        }

        $BePrintOrderStr = implode(',', $BePrintOrder);
        $FailurePrintStr = implode(',', $FailurePrint);

        $this->assign('TotalCount', count($BePrintOrder));
        $this->assign('TotalFailureCount', count($FailurePrint));

        $this->assign('SuccessPrintStr', $BePrintOrderStr);
        $this->assign('FailurePrintStr', $FailurePrintStr);
        $this->assign('mod', $_GET['mod']);


        $this->assign('HTML', $Html);
        $this->assign('carrier', $carrier);
        $this->display('whouseprint');
    }


    function test(){
        echo '<style>.view{height:100mm;width:100mm;}body{margin:0;padding:0;}</style>';
        $PrintService=new PrintService();
        //$data=$PrintService->pdf2png('cache/pdf/ost/5783665_label.pdf');
        $data=$PrintService->pdf2png('cache/pdf/kuajing/5796294.pdf');
        if($data['status']){
            $imgs=$data['data'];
            foreach($imgs as $lis){
                echo '<div class="view"><img  class="view" src="'.$lis.'"></div>';
            }
        }
    }
}
