<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name StatisticsController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/1
 * @Time: 19:37
 * @Description 新的统计程序
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Order\Model\AbnormalOrderModel;
use Order\Model\CanWeightFailureLogModel;
use Order\Model\PickAbnormalitySkuModel;
use Package\Model\ErpEbayUserModel;
use Package\Model\PackerStatisticsModel;
use Package\Model\PackSubsidyStatisticsModel;
use Package\Model\PickerStatisticsModel;
use Package\Model\StatisticsModel;


/**
 * 以包装人员进行分组，包装/拣货统计展示
 * Class StatisticsController
 * @package Package\Controller
 */
class StatisticsController extends CommonController
{
    protected $typeInfo = [
        1   =>'单品单货',
        2   =>'单品多货',
        3   =>'多品多货',
        4   =>'核对扫描'
    ];

    protected $chart_name_1 = [1=>'单品单货订单出库占比',2=>'单品多货订单出库占比',3=>'多品多货订单出库占比',4=>'核对扫描订单出库占比'];
    protected $chart_name_2 = [1=>'单品单货sku出库占比',2=>'单品多货sku出库占比',3=>'多品多货sku出库占比',4=>'核对扫描sku出库占比'];
    protected $chart_name_3 = [1=>'单品单货sku总数',2=>'单品多货sku总数',3=>'多品多货sku总数',4=>'核对扫描sku总数'];

    /**
     * 拣货统计（新）
     * @author Shawn
     * @date 2018/8/3
     */
    public function showPickerStatistics()
    {
        if (!can('order_package_statistic')) {
            $this->error("您没有权限查看拣货费用计的权限");
        }
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        //类型查询
        $pickType= isset($_POST['pickType']) ? (int)$_POST['pickType'] : 1;//默认单品单货
        $map = [
            'ptime' => ['between', $timeArea],
            'type'     => $pickType
        ];
        //显示查询时间内日期
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $data = $this->getPickerDetailData($map);
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->assign("typeInfo", $this->typeInfo);
        $this->assign("pickType", $pickType);
        $this->display();
    }

