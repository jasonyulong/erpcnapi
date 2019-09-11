<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ErpEbayGoodsModel;
use Common\Model\OrderModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\BeltLayerModel;
use Package\Model\CarrierGroupModel;
use Package\Model\CheckScanOrderModel;
use Package\Model\EbayGoodsModel;
use Package\Model\GoodsSaleDetailModel;
use Package\Model\OneSkuPackLogModel;
use Package\Model\OrderPackageModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\ScanQrcodeModel;
use Package\Service\MakeBaleService;
use Think\Cache\Driver\Redis;
use Think\Page;

/**
 * Class CreatePickController
 * @package Package\Controller
 * 打包作业
 */
class MakeBaleController extends CommonController
{

    public $OrderStstus = array(
        '0'   => '等待打印',
        '1'   => '已打印未确认',
        '2'   => '已经确认',
        '3'   => '已经完成',
        '100' => '废除',
    );
    //录入 一个订单 是单品单货还是什么玩意
    //TODO 任何包装员 只能进行一个捡货单的 包装
    //如果一个包装没有搞定，尝试 扫描另一个包装 则不允许！
    function index()
    {

        $MakeSvs     = new MakeBaleService();
        $workOrdersn = $MakeSvs->GetWorkingOrdersn();
        $this->assign('workOrdersn', $workOrdersn);
        $this->display();
    }


    // 自动选择一个 工作台
    function selectWorkbench()
    {

        $ordersn = trim(I('ordersn'));
        if ($ordersn == '') {
            $this->display('index');
            return;
        }

        if (!preg_match("/^PK\d{10}$/", $ordersn)) {
            $this->assign('ErrorMsg', "严重错误，订单号有误!");
            $this->display('index');
            return;
        }

        $PickOrderModel = new PickOrderModel();
        $RR             = $PickOrderModel->where("ordersn='$ordersn'")->field('id,type,isprint,is_work,baleuser')->find();
        if (!$RR) {
            $this->assign('ErrorMsg', "严重错误:拣货单【{$ordersn}】，不存在!");
            $this->display('index');
            return;
        }
        if ($RR['type'] != 3) {
            $this->assign('ErrorMsg', "严重错误:拣货单【{$ordersn}】，不是多品多货!");
            $this->display('index');
            return;
        }
        if ($RR['isprint'] == 3) {
            $this->assign('ErrorMsg', "严重错误:拣货单【{$ordersn}】，已经完成了，无法包装!");
            $this->display('index');
            return;
        }
        $markedWork = false;
        //多品多货逻辑不变
        if ($RR['type'] == 3) {
            if ($RR['baleuser'] != $_SESSION['truename'] && $RR['is_work'] == 1) {
                $this->assign('ErrorMsg', "严重错误：拣货单【{$ordersn}】包装员【{$RR['baleuser']}】正在作业中！");
                $this->display('index');
                return;
            }
            $map['isprint']  = 2; // 没完结
            $map['baleuser'] = $_SESSION['truename']; // 打包人
            $map['is_work']  = 1;//包装作业中
            $map['type']     = 3;//多品多货
            $workingData     = $PickOrderModel->where($map)->field('id,ordersn')->find();
            if (!empty($workingData)) { // 正在作业的单子 居然存在，难道是待会去吃饭去了？？？
                // 一个打包员  只能同时进行一个作业
                if ($workingData['ordersn'] != $ordersn) {
                    $this->assign('ErrorMsg', "严重错误：您希望开始作业的拣货单是【{$ordersn}】，当前您还有一个正在作业的拣货单是【{$workingData['ordersn']}】");
                    $this->display('index');
                    return;
                }
            } else { // 要把新的单号标记为 开始作业！
                $markedWork = true;
            }
        } else {
            //单品需要查询pick_order_work表 看有没有正在作业的
            $workingMap['baleuser'] = array("eq", $_SESSION['truename']);
            $workingMap['ordersn']  = array("eq", $ordersn);
            $pickOrderWork          = M("pick_order_work")
                ->where($workingMap)
                ->find();
            if (!empty($pickOrderWork)) {
                if ($pickOrderWork['status'] == 2) {
                    $this->assign('ErrorMsg', "严重错误：拣货单【{$ordersn}】是已完成状态,不允许包装作业！");
                    $this->display('index');
                    return;
                }
            } else {
                $markedWork = true;
            }
        }
        $type    = $RR['type'];
        $isprint = $RR['isprint']; // 只能等于2
        $id      = $RR['id']; // 只能等于2
        if ($isprint != 2 && $type == 3) {
            $statusStr = $this->OrderStstus[$isprint];
            $this->assign('ErrorMsg', "严重错误：拣货单【{$ordersn}】是{$statusStr} 状态,不允许包装作业！");
            $this->display('index');
            return;
        }


        if ($markedWork) { // 如果 需要标记为正在进行中

            if ($type == 3) { // 如果是 多屏 多见  必须要验证一下 是不是 经过了 二次分拣
                $PickOrderDetailModel = new PickOrderDetailModel();
                $map['combineid']     = 0;
                $map['ordersn']       = $ordersn;
                $map['is_stock']      = 0;
                $map['is_delete']     = 0;
                $RS                   = $PickOrderDetailModel->where($map)->field('id')->find();
                if ($RS) {
                    $this->assign('ErrorMsg', "错误：多品多件拣货单【{$ordersn}】必须二次分拣,否则不允许包装作业");
                    $this->display('index');
                    return;
                }
            }


            // 标记为 正在 作业中
            $save['baleuser']   = $_SESSION['truename'];
            $save['is_work']    = 1;
            $save['work_start'] = time();
            $PickOrderModel->where("id='$id'")->save($save);
            //添加作业数据，多品多货的不加 Shawn  2018-05-25
            if ($type != 3) {
                $workData['orderid']    = $id;
                $workData['ordersn']    = $ordersn;
                $workData['baleuser']   = $_SESSION['truename'];
                $workData['status']     = 1;
                $workData['start_time'] = time();
                $workData['end_time']   = time();
                M("pick_order_work")->add($workData);
            }
        }

        if ($type == 1) {
            $this->redirect('MakeBale/ViewWorkPacgeOne', array('ordersn' => $ordersn), 0, '...');
            return;
        }

        if ($type == 2) {
            $this->redirect('MakeBale/ViewWorkPacgeOneMoreSKU', array('ordersn' => $ordersn), 0, '...');
            return;
        }

        if ($type == 3) {
            $this->redirect('MakeBale/ViewWorkPacgeMoreSKU', array('ordersn' => $ordersn), 0, '...');
            return;
        }

        $this->assign('ErrorMsg', "严重错误：拣货单【{$ordersn}】,类型异常：{$type}!");
        $this->display('index');
        return;

    }

