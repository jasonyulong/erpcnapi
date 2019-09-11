<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/25
 * Time: 9:35
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ConfigsModel;
use Common\Model\EbayOrderExtModel;
use Common\Model\EbayUserModel;
use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Order\Model\EbayOrderModel;
use Order\Model\EbayStoreModel;
use Order\Model\OrderTypeModel;
use Package\Model\CarrierGroupModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderLogModel;
use Package\Model\PickOrderModel;
use Package\Service\CreatePickService;
use Think\Exception;
use Think\Log;
use Think\Page;
use Transport\Model\CarrierCompanyModel;

class PrintPickingListController extends CommonController
{
    /**
     * 默认每页显示100条数字，如果有配置的话则使用当前用户的配置条数
     * @var int
     */
    private $pageSize = 10;
    /**
     * 打印的拣货单每页的SKU记录数据条数
     * @var int
     */
    private $printPageSize = 40;

    /**
     * 当前控制器的初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->pageSize = session('pagesize') ? session('pagesize') : 100;
    }

    /**
     * 订单包裹列表
     * @param $isPrint
     */
    public function orderPackages($isPrint = 0) {
        $isPrint = isset($_GET['isPrint']) ? (int)$_GET['isPrint'] : 0;
        $s_ordersn = isset($_GET['s_ordersn']) ? trim($_GET['s_ordersn']) : '';
        $type = isset($_GET['type']) ? (int)$_GET['type'] : 0;
        $group = isset($_GET['group']) ? (int)$_GET['group'] : 0;
        $start = strtotime($_GET['start']);
        if(!$start){
            $start = strtotime("-3 days");
        }
        $end = strtotime($_GET['end']);
        if(!$end){
            $end = time();
        }
        $map['addtime'] = array("between",array($start,$end));
        $map['isprint'] = $isPrint;
        if(!empty($s_ordersn) && $s_ordersn != ""){
            $map['ordersn'] = $s_ordersn;
        }
        if($type > 0){
            $map['type'] = $type;
        }
        if($group > 0){
            $map['carrier_company'] = ($group == 1) ? array("in",[1,5,7]) : 2;
        }
        //如果搜索单号 去除时间条件
        if(isset($map['ordersn'])){
            unset($map['addtime']);
        }
        $pickOrderModel       = new PickOrderModel();
        $pickOrderDetailModel = new PickOrderDetailModel();
        $ebayUserModel = new EbayUserModel();
        // 实现分页查询
        $counts     = $pickOrderModel->where($map)->count();
        $pageObj    = new Page($counts, $this->pageSize, $map);
        $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
        $pageString = $pageObj->show();
        $packages   = $pickOrderModel->where($map)
            ->field('ordersn, carrier_company, type, addtime, adduser, storeid,is_work,work_start,work_end,baleuser,is_cross,pickuser')
            ->order('id desc')
            ->limit($limit)
            ->select();
        // 各类包裹状态的统计汇总
        $havePrintCount = $pickOrderModel
            ->where(['is_print' => ['neq', 100]])
            ->order('isprint asc')
            ->group('isprint')
            ->getField("isprint, count('id') as counts", true);
        // 获取分组名称
        $carrierGroup     = new CarrierGroupModel();
        $carrierGroupInfo = $carrierGroup->getField('id,group_name', true);
        foreach ($packages as $key => $val) {
            // 查询包裹中订单的数量
            $packageOrderCount             = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('ebay_id')
                ->field('id')
                ->select();
            $packages[$key]['order_count'] = count($packageOrderCount);
            // 查询包裹中SKU 的种类数
            $packagesOrderSkus           = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('sku')
                ->field('id')
                ->select();
            $packages[$key]['sku_count'] = count($packagesOrderSkus);
            // 查询包裹中所有SKU的总数
            $packages[$key]['sum_count'] = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('ordersn')
                ->getField('sum(qty)');
            //单品拣货单调整需要从pick_order_work查询数据
            if($val['type'] < 3){
                $packages[$key]['order_work'] = M("pick_order_work")
                    ->where(['ordersn' => $val['ordersn']])
                    ->field('baleuser,status')
                    ->select();
            }else{
                //多品拣货单找下拣货员的工号
                $packages[$key]['pickuser'] = $ebayUserModel->where(['username'=>$val['pickuser']])->getField("id");
            }
        }
        $this->isPrint        = $isPrint;
        $this->pageStr        = $pageString;
        $this->packages       = $packages;
        $this->havePrintCount = $havePrintCount;
        $this->carrierGroup   = $carrierGroupInfo;
        $this->s_ordersn      = $s_ordersn;
        $this->type           = $type;
        $this->group          = $group;
        $this->startTime      = date("Y-m-d H:i:s",$start);
        $this->endTime        = date("Y-m-d H:i:s",$end);
        $this->display();
    }