    /**
     * 拣货查询、导出数据获取
     * @param $map
     * @return mixed
     * @author Shawn
     * @date 2018/8/3
     */
    public function getPickerDetailData($map){
        //查询从库
        $pickerStatisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,sum(qty) as count,picker";
        $countResult = $pickerStatisticsModel->where($map)->group('picker,pktime')->field($field)->select();
        $timeCounts = $pickerStatisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        foreach ($countResult as &$v){
            $v['picker'] = strtoupper($v['picker']);
        }
        $pkuser = array_unique(array_column($countResult, 'picker'));
        $dataArr = [];
        $userIdModel = new ErpEbayUserModel("","",C('DB_CONFIG_READ'));
        //找到用户id
        foreach ($pkuser as $key => $val) {
            $userId        = $userIdModel->where(['username' => $val])->getField("id");
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['picker'] == $val) {
                    $dataArr[$val]['pickerId']                 = (int)$userId;
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }

    /**
     * 拣货统计数据导出
     * @author Shawn
     * @date 2018/8/3
     */
    public function exportPickerStatistics(){
        if (!can('order_package_statistic')) {
            $this->error("您没有导出拣货统计的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $index = 0;
        foreach ($this->typeInfo as $t=>$y){
            $objPHPExcel->setActiveSheetIndex($index);
            $map = [
                'ptime' => ['between', $timeArea],
                'type'     => $t
            ];
            //获取导出数据
            $data = $this->getPickerDetailData($map);
            //循环设置第一列excel数据
            $letter = range('A','Z');
            $j = 0;
            $m = 0;
            $reset = false;
            $over = false;
            $todayData = [];
            for($i=0;$i<35;$i++){
                if($over){
                    continue;
                }
                //重置
                if($i > 25 && $reset == false){
                    $reset = true;
                    $m = 0;
                }
                $str = ($i > 25) ? 'A' : '';
                $dis = $str.$letter[$m];
                $col = $dis."1";
                $title = '';
                if($i == 0){
                    $title = '序号';
                }
                if($i == 1){
                    $title = '拣货人\日期';
                }
                if($i > 1){
                    if(!isset($timeData[$j])){
                        $over = true;
                        $title = "合计";
                        $todayData[$dis] = 'total';
                    }else{
                        $title = $timeData[$j];
                        $todayData[$dis] = $timeData[$j];
                    }
                    $j++;
                }
                $m++;
                $objPHPExcel->getactivesheet()->setCellValue($col, $title);
                $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
            }
            //设置excel表格数据
            $l = 2;
            $number = 1;
            foreach ($data['data'] as $key=>$value){
                $begin = 'A' . $l;
                $second = 'B'.$l;
                $objPHPExcel->getactivesheet()->setCellValue($begin, $number);
                $objPHPExcel->getactivesheet()->setCellValue($second, $key.'（'.$value['pickerId'].'）');
                $count = 0;
                foreach ($todayData as $k=>$day){
                    $col = $k.$l;
                    if(in_array($day,array_keys($value['arr']))){
                        $num = $value['arr'][$day];
                        $count = $count + $num;
                    }else{
                        if($day == "total"){
                            $num = $count;
                        }else{
                            $num = 0;
                        }
                    }
                    $objPHPExcel->getactivesheet()->setCellValue($col, $num);
                }
                $l++;
                $number++;
            }
            $l++;
            //合计
            $last = 'A'.$l;
            $lastB = 'B'.$l;
            $objPHPExcel->getactivesheet()->setCellValue($last, '');
            $objPHPExcel->getactivesheet()->setCellValue($lastB, '合计');
            $counts = 0;
            foreach ($todayData as $k => $day) {
                $col = $k . $l;
                if (in_array($day, array_keys($data['countsArr']['arr']))) {
                    $num = $data['countsArr']['arr'][$day];
                    $counts = $counts + $num;
                } else {
                    if ($day == "total") {
                        $num = $counts;
                    } else {
                        $num = 0;
                    }
                }
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
            $objPHPExcel->getActiveSheet()->setTitle($y);
            $index++;
            if($index < 4){
                $objPHPExcel->createSheet();
            }
        }

        $titlename = "拣货统计导出文档-" . date('Y-m-d') . ".xls";
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

    /**
     * 包装统计（新）
     * @author Shawn
     * @date 2018/8/3
     */
    public function showPackerStatistics()
    {
        if (!can('order_package_packing')) {
            $this->error("您没有查看订单包装统计的权限");
        }
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        //类型查询
        $packType= isset($_POST['packType']) ? (int)$_POST['packType'] : 1;//默认单品单货
        $map = [
            'ptime' => ['between', $timeArea],
            'type'     => $packType
        ];
        //显示查询时间内日期
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $data = $this->getPackerDetailData($map);
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->assign("typeInfo", $this->typeInfo);
        $this->assign("packType", $packType);
        $this->display();
    }
    /**
     * 包装查询、导出数据获取
     * @param $map
     * @return array
     * @author Shawn
     * @date 2018/8/3
     */
    public function getPackerDetailData($map){
        //查询从库
        $packerStatisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,sum(qty) as count,packer";
        $countResult = $packerStatisticsModel->where($map)->group('packer,pktime')->field($field)->select();
        $timeCounts = $packerStatisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        foreach ($countResult as &$v){
            $v['packer'] = strtoupper($v['packer']);
        }
        $pkuser = array_unique(array_column($countResult, 'packer'));
        $dataArr = [];
        $userIdModel = new ErpEbayUserModel("","",C('DB_CONFIG_READ'));
        //找到用户id
        foreach ($pkuser as $key => $val) {
            $userId        = $userIdModel->where(['username' => $val])->getField("id");
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['packer'] == $val) {
                    $dataArr[$val]['packerId']                 = (int)$userId;
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }
    public function exportPackerStatistics(){
        if (!can('order_package_packing')) {
            $this->error("您没有导出订单包装统计的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $index = 0;
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        foreach ($this->typeInfo as $t=>$y){
            $objPHPExcel->setactivesheetindex($index);
            $map = [
                'ptime' => ['between', $timeArea],
                'type'     => $t
            ];
            //获取导出数据
            $data = $this->getPackerDetailData($map);
            //循环设置第一列excel数据
            $letter = range('A','Z');
            $j = 0;
            $m = 0;
            $reset = false;
            $over = false;
            $todayData = [];
            for($i=0;$i<35;$i++){
                if($over){
                    continue;
                }
                //重置
                if($i > 25 && $reset == false){
                    $reset = true;
                    $m = 0;
                }
                $str = ($i > 25) ? 'A' : '';
                $dis = $str.$letter[$m];
                $col = $dis."1";
                $title = '';
                if($i == 0){
                    $title = '序号';
                }
                if($i == 1){
                    $title = '包装员\日期';
                }
                if($i > 1){
                    if(!isset($timeData[$j])){
                        $over = true;
                        $title = "合计";
                        $todayData[$dis] = 'total';
                    }else{
                        $title = $timeData[$j];
                        $todayData[$dis] = $timeData[$j];
                    }
                    $j++;
                }
                $m++;
                $objPHPExcel->getactivesheet()->setCellValue($col, $title);
                $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
            }
            //设置excel表格数据
            $l = 2;
            $number = 1;
            foreach ($data['data'] as $key=>$value){
                $begin = 'A' . $l;
                $second = 'B'.$l;
                $objPHPExcel->getactivesheet()->setCellValue($begin, $number);
                $objPHPExcel->getactivesheet()->setCellValue($second, $key.'（'.$value['packerId'].'）');
                $count = 0;
                foreach ($todayData as $k=>$day){
                    $col = $k.$l;
                    if(in_array($day,array_keys($value['arr']))){
                        $num = $value['arr'][$day];
                        $count = $count + $num;
                    }else{
                        if($day == "total"){
                            $num = $count;
                        }else{
                            $num = 0;
                        }
                    }
                    $objPHPExcel->getactivesheet()->setCellValue($col, $num);
                }
                $l++;
                $number++;
            }
            $l++;
            //合计
            $last = 'A'.$l;
            $lastB = 'B'.$l;
            $objPHPExcel->getactivesheet()->setCellValue($last, '');
            $objPHPExcel->getactivesheet()->setCellValue($lastB, '合计');
            $counts = 0;
            foreach ($todayData as $k => $day) {
                $col = $k . $l;
                if (in_array($day, array_keys($data['countsArr']['arr']))) {
                    $num = $data['countsArr']['arr'][$day];
                    $counts = $counts + $num;
                } else {
                    if ($day == "total") {
                        $num = $counts;
                    } else {
                        $num = 0;
                    }
                }
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
            $objPHPExcel->getActiveSheet()->setTitle($y);
            $index++;
            if($index < 4){
                $objPHPExcel->createSheet();
            }
        }
        $titlename = "包装统计导出文档-" . date('Y-m-d') . ".xls";
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

    /**
     * 根据选择时间展示每一天
     * @param $start
     * @param $end
     * @return array
     * @author Shawn
     * @date 2018/7/31
     */
    private function showEveryDayDetail($start, $end)
    {
        //根据选择时间范围，展示每一天的数据
        $year = date("Y", $start);
        $month = date("m", $start);
        $day = date("d", $start);
        $j = 0;
        $today = array();
        for ($i = $start; $i < $end; $i += 86400) {
            $y = mktime(0, 0, 0, $month, $day, $year);
            $today[$j] = date("Y-m-d", $y + $j * 24 * 3600);
            $j++;
        }
        return $today;

    }

    /**
     * 获取查询的时间节点的范围
     * @param $rawCondition
     * @return array
     */
    private function getStatisticArea(&$rawCondition = '')
    {
        $rawCondition = $_REQUEST;

        if ($rawCondition['timeArea_start'] && $rawCondition['timeArea_end']) {
            $timeAreaCondition = [
                strtotime($rawCondition['timeArea_start'] . ' 00:00:00'),
                strtotime($rawCondition['timeArea_end'] . ' 23:59:59')
            ];
        } else {
            $rawCondition['timeArea_start'] = date('Y-m-d', strtotime('-10 days'));
            $rawCondition['timeArea_end'] = date('Y-m-d');

            $timeAreaCondition = [
                strtotime(date('Y-m-d', strtotime('-10 days')) . ' 00:00:00'),
                strtotime(date('Y-m-d') . ' 23:59:59')
            ];
        }
        return $timeAreaCondition;
    }

    /**
     * 包装补贴统计
     * @author Shawn
     * @date 2018/8/15
     */
    public function showPackSubsidyStatistics()
    {
        if (!can('order_package_packing')) {
            $this->error("您没有查看订单包装补贴统计的权限");
        }
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $typeInfo = [
            '0'   =>'全部',
            '1'   =>'钢化',
            '2'   =>'总成',
        ];
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        //类型查询
        $packType= isset($_POST['packType']) ? (int)$_POST['packType'] : 1;//默认钢化
        $map = [
            'ptime' => ['between', $timeArea],
            'sub_type'     => $packType
        ];
        if($packType == 0){
           $map['sub_type'] = array("in",[1,2]);
        }
        //显示查询时间内日期
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $data = $this->getPackSubsidyDetailData($map);
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->assign("typeInfo", $typeInfo);
        $this->assign("packType", $packType);
        $this->display();
    }

    /**
     * 包装补贴查询、导出数据获取
     * @param $map
     * @return array
     * @author Shawn
     * @date 2018/8/15
     */
    public function getPackSubsidyDetailData($map){
        //查询从库
        $PackSubsidyStatisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,sum(qty) as count,packer";
        $countResult = $PackSubsidyStatisticsModel->where($map)->group('packer,pktime')->field($field)->select();
        $timeCounts = $PackSubsidyStatisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        foreach ($countResult as &$v){
            $v['packer'] = strtoupper($v['packer']);
        }
        $pkuser = array_unique(array_column($countResult, 'packer'));
        $dataArr = [];
        $userIdModel = new ErpEbayUserModel("","",C('DB_CONFIG_READ'));
        //找到用户id
        foreach ($pkuser as $key => $val) {
            $userId        = $userIdModel->where(['username' => $val])->getField("id");
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['packer'] == $val) {
                    $dataArr[$val]['packerId']                 = (int)$userId;
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }

    /**
     * 包装补贴导出
     * @author Shawn
     * @date 2018/8/15
     */
    public function exportPackSubsidyStatistics()
    {
        if (!can('order_package_packing')) {
            $this->error("您没有导出订单包装补贴统计的权限");
        }

        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        //类型查询
        $packType = (int)$_GET['packType'];
        $typeInfo = [
            '0'   =>'全部',
            '1'   =>'钢化',
            '2'   =>'总成',
        ];
        $typeName = $typeInfo[$packType];
        $map = [
            'packtime' => ['between', $timeArea],
            'sub_type'     => $packType
        ];
        if($packType == 0){
            $map['sub_type'] = array("in",[1,2]);
        }
        //获取导出数据
        $data = $this->getPackSubsidyDetailData($map);
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '序号';
            }
            if($i == 1){
                $title = '包装员\日期';
            }
            if($i > 1){
                if(!isset($timeData[$j])){
                    $over = true;
                    $title = "合计";
                    $todayData[$dis] = 'total';
                }else{
                    $title = $timeData[$j];
                    $todayData[$dis] = $timeData[$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $title);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $number = 1;
        foreach ($data['data'] as $key=>$value){
            $begin = 'A' . $l;
            $second = 'B'.$l;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($begin, $number);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($second, $key.'（'.$value['packerId'].'）');
            $count = 0;
            foreach ($todayData as $k=>$day){
                $col = $k.$l;
                if(in_array($day,array_keys($value['arr']))){
                    $num = $value['arr'][$day];
                    $count = $count + $num;
                }else{
                    if($day == "total"){
                        $num = $count;
                    }else{
                        $num = 0;
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $num);
            }
            $l++;
            $number++;
        }
        $l++;
        //合计
        $last = 'A'.$l;
        $lastB = 'B'.$l;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($last, '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($lastB, '合计');
        $counts = 0;
        foreach ($todayData as $k => $day) {
            $col = $k . $l;
            if (in_array($day, array_keys($data['countsArr']['arr']))) {
                $num = $data['countsArr']['arr'][$day];
                $counts = $counts + $num;
            } else {
                if ($day == "total") {
                    $num = $counts;
                } else {
                    $num = 0;
                }
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $num);
        }
        $title = $typeName."包装补贴统计导出文档-" . date('Y-m-d');
        $titlename = $typeName."包装补贴统计导出文档-" . date('Y-m-d') . ".xls";
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

    /**
     * 拣货异常sku统计
     * @author leo
     * @date 2018年9月5日17:40:52
     */
    public function showPickingAbnormalityStatistics(){
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $map = [
            'addtime' => ['between', $timeArea],
        ];
        //显示查询时间内日期
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $data = $this->getPickingAbnormalityData($map);
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->display();
    }

    /**
     * 拣货异常sku统计查询、导出数据获取
     * @param $map
     * @return array
     * @author leo
     * @date 2018年9月5日17:40:39
     */
    public function getPickingAbnormalityData($map){
        //查询从库
        $PackSubsidyStatisticsModel = new PickAbnormalitySkuModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(addtime, '%Y-%m-%d') as pktime,sum(qty) as count,picker";
        $countResult = $PackSubsidyStatisticsModel->where($map)->group('picker,pktime')->field($field)->select();
        $timeCounts = $PackSubsidyStatisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        $pkuser = array_unique(array_column($countResult, 'picker'));
        $dataArr = [];
        $userIdModel = new ErpEbayUserModel("","",C('DB_CONFIG_READ'));
        //找到用户id
        foreach ($pkuser as $key => $val) {
            $userId        = $userIdModel->where(['username' => $val])->getField("id");
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['picker'] == $val) {
                    $dataArr[$val]['pickerId']                 = (int)$userId;
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }

    /**
     * 拣货异常sku统计导出数据
     * @author leo
     * @date 2018年9月5日17:40:39
     */
    public function exportPickingAbnormalityStatistics(){
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $map = [
            'addtime' => ['between', $timeArea],
        ];
        //获取导出数据
        $data = $this->getPickingAbnormalityData($map);
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
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

        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '序号';
            }
            if($i == 1){
                $title = '拣货员\日期';
            }
            if($i > 1){
                if(!isset($timeData[$j])){
                    $over = true;
                    $title = "合计";
                    $todayData[$dis] = 'total';
                }else{
                    $title = $timeData[$j];
                    $todayData[$dis] = $timeData[$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $title);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $number = 1;
        foreach ($data['data'] as $key=>$value){
            $begin = 'A' . $l;
            $second = 'B'.$l;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($begin, $number);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($second, $key.'（'.$value['pickerId'].'）');
            $count = 0;
            foreach ($todayData as $k=>$day){
                $col = $k.$l;
                if(in_array($day,array_keys($value['arr']))){
                    $num = $value['arr'][$day];
                    $count = $count + $num;
                }else{
                    if($day == "total"){
                        $num = $count;
                    }else{
                        $num = 0;
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $num);
            }

            $countData[] =[
                'name'=> $key.'（'.$value['pickerId'].'）',
                'count'=> $count,
            ];
            $l++;
            $number++;
        }
        $l++;
        //合计
        $last = 'A'.$l;
        $lastB = 'B'.$l;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($last, '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($lastB, '合计');
        $counts = 0;
        foreach ($todayData as $k => $day) {
            $col = $k . $l;
            if (in_array($day, array_keys($data['countsArr']['arr']))) {
                $num = $data['countsArr']['arr'][$day];
                $counts = $counts + $num;
            } else {
                if ($day == "total") {
                    $num = $counts;
                } else {
                    $num = 0;
                }
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col, $num);
        }
        $title = "拣货异常sku统计导出文档-" . date('Y-m-d');
        $titlename = "拣货异常sku统计导出文档-" . date('Y-m-d') . ".xls";
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A1', '拣货员');
        $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B1', 'sku个数');
        $j      = 2;
        $sss = ['E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',];
        foreach($countData as $cname){
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A' . $j, $cname['name']);
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B' . $j, $cname['count'] ?: 0);
            $j++;
        }
        $num = count($countData);
        //图表
        $labels = array(
            new \PHPExcel_Chart_DataSeriesValues('String','拣货异常统计表!$B$1',null,1),
        );
        $xLabels = array(
            new \PHPExcel_Chart_DataSeriesValues('String','拣货异常统计表!$A$2:$A$'.$j,null,$num),//取x轴刻度
        );
        $datas = array(
            new \PHPExcel_Chart_DataSeriesValues('Number','拣货异常统计表!$B$2:$B$'.$j,null,$num),//取一班数据
        );
        $yAxisLabel = new \PHPExcel_Chart_Title('sku个数');
        $series = array(
            new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_SURFACECHART,
                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
                range(0, count($labels)-1),
                $labels,
                $xLabels,
                $datas
            )
        ); //图表框架
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        $areas = new \PHPExcel_Chart_PlotArea(null,$series);
        $legend =new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_BOTTOM,$layout,false);
        $title = new \PHPExcel_Chart_Title('拣货异常统计表');
        $chart = new \PHPExcel_Chart(
            'line_chart', //图表名称，可理解为我们常使用的图1，图2…
            $title,    //图表标题，和上面的名称不同
            $legend, //图表位标签的置
            $areas,  //构建好的图表框架
            true,     //没使用过
            false,  //没使用过（感觉没什么用，好奇的可以翻文档研究）
            null,  //x轴单位，配置类似title
            $yAxisLabel  //x轴单位，配置类似title
        );
        $p =  $num>5 ? $sss[$num] : $sss[5];
        $p =  $num>20 ? 'Z' : $p;
        $chart->setTopLeftPosition('F1')->setBottomRightPosition($p.'20');
        $objPHPExcel->getActiveSheet()->addChart($chart);
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle('拣货异常统计表');

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$titlename}");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->save('php://output');

    }

    /**
     * 订单出库列表
     * @author Shawn
     * @date 2018/10/29
     */
    public function orderOutStoreStatistics()
    {
        if (!can('orderOutStoreStatistics')) {
            $this->error("您没有权限查看订单出库报表的权限");
        }
        $model = isset($_REQUEST['model']) ? $_REQUEST['model'] : 'table';
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $map = [
            'ptime' => ['between', $timeArea],
        ];
        $fetch = array();
        $dateData = [];
        if($model == 'chart'){
            $data = $this->getChartsData($timeArea[0],$timeArea[1],1);
            $this->assign($data);
            $fetch = $this->fetch("chart");
        }else{
            //显示查询时间内日期
            $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
            $data = $this->getOrderOutDetailData($map);
        }

        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->assign("typeInfo", $this->typeInfo);
        $this->assign("model", $model);
        $this->assign("fetch", $fetch);
        $this->display();
    }

    /**
     * 获取订单出库表报数据
     * @param $map
     * @return mixed
     * @author Shawn
     * @date 2018/10/29
     */
    public function getOrderOutDetailData($map)
    {
        //查询从库
        $statisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,count(DISTINCT ebay_id) as count,type";
        $countResult = $statisticsModel->where($map)->group('type,pktime')->field($field)->select();
        $timeCounts = $statisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        $typeData = array_unique(array_column($countResult, 'type'));
        $dataArr = [];
        foreach ($typeData as $key => $val) {
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['type'] == $val) {
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }

    /**
     * 订单出库报表导出
     * @author Shawn
     * @date 2018/10/29
     */
    public function exportOrderOutStatistics()
    {
        if (!can('orderOutStoreStatistics')) {
            $this->error("您没有导出订单出库报表的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0);
        $map = [
            'ptime' => ['between', $timeArea],
        ];
        //获取导出数据
        $data = $this->getOrderOutDetailData($map);
        $model = $_REQUEST['model'];
        $typeInfo = $this->typeInfo;
        //折线图的导出好像不需要，先注释吧，没准哪天又要了
//        if($model == 'chart'){
//            $typeInfo = $this->chart_name_1;
//            foreach ($data['data'] as $k=>$v){
//                foreach ($data['countsArr']['arr'] as $kk=>$vv){
//                    $count = $v['arr'][$kk];
//                    $data['data'][$k]['arr'][$kk] = number_format($count/$vv,4);
//                }
//            }
//        }
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '类型\日期';
            }
            if($i >= 1){
                if(!isset($timeData[$j])){
                    $over = true;
                    $title = "合计";
                    $todayData[$dis] = 'total';
                }else{
                    $title = $timeData[$j];
                    $todayData[$dis] = $timeData[$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->getactivesheet()->setCellValue($col, $title);
            $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $number = 1;
        foreach ($data['data'] as $key=>$value){
            $begin = 'A' . $l;
            $objPHPExcel->getactivesheet()->setCellValue($begin, $typeInfo[$key]);
            $count = 0;
            foreach ($todayData as $k=>$day){
                $col = $k.$l;
                if(in_array($day,array_keys($value['arr']))){
                    $num = $value['arr'][$day];
                    $count = $count + $num;
                }else{
                    if($day == "total"){
                        $num = $count;
                    }else{
                        $num = 0;
                    }
                }
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
            $l++;
            $number++;
        }
        $l++;
        //合计
        $last = 'A'.$l;
        $objPHPExcel->getactivesheet()->setCellValue($last, '合计');
        $counts = 0;
        foreach ($todayData as $k => $day) {
            $col = $k . $l;
            if (in_array($day, array_keys($data['countsArr']['arr']))) {
                $num = $data['countsArr']['arr'][$day];
                $counts = $counts + $num;
            } else {
                if ($day == "total") {
                    $num = $counts;
                } else {
                    $num = 0;
                }
            }
            $objPHPExcel->getactivesheet()->setCellValue($col, $num);
        }
        $titlename = "订单出库报表-" . date('Y-m-d') . ".xls";
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

    /**
     * 获取显示折线图数据
     * @param $beginTime
     * @param $endTime
     * @param $type
     * @return mixed
     * @author Shawn
     * @date 2018/10/30
     */
    public function getChartsData($beginTime,$endTime,$type)
    {
        $map = [
            'ptime' => ['between', array($beginTime,$endTime)],
        ];
        $dateData = $this->showEveryDayDetail($beginTime,$endTime);
        $statisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        if($type == 1){
            $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,count(DISTINCT ebay_id) as count,type";
        }else{
            $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,sum(qty) as count,type";
        }
        $countResult = $statisticsModel->where($map)->group('type,pktime')->field($field)->select();
        $typeData = array_unique(array_column($countResult, 'type'));
        $name = 'chart_name_'.$type;
        $x_data_names = $this->$name;
        $y_data = [];
        $dataArr = [];
        $countArr = [];//每天总数
        foreach ($typeData as $key => $val) {
            $y_data[$val] = [];
            foreach ($dateData as $v){
                $count = 0;
                foreach ($countResult as $reVal) {
                    if ($v == $reVal['pktime'] && $val == $reVal['type']) {
                        $count = $reVal['count'];
                        $countArr[$reVal['pktime']]['count']+=$count;
                    }

                }
                $y_data[$val][] = $count;
            }

        }
        //计算占比
        foreach ($y_data as $k=>$value){
            foreach ($value as $kk=>$va){
                $date = $dateData[$kk];
                $y_data[$k][$kk] = number_format($va/$countArr[$date]['count'],4);
            }
        }
        $data['y_data'] = json_encode($y_data);
        $data['x_data'] = json_encode($dateData);
        $data['x_data_names'] = json_encode($x_data_names);
        return $data;
    }

    /**
     * sku出库报表列表
     * @author Shawn
     * @date 2018/10/30
     */
    public function skuOutStoreStatistics()
    {
        if (!can('skuOutStoreStatistics')) {
            $this->error("您没有权限查看sku出库报表");
        }
        $model = isset($_REQUEST['model']) ? $_REQUEST['model'] : 'table';
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $map = [
            'ptime' => ['between', $timeArea],
        ];
        $fetch = array();
        $dateData = [];
        if($model == 'chart'){
            $data = $this->getChartsData($timeArea[0],$timeArea[1],2);
            $this->assign($data);
            $fetch = $this->fetch("chart");
        }else{
            //显示查询时间内日期
            $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
            $data = $this->getSkuOutDetailData($map);
        }
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("countsArr", $data['countsArr']);
        $this->assign("dateData", $dateData);
        $this->assign("data", $data['data']);
        $this->assign("typeInfo", $this->chart_name_3);
        $this->assign("model", $model);
        $this->assign("fetch", $fetch);
        $this->display();
    }

    /**
     * 获取sku出库数据
     * @param $map
     * @return mixed
     * @author Shawn
     * @date 2018/10/30
     */
    public function getSkuOutDetailData($map)
    {
        //查询从库
        $statisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        //这段时间内的拣货人哪些天有数据
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,sum(qty) as count,type";
        $countResult = $statisticsModel->where($map)->group('type,pktime')->field($field)->select();
        $timeCounts = $statisticsModel->where($map)->group('pktime')->field($field)->select();
        $countsArr     = [];
        foreach ($timeCounts as $counts) {
            $countsArr['arr'][$counts['pktime']] = $counts['count'];
        }
        $typeData = array_unique(array_column($countResult, 'type'));
        $dataArr = [];
        foreach ($typeData as $key => $val) {
            $dataArr[$val] = [];
            foreach ($countResult as $reKey => $reVal) {
                if ($reVal['type'] == $val) {
                    $dataArr[$val]['arr'][$reVal['pktime']] = $reVal['count'];
                }
            }
        }
        $data['countsArr'] = $countsArr;
        $data['data'] = $dataArr;
        return $data;
    }

    /**
     * sku报表导出
     * @author Shawn
     * @date 2018/10/30
     */
    public function exportSkuOutStatistics()
    {
        if (!can('skuOutStoreStatistics')) {
            $this->error("您没有导出sku出库报表的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $timeData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0);
        $map = [
            'ptime' => ['between', $timeArea],
        ];
        //获取导出数据
        $data = $this->getSkuOutDetailData($map);
        $model = $_REQUEST['model'];
        $typeInfo = $this->chart_name_3;
        //折线图的导出好像不需要，先注释吧，没准哪天又要了
//        if($model == 'chart'){
//            $typeInfo = $this->chart_name_2;
//            foreach ($data['data'] as $k=>$v){
//                foreach ($data['countsArr']['arr'] as $kk=>$vv){
//                    $count = $v['arr'][$kk];
//                    $data['data'][$k]['arr'][$kk] = number_format($count/$vv,4);
//                }
//            }
//        }
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '类型\日期';
            }
            if($i >= 1){
                if(!isset($timeData[$j])){
                    $over = true;
                    $title = "合计";
                    $todayData[$dis] = 'total';
                }else{
                    $title = $timeData[$j];
                    $todayData[$dis] = $timeData[$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->getactivesheet()->setCellValue($col, $title);
            $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $number = 1;
        foreach ($data['data'] as $key=>$value){
            $begin = 'A' . $l;
            $objPHPExcel->getactivesheet()->setCellValue($begin, $typeInfo[$key]);
            $count = 0;
            foreach ($todayData as $k=>$day){
                $col = $k.$l;
                if(in_array($day,array_keys($value['arr']))){
                    $num = $value['arr'][$day];
                    $count = $count + $num;
                }else{
                    if($day == "total"){
                        $num = $count;
                    }else{
                        $num = 0;
                    }
                }
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
            $l++;
            $number++;
        }
        $l++;
        //合计
        $last = 'A'.$l;
        $objPHPExcel->getactivesheet()->setCellValue($last, '合计');
        $counts = 0;
        foreach ($todayData as $k => $day) {
            $col = $k . $l;
            if (in_array($day, array_keys($data['countsArr']['arr']))) {
                $num = $data['countsArr']['arr'][$day];
                $counts = $counts + $num;
            } else {
                if ($day == "total") {
                    $num = $counts;
                } else {
                    $num = 0;
                }
            }
            $objPHPExcel->getactivesheet()->setCellValue($col, $num);
        }
        $titlename = "sku出库报表-" . date('Y-m-d') . ".xls";
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

    /**
     * 订单基数分析报表首页
     * @author Shawn
     * @date 2018/11/15
     */
    public function orderBaseAnalysis(){
        if (!can('orderBaseAnalysisStatistics')) {
            $this->error("您没有权限查看订单基数分析报表");
        }
        $model = isset($_REQUEST['model']) ? $_REQUEST['model'] : 'table';
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $fetch = array();
        if($model == 'chart'){
            $data = $this->getOrderBaseAnalysisData($timeArea,true);
            $this->assign($data);
            $fetch = $this->fetch("chart");
        }else{
            //显示查询时间内日期
            $data = $this->getOrderBaseAnalysisData($timeArea);
        }
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("data", $data);
        $this->assign("model", $model);
        $this->assign("fetch", $fetch);
        $this->display();
    }

    /**
     * 获取订单基数数据
     * @param $timeArea
     * @param bool $is_chart
     * @return array
     * @author Shawn
     * @date 2018/11/15
     */
    public function getOrderBaseAnalysisData($timeArea,$is_chart = false){
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
         $map = [
            'ptime' => ['between', $timeArea],
        ];
        $statisticsModel = new StatisticsModel("","",C('DB_CONFIG_READ'));
        $field = "from_unixtime(ptime, '%Y-%m-%d') as pktime,count(DISTINCT ebay_id) as order_count,sum(qty) as sku_count";
        $orderCountResult = $statisticsModel->where($map)->group('pktime')->field($field)->select();
        $tempData = [];
        $data = [];
        foreach ($dateData as $vo){
            $order_count = 0;
            $sku_count = 0;
            $base = 0;
            foreach ($orderCountResult as $value){
                if($vo == $value['pktime']){
                    $order_count = $value['order_count'];
                    $sku_count = $value['sku_count'];
                    $base = number_format($value['sku_count']/$value['order_count'],2);
                }
            }
            $tempData['order_count'][$vo] = $order_count;
            $tempData['sku_count'][$vo] = $sku_count;
            $tempData['base'][$vo] = $base;
        }
        $data['dateData'] = $dateData;
        $data['data'] = $tempData;
        //表格数据
        if($is_chart){
            $chatData = [];
            $x_data_names = ['倍基数'];
            $y_data = [];
            //计算占比
            foreach ($data['data']['base'] as $k=>$value){
                $y_data[] = $value;
            }
            $chatData['y_data'] = json_encode(array($y_data));
            $chatData['x_data'] = json_encode($dateData);
            $chatData['x_data_names'] = json_encode($x_data_names);
            $data = $chatData;
        }
        return $data;
    }

    /**
     * 导出订单基数分析报表
     * @author Shawn
     * @date 2018/11/15
     */
    public function exportOrderBaseAnalysis(){
        if (!can('orderBaseAnalysisStatistics')) {
            $this->error("您没有导出订单基数分析报表的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0);
        //获取导出数据
        $data = $this->getOrderBaseAnalysisData($timeArea);
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '类型\日期';
            }
            if($i >= 1){
                if(!isset($data['dateData'][$j])){
                    $over = true;
                }else{
                    $title = $data['dateData'][$j];
                    $todayData[$dis] = $data['dateData'][$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->getactivesheet()->setCellValue($col, $title);
            $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $number = 1;
        foreach ($data['data'] as $key=>$value){
            $name = '';
            switch ($key){
                case 'order_count':
                    $name = '出库订单';
                    break;
                case 'sku_count':
                    $name = '出库skupcs数';
                    break;
                case 'base':
                    $name = '倍基数';
                    break;
            }
            $begin = 'A' . $l;
            $objPHPExcel->getactivesheet()->setCellValue($begin, $name);
            foreach ($todayData as $k=>$day){
                $col = $k.$l;
                $num = $value[$day];
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
            $l++;
            $number++;
        }
        $l++;

        $titlename = "订单基数分析报表-" . date('Y-m-d') . ".xls";
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

    /**
     * 称重失败统计表
     * @author Shawn
     * @date 2018/12/27
     */
    public function weightFailureIndex(){
        if (!can('weightFailureStatistics')) {
            $this->error("您没有权限查看称重失败统计表");
        }
        $model = isset($_REQUEST['model']) ? $_REQUEST['model'] : 'table';
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $fetch = array();
        if($model == 'chart'){
            $data = $this->weightFailureData($timeArea,true);
            $this->assign($data);
            $fetch = $this->fetch("chart");
        }else{
            //显示查询时间内日期
            $data = $this->weightFailureData($timeArea);
        }
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("data", $data);
        $this->assign("model", $model);
        $this->assign("fetch", $fetch);
        $this->display();
    }

    /**
     * 称重失败统计表数据
     * @param $timeArea
     * @param bool $is_chart
     * @return array
     * @author Shawn
     * @date 2018/12/27
     */
    public function weightFailureData($timeArea,$is_chart = false){
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $map = [
            'operationtime' => ['between', $timeArea],
        ];
        $statisticsModel = new CanWeightFailureLogModel("","",C('DB_CONFIG_READ'));
        $field = "from_unixtime(operationtime, '%Y-%m-%d') as pktime,count(id) as fail_count";
        $orderCountResult = $statisticsModel->where($map)->group('pktime')->field($field)->select();
        $tempData = [];
        $data = [];
        $total = 0;
        foreach ($dateData as $vo){
            $fail_count = 0;
            foreach ($orderCountResult as $value){
                if($vo == $value['pktime']){
                    $fail_count = $value['fail_count'];
                }
            }
            $tempData[$vo] = $fail_count;
            $total += $fail_count;
        }
        $data['dateData'] = $dateData;
        $data['data'] = $tempData;
        $data['total'] = $total;
        //表格数据
        if($is_chart){
            $chatData = [];
            $x_data_names = ['渠道错误'];
            $y_data = [];
            //计算占比
            foreach ($tempData as $k=>$value){
                $y_data[] = $value;
            }
            $chatData['y_data'] = json_encode(array($y_data));
            $chatData['x_data'] = json_encode($dateData);
            $chatData['x_data_names'] = json_encode($x_data_names);
            $data = $chatData;
        }
        return $data;
    }

    /**
     * 导出报表数据
     * @author Shawn
     * @date 2018/12/27
     */
    public function exportWeightFailure(){
        if (!can('weightFailureStatistics')) {
            $this->error("您没有导出称重失败统计表的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0);
        //获取导出数据
        $data = $this->weightFailureData($timeArea);
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '类型\日期';
            }
            if($i >= 1){
                if(!isset($data['dateData'][$j])){
                    $title = '合计';
                    $todayData[$dis] = 'total';
                    $over = true;
                }else{
                    $title = $data['dateData'][$j];
                    $todayData[$dis] = $data['dateData'][$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->getactivesheet()->setCellValue($col, $title);
            $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $name = '渠道错误';
        $begin = 'A' . $l;
        $objPHPExcel->getactivesheet()->setCellValue($begin, $name);
        foreach ($todayData as $k=>$day){
            $col = $k.$l;
            if($day == 'total'){
                $objPHPExcel->getactivesheet()->setCellValue($col, $data['total']);
            }else{
                $num = $data['data'][$day];
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
        }

        $titlename = "称重失败统计表-" . date('Y-m-d') . ".xls";
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

    /**
     * 异常订单报表首页
     * @author Shawn
     * @date 2019/1/3
     */
    public function abnormalOrderIndex()
    {
        if (!can('abnormalOrderStatistics')) {
            $this->error("您没有权限查看异常订单报表");
        }
        $model = isset($_REQUEST['model']) ? $_REQUEST['model'] : 'table';
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        if($day > 31){
            exit("查询区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        $fetch = array();
        if($model == 'chart'){
            $data = $this->abnormalOrderData($timeArea,true);
            $this->assign($data);
            $fetch = $this->fetch("chart");
        }else{
            //显示查询时间内日期
            $data = $this->abnormalOrderData($timeArea);
        }
        $this->assign("rawCondition", $rawTimeCondition);
        $this->assign("data", $data);
        $this->assign("model", $model);
        $this->assign("fetch", $fetch);
        $this->display();
    }

    /**
     * 异常订单数据
     * @param $timeArea
     * @param bool $is_chart
     * @return array
     * @author Shawn
     * @date 2019/1/3
     */
    public function abnormalOrderData($timeArea,$is_chart = false){
        $dateData = $this->showEveryDayDetail($timeArea[0],$timeArea[1]);
        $timeArea[0] = date("Y-m-d H:i:s",$timeArea[0]);
        $timeArea[1] = date("Y-m-d H:i:s",$timeArea[1]);
        $map = [
            'addtime' => ['between', $timeArea],
        ];
        $abnormalModel = new AbnormalOrderModel("","",C('DB_CONFIG_READ'));
        $field = "substring(addtime,1,10) as pktime,count(distinct ebay_id) as fail_count";
        $orderCountResult = $abnormalModel->where($map)->group('pktime')->field($field)->select();
        $tempData = [];
        $data = [];
        $total = 0;
        foreach ($dateData as $vo){
            $fail_count = 0;
            foreach ($orderCountResult as $value){
                if($vo == $value['pktime']){
                    $fail_count = $value['fail_count'];
                }
            }
            $tempData[$vo] = $fail_count;
            $total += $fail_count;
        }
        $data['dateData'] = $dateData;
        $data['data'] = $tempData;
        $data['total'] = $total;
        //表格数据
        if($is_chart){
            $chatData = [];
            $x_data_names = ['异常订单数'];
            $y_data = [];
            //计算占比
            foreach ($tempData as $k=>$value){
                $y_data[] = $value;
            }
            $chatData['y_data'] = json_encode(array($y_data));
            $chatData['x_data'] = json_encode($dateData);
            $chatData['x_data_names'] = json_encode($x_data_names);
            $data = $chatData;
        }
        return $data;
    }

    /**
     * 导出异常订单报表
     * @author Shawn
     * @date 2019/1/3
     */
    public function exportAbnormalOrder(){
        if (!can('abnormalOrderStatistics')) {
            $this->error("您没有导出异常订单报表的权限");
        }
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $beginTime = strtotime($_REQUEST['timeArea_start']);
        $endTime = strtotime($_REQUEST['timeArea_end']);
        $day = ceil(($endTime-$beginTime)/86400);
        $maxDay = 31;
        if($day > $maxDay){
            exit("导出区间请勿超过一个月");
        }
        $timeArea = $this->getStatisticArea($rawTimeCondition);
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0);
        //获取导出数据
        $data = $this->abnormalOrderData($timeArea);
        //循环设置第一列excel数据
        $letter = range('A','Z');
        $j = 0;
        $m = 0;
        $reset = false;
        $over = false;
        $todayData = [];
        for($i=0;$i<35;$i++){
            if($over){
                continue;
            }
            //重置
            if($i > 25 && $reset == false){
                $reset = true;
                $m = 0;
            }
            $str = ($i > 25) ? 'A' : '';
            $dis = $str.$letter[$m];
            $col = $dis."1";
            $title = '';
            if($i == 0){
                $title = '类型\日期';
            }
            if($i >= 1){
                if(!isset($data['dateData'][$j])){
                    $title = '合计';
                    $todayData[$dis] = 'total';
                    $over = true;
                }else{
                    $title = $data['dateData'][$j];
                    $todayData[$dis] = $data['dateData'][$j];
                }
                $j++;
            }
            $m++;
            $objPHPExcel->getactivesheet()->setCellValue($col, $title);
            $objPHPExcel->getactivesheet()->getColumnDimension($dis)->setWidth(20);
        }
        //设置excel表格数据
        $l = 2;
        $name = '异常订单数';
        $begin = 'A' . $l;
        $objPHPExcel->getactivesheet()->setCellValue($begin, $name);
        foreach ($todayData as $k=>$day){
            $col = $k.$l;
            if($day == 'total'){
                $objPHPExcel->getactivesheet()->setCellValue($col, $data['total']);
            }else{
                $num = $data['data'][$day];
                $objPHPExcel->getactivesheet()->setCellValue($col, $num);
            }
        }

        $titlename = "异常订单报表-" . date('Y-m-d') . ".xls";
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
}