<?php
/**
 * WarehouseAnomaly Api
 * @author 	leo
 * @since 	2018年8月31日09:48:13
 */
namespace Api\Controller;

use Common\Model\OrderModel;
use Package\Model\ApiCheckskuModel;

class WarehouseAnomalyController {

    /**
     * @var array
     */
    private  $topMenus =  [
        '1723' => '待生成拣货单',
        '1745' => '待打印',
        '1724' => '待扫描（待包装）',
        '2009' => '待称重',
    ];
    /**
     * sendCarrierStatistics
     * @author 	leo
     * @since 	2018年8月31日18:09:59
     * @link    /t.php?s=/Api/WarehouseAnomaly/sendWarehouseAnomaly
     */
    public function sendWarehouseAnomaly($getWhere = []) {

        $request = $_REQUEST ?: $getWhere;
        $OrderModel=new OrderModel('','',C('DB_CONFIG_READ'));

        //需要查询数量的订单状态
        $topMenus = $this->topMenus;
        if(!empty($request['platform'])){
            $where['a.ordertype'] = trim($request['platform']);
        }

        if(!empty($request['carrier_name'])){
            $where['a.ebay_carrier'] = ['in',$request['carrier_name']];
        }
        $dataBetween[] = strtotime($request['date_start']);
        $dataBetween[] = strtotime($request['date_end']);
        $where['a.ebay_addtime'] = ['between', $dataBetween];
        $detainedTime = trim($request['detained_time']);
        if($detainedTime){
            $startTime = date("Y-m-d H:i:s",strtotime("-$detainedTime hours"));
            $where['a.w_update_time'] = ['elt',$startTime];
        }
        if(!empty($getWhere)){
            return $where;
        }
        foreach ($topMenus as $key=>$value){
            $map = $where ?: [];
            $map['a.ebay_status'] = $key;
            switch ($key) {
                case "1723":
                    $map['b.pick_status'] = 1;
                    $data[$key]['status_name'] = $value;
                    $data[$key]['num'] = $OrderModel->alias('a')->join('inner join erp_order_type b using(ebay_id)')->where($map)->count();
                    break;
                case "2009":
                    $data[$key]['status_name'] = $value;
                    $data[$key]['num'] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                    break;
                case "1745":
                    $data[$key]['status_name'] = $value;
                    $data[$key]['num'] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                    break;
                case "1724":
                    //等待包装去掉正在同步的数据
                    $apiCheckSkuModel = new ApiCheckskuModel('','',C('DB_CONFIG_READ'));
                    $orderData = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->field("a.ebay_id")->where($map)->select();
                    $count = count($orderData);
                    $ebay_ids = [];
                    foreach ($orderData as $v){
                        $ebay_ids[] = $v['ebay_id'];
                    }
                    if(!empty($ebay_ids)){
                        $apiMap['status'] = 1;
                        $apiMap['ebay_id'] = array("in",$ebay_ids);
                        $apiCount = $apiCheckSkuModel->where($apiMap)->count();
                        $count = $count - $apiCount;
                    }
                    $data[$key]['status_name'] = $value;
                    $data[$key]['num'] = $count;
                    break;
            }
        }
        echo json_encode($data);
    }

    /**
     * @desc 导出excel
     * @Author leo
     */
    public function getStatusOnOrder()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $request = $_REQUEST;
        $where = $this->sendWarehouseAnomaly($request);
        $OrderModel=new OrderModel('','',C('DB_CONFIG_READ'));
        Vendor('PHPExcelNew.PHPExcel');
        Vendor('PHPExcelNew.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $dataList = [];

        //需要查询数量的订单状态
        $topMenus = $this->topMenus;
        foreach ($topMenus as $key=>$value){
            $map = $where ?: [];
            $map['a.ebay_status'] = $key;
            switch ($key) {
                case "1723":
                    $map['b.pick_status'] = 1;
                    $dataList[] = $OrderModel->alias('a')->field('a.*,b.type')->join('inner join erp_order_type b using(ebay_id)')->where($map)->select();
                    break;
                case "2009":
                    $dataList[] = $OrderModel->alias('a')->field('a.*,b.type')->join('inner join erp_order_type b using(ebay_id)')->where($map)->select();
                    break;
                case "1745":
                    $dataList[] = $OrderModel->alias('a')->field('a.*,b.type')->join('inner join erp_order_type b using(ebay_id)')->where($map)->select();
                    break;
                case "1724":
                    //等待包装去掉正在同步的数据
                    $apiCheckSkuModel = new ApiCheckskuModel('','',C('DB_CONFIG_READ'));
                    $orderData = $OrderModel->alias('a')->join('inner join erp_order_type b using(ebay_id)')->field('a.*,b.type')->where($map)->select();
                    $ebay_ids = [];
                    foreach ($orderData as $v){
                        $ebay_ids[] = $v['ebay_id'];
                    }
                    if(!empty($ebay_ids)){
                        $apiMap['status'] = 1;
                        $apiMap['ebay_id'] = array("in",$ebay_ids);
                        $apiCount = $apiCheckSkuModel->where($apiMap)->getField('ebay_id',true);
                    }
                    if(!empty($apiCount)){
                        foreach ($orderData as $vkey => $val){
                            if(in_array($val['ebay_id'],$apiCount)){
                                unset($orderData[$vkey]);
                            }
                        }
                    }
                    $dataList[] = $orderData;
                    break;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '订单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '进wms时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '滞留时间（小时）');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '当前状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '平台');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '物流');
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(20);
        $j      = 2;
        $status = ['2' => '已发货', '2009' => '待称重', '1731' => '回收站', '1723' => '可打印', '1745' => '等待打印', '1724' => '等待扫描'];
        foreach($dataList as $dataArr){
            foreach($dataArr as $item){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $j, $item['ebay_id']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $j, $item['w_update_time']);
                $detainedTime = strtotime($item['w_update_time']);
                $hours=round((time()-$detainedTime)/3600,1);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $j, $hours);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $j, $status[$item['ebay_status']]);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $j, $item['ordertype']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $j, $item['ebay_carrier']);
                $j++;
            }
        }
        $title     = "仓库异常报表-" . date('Y-m-d');
        $titlename = "仓库异常报表-" . date('Y-m-d') . ".xls";
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$titlename}");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}

?>