    /**
     * 导出选中的包裹
     * @param $packages
     */
    public function exportPackages($packages) {
        $packagesArr          = explode(',', $packages);
        $pickOrderModel       = new PickOrderModel('', '', C('DB_CONFIG2'));
        $pickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $packagesInfo         = $pickOrderModel->where(['isprint' => 3, 'ordersn' => ['in', $packagesArr]])
            ->field('ordersn, carrier_company, type, addtime, adduser, storeid,is_work,work_start,work_end,baleuser')
            ->order('id desc')
            ->select();
        // 获取分组名称
        $carrierGroup     = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $carrierGroupInfo = $carrierGroup->getField('id,group_name', true);
        foreach ($packagesInfo as $key => $val) {
            // 查询包裹中订单的数量
            $packageOrderCount                 = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('ebay_id')
                ->field('id')
                ->select();
            $packagesInfo[$key]['order_count'] = count($packageOrderCount);
            // 查询包裹中SKU 的种类数
            $packagesOrderSkus               = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('sku')
                ->field('id')
                ->select();
            $packagesInfo[$key]['sku_count'] = count($packagesOrderSkus);
            // 查询包裹中所有SKU的总数
            $packagesInfo[$key]['sum_count']       = $pickOrderDetailModel
                ->where(['ordersn' => $val['ordersn'], 'is_delete' => 0])
                ->group('ordersn')
                ->getField('sum(qty)');
            //单品拣货单调整需要从pick_order_work查询数据
            if($val['type'] < 3){
                $orderWork = M("pick_order_work")
                    ->where(['ordersn' => $val['ordersn']])
                    ->field('baleuser,status')
                    ->select();
                $baleuser = "";
                foreach ($orderWork as $v){
                    $baleuser.= $v['baleuser']."，";
                }
                $baleuser = trim($baleuser,"，");
            }else{
                $baleuser = $val['baleuser'];
            }
            $packagesInfo[$key]['carrier_company'] = $carrierGroupInfo[$val['carrier_company']];
            $packagesInfo[$key]['type']            = $val['type'] == 1 ? '单品单货' : ($val['type'] == 2 ? '单品多货' : '多品多货');
            $packagesInfo[$key]['addtime']         = date('Y-m-d H:i:s', $val['addtime']);
            $packagesInfo[$key]['statusStr']       = $val['is_work'] == 1 ? '正在包装' : "({$baleuser})-";
            $packagesInfo[$key]['spendTime']       = round((($val['work_end'] - $val['work_start']) / 3600), 2);
            unset(
                $packagesInfo[$key]['storeid'],
                $packagesInfo[$key]['is_work'],
                $packagesInfo[$key]['work_start'],
                $packagesInfo[$key]['work_end'],
                $packagesInfo[$key]['baleuser']
            );
        }
        $header = [
            '包裹单号',
            '物流分组',
            '类型',
            '创建日期',
            '创建人',
            '订单数量',
            'SKU数量',
            '货品数',
            '包装状态',
            '包装消耗时间'
        ];
        array_unshift($packagesInfo, $header);
        $fileName = 'packages_' . date('Y-m-d-H-i-s') . '.csv';
        export_csv($fileName, $packagesInfo);
    }

    /**
     * 根据包裹号 查看包裹包含的订单详细信息
     * @param $packageOrdersn
     */
    public function showPackageDetail($packageOrdersn) {
        $packageOrdersModel  = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $packageOrderDetail  = $packageOrdersModel->where(['ordersn' => $packageOrdersn])
            ->field('ebay_id, sku, qty, goods_name, location, status, order_addtime, ordersn')
            ->select();
        $this->packageOrders = $packageOrderDetail;
        $this->display();
    }

