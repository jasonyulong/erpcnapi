<?php
namespace Package\Service;

use Common\Model\CarrierModel;
use Common\Model\ConfigsModel;
use Common\Model\EbayOrderExtModel;
use Common\Model\ErpOrderTypeModel;
use Common\Model\OrderModel;
use Mid\Model\EbayGlockModel;
use Package\Model\CarrierGroupItemModel;
use Package\Model\EbayGoodsModel;
use Package\Model\GoodsSaleDetailModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;

/**
 * Class CreatePickService
 * @package Package\Service
 * 捡货单 服务层
 */
class CreatePickService
{
    private $warehouse_token = 'F2FD11781A732C2E93847106A6513C7C';
//    private $url = 'http://192.168.1.53/t.php?s=/Order/';
    private $url = 'http://erp.spocoo.com/t.php?s=/Order/';

//    private $url = 'http://local.erp.com/t.php?s=/Order/';
    private $sort_count_same_sheet = '';  //品单件包裹与单品多件是否在同一张拣货单: 0=>否 1=>是
    private $sort_carrier_same_sheet = ''; //不同物流商的包裹进入同一张拣货单: 0=>否 1=>是
    private $sort_carrier_type = ''; //相同物流商不同邮寄方式进入同一张拣货单: 0=>否 1=>是
    private $sort_sheet_max_item = '';  //每张拣货单最大货品数的设置: TODO 已经废除
    private $sorted_order_to = ''; //拣货后订单状态转入到
    private $pick_order_status = ''; //可拣货状态
    private $pick_max_order1 = '';    //单品单货拣货单限制订单数
    private $pick_max_order2 = '';    //单品多货拣货单限制订单数
    private $pick_max_order3 = '';    //多品多货拣货单限制订单数
    private $moresku_floor = 100; // 多品的楼层
    private $OrderModel = null;
    private $OrderTypeModel = null;
    private $TopMenuModel = null;
    private $OrderslogModel = null;
    private $CarrierModel = null;
    private $PickOrderModel = null;
    private $PickOrderDetailModel = null;
    private $GoodsSaleDetailModel = null;
    private $EbayGoodsModel = null;
    private $EbayOrderExtModel = null;
    private $EbayLockData = array();
    private $errorArr = [
        '3'    => '转到【已生成拣货单】成功',
        '-5'   => '订单状态不是 1723(可打印),不允许生成拣货单',
        '-6'   => '拣货单已生成,不允许生成拣货单',
        '-100' => '方法已废除',
    ];

    function __construct() {
        $this->OrderModel           = new OrderModel();
        $this->OrderTypeModel       = new ErpOrderTypeModel();
        $this->TopMenuModel         = new TopMenuModel();
        $this->OrderslogModel       = new OrderslogModel();
        $this->CarrierModel         = new CarrierModel();
        $this->PickOrderModel       = new PickOrderModel();
        $this->PickOrderDetailModel = new PickOrderDetailModel();
        $this->GoodsSaleDetailModel = new GoodsSaleDetailModel();
        $this->EbayGoodsModel       = new EbayGoodsModel();
        $this->EbayOrderExtModel       = new EbayOrderExtModel();
        //查出所有锁定sku
        $lockModel = new EbayGlockModel();
        $lockData = $lockModel->field("sku,storeid")->select();
        if(!empty($lockData)){
            foreach ($lockData as $key=>$val){
                $k = strtoupper(trim($val['sku'])).'&'.trim($val['storeid']);
                $this->EbayLockData[$k] = 1;
            }
        }
    }

