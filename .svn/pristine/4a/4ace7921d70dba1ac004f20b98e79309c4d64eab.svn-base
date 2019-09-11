<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 14:16
 */
include "include/config.php";
include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/PHPExcel/PHPExcel.php";

error_reporting(0);
set_time_limit(0);
ini_set('memory_limit', '228M');
$dbconLocal = new DBMysqli($LOCAL);
// hank 2017-11-08 删除多余链接
//$dbcon      = new DBMysqli($ERPCONF);

if (!trim($_REQUEST['transport'])) {
    echo <<<TEXT
<meta charset='utf-8' />
<h1 style='color: red;font-size: 25px'>运输方式必选,请先选择一种运输方式.</h1>
TEXT;
    return null;
}
$where = '';
if(isset($_REQUEST['type']) && $_REQUEST['type']=='toExcel'){
    $bagMark = $_REQUEST['bagMark'];
    $bagMark = explode(',',$bagMark);
    $strbagMark = "('".implode("','",$bagMark)."')";
    $where = " b.mark_code in {$strbagMark}";
}else{
    $transport = trim($_REQUEST['transport']);
    $createBy  = trim($_REQUEST['createBy']);
    $bagCode   = trim($_REQUEST['bagCode']);
    $addTimeStart = trim($_REQUEST['addTimeStart']);
    $addTimeEnd   = trim($_REQUEST['addTimeEnd']);

    $deliveryValue = trim($_REQUEST['deliveryStatus']);
    $deliveryStatus = ($deliveryValue == 0 || $deliveryValue == '') ? 0 : $deliveryValue;

    if ($createBy) {
        $where = "a.scan_user = '{$createBy}'";
    }

    $where .= ($where ? ' and ' : '') . "a.transport='{$transport}'";


    if ($bagCode) {
        $where .= " and b.mark_code = '{$bagCode}'";
    }

    if ($addTimeStart) {
        $addTimeStartStamp = strtotime($addTimeStart.' 00:00:00');
        $where .= " and b.create_at > '{$addTimeStartStamp}'";
    }

    if ($addTimeEnd) {
        $addTimeEndStamp = strtotime($addTimeEnd.' 23:59:59');
        $where .= " and b.create_at < '{$addTimeEndStamp}'";
    }

    if ($deliveryStatus == 0) {
        $where .= " and b.delivery_status=0";
    } elseif ($deliveryStatus == 1) {
        $where .= " and b.delivery_status=1";
    }
}

$getResultSql = <<<HereDoc
SELECT [-REPLACE-] FROM api_orderweight AS a INNER JOIN api_bags AS b ON a.bag_mark=b.mark_code
WHERE {$where}
HereDoc;

file_put_contents('aa.log', $getResultSql."\n\n", true);

//$query = str_replace('[-REPLACE-]','a.ebay_id,a.weight, a.transport, a.bag_mark',$getResultSql).' ORDER BY b.mark_code DESC';
//echo $query;
//die;
$orderSets = $dbconLocal -> getResultArrayBySql(str_replace('[-REPLACE-]','a.ebay_id,a.weight, a.transport, a.bag_mark',$getResultSql).' ORDER BY b.mark_code DESC');
$bagSets   = $dbconLocal -> getResultArrayBySql(str_replace('[-REPLACE-]', 'b.mark_code, b.weight as real_weight, sum(a.weight)/1000 as calc_weight, b.create_at, a.transport , b.create_by', $getResultSql).' GROUP BY a.bag_mark');

$date = date('Y-m-d');
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename=扫描订单导出-{$date}");
header('Cache-Control: max-age=0');

$excelHandler = new PHPExcel();
$excelHandler -> getProperties()
    ->setCreator("chengxiang")
    ->setLastModifiedBy("chengxiang")
    ->setTitle("Export scan orders")
    ->setSubject("Orders Excel document")
    ->setDescription("Exported orders")
    ->setKeywords("orders")
    ->setCategory("order export cate");

// 创建第一张表 扫描打包表
$excelSheet1 = $excelHandler -> setActiveSheetIndex(0);
$excelSheet1 -> setTitle('装袋列表');