    /**
     * 查看包裹中订单列表
     * @param $ordersn
     */
    public function packageOrderList($ordersn) {
        $pickOrderDetail = new PickOrderDetailModel();
        //分页
        $counts     = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])->count();
        $pageObj    = new Page($counts, $this->pageSize, ['ordersn' => $ordersn]);
        $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
        $pageString = $pageObj->show();
        $orderDetail     = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])
            ->field('ebay_id, sku, qty, goods_name, location, status, order_addtime')
            ->limit($limit)
            ->select();
        //数据格式重新组合
        $orderFormat = [];
        foreach ($orderDetail as $key => $order) {
            if (!isset($orderFormat[$order['ebay_id']])) {
                $orderFormat[$order['ebay_id']] = [
                    'status'        => $order['status'],
                    'order_addtime' => $order['order_addtime'],
                ];
            }
            $orderFormat[$order['ebay_id']]['skus'][] = [
                'sku'        => $order['sku'],
                'qty'        => $order['qty'],
                'goods_name' => $order['goods_name'],
                'location'   => $order['location'],
            ];
        }
        $this->assign('orderFormat', $orderFormat)
            ->assign('ordersn', $ordersn)
            ->assign('pageString', $pageString)
            ->display();
    }

    /**
     * 查看包裹中SKU的列表
     * @param $ordersn
     */
    public function packageSkuList($ordersn) {
        // SELECT sku, SUM(qty) AS counts, goods_name, location, pic FROM pick_order_detail WHERE ordersn='PK1705240019' GROUP BY sku
        $pickOrderDetail = new PickOrderDetailModel();
        //分页
        $counts     = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])->count("distinct sku");
        $pageObj    = new Page($counts, $this->pageSize, ['ordersn' => $ordersn]);
        $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
        $pageString = $pageObj->show();
        $orderSku        = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])
            ->field('sku, sum(qty) AS counts, goods_name, location, pic')
            ->group('sku')
            ->limit($limit)
            ->select();
        $this->assign('orderSku', $orderSku)
            ->assign("pageString",$pageString)
            ->display();
    }

    /**
     * @param $ordersn
     * @param boolean $return
     * @return null | string
     */
    public function printPackage($ordersn, $return = false) {
        $pickOrderModel  = new PickOrderModel();
        $EbayUserModel   = new EbayUserModel();

        $pickOrderMain   = $pickOrderModel->where(['ordersn' => $ordersn])
            ->field('ordersn, type, addtime, adduser, storeid, carrier_company,pick_status,isprint,pickuser')
            ->find();
        if($pickOrderMain['pick_status'] == 0){
            $this->error("拣货单还未生成完毕");
        }
        if($pickOrderMain['type'] == 3){
            if($pickOrderMain['pickuser']){
                $this->assign('pkuser', $pickOrderMain['pickuser']);
            }
        }
        //如果是多品多货打印需要添加拣货人 Shawn
        if($pickOrderMain['type'] == 3 && $pickOrderMain['isprint'] < 2){
            $pkuser = trim(I('picker'));
            /**
            *测试人员谭 2018-08-03 14:24:04
            *说明: url 传过来的 包装员 必须是有效果的
            */
            $rs=$EbayUserModel->where(['id'=>$pkuser])->getField('username');
            if(empty($rs)){
                echo '<meta charset="utf-8">拣货员工号【'.$pkuser.'】,无效,禁止打印出来!';
                return false;
            }

            $pkuser=$rs; // id 改成 那啥!
            $beforePkuser=$pickOrderMain['pickuser'];

            $this->assign('pkuser', $pkuser);

            $setResult = $pickOrderModel->where(['ordersn' => $ordersn,'isprint'=>0])->save(['pickuser'=>$pkuser,'pick_end'=>time()]);
            if($setResult){
                $pickOrderLog = new PickOrderLogModel();
                $msg = "修改之前是【".$beforePkuser.'】';
                $note = '拣货单打印填写拣货人为['.$pkuser.']，修改成功! '.$msg;
                $pickOrderLog->addOneLog($ordersn,$note);
            }

        }
        $storeName       = (new EbayStoreModel())
            ->where(['id' => $pickOrderMain['storeid']])
            ->getField('store_name');
        $labelType       = (new CarrierGroupModel())
            ->where(['id' => $pickOrderMain['carrier_company']])
            ->getField('group_name');
        $pickOrderDetail = new PickOrderDetailModel();
        $orderSku        = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])
            ->field('sku, sum(qty) AS counts, goods_name, location,store_id as storeid')
            ->order('location asc')
            ->group('sku')
            ->select();
        $groupedSkuInfo  = array_group($orderSku, $this->printPageSize);
        $this->assign('pickOrderMain', $pickOrderMain)
            ->assign('storeName', $storeName)
            ->assign('pageSkuInfo', $groupedSkuInfo)
            ->assign('labelType', $labelType);
        if ($return) {
            return $this->fetch('_returnPrint');
        }
        $this->display();
    }

    /**
     * 将拣货单标记为已打印
     * TODO : 这里需要稍微修改下使之支持多个拣货单的同时打印
     * @param $ordersn
     * @return null;
     */
    /**
    *测试人员谭 2018-08-02 23:07:42
    *说明: 转到已打印 的意义变成了 转到 待包装
     *    此时订单状态被修改成  1724 ---------
     *    修改之前的状态应该是 1745
    */
    public function markAsPrinted($ordersn) {
        $pickOrderModel        = new PickOrderModel();
        $pickOrderDetailModel  = new PickOrderDetailModel();
        $pickOrderConfirmModel = new PickOrderConfirmModel();
        $erpEbayOrderModel     = new EbayOrderModel();
        $EbayOrderExtModel     = new EbayOrderExtModel();
        //验证是否已经打印，防止逆向操作
        $pickOrderData = $pickOrderModel->where(['ordersn'=>trim($ordersn)])->field('is_cross,isprint,type,pick_status,pickuser')->find();
        if(empty($pickOrderData)){
            $this->ajaxReturn(['status' => false,'data'=>'未找到该订单:'.$ordersn]);
        }else{
            if($pickOrderData['isprint'] != 0){
                $msg = '';
                switch ($pickOrderData['isprint']){
                    case '1':
                        $msg = '该订单：'.$ordersn.'已打印待确认（状态异常的）';
                        break;
                    case '2':
                        $msg = '该订单：'.$ordersn.'已确认';
                        break;
                    case '3':
                        $msg = '该订单：'.$ordersn.'已完成';
                        break;
                    default:
                        $msg = '该订单：'.$ordersn.'不存在';
                        break;
                }
                $this->ajaxReturn(['status' => false,'data'=>$msg]);
            }
        }

        $pick_status=$pickOrderData['pick_status'];
        $pickuser=trim($pickOrderData['pickuser']);

        if(0==$pick_status){
            echo json_encode(['status' => false, 'data' => '拣货单还在生成中....']);
            return false;
        }

        //TODO 跨仓单的操作暂时先暂停===============
/*        if($pickOrderData['is_cross']==1){
            //防止订单状态逆向变动
            $setResult = $pickOrderModel
                ->where(['ordersn' => $ordersn,'isprint'=>0])
                ->setField('isprint',2);

            echo json_encode(['status' => true, 'data' => "拣货单: <span style='color: #13d241'>{$ordersn}</span> 标记为已经打印成功."]);
            return true;
        }*/

        $type=(int)$pickOrderData['type'];

        if($type==3 && empty($pickuser)){ // 如果是多品多货的话，小伙子 ，你要填写了 拣货员才可以啊
            echo json_encode(['status' => false, 'data' => '多品多货转到待包装之前必须要填写拣货小伙伴!']);
            return false;
        }

        //获取拣货单包含的订单 ebay_id
        $ebay_ids = $pickOrderDetailModel->where(['ordersn' => $ordersn])->getField('ebay_id', true);

        $ebay_ids=array_unique($ebay_ids);

        if(count($ebay_ids)==0){
            echo json_encode(['status' => false, 'data' => '异常的']);
            return false;
        }


        try {
            $pickOrderModel->startTrans();
            /*
             * 拣货单 状态 从等待打印 进入 已打印待确认
             * 拣货单包含 订单 状态 可打印 进入 等待打印
             * 朱诗萌 2017/11/4
             */
            //防止订单状态逆向变动
            //TODO 开始修改了啊！！！！
            $setResult = $pickOrderModel
                ->where(['ordersn' => $ordersn,'isprint'=>0]) // 以后没有1 了哦
                ->setField('isprint',2);

            if(!$setResult){
                $pickOrderModel->rollback();
                echo json_encode(['status' => false, 'data' => '状态已经发生改变.操作错误']);
                return false;
            }


            /**
            *测试人员谭 2018-08-02 23:59:34
            *说明: 订单状态 改成 待包装（ 也就是原来的等待扫描! ）
            */
            $map=[];
            $map['ebay_id']=['in',$ebay_ids];
            $map['ebay_status']=1745;
            $Rs=$erpEbayOrderModel->where($map)->save(['ebay_status'=>1724]); // 转到原来的 可打印 现在的待包装

            if($Rs==false){
                $pickOrderModel->rollback();
                echo json_encode(['status' => false, 'data' => '打印确认失败. 请重新操作打印确认.']);
                return false;
            }


        } catch (Exception $e) {
            $pickOrderModel->rollback();
            $ss=$e->getMessage();
            echo json_encode(['status' => false, 'data' => '打印确认失败. 请重新操作打印确认...'.$ss]);
            return false;
        }

        $pickOrderModel->commit();

        echo json_encode(['status' => true, 'data' => "拣货单: <span style='color: #13d241'>{$ordersn}</span> 转到待包装."]);

        $PickOrderLog=new PickOrderLogModel();

        $note = '拣货单转到待包装成功!';

        $PickOrderLog->addOneLog($ordersn,$note);

        return true;
    }

    /**
     * @param $ordersn
     */
    public function batchMarkAsPrinted($ordersn) {
        $ordersnArr = explode(',', $ordersn);
        $fail       = [];
        ob_start();
        foreach ($ordersnArr as $key => $val) {
            $val = trim($val);
            if($val == "") continue;
            $result = $this->markAsPrinted($val);
            if (!$result) {
                $fail[] = $val;
            }
        }
        ob_get_clean();
        echo json_encode(['status' => true, 'data' => $fail ? '失败记录' . join(',', $fail) . ', 请重新标记失败记录' : '标记成功完成.']);
    }

    /**
     * @param $ordersn
     */
    public function showConfirm($ordersn) {
        $type = isset($_GET['type']) ? $_GET['type'] : 1;
        $pickOrderModel = new PickOrderModel('', '', C('DB_CONFIG2'));

        $is_cross=$pickOrderModel->where(['ordersn'=>$ordersn])->getField('is_cross');

        if($is_cross){
            echo "<h1 style='color:#911;'>跨仓订单不允许在此处确认.</h1>";
            return null;
        }


        $pickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        if ($type == 1 || $type == 2) {
            if (!can('pick_order_confirm')) {
                echo "<h1>没有足够权限.</h1>";
                return null;
            }
            $pickOrderConfirmModel = new PickOrderConfirmModel('', '', C('DB_CONFIG2'));
            $isFind                = $pickOrderConfirmModel->where(['ordersn' => $ordersn, 'status' => 2])->find();
            $packageSkuInfo        = $pickOrderDetailModel
                ->where(['ordersn' => $ordersn, 'is_delete' => 0])
                ->field('sku, sum(qty) AS counts, goods_name, location,pic,id')//not_picked
                ->group('sku')
                ->select();
            if ($isFind) {  // 已经确认数量的情况的处理，把数量也查出来
                foreach ($packageSkuInfo as $key => &$val) {
                    $packageSkuInfo[$key]['fillCounts'] = $pickOrderConfirmModel
                        ->where(['ordersn' => $ordersn, 'sku' => $val['sku']])
                        ->getField('real_qty');
                }
            }


            $WMS2SKU=[];
            $InterModel=new InternalStoreSkuModel();
            foreach($packageSkuInfo as $key=>$List){
                $sku=$List['sku'];
                $storeid=$InterModel->getOrderSkuStore($sku,196);
                if($storeid== C("MERGE_STORE_ID")){
                    //$WMS2SKU[]=$sku;
                    $packageSkuInfo[$key]['counts2']=0;
                }else{
                    $packageSkuInfo[$key]['counts2']=$List['counts'];
                }

            }

            $this->assign('packageSkuInfo', $packageSkuInfo);
            $this->assign('WMS2SKU', $WMS2SKU);
        }
        $this->assign('ordersn', $ordersn)
            ->assign('type', $type)
            ->display();
    }

    /**
     * 拣货单中的SKU 数量确认
     */
    public function doConfirmSku() {
        $packageInfo = $_POST['skuSet'];
        $ordersn     = $_POST['ordersn'];
//        Log::write(print_r($_POST, true), Log::DEBUG);
        $pickConfirmModel = new PickOrderConfirmModel();
        // 执行批量的更新操作
        try {
            $pickConfirmModel->startTrans();
            foreach ($packageInfo as $key => $val) {
                $pickConfirmModel->where(['ordersn' => $ordersn, 'sku' => $val['sku']])
                    ->save([
                        'real_qty'     => $val['counts'],
                        'confirm_user' => session('truename'),
                        'confirm_time' => time(),
                        'status'       => 2,
                    ]);
            }
        } catch (Exception $e) {
            Log::write($e->getTraceAsString(), Log::ERR);
            echo json_encode(['status' => false, 'data' => '拣货单确认失败.']);
            return false;
        }
        // 运行到这里表明事物成功了可以进行commit
        $pickConfirmModel->commit();
        // 查询出物流公司的ID
        $pickOrderModel = new PickOrderModel();
        $carrierId      = $pickOrderModel->where(['ordersn' => $ordersn])->getField('carrier_company');
        // 查询出物流公司的公司名称
        $carrierCompanyModel = new CarrierCompanyModel();
        $carrierCompanyName  = $carrierCompanyModel->where(['id' => $carrierId])->getField('sup_name');
        unset($pickOrderModel, $carrierCompanyModel, $pickConfirmModel);
//        Log::write('操作成功执行到此处.', Log::DEBUG);
        echo json_encode(['status' => true, 'data' => '拣货单确认成功.', 'carrier_company' => $carrierCompanyName]);
        return true;
    }

    /**
     * 展示预确认页面的相关数据
     * @param $ordersn
     */
    public function showPreConfirmPage($ordersn) {
        $pickOrderConfirmModel = new PickOrderConfirmModel('', '', C('DB_CONFIG2'));
        $pickOrderDetailModel  = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $orderSkuCount         = $pickOrderConfirmModel->where(['ordersn' => $ordersn])
            ->getField('sku, real_qty', true);
        $orderSkuInfo          = $pickOrderDetailModel
            ->where(['ordersn' => $ordersn])
            ->field('ebay_id, sku, is_delete, qty, order_addtime')
            ->order('order_addtime asc')
            ->select();
        unset($pickOrderConfirmModel, $pickOrderDetailModel);
        $targetOrderFormat = [];
        $flg               = 0;
        $message           = '';
        foreach ($orderSkuInfo as $key => $orderSku) {
            // sku 拣出的SKU 数量剩余 大于或等于 订单所需的并且订单不是已删除状态的可以进行正常的分配SKU
            if ($orderSkuCount[$orderSku['sku']] >= $orderSku['qty'] && $orderSku['is_delete'] == 0) {
                $orderSkuCount[$orderSku['sku']] -= $orderSku['qty'];
                $flg = 1;
            } else {
                $message = "需要{$orderSku['qty']} 个，当前有{$orderSkuCount[$orderSku['sku']]} 个";
            }
            $targetOrderFormat[$orderSku['ebay_id']]['order_addtime']          = $orderSku['order_addtime'];
            $targetOrderFormat[$orderSku['ebay_id']]['is_delete']              = $orderSku['is_delete'];
            $targetOrderFormat[$orderSku['ebay_id']]['skus'][$orderSku['sku']] = [
                'info'       => $message,
                'is_success' => $flg,
                'need_qty'   => $orderSku['qty'],
            ];
            $message                                                           = '正常配货';
            $flg                                                               = 0;
        }
        $orderModel = new OrderModel();
        // 查找订单的运输方式
        foreach ($targetOrderFormat as $ebayId => $value) {
            $targetOrderFormat[$ebayId]['carrier'] = $orderModel->where(['ebay_id' => $ebayId])->getField('ebay_carrier');
        }
        unset($orderSkuInfo, $orderSkuCount);
        $this->assign('assignOrderInfo', $targetOrderFormat)
            ->assign('ordersn', $ordersn)
            ->assign('type', 3)
//              -> assign('companyName', $carrierCompanyName)
            ->display('showConfirm');
    }


    /**
     * 从拣货单中删除一个订单
     * @param $ordersn
     * @param $ebayId
     * @return boolean
     */