    /**
     *测试人员谭 2017-05-27 14:39:45
     *说明: 单品单货
     */
    function ViewWorkPacgeOne($ordersn = '')
    {
        $goodsModel = new ErpEbayGoodsModel();
        $sku        = '';
        $SKUArr     = [];
        $Orders     = [];
        if ($_REQUEST['ordersn']) {
            $sku     = trim($_REQUEST['sku']); // 这个是 分拣好了 的那个 小格子号
            $ordersn = $_REQUEST['ordersn']; // 订单号  捡货单
        }
        if (!empty($sku)) {
            $isNoPackaging = $goodsModel->where(['goods_sn' => ['eq', $sku]])->getField('isnopackaging');
            $this->assign('is_no_packaging', $isNoPackaging);
        }
        $ebay_id = '';

        $print_mod = 1;

        if (!empty($sku) && !empty($ordersn)) {
            $MakeSvs = new MakeBaleService();
            $SKUArr  = $MakeSvs->GetOrderSKUBysku($ordersn, $sku);
            //            dump($SKUArr);
            //5 Orders
            $Orders = $MakeSvs->GetOrderHadPrint($ordersn, 5);
            //            dump($Orders);
        }

        //debug($SKUArr);


        if ($SKUArr['ebay_id']) {
            $ebay_id = $SKUArr['ebay_id'];
        }

        if ($SKUArr['print_mod']) {
            $print_mod = $SKUArr['print_mod'];
        }

        $this->assign('Orders', $Orders);
        $this->assign('ebay_id', $ebay_id);
        $this->assign('print_mod', $print_mod);
        $this->assign('SKUInfoArr', $SKUArr);

        $this->assign('ordersn', $ordersn);
        $this->assign('print_url', $SKUArr['print_url']);
        $this->display('onesku');
    }

    /**
     *测试人员谭 2017-05-27 14:40:16
     *说明: 单品多件
     */
    function ViewWorkPacgeOneMoreSKU($ordersn = '')
    {
        $goodsModel = new ErpEbayGoodsModel();
        $sku        = '';
        $SKUArr     = [];
        $Orders     = [];
        if ($_REQUEST['ordersn']) {
            $sku     = trim($_REQUEST['sku']); // 这个是 分拣好了 的那个 小格子号
            $ordersn = $_REQUEST['ordersn']; // 订单号  捡货单
        }
        if (!empty($sku)) {
            $isNoPackaging = $goodsModel->where(['goods_sn' => ['eq', $sku]])->getField('isnopackaging');
            $this->assign('is_no_packaging', $isNoPackaging);
        }

        $ebay_id = '';

        $print_mod = 1;

        if (!empty($sku) && !empty($ordersn)) {
            $MakeSvs = new MakeBaleService();
            $SKUArr  = $MakeSvs->GetOrderSKUBysku($ordersn, $sku);
            //5 Orders
            $Orders = $MakeSvs->GetOrderHadPrint($ordersn, 5);
        }


        if ($SKUArr['ebay_id']) {
            $ebay_id = $SKUArr['ebay_id'];
        }

        if ($SKUArr['print_mod']) {
            $print_mod = $SKUArr['print_mod'];
        }


        $this->assign('Orders', $Orders);
        $this->assign('ebay_id', $ebay_id);
        $this->assign('ebay_carrier', $SKUArr['ebay_carrier']);
        $this->assign('print_mod', $print_mod);
        $this->assign('ordersn', $ordersn);
        $this->assign('sku', $sku);
        $this->assign('SKUInfoArr', $SKUArr);
        $this->assign('print_url', $SKUArr['print_url']);
        $this->display('OneskuOrdermore');
    }