$excelSheet1 -> setCellValue('A1', '打包编号') -> getColumnDimension('A') -> setWidth(20);
$excelSheet1 -> getStyle('A1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet1 -> getStyle('A1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet1 -> setCellValue('B1', '实际重量/Kg') -> getColumnDimension('B') -> setWidth(20);
$excelSheet1 -> getStyle('B1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet1 -> getStyle('B1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet1 -> setCellValue('C1', '计算重量/Kg') -> getColumnDimension('C') -> setWidth(20);
$excelSheet1 -> getStyle('C1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet1 -> getStyle('C1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet1 -> setCellValue('D1', '创建时间') -> getColumnDimension('D') -> setWidth(25);
$excelSheet1 -> getStyle('D1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet1 -> getStyle('D1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet1 -> setCellValue('E1', '创建人') -> getColumnDimension('E') -> setWidth(20);
$excelSheet1 -> getStyle('E1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet1 -> getStyle('E1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

foreach ($bagSets as $key => $bag) {
    $excelSheet1 -> setCellValue('A'.($key+2), $bag['mark_code'])-> getStyle('A'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet1 -> setCellValue('B'.($key+2), $bag['real_weight'])-> getStyle('B'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet1 -> setCellValue('C'.($key+2), $bag['calc_weight'])-> getStyle('C'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet1 -> setCellValue('D'.($key+2), date('Y-m-d H:i:s' ,$bag['create_at']))-> getStyle('D'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet1 -> setCellValue('E'.($key+2), $bag['create_by'])-> getStyle('E'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}



// 创建第二张表 扫描订单详情表
$excelSheet2 = $excelHandler -> addSheet((new PHPExcel()) -> setActiveSheetIndex(0), 1);

$excelSheet2 -> setTitle('装袋订单列表');
$excelSheet2 -> setCellValue('A1', '订单编号') -> getColumnDimension('A') -> setWidth(20);
$excelSheet2 -> getStyle('A1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet2 -> getStyle('A1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet2 -> setCellValue('B1', '跟踪号') -> getColumnDimension('B') -> setWidth(20);
$excelSheet2 -> getStyle('B1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet2 -> getStyle('B1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet2 -> setCellValue('C1', '运输方式') -> getColumnDimension('C') -> setWidth(25);
$excelSheet2 -> getStyle('C1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet2 -> getStyle('C1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet2 -> setCellValue('D1', '包裹号') -> getColumnDimension('D') -> setWidth(20);
$excelSheet2 -> getStyle('D1') -> getFont() -> setBold(true) -> setSize(13);
$excelSheet2 -> getStyle('D1') -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$excelSheet2 -> setCellValue('E1', '重量') -> getColumnDimension('D') -> setWidth(20);

foreach ($orderSets as $key => $val) {
/*    $getTraceNumSql = <<<HereDoc
SELECT ebay_tracknumber FROM api_checksku WHERE ebay_id = {$val['ebay_id']} limit 1
HereDoc;*/

    $getTraceNumSql="select ebay_tracknumber from erp_ebay_order where ebay_id = {$val['ebay_id']} limit 1";

    $getTraceNum = $dbconLocal -> getResultArrayBySql($getTraceNumSql);
    $traceNum = $getTraceNum ?  $getTraceNum[0]['ebay_tracknumber'] : '--';
    $excelSheet2 -> setCellValueExplicit('A'.($key+2), $val['ebay_id'] ) -> getStyle('A'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet2 -> setCellValueExplicit('B'.($key+2), $traceNum) -> getStyle('B'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet2 -> setCellValueExplicit('C'.($key+2), $val['transport'] ) -> getStyle('C'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet2 -> setCellValueExplicit('D'.($key+2), $val['bag_mark'] ) -> getStyle('D'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excelSheet2 -> setCellValueExplicit('E'.($key+2), $val['weight'] ) -> getStyle('E'.($key+2)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}


$objWriter = PHPExcel_IOFactory::createWriter($excelHandler, 'Excel5');
$objWriter->save('php://output');