//    public function deleteAnOrder($ordersn, $ebayId)
//    {
//        $orderDetailModel = new PickOrderDetailModel('','', C('DB_CONFIG2'));
//        $isDeleteOk = $orderDetailModel -> where(['ordersn' => $ordersn, 'ebay_id' => $ebayId])
//            -> setField('is_delete', 1);
//
//        if ($isDeleteOk !== false) {
//            echo json_encode(['status' => true, 'data' => '订单删除成功!']);
//            return true;
//        }
//        echo json_encode(['status' => false, 'data' => '订单删除失败.']);
//        return false;
//    }
    /**
     * 拣货单中删除的订单的撤销删除状态
     * @param $ordersn
     * @param $ebayId
     * @return boolean
     */
    public function reverseDeleteOrder($ordersn, $ebayId) {
        $orderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $isDeleteOk       = $orderDetailModel->where(['ordersn' => $ordersn, 'ebay_id' => $ebayId])
            ->setField('is_delete', 0);
        $CreatePick       = new CreatePickService();
        $RR               = $CreatePick->setOrderHasBeenCreateds($ebayId);
        if (!$RR['status']) {
            echo json_encode(['status' => false, 'data' => $RR['msg'] . '订单撤销删除失败.']);
            return false;
        }
        if ($isDeleteOk !== false) {
            echo json_encode(['status' => true, 'data' => '订单撤销删除成功!']);
            return true;
        }
        echo json_encode(['status' => false, 'data' => '订单撤销删除失败.']);
        return false;
    }

    /**
     * 订单最终确认，订单将转到当前已确认状态
     * @param $ordersn
     * @return null
     */
    public function orderConfirm($ordersn) {
        if (!can('pick_order_next_confirm')) {
            echo json_encode(['status' => false, 'data' => '权限不足，请先开通该权限.']);
            return null;
        }
        $pickOrderModel = new PickOrderModel('', '', C('DB_CONFIG2'));
        $isFind         = $pickOrderModel->where(['ordersn' => $ordersn])->field('isprint')->find();
        if (!$isFind) {
            echo json_encode(['status' => false, 'data' => '未找到指定订单']);
            return null;
        } elseif ($isFind['isprint'] == 100) {
            echo json_encode(['status' => false, 'data' => '指定订单当前处于已删除状态.']);
            return null;
        }
        $confirmResult = $pickOrderModel->where(['ordersn' => $ordersn])->setField(['isprint' => 2]);
        if ($confirmResult === false) {
            echo json_encode(['status' => false, 'data' => '订单转入到已确认失败']);
            return null;
        }
        echo json_encode(['status' => true, 'data' => '订单转入到已确认成功.']);
    }

    /**
     * 批量打印选中的记录
     * @param $_ordersn
     */
    public function batchPrint($_ordersn) {
        $ordersns = explode(',', $_ordersn);
        $pageStr  = '';
        foreach ($ordersns as $item) {
            $pageStr .= $this->printPackage($item, true);
        }
        // 压缩输出
        ob_start(function_exists('ob_gzhandler') ? 'ob_gzhandler' : '');
        echo $pageStr;
        $compressed = ob_get_clean();
        $this->assign('pages', $compressed)
            ->display();
    }

    /**
     *测试人员谭 2017-07-03 16:14:44
     *说明: 预览配货之后，将那些缺货的订单 显示出来吧
     */
    function showStockOrder() {
        $ordersn           = I('ordersn');
        $OrderModel        = new OrderModel();
        $Config            = new ConfigsModel();
        $configArr         = $Config->find();
        $pick_order_status = $configArr['pick_order_status'];
        $pick_order_status = explode(',', $pick_order_status);
        if ($ordersn == '') {
            echo json_encode(['status' => 0, 'msg' => '参数错误!']);
            return;
        }
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $map['ordersn']       = $ordersn;
        $map['is_delete']     = 1; // 删掉的
        //$map['is_stock']=1;  // 已经缺货的
        $RR       = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();
        $ebay_ids = '';
        $i        = 0;
        foreach ($RR as $List) {
            $ebay_id = $List['ebay_id'];
            $ss      = $OrderModel->where(['ebay_id' => $ebay_id])->getField('ebay_status');
            if (!in_array($ss, $pick_order_status)) {
                continue;
            }
            $ebay_ids .= $List['ebay_id'] . ',';
            $i++;
        }
        $data = '<div style="margin:15px;max-width:400px;">' . $ebay_ids . '</div>';
        $data .= '<div style="margin:10px;">' . $i . '单</div>';
        if ($i > 0) {
            $data .= '<div style="margin:10px;"><a target="_blank" href="http://47.90.38.119/t.php?s=/Order/OrderManage/lists&search_field=a.ebay_id&search_value=' . $ebay_ids . '">';
            $data .= '在ERP系统查看</a></div>';
        }
        echo json_encode(['status' => 1, 'msg' => '获取成功', 'data' => $data]);
        return;
    }

    /**
     *测试人员谭 2017-07-10 13:47:43
     *说明: 确认之后 还要把订单打出来  跟 showStockOrder  基本是 重复的
     */
    function PickFailure() {
        $ordersns          = I('packages');
        $Config            = new ConfigsModel();
        $configArr         = $Config->find();
        $pick_order_status = $configArr['pick_order_status'];
        $pick_order_status = explode(',', $pick_order_status);
        if ($ordersns == '') {
            echo '<div style="color:#911">参数错误了</div>';
            return;
        }
        $ordersns = explode(',', trim($ordersns, ','));
        if (count($ordersns) == 0) {
            echo '<div style="color:#911">参数错误了</div>';
            return;
        }
        //$map['is_stock']=1; 什么情况下 不缺货但是删除? 那就是
        $map['is_delete']     = 1;
        $map['ordersn']       = ['in', $ordersns];
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $OrderModel           = new OrderModel();
        $RR                   = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();
        $ebay_ids             = '';
        $i                    = 0;
        foreach ($RR as $List) {
            $ebay_id = $List['ebay_id'];
            $ss      = $OrderModel->where(['ebay_id' => $ebay_id])->getField('ebay_status');
            if (!in_array($ss, $pick_order_status)) {
                continue;
            }
            $ebay_ids .= $List['ebay_id'] . ',';
            $i++;
        }
        $data = '<div style="margin:15px;max-width:400px;">' . $ebay_ids . '</div>';
        $data .= '<div style="margin:10px;">' . $i . '单</div>';
        if ($i > 0) {
            $data .= '<div style="margin:10px;"><a target="_blank" href="http://47.90.38.119/t.php?s=/Order/OrderManage/lists&search_field=a.ebay_id&search_value=' . $ebay_ids . '">';
            $data .= '在ERP系统查看</a></div>';
        }
        echo $data;
        die();
    }

    /**
     * 确认没有捡到货的
     * abel
     */
    public function notPicked(){
        $post = I('post.');
        if(empty($post['data_id']) || empty($post['data_num'])){
            exit(json_encode(['status'=>'-1','msg'=>'请填写未捡货的数量']));
        }
        $pick_id  = explode(',',$post['data_id']);
        $pick_num = explode(',',$post['data_num']);
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        foreach($pick_id as $k => $v){
            $pickflg = $PickOrderDetailModel->where(['id'=>$v])->setField("not_picked",$pick_num[$k]);
        }
        $msg = $pickflg === false ?  ['status'=>'-1','msg'=>'设置失败'] : ['status'=>1,'msg'=>'设置成功'];
        exit(json_encode($msg));
    }

    public function getPickInfo(){
        $get = I('get.');
        $sku = explode(',',$get['sku']);
        $map['a.sku'] = ['in',$sku];
        $map['a.ordersn'] = $get['ordersn'];
        $pickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        //捡货单的详情
        $packageSkuInfo        = $pickOrderDetailModel
            ->join("AS a LEFT JOIN ebay_onhandle_196 AS b ON a.sku = b.goods_sn")
            ->where($map)
            ->field('a.sku, sum(a.qty) AS counts, a.goods_name, a.location,a.not_picked,b.goods_name')
            ->group('sku')
            ->select();

        //获取已经捡了多少 哈哈
        $pickOrderConfirmModel = new PickOrderConfirmModel('', '', C('DB_CONFIG2'));
        foreach($packageSkuInfo as $key => $val){
            $packageSkuInfo[$key]['fillCounts'] = $pickOrderConfirmModel
                ->where(['ordersn' => $get['ordersn'], 'sku' => $val['sku']])
                ->getField('real_qty');
        }
        $this->assign("lists",$packageSkuInfo);
        $this->display();
    }

    /**
     * 获取拣货单拣货人员
     * @param orderSn 拣货单号
     * @author Shawn
     * @date 2018-05-26
     */
    public function getPicker()
    {
        $ordersn = I('orderSn');
        $pickOrderDetail = new PickOrderDetailModel();
        $picker = $pickOrderDetail->where(['ordersn' => $ordersn, 'is_delete' => 0])
            ->field('picker')
            ->group('picker')
            ->select();
        $this->ajaxReturn(['data'=>$picker,'status'=>1]);
    }

    /**
     * 完成拣货单操作
     * @param ordersn 拣货单号
     * @author Shawn
     * @date 2018-06-02
     */
    public function setPickOrderOver(){
        $ordersn = trim($_GET['ordersn']);
        if($ordersn==''||!preg_match("/^PK\d{10}$/",$ordersn)){
            $this->assign('Errormsg','拣货单号识别失败!');
            $this->display();
        }
        $pickOrder = new PickOrderModel();
        $pickOrderData = $pickOrder->where("ordersn='$ordersn'")->field('isprint')->find();
        if(empty($pickOrderData)){
            $this->assign('Errormsg','拣货单不存在!');
            $this->display();
        }
        if($pickOrderData['isprint'] == 3){
            $this->assign('Errormsg','拣货单已经结束!');
            $this->display();
        }
        //计算正在包装和已经包装完成人员个数
        $workingCount = M("pick_order_work")->where(["ordersn"=>$ordersn,"status"=>1])->count();
        if($workingCount > 0){
          
            $this->assign('Errormsg','该拣货单有人正在进行包装，是否强制完成拣货单!');
        }
        $workedCount = M("pick_order_work")->where(["ordersn"=>$ordersn,"status"=>2])->count();
        //计算已经包装完成和全部订单数量
        $map['ordersn'] = $ordersn;
        $PickOrderDetailModel = new PickOrderDetailModel();
        $orderAllCount = $PickOrderDetailModel->where($map)->field('ebay_id')->count();
        $map['is_baled'] = 1;
        $orderBaledCount = $PickOrderDetailModel->where($map)->field('ebay_id')->count();
        $this->assign('workingCount',$workingCount);
        $this->assign('workedCount',$workedCount);
        $this->assign('orderAllCount',$orderAllCount);
        $this->assign('orderBaledCount',$orderBaledCount);
        $this->assign('ordersn',$ordersn);
        $this->display();

    }

    /**
     * 确认完成拣货单操作
     * @param ordersn 拣货单号
     * @return array
     * @author Shawn
     * @date 2018-06-02
     */
    public function confirmOverOrder(){
        /**
        *测试人员谭 2018-07-27 14:39:45
        *说明: 有自动结束程序
        */
        $this->ajaxReturn(['status'=>0,'msg'=>'禁止操作！请直接在首页处理异常订单即可，当拣货单中的订单全部包装完毕或者异常处理完毕拣货单将自动结束(单品)!']);

        return false;

        $ordersn = trim($_POST['ordersn']);
        $pickOrder = new PickOrderModel();
        $pickOrderDetail = new PickOrderDetailModel();
        $orderType = new OrderTypeModel();
        $orderModel = new OrderModel();
        $orderExtModel =  new EbayOrderExtModel();
        $orderLogModel = new OrderslogModel();
        $pickOrderData = $pickOrder->where("ordersn='$ordersn'")->field('isprint')->find();
        if(empty($pickOrderData)){
           $this->ajaxReturn(['status'=>0,'msg'=>'拣货单不存在']);
        }
        //查出没有打包的订单，转入异常处理
        $map['is_baled'] = 0;
        $map['is_delete'] = 0;
        $map['ordersn'] = $ordersn;
        //是否存在未包装的订单
        $pickDetailData = $pickOrderDetail->field("ebay_id")->where($map)->select();
        $pickOrderSaveData['isprint'] = 3;
        $pickOrderSaveData['is_work'] = 0;
        $pickOrderSaveData['pick_end'] = time();
        $pickOrderSaveData['work_end'] = time();
        $pickOrderSaveData['pickuser'] = $_SESSION['truename'];
        //开启事务
        $pickOrder->startTrans();
        try{
            //修改拣货单表状态已完成
            $pickOrderResult = $pickOrder->where("ordersn='$ordersn'")->save($pickOrderSaveData);
            if(!$pickOrderResult){
                $pickOrder->rollback();
                $this->ajaxReturn(['status'=>0,'msg'=>'拣货单表更新数据失败']);
            }
            //拣货单工作表结束工作
            $workMap['status']     = 1;
            $workMap['ordersn']     = $ordersn;
            $workData['end_time']   = time();
            $workData['status']   = 2;
            $workDataResult = M("pick_order_work")->where($workMap)->save($workData);
            //未包装订单转入异常
            if(!empty($pickDetailData)){
                $pickDetailResult = $pickOrderDetail->where($map)->save(['status'=>3,'scaning'=>2,'scan_user'=>$_SESSION['truename']]);
                if(!$pickDetailResult){
                    $pickOrder->rollback();
                    $this->ajaxReturn(['status'=>0,'msg'=>'拣货单详情表更新数据失败']);
                }
                $log = '[' . date('Y-m-d H:i:s') . ']订单在包装流程有问题,确认完成拣货单后该订单自动转到捡货单异常! ';
                $orderData['ebay_status'] = 1723;
                $orderData['ebay_noteb'] = $log;
                foreach ($pickDetailData as $v){
                    $ebay_id = $v['ebay_id'];
                    //更新订单类型表
                    $orderType->where("ebay_id=$ebay_id")->save(['pick_status'=>3]);
                    //订单状态修改,更新修改时间
                    $orderExtModel->saveToExt($ebay_id,1723);
                    //记录日志
                    $orderLogModel->addordernote($ebay_id, $log, 3);
                    //更新订单表状态
                    /**
                    *测试人员谭 2018-07-20 21:07:41
                    *说明: 这里有个大坑 如果订单已经完成就完了
                    */
                    $where=[];
                    $where['ebay_id']=$ebay_id;
                    $where['ebay_status']=['in',[1723,1724,1745]];
                    $orderModel->where($where)->save($orderData);
                }
            }
        }catch (Exception $e) {
            $pickOrder->rollback();
            $this->ajaxReturn(['status'=>0,'msg'=>'发生错误了','info'=>$e->getMessage()]);
        }
        $pickOrder->commit();
        $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);

    }

    /**
     * 新的打单界面
     * @author Shawn
     * @date 2018/7/11
     */
    public function printPackageNew()
    {
        set_time_limit(0);
        $ordersn = I("ordersn");
        $users = I("picker");
        //如果没有传拣货人，默认全选
        $map['ordersn'] = $ordersn;
        $pickOrderDetail = new PickOrderDetailModel();
        if(empty($users))
        {
            $picker = $pickOrderDetail->where($map)->group('picker')->getField("picker",true);
        }else{
            $picker = explode(",",$users);
        }
        //根据拣货人进行分组处理
        foreach ($picker as $k=>$v)
        {
            $map['is_delete'] = 0;
            $map['picker'] = $v;
            $orderSku        = $pickOrderDetail->where($map)
                ->field('sku, sum(qty) AS counts, goods_name, location,store_id as storeid,picker')
                ->order('location asc')
                ->group('sku')
                ->select();
            //切割数组100个分一组
            $data = [];
            $j = 0;
            $i = 0;
            foreach($orderSku as $key=>$value){
                $data[$j][$i] = $value;
                $i++;
                if($i == 100){
                    $j++;
                    $i = 0;
                }
            }
            $groupedSkuInfo[$v] = $data;
        }
        $pickOrderModel  = new PickOrderModel();
        $pickOrderMain   = $pickOrderModel->where(['ordersn' => $ordersn])
            ->field('ordersn, type, addtime, adduser, storeid, carrier_company,pick_status')
            ->find();
        if($pickOrderMain['pick_status'] == 0){
            $this->error("拣货单还未生成完毕");
        }
        $storeName       = (new EbayStoreModel())
            ->where(['id' => $pickOrderMain['storeid']])
            ->getField('store_name');
        $labelType       = (new CarrierGroupModel())
            ->where(['id' => $pickOrderMain['carrier_company']])
            ->getField('group_name');
        $this->assign('pickOrderMain', $pickOrderMain)
            ->assign('storeName', $storeName)
            ->assign('pageSkuInfo', $groupedSkuInfo)
            ->assign('labelType', $labelType);
        $this->display();
    }

    /**
     * 老的打单界面
     * @author Shawn
     * @date 2018/7/11
     */
    public function printPackageold()
    {
        set_time_limit(0);
        $ordersn = I("ordersn");
        $users = I("picker");
        //如果没有传拣货人，默认全选
        $map['ordersn'] = $ordersn;
        $pickOrderDetail = new PickOrderDetailModel();
        if(empty($users))
        {
            $picker = $pickOrderDetail->where($map)->group('picker')->getField("picker",true);
        }else{
            $picker = explode(",",$users);
        }
        //根据拣货人进行分组处理
        foreach ($picker as $k=>$v)
        {
            $map['is_delete'] = 0;
            $map['picker'] = $v;
            $orderSku        = $pickOrderDetail->where($map)
                ->field('sku, sum(qty) AS counts, goods_name, location,store_id as storeid,picker')
                ->order('location asc')
                ->group('sku')
                ->select();
            //切割数组100个分一组
            $data = [];
            $j = 0;
            $i = 0;
            foreach($orderSku as $key=>$value){
                $data[$j][$i] = $value;
                $i++;
                if($i == 40){
                    $j++;
                    $i = 0;
                }
            }
            $groupedSkuInfo[$v] = $data;
        }
        $pickOrderModel  = new PickOrderModel();
        $pickOrderMain   = $pickOrderModel->where(['ordersn' => $ordersn])
            ->field('ordersn, type, addtime, adduser, storeid, carrier_company,pick_status')
            ->find();
        if($pickOrderMain['pick_status'] == 0){
            $this->error("拣货单还未生成完毕");
        }
        $storeName       = (new EbayStoreModel())
            ->where(['id' => $pickOrderMain['storeid']])
            ->getField('store_name');
        $labelType       = (new CarrierGroupModel())
            ->where(['id' => $pickOrderMain['carrier_company']])
            ->getField('group_name');
        $this->assign('pickOrderMain', $pickOrderMain)
            ->assign('storeName', $storeName)
            ->assign('groupedSkuInfo', $groupedSkuInfo)
            ->assign('labelType', $labelType);
        $this->display();
    }

    /**
     * 打印完成自动转入待包装
     * @author Shawn
     * @date 2018/8/1
     */
    public function printAutoGocConfirmed(){
        $ordersn = I("ordersn");
        $pickOrderModel  = new PickOrderModel();
        $is_print   = $pickOrderModel->where(['ordersn' => $ordersn])->getField("isprint");
        if(!isset($is_print)){
            $this->ajaxReturn(['status'=>0,'msg'=>'未找到拣货单，转入待包装失败！']);
        }else{
            if($is_print < 2){
                //记录下日志，定位问题
                $pickOrderLog = new PickOrderLogModel();
                $setResult = $pickOrderModel->where(['ordersn' => $ordersn,'isprint'=>0])->setField('isprint',2);
                $msg = $setResult ? "成功" : "失败".$pickOrderModel->_sql();
                $note = '拣货单被打印了，拣货单状态为'.$is_print."自动转入待包装，操作".$msg;
                $pickOrderLog->addOneLog($ordersn,$note);
                if($setResult){
                    $this->ajaxReturn(['status'=>1,'msg'=>'该拣货单已经自动转入待包装！']);
                }else{
                    $this->ajaxReturn(['status'=>0,'msg'=>'该拣货单自动转入待包装失败！']);
                }
            }else{
                $this->ajaxReturn(['status'=>2,'msg'=>'该拣货单不需要转入待包装！']);
            }
        }
    }
}