    /**
     *测试人员谭 2017-11-29 20:25:56
     *说明: 代码优化 减少嵌套
     */
    function setOrderHasBeenCreateds($ebay_ids) {
        $data['status']  = 1;
        $data['msg']     = '全部成功';
        $data['data']    = [];
        $data['err_msg'] = [];
        $Allcount        = count($ebay_ids);
        $ebay_ids        = explode(',', $ebay_ids);
        $errorArr        = $this->errorArr;
        foreach ($ebay_ids as $v) {
            /**
             *测试人员谭 2017-11-29 20:10:10
             *说明: 这里应该尽可能精简 setOrderCreated  减少 嵌套调用 $rs = $this->setOrderCreated($v);
             */
            // 精简 setOrderCreated  START ================================================
            $rs         = -6; // 默认转到【已生成拣货单】失败
            $ebayStatus = $this->OrderModel->where(['ebay_id' => $v])->getField('ebay_status');
            //如果订单状态不是 1723(可打印),不允许生成拣货单 朱诗萌 2017/11/6
            if (in_array($ebayStatus, [1723])) {
                $pickStatus = $this->OrderTypeModel->where(['ebay_id' => $v])->getField('pick_status');
                if ($pickStatus == 1) { // 就是没生成
                    //订单设置为已生成拣货单
                    //转到【已生成拣货单】
                    $mod_rs = $this->OrderTypeModel->where(['ebay_id' => $v])->setField('pick_status', 2);
                    if ($mod_rs !== false) {
                        $this->OrderModel->where(['ebay_id' => $v])->setField('ebay_status', 1745);
                        //订单状态修改,更新修改时间  王模刚  2017 12 6
                        $this->EbayOrderExtModel->saveToExt($v,1745);
                        $rs = 3;
                    }
                }
            } else {
                $rs = -5;
            }
            // 精简 setOrderCreated  END ================================================
            // 只记录失败者
            if ($rs < 0) {
                $data['data'][]      = $v;
                $data['err_msg'][$v] = $errorArr[$rs];;
            }
        }
        $Failure_count = count($data['data']);
        if ($Failure_count < $Allcount && $Failure_count > 0) {
            $data['msg'] = '部分成功';
        } elseif ($Failure_count == $Allcount) {
            $data['msg'] = '全部失败';
        }
//        $log = "ebay_id:" . $ebay_ids . "\n" . $rs . "\n" . date('H:i:s') . "\n\n";
//        writeFile($file, $log);
        return $data;
    }

    /**
     *测试人员谭 2017-11-18 11:49:41
     *说明:
     * 捡货单里面的 订单被打回 分三种情况：
     * 1. sku 不够，人为删除 （这个时候未确认 在1745 中）
     * 2. sku 不够，系统自动删除 （这个时候 确认的一瞬间，成功后转到 1724 from  1745 ）
     * 3. 包装的时候，1，2 填写的库存数量有误，或者 2- 3 之间， sku 丢了，或者订单被拦截，发生退回 （现在是 from 1724 to 1745 ）
     */
    function setOrderCancelCreated($ebay_id, $type = 1,$error_type = 0) {
        $file           = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.cancal.txt';
        $typeArr[1]     = '捡货流程';
        $typeArr[2]     = '包装流程';
        $typeArr[3]     = '本仓分拣流程';
        $typeArr[4]     = '跨仓集货流程';
        $errorArr       = $this->errorArr;
        $data['status'] = 1;
        $data['msg']    = '操作成功';
        /**
         *测试人员谭 2017-11-18 11:53:56
         *说明: TODO :重要修改： 如果订单被从捡货单上拦截，则必须要人工确认！  否则 ，无法 生成 捡货单!
         * TODO : 如果不这么干，会形成，生成捡货单-> 从捡货单删除 -> 生成捡货单 的死循环
         * TODO： 为什么以前没有这个问题： 仓库以前 线上erp 1723  会做日清！
         */
        $rs = $this->OrderTypeModel->where("ebay_id='$ebay_id'")->save(['pick_status' => 3,'error_type'=>$error_type]);
        if ($rs !== false) {
            //说明:  如果是包装的时候取消的 这个时候要把订单转到 等待打印
            $TopName     = $this->TopMenuModel->getMenuName();
            $Status      = $this->OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status')->find();
            $ebay_status = $Status['ebay_status'];
            $log         = '[' . date('Y-m-d H:i:s') . ']订单在' . $typeArr[$type] . ' 有问题,转到捡货单异常! 当时订单状态是:' . $TopName[$ebay_status];
            if (in_array($ebay_status, [1724, 1745])) {
                // 如果订单 现在是 等扫描，已捡货，则将订单改成 可打印
                // 修改 状态
                $save                = [];
                $save['ebay_status'] = 1723; // 再一次转到 等待打印
                $rr                  = $this->OrderModel->where("ebay_id=$ebay_id")->save($save);
                //订单状态修改,更新修改时间  王模刚  2017 12 6
                $this->EbayOrderExtModel->saveToExt($ebay_id,1723);
                $log .= '--且修改状态为:' . $TopName[$save['ebay_status']]; //---只有等扫描,等打印才会修改
            }
            $this->OrderslogModel->addordernote($ebay_id, $log, 3);
            $this->OrderModel->where("ebay_id=$ebay_id")->save(['ebay_noteb' => $log]);
            $rs = 3;   // 取消【已生成拣货单】 成功
        } else {
            $rs = -7; // 取消【已生成拣货单】 失败
        }
        if ($rs < 0) {  // 取消失败
            $data['status'] = 0;
            $data['msg']    = $errorArr[$rs];
        }
        if ($data['status']) {
            $log = "订单:" . $ebay_id . ',被取消了【生成拣货单】状态----' . $_SESSION['truename'] . "---" . date('H:i:s') . "\n";
            writeFile($file, $log);
        }
        return $data;
    }