    function GetOrderInfoBySKUS()
    {
        //$ordersn = $_POST['ordersn']; // 订单号 捡货单 不需要了
        $skus    = $_POST['sku']; // 订单号 捡货单
        $lableType    = $_POST['type']; // 订单号 捡货单
        $skus    = trim(trim($skus), ',');
        $skus    = explode(',', $skus);

        $skuarr = [];
        foreach ($skus as $list) {
            $skuQty = explode('*', $list);
            $sku    = $skuQty[0];
            $qty    = $skuQty[1];
            if ($sku == '') {
                echo json_encode(['status' => 0, 'msg' => '数据格式有误啊!']);
                return;
            }
            $skuarr[$sku] = $qty;
        }

        $MakeSvs = new MakeBaleService();
        $Arr     = $MakeSvs->GetOrderBySkuStrings($skuarr,$lableType);
        echo json_encode($Arr);
    }


    function GetSKUInfoByBoxid()
    {
        $boxid   = $_POST['boxid']; // 这个是 分拣好了 的那个 小格子号
        $ordersn = $_POST['ordersn']; // 订单号 捡货单
        $MakeSvs = new MakeBaleService();
        $SKUArr  = $MakeSvs->GetSKUinfoByBoxid($ordersn, $boxid);
        echo json_encode($SKUArr);
    }


    function GetPickOrderStatus()
    {
        $ordersn = I('ordersn'); // 订单号 捡货单
        if (empty($ordersn)) {
            $Error = '<div style="color:#911">拣货单号为空</div>';
            $this->assign('ErrorMsg', $Error);
            $this->display();
            return;
        }


        $MakeSvs = new MakeBaleService();
        // 获取 已经打印了的
        $RR = $MakeSvs->GetOrderHadPrint($ordersn);

        // 这里获取没有打印的
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $map['ordersn']       = $ordersn;
        $map['is_baled']      = 0;
        $map['is_delete']     = 0;

        $Rs = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();

        //等待打包
        $this->assign('WaitBale', $Rs);

        //已经打包好了的
        $this->assign('SKUInfoArr', $RR);

        $this->display('balestatus');
    }


    /**
     *测试人员谭 2017-06-02 17:24:04
     *说明: 模拟原生的一些东西 顺便 加一点东西
     */
    function updatePackUser()
    {

        $ebayid        = $_POST['ebayid'];
        $auditUser     = $_POST['auditUser'];
        $baozhuangUser = $_POST['baozhuangUser'];
        $ordersn       = $_POST['ordersn'];
        $type          = (int)$_POST['type'];
        $qrcode        = isset($_POST['qrcode']) ? trim($_POST['qrcode'],",") : '';
        //所有扫描sku二维码原文
        $qrcodeText = '';
        if(strpos($qrcode,'$') !== false){
            $qrcodeText = $this->getQrcodeText($qrcode);
        }
        if ($ebayid == '' || empty($auditUser) || empty($baozhuangUser)) {
            echo json_encode(['status' => 0, 'msg' => '参数错误']);
            return '';
        }

        if($type!=4 && empty($ordersn)) { // 这里是 给那个 验货出库用的----
            echo json_encode(['status' => 0, 'msg' => '参数错误']);
            return '';
        }


        $ApiModel = new ApiCheckskuModel();

        $rr = $ApiModel->where("ebay_id='$ebayid'")->field('id')->find();

        if ($rr) {
            echo json_encode(['status' => 0, 'msg' => '已经包装过，或者扫描过了']);
            return '';
        }

        $add['ebay_id']        = $ebayid;
        $add['packinguser']    = $auditUser;
        $add['packagingstaff'] = $baozhuangUser;
        $add['addtime']        = time();

        $rr=$ApiModel->add($add);

        $log=$baozhuangUser.'通过拣货单包装打包';
        if($type==4){
            $orderPage['ebay_id'] = $ebayid;
            $orderPage['scan_user'] = $baozhuangUser;
            $orderPage['scan_time'] = time();
            $checkScanOrderModel = new CheckScanOrderModel();
            $checkSaveResult = $checkScanOrderModel->add($orderPage);
            if($checkSaveResult){
                //更新包装数量
                $count_key = "today_pick_count4:".$baozhuangUser;
                $redis = new Redis();
                $count = $redis->get($count_key);
                // 如果不存在 就啥都不干 存在 就更新redis
                if($count){
                    $goodsSaleModel = new GoodsSaleDetailModel();
                    $count['total']++;
                    $goodsData = $goodsSaleModel->where("ebay_id='$ebayid'")->field('sum(qty) as cc')->find();
                    $qty = $goodsData['cc'];
                    $count['sku_total'] += (int)$qty;
                    //计算过期时间
                    $today_end = strtotime(date("Y-m-d")." 23:59:59");
                    $limitTime = $today_end - time();
                    $redis->set($count_key,$count,$limitTime);
                }
            }
            $log=$baozhuangUser.'通过验货包装打包';
        }

        $Orderslog=new OrderslogModel();
        $Orderslog->addordernote($ebayid,$log,12);
        //不是多品多货
        if($type != 3 && !empty($qrcodeText)){
            //添加二维码扫描记录
            $scanQrcodeModel = new ScanQrcodeModel();
            $scanQrcodeData['ebay_id'] = $ebayid;
            $scanQrcodeData['addtime'] = time();
            $scanQrcodeData['qrcode'] = $qrcodeText;
            $scanQrcodeData['ordersn'] = empty($ordersn) ? '' : $ordersn;
            $scanQrcodeModel->add($scanQrcodeData);
        }


        if($type==4){
            echo json_encode(['status' => 1, 'msg' => '订单标记为已打包成功']);
            return '';
        }

        //===============================================================
        /**
         *测试人员谭 2017-06-02 18:05:24
         *说明: 包裹 标记为 已经 打印
         */
        $ebay_id    = $ebayid;
        $OrdersPack = new OrderPackageModel();

        if (!$ordersn) {
            echo json_encode(['status' => 0, 'msg' => '错误的拣货单号']);
            return '';
        }

        if (!$ebay_id) {
            echo json_encode(['status' => 0, 'msg' => '错误的订单号']);
            return '';
        }

        //die('sss');

        //添加
        $add['ebay_id']  = $ebay_id;
        $add['ordersn']  = $ordersn;
        $add['baleuser'] = $_SESSION['truename'];
        $add['baletime'] = time();


        $OrdersPack->add($add);


        $PickOrderDetailModel = new PickOrderDetailModel();
        $map['ordersn']       = $ordersn;
        $map['ebay_id']       = $ebay_id;
        $RR                   = $PickOrderDetailModel->where($map)->save(['is_baled' => 1, 'scaning' => 2]); // 标记为已经打包

        if ($RR) {

            //更新包装数量
            $count_key = "today_pick_count{$type}:".$add['baleuser'];
            $redis = new Redis();
            $count = $redis->get($count_key);

            // 如果不存在 就啥都不干 存在 就更新redis

            if($count && $type>0){
                $count['total']++;
                $Rs = $PickOrderDetailModel->where($map)->field('sum(qty) as cc')->find();
                $qty=$Rs['cc'];
                $count['sku_total'] += (int)$qty;
                //计算过期时间
                $today_end = strtotime(date("Y-m-d")." 23:59:59");
                $limitTime = $today_end - time();
                $redis->set($count_key,$count,$limitTime);
            }

            echo json_encode(['status' => 1, 'msg' => '订单标记为已打包成功']);
            return '';
        }

        echo json_encode(['status' => 0, 'msg' => '订单标记为已打包失败']);
        return '';
    }


