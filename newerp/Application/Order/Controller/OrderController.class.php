<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 13:50
 */
namespace Order\Controller;
use  Common\Model\CarrierModel;
use Common\Model\GoodsSaleDetailModel;
use Common\Model\OrderDetailModel;
use Common\Model\OrderModel;

use Order\Model\EbayAccountModel;
use Order\Model\GoodsLocationModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;
use Package\Service\CreateSingleOrderService;
use Think\Controller;
use Think\Page;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @author Simon 2017/11/16
     */
    private $maxExport = 10000;

    public function index() {

        $prePage=100;

        $orderModel            = new OrderModel();

        $Account=new EbayAccountModel();

        $platformArr=$Account->getAllPlaforms();


        /*$orderDetailModel      = new OrderDetailModel();
         $topMenuModel          = new TopMenuModel();
         $topMenus      = $topMenuModel->getField('id,name');*/
        $request               = $_REQUEST;

        if($request['pageSize']){
            $prePage = trim($request['pageSize']);
        }

        if($request['platform']=='null'){
            unset($request['platform']);
        }
        $map = $this->getWhere($request);

        if($_SESSION['truename']=='测试人员谭'){
            //print_r($request);
        }

        if(strstr($_SESSION['tname'],'测试') || strstr($_SESSION['tname'],'程序')){
            $nomap['a.ebay_status']=1723;
            $nomap['_string']=' b.id is null';
            // 没初始化
            $noInit=$orderModel->alias('a')->join('left join erp_order_type b using(ebay_id)')->where($nomap)->count();
            $this->assign('noInit', $noInit);
        }


        $platform=[];

        if($request['platform']){
            $platform=explode(',',$request['platform']);
        }

        if ($request['sort_name'] && $request['sort_value']) {
            $sort[$request['sort_name']] = $request['sort_value'];
        }
        //如果选择了订单类型，需要连order_type表
        if(1==1){
            foreach ($map as $k=>$v){
                $key = 'a.'.$k;
                unset($map[$k]);
                $map[$key]  = $v;
            }
            if($request['order_type']){
                $map['b.type'] = (int)$request['order_type'];
            }
            $count = $orderModel->alias('a')
                ->join('inner join erp_order_type as b on a.ebay_id = b.ebay_id')
                ->where($map)
                ->count();
            if ($request['sort_name'] && $request['sort_value']) {
                $sort_new['a.'.$request['sort_name']] = $request['sort_value'];
            }
            $pageServer    = new Page($count, $prePage);
            $orders        = $orderModel->alias('a')->field('a.*,b.type')
                ->join('inner join erp_order_type as b on a.ebay_id = b.ebay_id')
                ->where($map)
                ->limit($pageServer->firstRow . ',' . $pageServer->listRows)
                ->order($sort_new)
                ->select();
        }else{
            $count         = $orderModel->where($map)->count();
            $pageServer    = new Page($count, $prePage);
            $orders        = $orderModel->where($map)->limit($pageServer->firstRow . ',' . $pageServer->listRows)->order($sort)->select();
        }
        $this->assign('fields', ['ebay_id' => '订单号(多)', 'ebay_tracknumber' => '跟踪号(多)', 'ebay_userid' => '客户id', 'ebay_username' => '客户名', 'pxorderid' => 'pxid(多)']);
        // hank 2018/1/18 11:13 添加 缺货订单 状态 否则订单界面状态不显示
        $topMenus =  [
            '2'=>'已出库',
            '1731'=>'回收站',
            '1723' => '可打印',
            '1745' => '待打印',
            '1724' => '待扫描（待包装）',
            '2009' => '待称重',
            '1725' => '缺货订单',
        ];
        $this->assign('topMenus',$topMenus);
        $this->assign('show', $pageServer->show());
        $this->assign('orders', $orders);
        $this->assign('request', $request);
        $this->assign("deliver_goods",$request['deliver_goods']);
        //统计每个渠道的数量

//        $countArr = $this->getAllAccountsFormat();
//        $this->assign("countArr",$countArr);

//        $listCount = $this->countStatus($map,$topMenus);
//        $this->assign("listCount",$listCount);
        $this->assign("platformArr",$platformArr);
        $this->assign("erp_op_id",$request['erp_op_id']);
        $this->assign("requestPlatform",$platform);
        $this->display();
    }

    //查询 和 导出的条件 abel
    public function getWhere($request){
        $request['sort_name']  = $request['sort_name'] ?: 'w_add_time';
        $request['sort_value'] = $request['sort_value'] ?: 'desc';
        $platform=[];
        if($request['platform']){
            $platform=explode(',',$request['platform']);
        }

        $map                   = [];
        if ($request['ebay_status']) {
            $map['ebay_status'] = ['in', $request['ebay_status']];
        }
        if ($request['ebay_addtime_start']) {
            $map['ebay_addtime'][] = ['egt', strtotime($request['ebay_addtime_start'])];
        }
        if ($request['ebay_addtime_end']) {
            $map['ebay_addtime'][] = ['elt', strtotime($request['ebay_addtime_end'])];
        }
        if ($request['w_add_time_start']) {
            $map['w_update_time'][] = ['egt', $request['w_add_time_start']];
        }
        if ($request['w_add_time_end']) {
            $map['w_update_time'][] = ['elt', $request['w_add_time_end']];
        }

        if(!empty($platform)){
            $map['ordertype']=['in',$platform];
        }

        if ($request['content']) {
            switch ($request['field']) {
                case 'ebay_id':
                case 'ebay_tracknumber':
                case 'pxorderid':
                    $request['content']     = str_replace('，', ',', $request['content']);
                    $map[$request['field']] = ['in', explode(',', $request['content'])];
                    break;
                case 'ebay_userid':
                    $map[$request['field']] = ['eq', $request['content']];
                    break;
                case 'ebay_username':
                    $map[$request['field']] = ['like', '%' . $request['content'] . '%'];
                    break;
            }
        }

        if ($request['erp_op_id']) {
            switch ($request['erp_op_id']) {
                case '1':
                case '2':
                case '3':
                    $map['erp_op_id'] = ['eq', $request['erp_op_id']];
                    break;
                case '4':
                    $map['erp_op_id'] = ['egt', $request['erp_op_id']];
                    break;
            }
        }

        $request['sku'] = trim($request['sku']);
        if ($request['sku']) {
            $request['sku']     = str_replace('，', ',', $request['sku']);
            $orderModel            = new OrderModel();
            $where['b.sku'] = ['in',$request['sku']];
            if($map['ebay_id']){
                $where['a.ebay_id'] = ['in',explode(',', $request['content'])];
            }
            $ebayIdArr = $orderModel->alias('a')->field('a.ebay_id')->where($where)->join('inner join erp_goods_sale_detail as b on a.ebay_id = b.ebay_id')->select();
            $newIdArr = [];
            foreach($ebayIdArr as $id){
                $newIdArr[] = $id['ebay_id'];
            }
            if(!empty($newIdArr)){
                $map['ebay_id'] = ['in',$newIdArr];
            }else{
                $map['ebay_id'] = "null";
            }
        }
        return  $map;
    }

    //导出数据组装
    public function exportsStatus(){
        $request               = $_REQUEST;
        $map = $this->getWhere($request);
        $orderModel            = new OrderModel();

        if ($request['sort_name'] && $request['sort_value']) {
            $sort[$request['sort_name']] = $request['sort_value'];
        }
        //如果选择了订单类型，需要连order_type表
        if(1==1){
            foreach ($map as $k=>$v){
                $key = 'a.'.$k;
                unset($map[$k]);
                $map[$key]  = $v;
            }
            if($request['order_type']){
                $map['b.type'] = (int)$request['order_type'];
            }
            if ($request['sort_name'] && $request['sort_value']) {
                $sort_new['a.'.$request['sort_name']] = $request['sort_value'];
            }
            $orders        = $orderModel->alias('a')->field('a.*,b.type,c.packinguser,c.addtime as bztime,d.scantime as smtime,d.scan_user')
                ->join('inner join erp_order_type as b on a.ebay_id = b.ebay_id')
                ->join('left join api_checksku as c ON c.ebay_id = a.ebay_id')
                ->join('left join api_orderweight as d ON d.ebay_id = a.ebay_id')
                ->where($map)
                ->order($sort_new)
                ->select();
        }else{
            $orders   = $orderModel->where($map)->order($sort)->select();
        }
        if (empty($orders)) {
            exit("<span style='color:red'>当前没有记录可导出</span>");
        }
        if (count($orders) > $this->maxExport) {
            exit("<span style='color:red'>当前记录有" . count($orders) . "条系统允许最多可以导出" . $this->maxExport . "条缩减范围导出</span>");
        }

        $type = ['1'=>'单品单货','2'=>'单品多货','3'=>'多品多货'];
//        $countArr = $this->getAllAccountsFormat();

        $logField='operationuser,operationtime,notes';
        $EbayOrdersLogModel = new OrderslogModel();
        $PickOrderDetail=new PickOrderDetailModel();
        $PickOrder=new PickOrderModel();
        $userIdModel = M('ebay_user');

        foreach($orders as $key => $val){
//            $orders[$key]['platform']  = $countArr[$val['ebay_account']]['platform'];
            $orders[$key]['type']      = $type[$val['type']];
            $logMap['ebay_id'] = $val['ebay_id'];
            $orders[$key]['log'] = $EbayOrdersLogModel->field($logField)->where($logMap)->order('operationtime desc')->find();

            $picker=$PickOrderDetail->where($logMap)->field('picker,ordersn')->group('ordersn')->order('id asc')->select();
            $orders[$key]['picker'] = end($picker)['picker'];
            $orders[$key]['pickOrdersn'] = end($picker)['ordersn'];
            $PickOrderTime=$PickOrder->where(['ordersn'=>$orders[$key]['pickOrdersn']])->getField('addtime');
            $orders[$key]['pickOrderTime'] = $PickOrderTime;

            $user = [];
            if($orders[$key]['scan_user']){
                $user[]=$orders[$key]['scan_user'];
            }
            if($orders[$key]['picker']){
                $user[]=$orders[$key]['picker'];
            }
            if($orders[$key]['packinguser']){
                $user[]=$orders[$key]['packinguser'];
            }
            if(!empty($user)){
                $mapp['username'] = ['in',$user];
                $orders[$key]['userIdArr']     = $userIdModel->where($mapp)->getField('username,id',true);
            }
        }
        return $orders;
    }


    //开始干活导出
    public function exportStatusOrder() {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '序号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '订单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '销售平台');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '运输方式');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '跟踪号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '客户姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'wms更新时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '当前状态');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '订单类型');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '拣货单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '拣货员');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '拣货时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '包装记录');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '包装员');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '包装时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '称重员');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', '称重时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', '日志');

        $pickerArr = [];
        $packingArr = [];
        $list   = $this->exportsStatus();
        $goodSaleDetilModel = new GoodsSaleDetailModel();
        $status = ['2' => '已发货', '2009' => '待称重', '1731' => '回收站', '1723' => '可打印', '1745' => '等待打印', '1724' => '等待扫描', '2009' => '已出库待称重'];
        $j      = 2;
        $sss = ['E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',];
        foreach ($list as $key => $item) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $j, $key + 1);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $j, $item['ebay_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $j, $item['ordertype']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $j, $item['ebay_carrier']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $j, $item['ebay_tracknumber']."\t");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $j, $item['ebay_username']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $j,$item['w_update_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $j, $status[$item['ebay_status']]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $j, $item['type']);

            $pickOrderTime = $item['pickOrderTime']>0 ? date("Y-m-d H:i:s", $item['pickOrderTime']) : 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $j, $item['pickOrdersn']);

            $pickerid = $item['picker'] ? "({$item['userIdArr'][$item['picker']]})" : '';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $j, $item['picker'].$pickerid);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $j, $pickOrderTime);

            $packinguserid = $item['packinguser'] ? "({$item['userIdArr'][$item['packinguser']]})" : '';
            $bztime = $item['bztime']>0 ? date("Y-m-d H:i:s", $item['bztime']) : 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $j, $item['packinguser'] ? 'Y' : 'N');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $j, $item['packinguser'].$packinguserid);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $j, $bztime);

            $scan_userid = $item['scan_user'] ? "({$item['userIdArr'][$item['scan_user']]})" : '';
            $smtime = $item['smtime']>0 ? date("Y-m-d H:i:s", $item['smtime']) : 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $j, $item['scan_user'].$scan_userid);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $j, $smtime);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $j, $item['log']['notes']);

            if($item['packinguser']){
                $packingArr[$item['packinguser'].$packinguserid]++;
            }else{
                $skuqty = $goodSaleDetilModel->field('sum(qty) as qty')->where(['ebay_id'=>$item['ebay_id']])->find();
                $pickerArr[$item['picker'].$pickerid][$item['type']]+=$skuqty['qty'];
            }
            $j++;
        }

        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
        $title     = "订单列表-" . date('Y-m-d');
        $titlename = "订单列表-" . date('Y-m-d') . ".xls";
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);

        if($_REQUEST['ebay_status'] == '1724' || $_REQUEST['ebay_status'] == '2009'){
            $j      = 2;
            if($_REQUEST['ebay_status'] == '1724'){
                $t = '配货';
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A1', $t.'员');
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B1', '单品单货');
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('C1', '单品多货');
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('D1', '多品多货');
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('E1', '合计');
                foreach ($pickerArr as $key => $picker) {
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A' . $j, $key);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B' . $j, $picker['单品单货'] ?: 0);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('C' . $j, $picker['单品多货'] ?: 0);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('D' . $j, $picker['多品多货'] ?: 0);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('E' . $j, array_sum($picker) ?: 0);
                    $j++;
                }
                $num = count($pickerArr);
                //图表
                $labels = array(
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$B$1',null,1),
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$C$1',null,1),
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$D$1',null,1),
                );
                $xLabels = array(
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$A$2:$A$'.$j,null,$num),//取x轴刻度
                );
                $datas = array(
                    new \PHPExcel_Chart_DataSeriesValues('Number',$t.'异常统计表!$B$2:$B$'.$j,null,$num),//取一班数据
                    new \PHPExcel_Chart_DataSeriesValues('Number',$t.'异常统计表!$C$2:$C$'.$j,null,$num),//取一班数据
                    new \PHPExcel_Chart_DataSeriesValues('Number',$t.'异常统计表!$D$2:$D$'.$j,null,$num),//取一班数据
                );
                $yAxisLabel = new \PHPExcel_Chart_Title('数量');
            }
            if($_REQUEST['ebay_status'] == '2009'){
                $t = '包装';
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A1', $t.'员');
                $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B1', '票数');
                foreach ($packingArr as $key => $packing) {
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A' . $j, $key);
                    $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B' . $j, $packing);
                    $j++;
                }
                $num = count($packingArr);
                //图表
                $labels = array(
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$B$1',null,1),
                );
                $xLabels = array(
                    new \PHPExcel_Chart_DataSeriesValues('String',$t.'异常统计表!$A$2:$A$'.$j,null,$num),//取x轴刻度
                );
                $datas = array(
                    new \PHPExcel_Chart_DataSeriesValues('Number',$t.'异常统计表!$B$2:$B$'.$j,null,$num),//取一班数据
                );
                $yAxisLabel = new \PHPExcel_Chart_Title('订单量');
            }
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
            $title = new \PHPExcel_Chart_Title($t.'异常统计表');
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
            $objPHPExcel->getActiveSheet()->setTitle($t.'异常统计表');
        }

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


    //统计每个渠道的数量 abel
    public function countStatus($map,$topMenus){
        $ustatus = $map['ebay_status'];
        $l =  explode(',',$ustatus[1]);
        unset($map['ebay_status']);   //干掉重来统计
        $orderModel            = new OrderModel();
        $res =  [];
        foreach($topMenus as $k => $v){
            $map['ebay_status'] = $k;
            if(in_array($k,$l)){
                $res[$k] = $orderModel->where($map)->count();
            }
        }
        return $res;
    }

    public function pickingList() {
        $get          = I('get.');
        $sort_name    = $get['sort_name'];
        $sort         = $get['sort_value'];
        $order = !isset($sort_name) || empty($sort_name) ? 'b.w_add_time DESC' : ($sort_name == "w_add_time" ? "b.w_add_time ". $sort : "a.to_time_".$get['types']." $sort" );
        $map          = $this->orderExt();
        $ebayOrderExt = new \Order\Model\ErpEbayOrderExtModel();
        $count        = $ebayOrderExt->join("AS a LEFT Join erp_ebay_order AS b ON a.ebay_id = b.ebay_id")->where($map)->count();
        $pageServer   = new Page($count, 50);
        $field        = "a.to_time_1723,a.to_time_1745,a.to_time_1724,a.to_time_2009,a.w_update_time,";
        $field       .= "b.ebay_carrier,b.pxorderid,b.ebay_tracknumber,b.ebay_username,b.ebay_status,b.ebay_addtime,b.ebay_id,b.w_add_time";
        $orders      = $ebayOrderExt
            ->join("AS a INNER Join erp_ebay_order AS b ON a.ebay_id = b.ebay_id")
            ->field($field)
            ->where($map)
            ->order($order)
            ->limit($pageServer->firstRow . ',' . $pageServer->listRows)
            ->select();
        $this->assign('request', $get);
        $this->assign('show', $pageServer->show());
        $this->assign('orders', $orders);
        $this->assign('fields', ['ebay_id' => '订单号(多)', 'ebay_tracknumber' => '跟踪号(多)', 'ebay_userid' => '客户id', 'ebay_username' => '客户名', 'pxorderid' => 'pxid(多)']);
        $this->assign('topMenus', ['1723' => '可打印', '1745' => '等待打印', '1724' => '等待扫描', '2009' => '已出库待称重', '2' => '已发货', '1731' => '回收站']);
        $this->assign('types',['1'=>'进入可打印时间',2=>'进入等待打印时间',3=>'进入等待扫描时间',4=>'进入已出库待称重时间']);
        $this->assign('carrier', $this->getLogistics());
        $this->display();
    }


    //获取物流渠道
    public function getLogistics(){
        $getLogistics = new \Order\Model\EbayCarrierModel();
        $lists = $getLogistics->getInlandCarrier();
        return $lists;
    }
    /**
     * 查询条件
     * @return mixed
     */
    public function orderExt() {
        $type               = I('get.types', 1723);
        $ebay_addtime_start = I('get.ebay_addtime_start');
        $ebay_addtime_end   = I('get.ebay_addtime_end');
        $w_add_time_start   = I('get.w_add_time_start');
        $w_add_time_end     = I('get.w_add_time_end');
        $start              = I('get.print_time_start');
        $end                = I('get.print_time_end');
        $ebay_id            = I('get.ebay_id');
        $mtype              = I('get.mtype',1);
        $start1             =I('get.print_time_start1');
        $end1            = I('get.print_time_end1');
        if (!empty($ebay_addtime_start)){
            $map['b.ebay_addtime'][] = array('egt', strtotime($ebay_addtime_start));
        }

        if(!empty($ebay_addtime_end)){
            $map['b.ebay_addtime'][] = array('elt', strtotime($ebay_addtime_end));
        }

        if(!empty($w_add_time_start)){
            $map['b.w_add_time'][] = array('egt', $w_add_time_start);
        }

        if(!empty($w_add_time_end)){
            $map['b.w_add_time'][] = array('elt', $w_add_time_end);
        }

        switch ($type) {
            case  1723:
                $string        = 'a.to_time_1723';
                break;
            case 1745:
                $string        = 'a.to_time_1745';
                break;
            case 1724:
                $string        = 'a.to_time_1724';
                break;
            case 2009:
                $string        = 'a.to_time_2009';
                break;
        }

        $map['b.ebay_status'] = $type;
        $printWhere    = time() - $start * 60 * 60;
        $printWhereEnd = time() - $end * 60 * 60;
        if($mtype == 2){
            if(!empty($start1)){
                $map[$string][] = array('egt',$start1 );
            }
            if(!empty($end1)){
                $map[$string][] = array('elt',$end1);
            }
        }else{

            if (!empty($start)) {
                $map[$string][] = array('elt',date('Y-m-d H:i:s',$printWhere));
            }

            if (!empty($end)) {
                $map[$string][] = array('egt',date('Y-m-d H:i:s',$printWhereEnd));
            }
        }


        if (!empty($ebay_id)) {
            unset($map);
            $ebay_id          = explode(',', $ebay_id);
            $map['a.ebay_id'] = array('in', $ebay_id);
        }
        return $map;
    }

    public function exportList() {
        $ebayOrderExt = new \Order\Model\ErpEbayOrderExtModel();
        $map          = $this->orderExt();
        $field        = "a.to_time_1723,a.to_time_1745,a.to_time_1724,a.to_time_2009,a.w_update_time,";
        $field .= "b.ebay_carrier,b.pxorderid,b.ebay_tracknumber,b.ebay_username,b.ebay_status,b.ebay_addtime,b.ebay_id,b.w_add_time,";
        $field .= "a.to_time_1723,a.to_time_1745,a.to_time_1724,a.to_time_2009";
        $OrderData = $ebayOrderExt
            ->join("AS a INNER Join erp_ebay_order AS b ON a.ebay_id = b.ebay_id")
            ->field($field)
            ->where($map)
            ->select();
        if (empty($OrderData)) {
            exit("<span style='color:red'>当前没有记录可导出</span>");
        }
        if (count($OrderData) > $this->maxExport) {
            exit("<span style='color:red'>当前记录有" . count($OrderData) . "条系统允许最多可以导出" . $this->maxExport . "条缩减范围导出</span>");
        }
        return $OrderData;
    }


    public function exportOrder() {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '序号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '订单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'WMS状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '物流方式');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '进入WMS时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '进入erp时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '进入可打印时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '进入等待打印时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '进入等待扫描时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '进入已出库待称重时间');
        $list   = $this->exportList();
        $status = ['2' => '已发货', '2009' => '待称重', '1731' => '回收站', '1723' => '可打印', '1745' => '等待打印', '1724' => '等待扫描', '2009' => '已出库待称重'];
        $j      = 2;
        foreach ($list as $key => $item) {
            $ebay_addtime = date("Y-m-d H:i:s", $item['ebay_addtime']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $j, $key + 1);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $j, $item['ebay_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $j, $status[$item['ebay_status']]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $j, $item['ebay_carrier']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $j, $item['w_add_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $j, $ebay_addtime);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $j, $item['to_time_1723']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $j, $item['to_time_1745']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $j, $item['to_time_1724']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $j, $item['to_time_2009']);
            $j++;
        }
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
        $title     = "订单拣货列表-" . date('Y-m-d');
        $titlename = "订单拣货列表-" . date('Y-m-d') . ".xls";
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

    //时间转换
    public function getDate(){
        $get = I('get.');
        if($get['types'] == 1){
            if(!empty($get['print_time_start'])){
                $start = (time() - strtotime($get['print_time_start'])) / 3600;
                $start = $start <=0 ? 0 : round($start,2);
            }

            if(!empty($get['print_time_end'])){
                $end = (time() - strtotime($get['print_time_end'])) / 3600;
                $end = $end <=0 ? 0 : round($end,2);
            }
        }

        if($get['types'] == 2){
            if(!empty($get['print_time_start'])){
                $start = date("Y-m-d H:i:s",time() - $get['print_time_start']*3600);
            }

            if(!empty($get['print_time_end'])){
                $end = date("Y-m-d H:i:s",time() - $get['print_time_end']*3600);

            }
        }

        $arr = ['start'=>$start,'end'=>$end];
        return $arr;

    }

    public function getDateInfo(){
        $list = $this->getDate();
        exit(json_encode($list));
    }

    //已包装/验货未出库
    public function hadInspection() {
        exit("功能已被废除！");
        $orderModel = new OrderModel();
        $request = $_REQUEST;
        $request['sort_name']  = $request['sort_name'] ?: 'w_add_time';
        $request['sort_value'] = $request['sort_value'] ?: 'desc';
        $map = [];
        $map['b.status'] = 1;

        if ($request['ebay_status']) {
            $map['a.ebay_status'] = ['in', $request['ebay_status']];
        }else{
            $map['a.ebay_status'] = ['in', [1723,1724,1745]];
        }
        if ($request['ebay_addtime_start']) {
            $map['a.ebay_addtime'][] = ['egt', strtotime($request['ebay_addtime_start'])];
        }
        if ($request['ebay_addtime_end']) {
            $map['a.ebay_addtime'][] = ['elt', strtotime($request['ebay_addtime_end'])];
        }
        if ($request['w_add_time_start']) {
            $map['a.w_update_time'][] = ['egt', $request['w_add_time_start'] . ' 00:00:00'];
        }
        if ($request['w_add_time_end']) {
            $map['a.w_update_time'][] = ['elt', $request['w_add_time_end'] . ' 23:59:59'];
        }
        if ($request['content']) {
            switch ($request['field']) {
                case 'ebay_id':
                case 'ebay_tracknumber':
                case 'pxorderid':
                    $request['content']     = str_replace('，', ',', $request['content']);
                    $map['a.'.$request['field']] = ['in', explode(',', $request['content'])];
                    break;
                case 'ebay_userid':
                    $map['a.'.$request['field']] = ['eq', $request['content']];
                    break;
                case 'ebay_username':
                    $map['a.'.$request['field']] = ['like', '%' . $request['content'] . '%'];
                    break;
            }
        }
        if ($request['sort_name'] && $request['sort_value']) {
            if($request['sort_name'] == 'addtime') {
                $sort['b.'.$request['sort_name']] = $request['sort_value'];
            }else {
                $sort['a.'.$request['sort_name']] = $request['sort_value'];
            }
        }

        //print_r($map);

        $total = $orderModel->alias('a')
            ->join('inner join api_checksku as b on (a.ebay_id = b.ebay_id)')
            ->where($map)
            ->count();
        $page = new Page($total,50);
        $limit = $page->firstRow . ',' . $page->listRows;
        $field = 'a.ebay_id,a.ebay_carrier,a.ebay_tracknumber,a.pxorderid,a.ebay_username,a.w_add_time,a.ebay_addtime,';
        $field .= 'a.ebay_status,a.status,b.addtime';
        $info = $orderModel->alias('a')
            ->join('inner join api_checksku as b on (a.ebay_id = b.ebay_id)')
            ->field($field)
            ->where($map)
            ->limit($limit)
            ->order($sort)
            ->select();

        $show = $page->show();
        $this->assign('fields', ['ebay_id' => '订单号(多)', 'ebay_tracknumber' => '跟踪号(多)', 'ebay_userid' => '客户id', 'ebay_username' => '客户名', 'pxorderid' => 'pxid(多)']);
        $this->assign('topMenus', ['1723' => '可打印', '1745' => '等待打印', '1724' => '等待扫描', '2009' => '已出库待称重', '2' => '已发货', '1731' => '回收站']);
        $this->assign('orders',$info);
        $this->assign('show',$show);
        $this->assign('request', $request);
        $this->display();

    }

    //异常库位
    public function abnormal(){
        exit("功能已被废除！");
        $OrderData = $this->abnormalList();
        $TopMenu=new TopMenuModel();
        $TopMenuArr=$TopMenu->getMenuName();
        $carrier=I('carrier');
        $carriers = array_filter(explode(',',$carrier));
        $this->assign('request_carrier',$carriers);
        $this->assign('TopMenuArr',$TopMenuArr);
        $CarrierModel=new CarrierModel();
        $carrier=$CarrierModel->where(['ebay_warehouse'=>C("CURRENT_STORE_ID")])->field('distinct name')->select();
        $this->assign('CarrierList',$carrier);
        $this->assign("types",[1=>'单品单货',2=>'单品多货','3'=>'多品']);
        $this->assign('lists',$OrderData);
        $this->assign('erp_url','http://erp.spocoo.com');
        $this->display();
    }
    public function exportAbnormalOrder(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '序号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '订单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'WMS状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '物流方式');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '进入WMS时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '进入erp时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '类型');
        $list = $this->abnormalList();
        $TopMenu=new TopMenuModel();
        $TopMenuArr=$TopMenu->getMenuName();
        $j      = 2;
        foreach ($list as $key => $item) {
            $types = $item['type'] == 1 || $item['type'] == 2 ? '单品' : "多品";
            $ebay_addtime = date("Y-m-d H:i:s", $item['ebay_addtime']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $j, $key + 1);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $j, $item['ebay_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $j, $TopMenuArr[$item['ebay_status']]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $j, $item['ebay_carrier']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $j, $item['w_add_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $j, $ebay_addtime);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $j, $types);
            $j++;
        }
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
        $title     = "特殊库位订单-" . date('Y-m-d');
        $titlename = "特殊库位订单-" . date('Y-m-d') . ".xls";
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

    //异常单数据 用于导出和列表
    public function abnormalList(){
        $status=(int)I('status');
        $carrier=I('carrier');
        $ebay_id=trim(I('ebay_id'));
        $ebay_addtime_s=trim(I('ebay_addtime_s'));
        $ebay_addtime_e=trim(I('ebay_addtime_e'));
        $label_type=intval(I('label_type'));
        $carriers = array_filter(explode(',',$carrier));
        $ebay_id=str_replace('，',',',$ebay_id);
        if(empty($ebay_id)){
            $ebay_ids=[];
        }else{
            $ebay_ids=explode(',',trim($ebay_id));
        }

        if(empty($status)){
            $condition['a.ebay_status'] = ['in','1723,1745'];
        }else{
            $condition['a.ebay_status'] = ['eq',$status];
        }

        if(count($carriers)){
            $condition['a.ebay_carrier']=['in',$carriers];
            /* $this->assign('request_carrier',$carriers);*/
        }

        if(count($ebay_ids)){
            $condition['a.ebay_id']=['in',$ebay_ids];
        }

        if($ebay_addtime_s&&$ebay_addtime_e){
            $start=strtotime($ebay_addtime_s.' 00:00:00');
            $end=strtotime($ebay_addtime_e.' 23:59:59');
            $condition['b.ebay_addtime']=['between',[$start,$end]];
        }

        $TransportArr=include_once(dirname(THINK_PATH).'/Application/Transport/Conf/config.php');
        if($label_type==1||2==$label_type){
            $carrier_mod2=array_keys($TransportArr['CARRIER_TEMPT_15']);
            $carrier_mod1=[];
            foreach($TransportArr['CARRIER_TEMPT'] as $carriers=>$v){
                if(!in_array($carriers,$carrier_mod2)){
                    $carrier_mod1[]=$carriers;
                }
            }

            if($label_type==1){
                $condition['a.ebay_carrier']=['in',$carrier_mod1];
            }else{
                $condition['a.ebay_carrier']=['in',$carrier_mod2];
            }

        }
        $orderMode     = new \Order\Model\EbayOrderModel();
        $condition['b.floor'] = ['eq',6];
        $field = "a.ebay_id,b.type,a.ebay_id,a.ebay_carrier,a.ebay_addtime,a.ebay_status,a.w_add_time,b.type";
        $OrderData = $orderMode
            ->join("AS a INNER Join erp_order_type AS b ON a.ebay_id = b.ebay_id")
            ->field($field)
            ->where($condition)
            ->select();
        return $OrderData;
    }


    /**
     *   http://terryzhang.vicp.io  11749456
     *   检查WMS 和 ERP状态
     *
     */
    public function statusList(){
        exit("功能已被废除！");
        $get = I('get.');
        if(!empty($get['data'])){
            $type = $get['status'] == 1 ? "ebay_id" : ($get['status'] == 2 ? "ebay_tracknumber" : "pxorderid");
            $url = "http://erp.spocoo.com/t.php?s=/Order/OrderStatusWMS/postWms/".trim($type).'/'.trim($get['data']);
            //$url = "http://local.erp.com/t.php?s=/Order/OrderStatusWMS/postWms/".trim($type).'/'.trim($get['data']);

            //echo $url.'<br>';
            $list =  json_decode(curl_get($url,20),TRUE);
            $list = $this->getApiCheck($list);
        }
        $ebay_status = ['2'=>'已发货','1722'=>'等待分配', '1723' => '可打印','1724'=>'等待扫描','1725'=>'缺货订单','1728'=>'有问题订单','1731'=>'回收站','1738'=>'低利润订单','1745'=>'等待打印','2009'=>'已出库待称重','2018'=>'订单拦截'];
        $this->assign("lists",$list);
        $this->assign('get',$get);
        $this->assign('ebayStatus',$ebay_status);
        $this->display();
    }

    //获取验货记录状态返回
    public function getApiCheck($list){
        if(is_array($list) && !empty($list)){
            foreach($list as $key => &$val){
                $pickStatus = $this->getPickArr($val['ebay_id']);
                if(empty($pickStatus))
                    $val['wms_code'][6] = '-6';
                elseif($pickStatus == 1 )
                    $val['wms_code'][7] = '-7';
                elseif($pickStatus == 3)
                    $val['wms_code'][8] = '-8';
            }
            return $list;
        }
    }

    //1号仓库
    public function getpickeckSkuStatus($ebay_id){
        $pickModel = new \Package\Model\ApiCheckskuModel();
        $arr = $pickModel->where(['ebay_id'=>$ebay_id])->getField('status');
        return $arr;
    }

    //2号仓库
    private function getpickeckSkuStatus234($ebay){
//        $url = "http://sukidong.iask.in/t.php?s=/Order/Order/getpickeckSkuStatus/ebay_id/".$ebay;
        $url = "http://192.168.32.2/t.php?s=/Order/Order/getpickeckSkuStatus/ebay_id/".$ebay;
        $arr = json_decode(curl_get($url),1);
        return $arr;
    }

    //组合
    public function getPickArr($ebay){
        $pick196 = $this->getpickeckSkuStatus($ebay);
        $pick234 = $this->getpickeckSkuStatus234($ebay);
        return empty($pick196) ?  $pick234 : $pick196;
    }


    //同步跟踪号
    public function synchronization($id){
        if(empty($id) || !isset($id)){
            self::returnJosn(['status'=>'-1','msg'=>'参数错误']);
        }

        /* $url = 'http://local.erp.com/t.php?s=/Order/OrderStatusWMS/sysTracknumber';*/
        $url = "http://erp.spocoo.com/t.php?s=/Order/OrderStatusWMS/sysTracknumber";
        $data = json_encode(curl_post($url,['id'=>$id]),1);
        self::returnJosn($data);
    }

    public static function returnJosn($arr){
        exit(json_encode($arr));
    }

    /**
     * hank 2018/1/26 19:53
     * 获取PDF面单
     * 主要用于我们纸质的面单在运输过程中损坏，物流公司无法扫描，
     * 给物流公司提供PDF面单给他们打印贴上
     */
    public function getPdfLabel(){
        echo '<meta charset="utf-8">功能已被废除';
    }

    public  function  updStatus($ebay_id,$status){
        self::returnJosn(['status' => '-1','msg'=>'功能被滥用']);
        return;

        if(empty($ebay_id) || empty($status)){
            self::returnJosn(['status' => '-1','msg'=>'参数错误']);
        }

        $ebay_id = explode(',',$ebay_id);
        $orderMode     = new \Order\Model\EbayOrderModel();
        $map['ebay_id'] = ['in',$ebay_id];
        $row = $orderMode->where($map)->setField('ebay_status',$status);
        $flg = $row === false ? ['status'=>'-1','msg'=>'操作失败'] :  ['status'=>'1','msg'=>'操作成功'];
        self::returnJosn($flg);
    }

    /**
     * 首页修改订单状态
     * @author Shawn
     * @date 2018/7/11
     */
    public function editOrderStatus(){
        $this->ajaxReturn(['status'=>'0','msg'=>'功能被滥用!']);
        return ;

        $ebay_id = I("ebay_id");
        $status = I("status");
        if(empty($ebay_id) || empty($status)){
            $this->ajaxReturn(['status'=>'0','msg'=>'参数错误']);
        }
        $ebay_id = explode(',',$ebay_id);
        $map['ebay_id'] = ['in',$ebay_id];
        $orderModel            = new OrderModel();
        $result = $orderModel->where($map)->setField('ebay_status',$status);
        if($result){
            $log = "[".date('Y-m-d H:i:s')."]ebay_id:".I("ebay_id")."修改状态为".$status."\r\n"."操作人：".session('truename')."\r\n";
            $file = dirname(dirname(THINK_PATH)).'/log/edit_order_status/'.date('YmdH').'.txt';
            writeFile($file,$log);
            $this->ajaxReturn(['status'=>'1','msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>'0','msg'=>'操作失败']);
        }
    }


    /**
     *测试人员谭 2018-07-20 17:41:51
     *说明: 1723 里面的异常
     */
    public function get1723Count(){
        $OrderModel=new OrderModel('','',C('DB_CONFIG_READ'));

        $map['a.ebay_status']=1723;
        $map['_string']=' b.id is null';



        // 没初始化
        $noInit=$OrderModel->alias('a')->join('left join erp_order_type b using(ebay_id)')->where($map)->count();
        $map=[];
        $map['a.ebay_status']=1723;
        $map['b.pick_status']=1;

        // 等待生成拣货单
        $noOrder=$OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();

        $map=[];
        $map['a.ebay_status']=1723;
        $map['b.pick_status']=2;

        // 已经生成拣货单
        $pickorder=$OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();


        $map=[];
        $map['a.ebay_status']=1723;
        $map['b.pick_status']=3;

        // 拣货打回
        $backOrder=$OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();

        echo '<br>可打印明细：没初始化:'.$noInit.' &nbsp;&nbsp;';
        echo '待生成拣货单:'.$noOrder.' &nbsp;&nbsp;';
        echo '已经生成:'.$pickorder.' &nbsp;&nbsp;';
        echo '拣货打回:'.$backOrder.' &nbsp;&nbsp;';
    }

    /**
     * @desc 打印拣货清单功能
     * @Author leo
     */
    public function printPicklist()
    {
        $pickServer = new CreateSingleOrderService();
        $request  = $_REQUEST;
        if(!$request['ebay_id'] && !$request['order_type']){
            echo "请先进行筛选!!";exit;
        }
        $map = $this->getWhere($request);

        if($request['ebay_id']){
            $map = ['ebay_id'=>['in',trim($request['ebay_id'])]];
        }
        $orderModel = new OrderModel();
        $goodsSaleDetilModel = new GoodsSaleDetailModel();
        if ($request['sort_name'] && $request['sort_value']) {
            $sort[$request['sort_name']] = $request['sort_value'];
        }
        foreach ($map as $k=>$v){
            $key = 'a.'.$k;
            unset($map[$k]);
            $map[$key]  = $v;
        }
        $map['b.type'] = (int)$request['order_type'];
        if ($request['sort_name'] && $request['sort_value']) {
            $sort_new['a.'.$request['sort_name']] = $request['sort_value'];
        }
        $orders        = $orderModel->alias('a')
            ->join('inner join erp_order_type as b on a.ebay_id = b.ebay_id')
            ->field('a.ebay_id,a.ebay_carrier,a.ebay_warehouse,b.type')
            ->where($map)
            ->order($sort_new)
            ->select();

        //每次操作一个订单，则将 erp_op_id + 1 2018年8月28日09:38:08
        $ebay_idArr = array_column($orders,'ebay_id');
        $orderModel->where(['ebay_id'=>['in',$ebay_idArr]])->setInc('erp_op_id',1);

        $storeidArr = array_unique(array_column($orders,'ebay_warehouse'));

        $config = include_once(dirname(THINK_PATH).'/Application/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $config['CARRIER_TEMPT_15'];
        foreach($storeidArr as $sval){
            foreach($orders as $key=>$val){
                $ebay_carrier = $val['ebay_carrier'];
                if($val['ebay_warehouse'] == $sval){
                    if($CARRIER_TEMPT_15[$ebay_carrier]){
                        $print15[$sval][] = $val;
                    }else{
                        $print10[$sval][] = $val;
                    }
                }
            }
        }

        $locationModel = new GoodsLocationModel();
        $field = 'a.shelves_id,b.picker';

        $print10_skuArr = [];
        foreach($print10 as $key => $val){
            $ebay_idArr =   array_column($val,'ebay_id');
            if(!empty($ebay_idArr)){
                $skuArr10 = $goodsSaleDetilModel->alias('a')->field('a.sku,a.qty,b.g_location')
                    ->where(['a.ebay_id'=>['in',$ebay_idArr]])
                    ->join("left join ebay_onhandle_{$key} as b on a.sku=b.goods_sn")
                    ->order('b.g_location')
                    ->select();
                foreach($skuArr10 as $skey_10 => $sval_10){
//                    $detil_10 = $locationModel->alias('a')->field($field)
//                        ->join("left join goods_shelves as b on a.shelves_id=b.id")
//                        ->where(['a.location'=>$sval_10['g_location'],'a.storeid'=>$key])
//                        ->find();
                    $sval_10['picker'] = $pickServer->getPicker($sval_10['g_location']);
                    $skuArr10[$skey_10] = $sval_10;
                }
                $print10_skuArr = array_merge($print10_skuArr,$skuArr10);
            }
        }

        $print10_skuArr = $this->unPrintSkuArr($print10_skuArr);
        $newPrint10_skuArr = $this->newPrintSkuArr($print10_skuArr);

        $print15_skuArr = [];
        foreach($print15 as $key => $val){
            $ebay_idArr     = array_column($val, 'ebay_id');
            if(!empty($ebay_idArr)) {
                $skuArr15       = $goodsSaleDetilModel->alias('a')->field('a.sku,a.qty,b.g_location')
                    ->where(['a.ebay_id' => ['in', $ebay_idArr]])
                    ->join("left join ebay_onhandle_{$key} as b on a.sku=b.goods_sn")
                    ->order('b.g_location')
                    ->select();

                foreach($skuArr15 as $skey_15 => $sval_15){
//                    $detil_15 = $locationModel->alias('a')->field($field)
//                        ->join("left join goods_shelves as b on a.shelves_id=b.id")
//                        ->where(['a.location'=>$sval_15['g_location'],'a.storeid'=>$key])
//                        ->find();
                    $sval_15['picker'] = $pickServer->getPicker($sval_15['g_location']);
                    $skuArr15[$skey_15] = $sval_15;
                }

                $print15_skuArr = array_merge($print15_skuArr, $skuArr15);
            }
        }

        $print15_skuArr = $this->unPrintSkuArr($print15_skuArr);
        $newPrint15_skuArr = $this->newPrintSkuArr($print15_skuArr);

        $this->assign('print15_skuArr',$newPrint15_skuArr);
        $this->assign('print10_skuArr',$newPrint10_skuArr);
        $this->assign('order_type',$request['order_type']);
        $this->display();
    }

    /**
     * @desc 将重复的sku数量累加
     * @Author leo
     */
    public function unPrintSkuArr($printSkuArr){
        $unPrint_skuArr = [];
        foreach($printSkuArr as $p10val){
            if($unPrint_skuArr[$p10val['sku']]){
                $unPrint_skuArr[$p10val['sku']]['qty'] += $p10val['qty'];
                continue;
            }
            $unPrint_skuArr[$p10val['sku']] = $p10val;
        }

        $sortpicker = array_column($unPrint_skuArr,'picker');
        $sortlocation = array_column($unPrint_skuArr,'g_location');
        array_multisort($sortpicker,SORT_ASC,$sortlocation,SORT_ASC,$unPrint_skuArr);
        return $unPrint_skuArr;
    }

    /**
     * @desc 数据分页
     * @Author leo
     */
    public function newPrintSkuArr($printSkuArr){

        $num = 1;
        $k = 0;
        $newPrint_skuArr = [];
        foreach($printSkuArr as $skuArr){
            $newPrint_skuArr[$k][] = $skuArr;
            $num++;
            if($num > 90){
                $num = 1;
                $k++;
            }
        }
        return $newPrint_skuArr;
    }

    /**
     * @desc 获取所有账号平台信息
     * @Author leo
     */
    public function getAllAccountsFormat()
    {
        $redis          = S(array('type' => 'redis'));
        $key        = 'erpcn:cache:account:format:all';
        $cache_time     = 60 * 60; // 1 hour
        $data = $redis->get($key);
        if ($data) {
            return $data;
        }
        $countModel = new EbayAccountModel();
        $all_account_list = $countModel->field('id, ebay_account, ebay_user,platform')->select();
        $format_list = [];
        foreach ($all_account_list as $value) {
            $format_list[$value['ebay_account']] = $value;
        }
        $redis->set($key, $format_list, $cache_time);
        return $format_list;
    }

    /**
     * 订单首页订单状态明细
     * @author Shawn
     * @date 2018/8/7
     */
    public function getOrderStatusList()
    {
        $OrderModel=new OrderModel('','',C('DB_CONFIG_READ'));
        $request               = $_REQUEST;
        $map = $this->getWhere($request);
        foreach ($map as $k=>$v){
            $key = 'a.'.$k;
            unset($map[$k]);
            $map[$key]  = $v;
        }
        //订单类型
        if(isset($request['order_type']) && $request['order_type'] > 0 ){
            $map['b.type'] = (int)$request['order_type'];
        }
        //需要查询数量的订单状态
        $topMenus =  [
            '1723' => '可打印',
            '1745' => '待打印',
            '1724' => '待扫描（待包装）',
            '2009' => '待称重',
        ];
        //根据类型进行统计数据
        $data = [];
        $ebay_status = trim($request['ebay_status']);
        foreach ($topMenus as $key=>$value){
            $find = strstr($ebay_status, (string)$key);
            $map['a.ebay_status'] = $key;
            switch ($key){
                case "1723":
                    if(!$find){
                        $data[$key]['noInit'] = 0;
                        $data[$key]['noOrder'] = 0;
                        $data[$key]['isOrder'] = 0;
                        $data[$key]['backOrder'] = 0;
                        $data[$key]['count'] = 0;
                    }else{
                        $map['_string'] = ' b.id is null';
                        // 没初始化
                        //$data[$key]['noInit'] = $OrderModel->alias('a')->join('left join erp_order_type b using(ebay_id)')->where($map)->count();
                        $data[$key]['noInit'] = 0;
                        unset($map['_string']);
                        // 等待生成拣货单
                        $map['b.pick_status'] = 1;
                        $data[$key]['noOrder'] = $OrderModel->alias('a')->join('inner join erp_order_type b using(ebay_id)')->where($map)->count();
                        // 已经生成拣货单
                        $map['b.pick_status'] = 2;
                        $data[$key]['isOrder'] = $OrderModel->alias('a')->join('inner join erp_order_type b using(ebay_id)')->where($map)->count();
                        // 拣货打回
                        $map['b.pick_status'] = 3;
                        $data[$key]['backOrder'] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                        $data[$key]['count'] =  $data[$key]['noInit'] +$data[$key]['noOrder'] + $data[$key]['isOrder'] + $data[$key]['backOrder'];
                        unset($map['b.pick_status']);
                        unset($map['_string']);
                    }
                    break;
                case "2009":
                    $data[$key]['count'] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                    break;
                case "1745":
                    //单品单货
                    $data[$key][1] = 0;
                    //单品多货
                    $data[$key][2] = 0;
                    //多品多货
                    $data[$key][3] = 0;
                    $data[$key]['count'] = 0;
                    if($find){
                        if(isset($map['b.type'])){
                            $data[$key][$map['b.type']] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                        }else{
                            $map['b.type'] = 1;
                            $data[$key][1] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();

                            $map['b.type'] = 2;
                            $data[$key][2] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();

                            $map['b.type'] = 3;
                            $data[$key][3] = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->where($map)->count();
                            unset($map['b.type']);
                        }
                        $data[$key]['count'] = $data[$key][1] + $data[$key][2] + $data[$key][3];
                    }
                    break;
                case "1724":
                    //单品单货
                    $data[$key][1] = 0;
                    //单品多货
                    $data[$key][2] = 0;
                    //多品多货
                    $data[$key][3] = 0;
                    $data[$key]['count'] = 0;
                    if(!$find){
                        break;
                    }else{
                        //等待包装去掉正在同步的数据
                        $apiCheckSkuModel = new ApiCheckskuModel('','',C('DB_CONFIG_READ'));
                        if(isset($map['b.type'])){
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
                            $data[$key][$map['b.type']] = $count;
                        }else{
                            $map['b.type'] = 1;
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
                            $data[$key][1] = $count;

                            $map['b.type'] = 2;
                            $orderData = $OrderModel->alias('a')->join('erp_order_type b using(ebay_id)')->field("a.ebay_id")->where($map)->select();
                            $ebay_ids = [];
                            foreach ($orderData as $v){
                                $ebay_ids[] = $v['ebay_id'];
                            }
                            $count = count($ebay_ids);
                            if(!empty($ebay_ids)){
                                $apiMap['status'] = 1;
                                $apiMap['ebay_id'] = array("in",$ebay_ids);
                                $apiCount = $apiCheckSkuModel->where($apiMap)->count();
                                $count = $count - $apiCount;
                            }
                            $data[$key][2] = $count;

                            $map['b.type'] = 3;
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
                            $data[$key][3] = $count;
                            unset($map['b.type']);
                        }
                        $data[$key]['count'] = $data[$key][1] + $data[$key][2] + $data[$key][3];
                        break;
                    }

            }
        }
        $this->ajaxReturn(['status'=>1,'data'=>$data]);
    }
}