    /**
     *测试人员谭 2017-05-31 17:59:43
     *说明: 订单数组
     */
    //吧订单转到等待扫描中去 这样才能出库 打包
    function setOrderToWriteScan($ebay_ids) {
        $file = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.toscan.txt';
        //writeFile($file,$log);
        $type                 = 'setOrderToWaitScan';
        $ebay_id_str          = implode(',', $ebay_ids);
        $access_token         = strtoupper(md5(sha1($type . $ebay_id_str . $this->warehouse_token)));
        $data['ebay_id']      = $ebay_id_str;
        $data['type']         = $type;
        $data['access_token'] = $access_token;

        $action               = 'OrderPackage/setOrderToWaitScan';
        $rs                   = $this->PackagePostCurl($this->url . $action, $data);
        $rs                   = json_decode($rs, true);
        //不修改erp下的订单状态，只改本地下的 erp状态
        $res = $this->setOrderToWaitScan($ebay_ids);
        $rs  = array();
        if ($res) {
            $rs['status'] = 1;
            $rs['msg']    = '全部成功';
        }
        if ($rs['status']) {
            $ebay_id_str = implode(',', $ebay_ids);
            $log         = "订单:" . $ebay_id_str . ',被转到【等待扫描】状态----' . $_SESSION['truename'] . "---" . date('H:i:s') . "\n";
            writeFile($file, $log);
        }
        return $rs;
    }