    /**
     *测试人员谭 2017-06-05 20:17:56
     *说明: 重新打印的东西
     */
    function getPrintLink()
    {

        $ebay_id = trim($_POST['ebay_id']);

        if (empty($ebay_id)) {
            echo json_encode(['status' => 0, 'msg' => '订单号有误' . $ebay_id]);
            return '';
        }
        $MakeBaleService = new MakeBaleService();

        $RR = $MakeBaleService->getPrintUrl($ebay_id);

        echo json_encode($RR);
        return '';


    }


    /**
     *测试人员谭 2017-06-05 21:08:13
     *说明: 查看进度
     */

    function getBaleProcess()
    {
        $ordersn         = trim($_GET['ordersn']);
        $MakeBaleService = new MakeBaleService();
        $MainData        = $MakeBaleService->getBaleProcess($ordersn);

        $this->assign('work_start', $MainData['work_start']);
        $this->assign('CostHours', $MainData['CostHours']);
        $this->assign('ordersn', $MainData['ordersn']);
        $this->assign('Total', $MainData['Total']);
        $this->assign('Len', $MainData['Len']);
        $this->assign('Arr', $MainData['Arr']);

        $this->display();

    }


    /**
     *测试人员谭 2017-06-02 18:31:57
     *说明: 假装结束一下工作任务
     */
    function OverPkOrder()
    {

        return '';
        //is_baled==0;
        $ordersn = trim($_GET['ordersn']);

        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            $this->assign('Errormsg', '拣货单号识别失败!');
            $this->display();
            return '';
        }

        $MakeBaleService = new MakeBaleService();

        $MainData = $MakeBaleService->GetCanNotBaleOrder($ordersn);

        if ($MainData == -3) {
            $this->assign('Errormsg', '拣货单已经结束!');
        }

        if (count($MainData) == 0 && is_array($MainData)) { // 全部都完成了  非常好！！！！
            // 标记为完成
            $Rs = $MakeBaleService->SetOrderOver($ordersn);

            if ($Rs['status'] != 1) {
                $this->assign('Errormsg', '拣货单结束失败!:' . $Rs['msg']);
            } else {
                $this->assign('Successmsg', '拣货单结束成功!');
            }
        }

