<?php
/**
 * Created by hank.
 * User: Administrator
 * Date: 2018/1/12
 * Time: 11:33
 * 订单信息导出
 */

include "../include/dbconnect.php";
require_once '../include/PHPExcel/PHPExcel.php';
date_default_timezone_set ("Asia/Chongqing");

if(isset($_GET['ordersn']) && !empty($_GET['ordersn'])){
    $ordersn = trim(trim($_GET['ordersn'],','));

    if(empty($ordersn)){
        echo '你还没有选择要导出的订单哦！';
        exit;
    }
    exportOrderList($ordersn);
}


/**
 * hank
 * 获取订单信息
 */
function getExeclData($ordersn){
    $dbcon	        = new DBClass();
    $carrierSql     = 'SELECT ec.`name`,ecc.sup_abbr FROM ebay_carrier ec LEFT JOIN ebay_carrier_company ecc ON ec.CompanyName=ecc.id';
    $carrierStem	= $dbcon->execute($carrierSql);
    $carrierInfo	= $dbcon->getResultArray($carrierStem);
    $carrierInfos   = [];
    foreach($carrierInfo as $v){
        $carrierInfos[$v['name']] = $v['sup_abbr'];
    }
    $orderSql   = 'SELECT ebay_id,ebay_tracknumber,ebay_carrier FROM erp_ebay_order WHERE ebay_id in ('.$ordersn.')';
    $orderStem	= $dbcon->execute($orderSql);
    $orderInfo	= $dbcon->getResultArray($orderStem);
    foreach($orderInfo as $k =>$item){
        if(isset($carrierInfos[$item['ebay_carrier']])){
            $orderInfo[$k]['ebay_carrier_company'] = $carrierInfos[$item['ebay_carrier']];
        }else{
            $orderInfo[$k]['ebay_carrier_company'] = '';
        }
    }
    return $orderInfo;
}

/**
 * hank
 * 导出Excel
 */
function exportOrderList($ordersn){
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("FILE");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '订单号');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '跟踪号');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '物流渠道');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '货代');

    $list = getExeclData($ordersn);

    if(is_array($list)  && !empty($list)){
        $i=2;
        foreach($list as $key => $item){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $item['ebay_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, ' '.$item['ebay_tracknumber']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $item['ebay_carrier']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $item['ebay_carrier_company']);
            $i++;
        }
    }

    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);

    $title = "订单导出文档-".date('Y-m-d');
    $titlename = "订单导出文档-".date('Y-m-d').".xls";
    $objPHPExcel->getActiveSheet()->setTitle($title);
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename={$titlename}");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}