    private function PackagePostCurl($url, $data) {
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_TIMEOUT, 300);
        $response = curl_exec($connection);
        $info     = curl_getinfo($connection);
        if ($_GET['debug'] == 1) {
            debug($info);
            debug($data);
            debug($response);
            echo '----info---';
        }
        curl_close($connection);
        return $response;
    }

    /**
     *测试人员谭 2017-05-23 17:05:49
     *说明: 创建捡货单子
     */
    function CreatePickOrder($data, $allowCarriers) {
        $Config                        = new ConfigsModel();
        $configArr                     = $Config->find();
        $this->sort_count_same_sheet   = $configArr['sort_count_same_sheet'];
        $this->sort_carrier_same_sheet = $configArr['sort_carrier_same_sheet'];
        $this->sort_carrier_type       = $configArr['sort_carrier_type'];
        $this->sort_sheet_max_item     = $configArr['sort_sheet_max_item']; //已经废除 TODO
        $this->sorted_order_to         = $configArr['sorted_order_to'];
        $this->pick_order_status       = $configArr['pick_order_status'];
        $this->pick_max_order1         = $configArr['pick_max_order1']; // 单品单货 订单数量
        $this->pick_max_order2         = $configArr['pick_max_order2']; // 单品多货 订单数量
        $this->pick_max_order3         = $configArr['pick_max_order3'];// 多品多货  订单数量
        if (strstr($_SESSION['truename'], '测试人员')) {
//            dump($configArr);
        }
        $isallpage    = $data['isallpage'];
        $bill         = $data['bill'];
        $group_id     = $data['groupId'];
        $carrier      = $data['carrier'];
        $includeType1 = $data['includeType1'];
        $includeType2 = $data['includeType2'];
        $includeType3 = $data['includeType3'];
        $floor        = $data['floor']; // 楼
        if ($carrier != '') {
            $name     = $this->CarrierModel->where("id=$carrier")->field('name')->find();
            $carrier  = $name['name'];
            $group_id = ''; // 如果你选择了 运输方式 公司 就不要了
        }
        if ($isallpage) {
            $bill = '';
        }
        $map                       = [];
        $map['a.ebay_status']      = ['in', explode(',', $this->pick_order_status)];
        $map['a.ebay_warehouse']   = C('CURRENT_STORE_ID');
        $map['a.ebay_tracknumber'] = ['neq', ''];
        $map['a.ebay_combine']     = ['neq', 1];
        /**
         *测试人员谭 2017-11-17 15:26:52
         *说明: 各种时间
         */
        $DateFilter            = $this->builderTime();
        $map['a.ebay_addtime'] = ['between', [$DateFilter['erp_add_sint'], $DateFilter['erp_add_eint']]];
        $map['a.w_add_time']   = ['between', [$DateFilter['wms_add_sint'], $DateFilter['wms_add_eint']]];
        // 非ali的订单 2017-11-12 阿里巴巴 的 订单
        if ($group_id) { ////分组 是必须要的
            $carrierArr = $this->getAllCarrierByCompanyid($group_id);
            if (count($carrierArr) == 0) {
                echo '<div style="color:#911;">分组ID' . $group_id . ',找不到运输方式' . "</div>";
                return '';
            }
            $carrierArr = array_intersect($allowCarriers, $carrierArr);
            if (count($carrierArr) == 0) {
                echo '<div style="color:#911;">严重错误:分组ID ' . $group_id . "对应的运输方式都没有完成订单混打，无法分拣</div>";
                return '';
            }
            $map['a.ebay_carrier'] = ['in', $carrierArr];
        }
        if ($carrier) {
            if (!in_array($carrier, $allowCarriers)) {
                echo '<div style="color:#911;">' . $carrier . " 尚未完成面单混打,无法分拣" . "</div>";
                return '';
            }
            $map['a.ebay_carrier'] = $carrier;
            $CarrierGroupItem      = new CarrierGroupItemModel();
            $data['carrier']       = $carrier;
            $RR                    = $CarrierGroupItem->where($data)->field('group_id')->find();
            $group_id              = $RR['group_id'];  //分组 是必须要的
        }
        if ($bill) {
            $map['a.ebay_id'] = ['in', $bill];
        }
        $map['b.pick_status'] = 1; // 还没有生成 拣货单
        // 三种不同的 单子 各自生成拣货单子
        if (empty($includeType1) && empty($includeType2) && empty($includeType3)) {
            echo '<div style="color:#911;">严重错误:运输方式 您没有选择任何 订单单品多品类型' . "</div>";
            return '';
        }
        $map['b.pick_status'] = 1;

        $map['b.is_cross']    = 0;

        if($data['is_cross']==1){
            $map['b.is_cross'] = 1; // 跨仓单 在此扬名立万！
        }

        //单品单货
        if ($includeType1) {
            $map['b.type']  = 1;
            $map['b.floor'] = $floor;
            $this->CreatePickOrderByMap($map, $group_id);
        }
        //单品多货
        if ($includeType2) {
            $map['b.type']  = 2;
            $map['b.floor'] = $floor;
            $this->CreatePickOrderByMap($map, $group_id);
        }
        //多品
        if ($includeType3) {
            $map['b.type']  = 3;
            $map['b.floor'] = $floor;
            $this->CreatePickOrderByMap($map, $group_id);
        }
    }

    /**
     *测试人员谭 2017-05-23 22:49:52
     *说明: 先排序出订单 按照 库位 sku 的顺序 库位等于第一个
     * ============第一阶段=================
     * 查询：SELECT
     * 选中的 物流方式（物流公司id 终究要转为物流方式）
     * 仓库，
     * 可拣货状态
     * 没有生成拣货单
     *==============第二阶段===================
     * 查出所有订单的 头一个SKU 库位 排序
     * 循环查询 goods_sale_detial   和 ebay_onhandle_196 ,ebay_goods
     * 保留 拣货单 所需要的信息
     *==============第三阶段===================
     * 开始生成 拣货单子
     * 将第二阶段查出的 sku 明细填写到 【捡货明细表】pick_order_detail  和【捡货单表】pick_order
     */
    private function CreatePickOrderByMap($map, $carrier_company) {
        if (empty($carrier_company)) {
            echo '<div style="color:#911">找不到相关的运输方式分组ID</div>';
            return;
        }
        $field = 'a.ebay_id,a.ebay_addtime';
        $rs    = $this->OrderModel->alias('a')->join("inner join erp_order_type b using(ebay_id)")
            ->where($map)
            ->field($field)
            ->limit(20000)
            ->select();
        echo 'del检查前:' . count($rs) . "<br>";
        foreach ($rs as $k => $v) {
            /**
            *测试人员谭 2018-01-03 18:45:59
            *说明: 是否有正在处理的捡货单里面包含了 这个订单!
             * 原因: 有时候 一些特殊的情况 已经完成的  单里面满足 is_delete 和 is_normal  但是订单没有发
            */
            $pickDetail = $this->PickOrderDetailModel->alias('a')
                ->join('inner join pick_order b using(ordersn)')
                ->where([
                    'a.ebay_id' => $v['ebay_id'],
                    'a.is_delete' => 0,
                    'a.is_normal' =>1,
                    'b.isprint'   =>['in',[0,1,2]] // 加入b表的目的是 如果捡货单 不是正常的处理状态  还是当作 已经生成捡货单这个事情不存在
                ])
                ->field('a.id')
                ->find();

            if (!empty($pickDetail)) {
                unset($rs[$k]);
            }
        }
        echo 'del检查后:' . count($rs) . "<br>";
        // echo $OrderModel->_sql();die();
        $type_name  = '';
        $max_orders = 0;
        if ($map['b.type'] == 1) {
            $type_name  = "单品单件:";
            $max_orders = $this->pick_max_order1;
        }
        if ($map['b.type'] == 2) {
            $type_name  = "单品多件:";
            $max_orders = $this->pick_max_order2;
        }
        if ($map['b.type'] == 3) {
            $type_name  = "多品订单:";
            $max_orders = $this->pick_max_order3;
        }
        echo "\n\n" . '<!--' . $this->OrderModel->_sql() . "-->\n\n";
        echo '<div style="font-size:14px;"><br><br><br>  ' . $type_name . '按照条件找到 ' . count($rs) . " 个订单</div>";#();
        //以订单头一个sku 的 [库位.ebay_id] 做为索引 利于排序
        /*
         * 以 为值  循环步进，检验 sku 的总个数 $this->sort_sheet_max_item
         * [
         * 'sku'=$sku,
         * 'qty'=$qty,
         * 'ebay_id'=$ebay_id,
         * 'order_addtime'=$ebay_addtime,
         * 'location'=$location,
         * 'goods_name'=$goods_name,
         * 'pic'=$pic
         * ],[...]
         *
         * */
        $MainArr    = []; // 这个用来存储数据
        $IndexArr   = []; // 这个用来 存储索引  排序只排序索引，不排序数据
        $IndexCount = [];
        /**
         *测试人员谭 2017-05-24 10:38:59
         *说明: 拼装数据
         */
//        dump($rs);
        foreach ($rs as $List) {
            $ebay_id     = $List['ebay_id'];
            $OrderSKUArr = $this->getPickDetailInfo($ebay_id);
            $OrderSKU    = $OrderSKUArr['data'];
            $count       = $OrderSKUArr['count'];
            if (false == $OrderSKU) {
                echo '<div style="color:#911">-订单：' . $ebay_id . ' 找不到sku信息 </div>';
                continue;
            }
            $index              = $OrderSKU[0]['location'] . '_' . $ebay_id;
            $IndexArr[$index]   = $ebay_id;
            $MainArr[$index]    = $OrderSKU; // 这个
            $IndexCount[$index] = $count; // 这个订单有几个sku 总数量
        }
        unset($rs);
        unset($OrderSKU);
        ksort($IndexArr);
        //==============第三阶段==============
        $file                             = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.info.txt';
        $fileErr                          = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.error.txt';
        $adduser                          = $_SESSION['truename'];
        $storeid                          = C('CURRENT_STORE_ID');
        $type                             = $map['b.type'];
        $BasePackOrder['is_cross']        = $map['b.is_cross']; // 注意这里 a.
        $BasePackOrder['adduser']         = $adduser;
        $BasePackOrder['storeid']         = $storeid;
        $BasePackOrder['type']            = $type;
        $BasePackOrder['addtime']         = 0;
        $BasePackOrder['carrier_company'] = $carrier_company;
        $packageSKUcount                  = 0; // 这个废除掉
        $PickOrderMain                    = [];
        $pickOrder_item                   = 0; // 捡货单的  index
        $max_orders_temp                  = 0; // 每一个 单子的 订单量累加
        //库位+ebay_id 索引的 sku 信息数组
        foreach ($IndexArr as $key => $val) {
            if ($max_orders_temp == $max_orders) { // 达到最大值的时候 要清空 累计值
                $max_orders_temp = 1; //
                $pickOrder_item  = $pickOrder_item + 1;// 开始下一张单子
            } else {
                $max_orders_temp = $max_orders_temp + 1;
            }
            $PickOrderMain[$pickOrder_item][] = $key;
        }
        //debug($PickOrderMain);debug(count($MainArr));die();
        //==============第四阶段==============
        $log = '';
        //$PickOrderMain 以自然数 为索引,以[location_ebay_id,...] 为 value 的 数组
        //每一个 自然数 索引 代表一个 预定义的拣货单!
        foreach ($PickOrderMain as $key => $PickOrderKeyArr) {
            $BasePackOrder['addtime'] = time();
            $BasePackOrderDetail      = [];
            echo '<div>第' . ($key + 1) . '单：开始创建</div>';
            $ebay_ids = '';
            foreach ($PickOrderKeyArr as $Location_ebayid) {
                $ebay_ids .= $IndexArr[$Location_ebayid] . ',';
            }
            // 必须要改成批量请求 这里就是一张捡货单子
            $HasBeenRs = $this->setOrderHasBeenCreateds($ebay_ids);
            if (!$HasBeenRs['status']) {
                echo '<div style="color:#911">本单全部标记失败:' . $HasBeenRs['msg'] . '</div>';
                $log .= '本单全部标记失败:' . $HasBeenRs['msg'] . date('H:i:s') . "\n";
                continue;
            }
            $FailureOrder = $HasBeenRs['data'];
            $FailureMsg   = $HasBeenRs['err_msg'];
            foreach ($PickOrderKeyArr as $Location_ebayid) {
                $ebay_id = $IndexArr[$Location_ebayid];
                if (in_array($ebay_id, $FailureOrder)) {
                    echo '<div style="color:#911">' . $ebay_id . ',标为已经生成拣货失败:' . $FailureMsg[$ebay_id] . '</div>';
                    $log .= $ebay_id . ',本捡货单全部标记失败:' . $HasBeenRs['msg'] . date('H:i:s') . "\n";
                    continue;
                }
                // $skudetail 多频的时候
//                dump($MainArr);
                foreach ($MainArr[$Location_ebayid] as $skudetail) {
                    $BasePackOrderDetail[] = $skudetail;
                }
            }
            if (count($BasePackOrderDetail) == 0) {
                echo '<div style="color:#911">拣货单子信息有误!</div>';
                continue;
            }
            //搞来一个 拣货单号
            $ordersn = $this->PickOrderModel->CreateAordersn();
            if ($ordersn == false) {
                $ordersn = $this->PickOrderModel->CreateAordersn();
            }
            if ($ordersn == false) {
                $ordersn = $this->PickOrderModel->CreateAordersn();
            }
            if ($ordersn == false) {
                echo '<div style="color:#911">第' . ($key + 1) . '单 严重错误!创建失败!</div>';
                writeFile($fileErr, "创建主单失败:\n" . print_r($BasePackOrderDetail, true) . "\n\n\n\n");
                continue;
            }
            $this->PickOrderModel->where("ordersn='$ordersn'")->save($BasePackOrder);
            //给子单 赋值咯
            for ($i = 0; $i < count($BasePackOrderDetail); $i++) {
                $BasePackOrderDetail[$i]['ordersn'] = $ordersn;
            }
            // debug($BasePackOrder);
            //  debug($BasePackOrderDetail);
            // 子单信息
            $rs = $this->PickOrderDetailModel->addAll($BasePackOrderDetail);
            if (!$rs) {
                echo '<div style="color:#911">第' . ($key + 1) . '单：创建失败</div>';
                writeFile($fileErr, "创建子单失败:\n" . print_r($BasePackOrderDetail, true) . "\n\n\n\n");
                // 主表信息设置为 删除
                $this->PickOrderModel->where("ordersn='$ordersn'")->save(['isprint' => 100]);
            } else {

                echo '<div style="color:#191">第' . ($key + 1) . '单：创建成功</div>';
            }
            unset($BasePackOrderDetail);
        }
        $log .= "\n\n\n";
        unset($PickOrderMain);
        unset($IndexArr);
        writeFile($file, $log);
    }

    /**
     *测试人员谭 2017-05-24 10:44:27
     *说明: 捡货单创建的时候 需要的明细
     */
    private function getPickDetailInfo($ebay_id) {
        $file = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.info.txt';
        $Arr  = $this->GoodsSaleDetailModel->where("ebay_id='$ebay_id'")->field('sku,qty,erp_addtime,storeid')->select();
        if (count($Arr) == 0) {
            $log = "订单{$ebay_id}错误：缓存表没有找到订单的sku明细!--" . $_SESSION['truename'] . '--' . date('H:i:s') . "\n";
            writeFile($file, $log);
            return ['data' => false, 'count' => false];
        }
        $Rs    = [];
        $count = 0;
        foreach ($Arr as $List) {
            $sku     = $List['sku'];
            $storeid = $List['storeid'];
            //判断sku是否在盘点锁定
            $specKey = strtoupper(trim($sku)).'&'.trim($storeid);
            if(!empty($this->EbayLockData) && array_key_exists($specKey,$this->EbayLockData)){
                $log = "订单{$ebay_id}错误：sku:{$sku},storeid:{$storeid}处于盘点锁定 " . '--' . date('H:i:s') . "\n";
                writeFile($file, $log);
                return ['data' => false, 'count' => false];
            }
            $GoodsInfo = $this->EbayGoodsModel->getGoodsInfo($sku,$storeid); // 如果是其他仓的话，那其他仓的 库位也要
            $local     = trim($GoodsInfo['goods_location']);
            if ($local == '') {
                $local = ' ';
            }
            $Rs[] = array(
                'sku'           => strtoupper($List['sku']),
                'qty'           => $List['qty'],
                'ebay_id'       => $ebay_id,
                'ordersn'       => '',
                'order_addtime' => $List['erp_addtime'],
                'location'      => $local,
                'pic'           => $GoodsInfo['goods_pic'],
                'goods_name'    => $GoodsInfo['goods_name'],
                'store_id'      => $storeid
            );
            $count += $List['qty'];
        }
        /**
         *测试人员谭 2017-05-24 13:54:08
         *说明: data: sku 数据
         * count 这个订单的 sku 总个数
         */
        return ['data' => $Rs, 'count' => $count];
    }

    /**
     *测试人员谭 2017-06-03 17:22:17
     *说明: 这里的 公司id 应该是 组ID
     */
    public function getAllCarrierByCompanyid($group_id) {
        $CarrierGroupItem = new CarrierGroupItemModel('', '', C('DB_CONFIG2'));
        $map['group_id']  = $group_id;
        $rs               = $CarrierGroupItem->where($map)->field('carrier as name')->select();
        $arr              = [];
        foreach ($rs as $List) {
            $arr[] = $List['name'];
        }
        return $arr;
    }

    /**
     * 将订单设置为已创建拣货单的状态
     * @author Simon 2017/11/6
     */
    function setOrderCreated($ebay_id) {
        $ebayStatus = $this->OrderModel->where(['ebay_id' => $ebay_id])->getField('ebay_status');
        //如果订单状态不是 1723(可打印),不允许生成拣货单 朱诗萌 2017/11/6
        if (!in_array($ebayStatus, [1723])) {
            return -5;
        }
        $pickStatus = $this->OrderTypeModel->where(['ebay_id' => $ebay_id])->getField('pick_status');
        // 如果拣货单已生成(pickStatus == 2) 转到【已生成拣货单】失败
        if ($pickStatus != 1) {
            return -6;
        }
        //订单设置为已生成拣货单
        $rs = $this->OrderTypeModel->where(['ebay_id' => $ebay_id])->setField('pick_status', 2);
        if ($rs !== false) {
            //将订单设置为等待打印状态
            $this->OrderModel->where(['ebay_id' => $ebay_id])->setField('ebay_status', 1745);
            //订单状态修改,更新修改时间  王模刚  2017 12 6
            $this->EbayOrderExtModel->saveToExt($ebay_id,1745);
            //转到【已生成拣货单】成功
            return 3;
        }
        // 转到【已生成拣货单】失败
        return -6;
    }

    //仓库在创建包裹的时候 将订单设置为已经 创建了 捡货单
    function setOrderHasBeenCreated($ebay_id) {
        $errorArr       = $this->errorArr;
        $data['status'] = 1;
        $data['msg']    = '操作成功';
        $rs             = $this->setOrderCreated($ebay_id);
        if ($rs < 0) {
            $data['status'] = 0;
            $data['msg']    = $errorArr[$rs];
        }
        return $data;
    }

    // 取消订单是已经 生成捡货单的状态
    /**
     *测试人员谭 2017-11-18 12:07:30
     *说明:  * TODO 废除 by smith
     */
    function setOrderCancelCreatedAction($ebay_id) {
        $data['status'] = 0;
        $data['msg']    = '功能已经废除';
        return $data;
    }

    /**
     *测试人员谭 2017-05-23 11:51:11
     *说明: 取消 【已生成拣货单】 状态
     * TODO 废除 by smith
     */
    function setOrderCancelCreatedData($ebay_id) {
        return -100;
    }

    /**
     * 将订单状态设置为等待扫描
     * @author Simon 2017/11/4
     */
    function setOrderWaitScan($ebay_ids) {
        //允许转入等待扫描的订单状态 [等待打印,等待扫描] 朱诗萌 2017/11/4
        $allowOrderStatus = [1745, 1724];
        $Error_Order      = '';
        foreach ($ebay_ids as $ebay_id) {
            $Status      = $this->OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status')->find();
            $ebay_status = $Status['ebay_status'];
            if ($ebay_id == '') {
                continue;
            }
            if (!in_array($ebay_status, $allowOrderStatus)) {
                $Error_Order .= $ebay_id . ',';
                continue;
            }
            $this->OrderModel->where(['ebay_id' => ['eq', $ebay_id]])->setField('ebay_status', 1724);
            //订单状态修改,更新修改时间  王模刚  2017 12 6
            $this->EbayOrderExtModel->saveToExt($ebay_id,1724);
        }
        return $Error_Order;
    }

    // 把订单转到等待扫描中去 这样才能出库 打包
    function setOrderToWaitScan($ebay_ids) {
        $data['status']  = 1;
        $data['msg']     = '全部成功';
        $data['data']    = [];
        $data['err_msg'] = [];
        $ErrorEbayID     = $this->setOrderWaitScan($ebay_ids);
        if ($ErrorEbayID == '') {
            return $data;
        }
        $data['status'] = 0;
        $data['msg']    = '失败:以下订单不能转到等待扫描，可能已经被修改，您可能需要手动删除拣货单中的订单【 ' . $ErrorEbayID . " 】";
        return $data;
    }

    function builderTime() {
        if ($_REQUEST['erp_addtime_start']) {
            $erp_addtime_start = $_REQUEST['erp_addtime_start'];
            $erp_addtime_end   = $_REQUEST['erp_addtime_end'];
            $wms_addtime_start = $_REQUEST['wms_addtime_start'];
            $wms_addtime_end   = $_REQUEST['wms_addtime_end'];
        } else {
            $erp_addtime_start = date('Y-m-d H:i:s', strtotime("-2 months"));
            $erp_addtime_end   = date('Y-m-d H:i:s');
            $wms_addtime_start = date('Y-m-d H:i:s', strtotime("-2 months"));
            $wms_addtime_end   = date('Y-m-d H:i:s');
        }
        $erp_addtime_start_int = strtotime($erp_addtime_start);
        $erp_addtime_end_int   = strtotime($erp_addtime_end);
        $wms_addtime_start_int = $wms_addtime_start;
        $wms_addtime_end_int   = $wms_addtime_end;
        return [
            'erp_add_s'    => $erp_addtime_start,
            'erp_add_e'    => $erp_addtime_end,
            'wms_add_s'    => $wms_addtime_start,
            'wms_add_e'    => $wms_addtime_end,
            'erp_add_sint' => $erp_addtime_start_int,
            'erp_add_eint' => $erp_addtime_end_int,
            'wms_add_sint' => $wms_addtime_start_int,
            'wms_add_eint' => $wms_addtime_end_int
        ];
    }
}