        $this->assign('ordersn', $ordersn);
        $this->assign('Arr', $MainData);
        $this->display();
    }

    /**
     * 结束作业（新拣货单逻辑）
     * @param ordersn 拣货单号
     * @return string
     * @author Shawn
     * @date 2018-06-02
     */
    public function overPickOrderNew()
    {
        $ordersn = trim($_GET['ordersn']);
        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            $this->assign('Errormsg', '拣货单号识别失败!');
            $this->display();
            return '';
        }
        $isShow = false;
        //判断拣货单是否已经结束
        $workMap['ordersn']  = $ordersn;
        $workMap['baleuser'] = $_SESSION['truename'];
        $pickOrderWorkData   = M("pick_order_work")->where($workMap)->find();
        if (empty($pickOrderWorkData)) {
            $this->assign('Errormsg', '拣货单不存在!');
        }
        if ($pickOrderWorkData['status'] == 2) {
            $this->assign('Errormsg', '拣货单已经结束!');
        }
        //结束自己正在作业的拣货单
        $workMap['status']    = 1;
        $workData['end_time'] = time();
        $workData['status']   = 2;
        $result               = M("pick_order_work")->where($workMap)->save($workData);
        if ($result) {
            $this->assign('Successmsg', '拣货单结束成功!');
            $isShow = true;
        }
        $this->assign('isShow', $isShow);
        $this->display();
    }


    /**
     *测试人员谭 2017-06-02 18:31:57
     *说明: 确定结束作业
     */
    function conFirmOverOrder()
    {
        $ordersn = trim($_GET['ordersn']);
        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            echo json_encode(['status' => 0, 'msg' => '拣货单号识别失败!']);
            return '';
        }
        $MakeBaleService = new MakeBaleService();
        $Rs              = $MakeBaleService->SetOrderOver($ordersn);
        echo json_encode($Rs);
    }


    // 查看包装的 时候没有报完的 sku 退回
    function ViewReturnsku()
    {
        $ordersn = trim($_GET['ordersn']);
        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            $this->assign('Errormsg', '拣货单号识别失败!');
            $this->display();
            return '';
        }

        $MakeBaleService = new MakeBaleService();

        $MainData = $MakeBaleService->GetCanNotBaleOrder($ordersn);
        if ($_GET['debug'] == 1) {
            debug($MainData);
        }

        $SKUArr = $MakeBaleService->GetBaledReturnList($MainData);

        $this->assign('ordersn', $ordersn);
        $this->assign('SKUArr', $SKUArr);
        $this->display();

    }


    function jumpOrder()
    {
        $ordersn = trim($_POST['ordersn']);
        $ebay_id = (int) trim($_POST['ebay_id']);
        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            echo json_encode(['status' => 0, 'msg' => '拣货单号识别失败']);
            return '';
        }

        if ($ebay_id == '') {
            echo json_encode(['status' => 0, 'msg' => '订单号未识别到']);
            return '';
        }

        $PickOrderModel       = new PickOrderModel('', '', C('DB_CONFIG2'));
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));

        $RR = $PickOrderModel->where("ordersn='$ordersn'")->field('isprint,is_work,type')->find();
        if ($RR['isprint'] != 2 && $RR['type'] == 3) {
            echo json_encode(['status' => 0, 'msg' => '捡货单必须在已经确认']);
            return '';
        }

        if ($RR['is_work'] != 1) {
            echo json_encode(['status' => 0, 'msg' => '捡货单必须是正在包装才能操作']);
            return '';
        }

        $RR  = $PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")->field('id,isjump,is_baled')->select();
        $len = count($RR);
        $RR  = $RR[0]; // 给后面的 save 用

        //$id       = $RR['id'];
        $is_baled = $RR['is_baled'];
        $isjump   = $RR['isjump'];

        if ($is_baled == 1) {
            echo json_encode(['status' => 0, 'msg' => '订单已经包装好了 不能操作']);
            return '';
        }
        if ($isjump == 1) {
            echo json_encode(['status' => 0, 'msg' => '订单已经跳过了不能操作']);
            return '';
        }

        $map            = [];
        $map['ordersn'] = $ordersn;
        $map['ebay_id'] = $ebay_id;

        $RR = $PickOrderDetailModel->where($map)->limit($len)->save(['isjump' => 1]);
        if ($RR) {
            $file = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.bale.txt';
            $Log  = "拣货单【{$ordersn}】{$ebay_id}，打包时被跳过!---";
            writeFile($file, $_SESSION['truename'] . '----' . $Log . date('Y-m-d H:i:s') . "\n\n\n");

            echo json_encode(['status' => 1, 'msg' => '订单跳过操作成功']);
            return '';
        }

        echo json_encode(['status' => 0, 'msg' => '订单跳过操作失败']);
        return '';
    }


    function getOneSKUinfo()
    {
        $SKU   = strtoupper(trim($_POST['sku']));
        $Goods = new EbayGoodsModel();
        $field = 'a.goods_sn,a.goods_name,a.goods_pic,a.accessories,a.isPacking,b.packingmaterial';
        $RR    = $Goods->alias('a')->join("inner join ebay_onhandle_196 b using(goods_sn)")
            ->where(['goods_sn' => $SKU])
            ->field($field)
            ->find();
        if (!$RR) {
            echo json_encode(['status' => 0, 'msg' => '系统找不到这个sku:' . $SKU]);
            return '';
        }

        $packingmaterial = (int) $RR['packingmaterial'];
        $MakeBaleService = new MakeBaleService();
        //包材信息
        $packingMater        = $MakeBaleService->getPackingmater($packingmaterial);
        $RR['accessories']   = $MakeBaleService->fomartAccessories($RR['accessories']);
        $RR['isnopackaging'] = $RR['isPacking'] == 1 ? '去包装' : '';

        $Arr = array_merge($packingMater, $RR, ['status' => 1, 'msg' => '获取信息成功']);

        echo json_encode($Arr);
        return '';
    }

    /**
     * 新的包装台（单品单件）
     * @author Shawn
     * @date 2018/7/11
     */
    public function newWorkbench()
    {
        //物流渠道分组id
        $type = isset($_GET['type']) ? trim($_REQUEST['type']) : 1;
        //二维码原文
        $qrcode = isset($_GET['qrcode']) ? trim($_REQUEST['qrcode']) : '';

        $goodsModel = new ErpEbayGoodsModel();
        $SKUArr     = [];
        $sku        = trim($_REQUEST['sku']);
        if (!empty($sku)) {
            $isNoPackaging = $goodsModel->where(['goods_sn' => ['eq', $sku]])->getField('ispacking');
            $this->assign('is_no_packaging', $isNoPackaging);
        }
        $ebay_id   = '';
        $print_mod = 1;
        $ordersn   = '';
        $MakeSvs = new MakeBaleService();
        if (!empty($sku)) {
            $SKUArr  = $MakeSvs->getPickOrderBySku($sku,$type);
        }
        $userName = session('truename');
        $Orders  = $MakeSvs->getMyselfOrderHadPrint();
        if (isset($SKUArr['ebay_id'])) {
            $ebay_id = $SKUArr['ebay_id'];
        }
        if (isset($SKUArr['ordersn'])) {
            $ordersn = $SKUArr['ordersn'];
        }
        if (isset($SKUArr['print_mod'])) {
            $print_mod = $SKUArr['print_mod'];
        }
        //获取今日包装统计
        $count = $this->getTodayPickCount($userName,1);
        //获取传送带层数
        $floor = 0;
        if(isset($SKUArr['ebay_carrier'])){
            $beltModel = new BeltLayerModel();
            $floor = $beltModel->getLayerByCarrier($SKUArr['ebay_carrier']);
        }
        $this->assign('Orders', $Orders);
        $this->assign('ebay_id', $ebay_id);
        $this->assign('ebay_carrier', $SKUArr['ebay_carrier']);
        $this->assign('print_mod', $print_mod);
        $this->assign('ordersn', $ordersn);
        $this->assign('sku', $sku);
        $this->assign('SKUInfoArr', $SKUArr);
        $this->assign('print_url', $SKUArr['print_url']);
        $this->assign('count', $count);
        $this->assign('floor', $floor);
        $this->assign('type', $type);
        $this->assign('carrierGroup', $this->getCarrierGroup());
        $this->assign('qrcode', $qrcode);

        $this->display();
    }

    /**
     * 新的包装台单品多件
     * @author Shawn
     * @date 2018/7/13
     */
    public function newWorkbenchMore()
    {
        //物流渠道分组id
        $type = isset($_GET['type']) ? trim($_REQUEST['type']) : 1;
        //二维码原文
        $qrcode = isset($_GET['qrcode']) ? trim($_REQUEST['qrcode']) : '';
        $goodsModel = new ErpEbayGoodsModel();
        $SKUArr     = [];
        $sku        = trim($_REQUEST['sku']);
        if (!empty($sku)) {
            $isNoPackaging = $goodsModel->where(['goods_sn' => ['eq', $sku]])->getField('ispacking');
            $this->assign('is_no_packaging', $isNoPackaging);
        }
        $ebay_id   = '';
        $print_mod = 1;
        $ordersn   = '';
        $MakeSvs = new MakeBaleService();
        if (!empty($sku)) {
            $SKUArr  = $MakeSvs->getPickOrderBySkuMore($sku,$type);
        }
        $userName = session('truename');
        $Orders  = $MakeSvs->getMyselfOrderHadPrint();
        if (isset($SKUArr['ebay_id'])) {
            $ebay_id = $SKUArr['ebay_id'];
            //记录一下扫描日志
            $oneSkuLog = new OneSkuPackLogModel();
            $oneSkuLog->addLog($ebay_id,$sku);
        }
        if (isset($SKUArr['ordersn'])) {
            $ordersn = $SKUArr['ordersn'];
        }
        if (isset($SKUArr['print_mod'])) {
            $print_mod = $SKUArr['print_mod'];
        }
        //获取今日包装统计
        $count = $this->getTodayPickCount($userName,2);
        //获取传送带层数
        $floor = 0;
        if(isset($SKUArr['ebay_carrier'])){
            $beltModel = new BeltLayerModel();
            $floor = $beltModel->getLayerByCarrier($SKUArr['ebay_carrier']);
        }
        $this->assign('Orders', $Orders);
        $this->assign('ebay_id', $ebay_id);
        $this->assign('ebay_carrier', $SKUArr['ebay_carrier']);
        $this->assign('print_mod', $print_mod);
        $this->assign('ordersn', $ordersn);
        $this->assign('sku', $sku);
        $this->assign('SKUInfoArr', $SKUArr);
        $this->assign('print_url', $SKUArr['print_url']);
        $this->assign('count', $count);
        $this->assign('floor', $floor);
        $this->assign('type', $type);
        $this->assign('carrierGroup', $this->getCarrierGroup());
        $this->assign('qrcode', $qrcode);
        $this->display();
    }


    /**
    *测试人员谭 2018-08-03 17:09:48
    *说明: 新的包装台 多品多货
    */

    public function newWorkbenchMoreSKU(){
        //物流渠道分组id
        $type = isset($_GET['type']) ? trim($_REQUEST['type']) : 1;


        $MakeSvs = new MakeBaleService();



        $Orders  = $MakeSvs->getMyselfOrderHadPrint(); // 最近的几个包裹


        $this->assign('Orders', $Orders);
        $this->assign('type', $type);
        $this->assign('carrierGroup', $this->getCarrierGroup());

        $this->display('moresku');
    }


    public function ajaxGetToday(){


        //获取今日包装统计
        $userName = session('truename');
        $count = $this->getTodayPickCount($userName,(int)$_POST['type']);
        echo json_encode($count);
        return '';
    }

    /**
     * 新包装窗口跳过订单程序
     * @return string
     * @author Shawn
     * @date 2018/7/11
     */
    function newJumpOrder()
    {
        $ordersn = trim($_POST['ordersn']);
        $ebay_id = (int) trim($_POST['ebay_id']);
        if ($ordersn == '' || !preg_match("/^PK\d{10}$/", $ordersn)) {
            echo json_encode(['status' => 0, 'msg' => '拣货单号识别失败']);
            return '';
        }

        if ($ebay_id == '') {
            echo json_encode(['status' => 0, 'msg' => '订单号未识别到']);
            return '';
        }

        $PickOrderModel       = new PickOrderModel('', '', C('DB_CONFIG2'));
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $RR                   = $PickOrderModel->where("ordersn='$ordersn'")->field('isprint,is_work,type')->find();
        if ($RR['isprint'] == 3) {
            echo json_encode(['status' => 0, 'msg' => '捡货单已经完成，禁止操作']);
            return '';
        }
        $RR       = $PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")->field('id,isjump,is_baled')->select();
        $len      = count($RR);
        $RR       = $RR[0];
        $is_baled = $RR['is_baled'];
        $isjump   = $RR['isjump'];

        if ($is_baled == 1) {
            echo json_encode(['status' => 0, 'msg' => '订单已经包装好了 不能操作']);
            return '';
        }
        if ($isjump == 1) {
            echo json_encode(['status' => 0, 'msg' => '订单已经跳过了不能操作']);
            return '';
        }
        $map            = [];
        $map['ordersn'] = $ordersn;
        $map['ebay_id'] = $ebay_id;

        $RR = $PickOrderDetailModel->where($map)->limit($len)->save(['isjump' => 1]);
        if ($RR) {
            $file = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.bale.txt';
            $Log  = "拣货单【{$ordersn}】{$ebay_id}，打包时被跳过!---";
            writeFile($file, $_SESSION['truename'] . '----' . $Log . date('Y-m-d H:i:s') . "\n\n\n");

            echo json_encode(['status' => 1, 'msg' => '订单跳过操作成功']);
            return '';
        }

        echo json_encode(['status' => 0, 'msg' => '订单跳过操作失败']);
        return '';
    }

    /**
     * 今日包装统计
     * @param int $userName
     * @return int|mixed
     * @author Shawn
     * @date 2018/7/12
     */
    private function getTodayPickCount($userName,$type=1)
    {
        //今日开始、结束时间
        $today_begin = strtotime(date("Y-m-d")." 00:00:00");
        $today_end = strtotime(date("Y-m-d")." 23:59:59");
        $pickDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG_READ'));
        //false表示只查数量
        $userName = trim($userName);
        $map['a.scan_time']        = array("BETWEEN",array($today_begin,$today_end));
        $map['a.scan_user']        = $userName;
        $map['a.is_baled']         = 1;

        if($type<=3){
            $map['b.type']         = $type;
        }


        $key = "today_pick_count{$type}:".$userName;
        $redis = new Redis();
        $count = $redis->get($key);

        if(!$count){
            //计算当天剩余时间
            $data['total'] = 0;
            $data['sku_total'] = 0;

            $total=0;
            $sku_total=0;
            $limitTime = $today_end-time();

            if($type==1){ // 单品单货  单品多货的情况
                $rs = $pickDetailModel->alias('a')->where($map)
                    ->join('pick_order b using(ordersn)')
                    ->field("count(a.id) as total")
                    ->find();
                $total=$rs['total'];
                $sku_total=$total;
            }

            if($type==2){
                $rs = $pickDetailModel->alias('a')->where($map)
                    ->join('pick_order b using(ordersn)')
                    ->field("count(a.id) as total,sum(a.qty) as sku_total")
                    ->find();
                $total=$rs['total'];
                $sku_total=$rs['sku_total'];
            }

            if($type==3){
                $rs = $pickDetailModel->alias('a')->where($map)
                    ->join('pick_order b using(ordersn)')
                    ->field("distinct a.ebay_id")
                    ->select();
                $total=count($rs);

                $rs = $pickDetailModel->alias('a')->where($map)
                    ->join('pick_order b using(ordersn)')
                    ->field("sum(a.qty) as sku_total")
                    ->find();
                $sku_total=$rs['sku_total'];

            }


            if($type==4){ // 验货出库的时候 比较特殊！！

            }



            if($total>0){
                $data['total'] =$total;
            }

            if($sku_total>0){
                $data['sku_total'] =$sku_total;
            }

            $isSet = $redis->set($key,$data,$limitTime);

            return $data;
        }else{
            return $count;
        }

    }

    /**
     * 获取今日包装清单
     * @author Shawn
     * @date 2018/7/12
     */
    public function getBaleProcessNew(){
        $userName = session('truename');
        //今日开始、结束时间
        $today_begin = strtotime(date("Y-m-d")." 00:00:00");
        $today_end = strtotime(date("Y-m-d")." 23:59:59");
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG_READ'));
        $EbayOrder            = new OrderModel('', '', C('DB_CONFIG_READ'));
        $map['scan_user']       = $userName;
        $map['is_baled']        = 1;
        $map['scan_time']       = array("BETWEEN",array($today_begin,$today_end));
        $field                = 'ebay_id,sku,qty,goods_name,combineid,scan_time';
        //分页开始
        $count =  $PickOrderDetailModel->where($map)->count();
        $pageObj    = new Page($count, 30);
        $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
        $pageString = $pageObj->show();
        //分页end
        $ordersData = $PickOrderDetailModel->where($map)->field($field)->limit($limit)->order("scan_time desc")->select();
        foreach ($ordersData as $k=>&$v) {
            $ebay_id = $v['ebay_id'];
            $orderData = $EbayOrder->where("ebay_id='$ebay_id'")->field('pxorderid,ebay_tracknumber,ebay_carrier')->find();
            $v['pxorderid'] = $orderData['pxorderid'];
            $v['ebay_tracknumber'] = $orderData['ebay_tracknumber'];
            $v['scan_time'] = date('Y-m-d H:i:s',$v['scan_time']);
            if($v['combineid']){
                $v['combineid_str'] = '盒子号：'.$v['combineid'];
            }else{
                $v['combineid_str'] = '';
            }
        }
        $this->assign('ordersData', $ordersData);
        $this->assign('pageString', $pageString);
        $this->display();
    }

    /**
     * 释放订单让其他人也能扫到
     * @author Shawn
     * @date 2018/7/14
     */
    public function releaseOrder(){
        $ordersn = I("ordersn");
        $ebay_id = I("ebay_id");
        $sku = I("sku");
        $msg = '';
        $error = false;
        if($ordersn == ""){
            $error = true;
            $msg = '拣货单号为空';
        }
        if($ebay_id == ""){
            $error = true;
            $msg = '订单号为空';
        }
        if($sku == ""){
            $error = true;
            $msg = 'sku为空';
        }
        if($error){
            $this->ajaxReturn(['status'=>'0','msg'=>$msg]);
        }
        $map['sku'] = $sku;
        $map['ordersn'] = $ordersn;
        $map['ebay_id'] = $ebay_id;
        $PickOrderDetailModel = new PickOrderDetailModel();
        $pickDetailData = $PickOrderDetailModel->where($map)->find();
        if(empty($pickDetailData)){
            $msg = '未找到单号';
            $this->ajaxReturn(['status'=>'0','msg'=>$msg]);
        }
        if($pickDetailData['is_baled'] == 1){
            $msg = '这个订单已经被包装了，无法释放';
            $this->ajaxReturn(['status'=>'0','msg'=>$msg]);
        }
        if($pickDetailData['is_delete'] == 1){
            $msg = '这个订单被删除了，无法释放';
            $this->ajaxReturn(['status'=>'0','msg'=>$msg]);
        }
        $id = $pickDetailData['id'];
        $saveData['scaning'] = 0;
        $saveData['scan_user'] = '';
        $result = $PickOrderDetailModel->where("id='$id'")->save($saveData);

        if($result){
            $msg = '释放成功';
            $this->ajaxReturn(['status'=>'1','msg'=>$msg]);
        }else{
            $msg = '释放失败';
            $this->ajaxReturn(['status'=>'0','msg'=>$msg]);
        }

    }

    /**
     * 打印剩余sku
     * @author Shawn
     * @date 2018/8/16
     */
    public function printResidueSku(){
        //拣货单号
        $ordersn = trim(I("ordersn"));
        $ebay_id = trim(I("ebay_id"));
        $sku = trim(strtoupper(I("sku")));
        $count = I("count");
        $PickOrderDetailModel = new PickOrderDetailModel();
        $map['ordersn'] = $ordersn;
        $map['ebay_id'] = $ebay_id;
        $map['sku'] = $sku;
        $pickDetailData = $PickOrderDetailModel->where($map)
            ->field("location,picker,qty,is_delete,goods_name")
            ->find();
        if(empty($pickDetailData)){
           echo '未找到单号';exit;
        }else{
            if($pickDetailData['is_delete'] == 1){
                echo "订单已经被删除了";exit;
            }
        }
        $pickDetailData['sku'] = $sku;
        $pickDetailData['ebay_id'] = $ebay_id;
        $pickDetailData['count'] = (int)$count;
        $pickDetailData['userName'] = session("truename");
        $this->assign('pickDetailData',$pickDetailData);
        $this->display();
    }

    /**
     * 根据提交过来的二维码字符串去重
     * @param $qrcode
     * @return string
     * @author Shawn
     * @date 2018/9/5
     */
    private function getQrcodeText($qrcode)
    {
        $qrcodeArr = explode(",",$qrcode);
        foreach ($qrcodeArr as $k=>$value){
            if(strpos($value,'$') === false){
                unset($qrcodeArr[$k]);
            }
        }
        //去掉重复的
        $qrcodeData = array_unique(array_filter($qrcodeArr));
        $qrcodeStr = implode(",",$qrcodeData);
        return $qrcodeStr;
    }

    /**
     * 获取物流分组
     * @return mixed
     * @author Shawn
     * @date 2018/11/17
     */
    public function getCarrierGroup(){
        $carrierGroupModel = new CarrierGroupModel('', '', C('DB_CONFIG_READ'));
        $carrierGroupData = $carrierGroupModel->getField("id,group_name",true);
        return $carrierGroupData;
    }
}