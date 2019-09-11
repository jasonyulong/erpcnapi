<?php
namespace Package\Service;

use Common\Model\ErpEbayGoodsModel;
use Common\Model\OrderModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\BeltLayerModel;
use Package\Model\EbayGoodsModel;
use Package\Model\EbayOnHandleModel;
use Package\Model\OrderPackageModel;
use Package\Model\OrderslogModel;
use Package\Model\PackingMaterialModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;
use Think\Cache\Driver\Redis;

/**
 * Class CreatePickService
 * @package Package\Service
 * 捡货单 2下次 分拣服务层
 */
class MakeBaleService
{
    public $debugmod = 1;
    public $allowScanStstus = 1724;
    public $canScaningStatus=[1723,1724,1745];

    function GetWorkingOrdersn() {
        $baleuser       = $_SESSION['truename'];
        $PickOrderModel = new PickOrderModel();
        // $PickOrderDetailModel = new PickOrderDetailModel('','',C('DB_CONFIG2'));
        $map['isprint']  = 2; // 没完结
        $map['baleuser'] = $baleuser; // 打包人
        $map['is_work']  = 1;//包装作业中
        $map['type']  = 3;//多品多货
        $RR              = $PickOrderModel->where($map)->field('id,ordersn')->find();
        //echo $PickOrderModel->_sql();
        $workOrdersn = '';
        if ($RR) {
            $workOrdersn = $RR['ordersn'];
        }
        return $workOrdersn;
    }

    /**
     *测试人员谭 2017-06-06 10:58:13
     *说明: 所有包装材料缓存一下
     */
    function CachePackingmaterial() {
        $Arr = S('ebay_packingmaterial');
        if (!$Arr) {
            $Packmater = new PackingMaterialModel();
            $RR        = $Packmater->field('id,model,notes')->select();
            $Arr       = [];
            foreach ($RR as $List) {
                $Arr[$List['id']] = $List;
            }
            S('ebay_packingmaterial', $Arr, 1800);
        }
        return $Arr;
    }

    /**
     *测试人员谭 2017-06-03 21:04:02
     *说明: 单品 单货 的玩意
     * 和单品多货的
     */
    function GetOrderSKUBysku($ordersn, $sku) {
        $sku                  = strtoupper(trim($sku));
        $PickOrderDetailModel = new PickOrderDetailModel();
        $map['is_delete']     = 0;// 没有被删除的
        $map['ordersn']       = $ordersn;
        $map['sku']           = $sku;
        $map['is_baled']      = 0; // 没有被包装的
        $map['isjump']        = 0; // 没有被跳过的
        $map['scaning']       = 0; // 没有扫描的
        /**
         *测试人员谭 2017-07-05 10:58:25
         *说明: 这里使用 order_addtime 排序，因为捡货确认的时候 还是可能错，在错误的情况下，
         * 打包小妹在打包的时候  优先考虑 库存分配给老订单。
         */
        $RR = $PickOrderDetailModel->where($map)
            ->field('ebay_id,sku,qty,goods_name,pic,id')
            ->order('order_addtime asc')->limit(1)->select();
        //如果没有找到未扫描的订单，开始查找扫描过但未完成包装的订单
        if(empty($RR)){
            $map['scaning'] = 1;
            $map['scan_user'] = session('truename');
            $RR = $PickOrderDetailModel->where($map)
                ->field('ebay_id,sku,qty,goods_name,pic,id')
                ->order('order_addtime asc')->limit(1)->select();
        }
        if (count($RR) == 0) {
            return ['status' => 0, 'msg' => "拣货单{$ordersn}中 SKU：{$sku} 不存在 或已经打印包装了"];
        }
        $handleModel     = new \Task\Model\EbayHandleModel();
        $goodsModel      = new \Common\Model\ErpEbayGoodsModel();
        $Packingmaterial = $this->CachePackingmaterial();
        //debug($Packingmaterial);
        $SKUARR    = [];
        $print_mod = 1;
        $currStoreId    = C("CURRENT_STORE_ID");
        foreach ($RR as $List) {
            // 获取包材
//            $picks=$OnHandleModel->alias('a')->join("ebay_goods b using(goods_sn)")
//                ->where("a.goods_sn='$sku'")->field('a.packingmaterial,b.accessories,b.isnopackaging')->find();
//            $picksid = $picks['packingmaterial'];
//            $accessories = $picks['accessories'];
            //获取包装方式 朱诗萌 2017/11/4
            //获取包材id
            $packageMaterialId = $handleModel->table($handleModel->getPartitionTableName(['store_id' => $currStoreId]))
                ->where(['goods_sn' => $sku])->getField('packingmaterial');
            $goodsInfo         = $goodsModel->where(['goods_sn' => $sku])->find();
            $picksid           = $packageMaterialId;
            $accessories       = $goodsInfo['accessories'];
            if ($picksid) {
                $List['model']       = $Packingmaterial[$picksid]['model'];
                $List['modelid']     = $picksid;
                $List['model_note']  = $Packingmaterial[$picksid]['notes'];
                $List['accessories'] = $this->fomartAccessories($accessories);
//                $List['isnopackaging']=$picks['isnopackaging']==1?'去包装':'';
                $List['isnopackaging'] = $goodsInfo['isnopackaging'] == 1 ? '去包装' : '';
            }
            $SKUARR[$sku] = $List;
        }
        $OrderModel = new OrderModel();
        $ebay_id    = $RR[0]['ebay_id'];
        $TopMenu    = $this->GetTopMenu();
        // 这里要查询一下 神奇的 订单类型====
        $Orders           = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        $ebay_status      = $Orders['ebay_status'];
        $ebay_ordersn     = $Orders['ebay_ordersn'];
        $ebay_carrier     = trim($Orders['ebay_carrier']);
        $allowCarrier     = load_config('newerp/Application/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2;
        }
//        if ($ebay_status != $this->allowScanStstus) {
//            return [
//                'status' => 0,
//                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id}在 【" . $TopMenu[$ebay_status] . "】中，禁止出库"
//            ];
//        }
        $interceptModel = new \Api\Model\OrderInterceptRecordModel();
        $interceptInfo  = $interceptModel->where(['ebay_id' => $ebay_id, 'status' => 0])->find();
        if (!empty($interceptInfo)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id} 已被拦截，禁止出库"
            ];
        }
        /**
         *测试人员谭 2017-06-02 21:44:11
         *说明: 有没有被扫描过
         */
        $APIChecksku = new ApiCheckskuModel();
        $ss          = $APIChecksku->where("ebay_id='$ebay_id'")->field('id')->find();
        if ($ss) {
            //当我扫描一个 已经扫描过的 包裹的时候 要考虑 这家伙的捡货单 是不是已经完成了
            /**
             *测试人员谭 2017-06-06 15:09:05
             *说明: 来考虑一下
             */
            $PickOrder = new PickOrderModel();
            $msg       = "拣货单{$ordersn}中 订单号{$ebay_id} 已经扫描过请联系您的主管!";
            $Rs        = $PickOrder->where("ordersn='$ordersn'")->field('isprint,is_work')->find();
            $isprint   = $Rs['isprint'];
            if ($isprint == 3) {
                $msg = "拣货单{$ordersn} 已经完成了!还扫个啥";
            } /*elseif ($isprint != 2) {
                $msg = "拣货单{$ordersn} 状态异常!不允许扫描";
            }*/
            return [
                'status' => 0,
                'msg'    => $msg
            ];
        }
        /**
         *测试人员谭 2017-05-28 18:59:37
         *说明: 验证一下  sku 有没有被修改
         */
        /*        $SaleDetailModel=new GoodsSaleDetailModel();
                $skuArr=$SaleDetailModel->where("ebay_id='$ebay_id'")->field('sku,qty')->select();*/
        $skuArr    = [];
        $skuArrTmp = $OrderModel->OrderResolve('', $ebay_ordersn);
        foreach ($skuArrTmp as $checksku => $Arrs) {
            $skuArr[] = ['sku' => strtoupper(trim($checksku)), 'qty' => $Arrs[0]];
        }
        /**
         *测试人员谭 2017-07-24 19:12:58
         *说明: 这个地方非常的坑爹  ---END
         */
        $checkSKU = strtoupper(trim($skuArr[0]['sku']));
        $checkQty = $skuArr[0]['qty'];
        if (count($skuArr) > 1) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},变成了多品订单，捡货之后发生数量修改!禁止出库! "
            ];
        }
        if ($SKUARR[$checkSKU]['qty'] != $checkQty) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},SKU,{$sku} 捡货之后发生数量修改!禁止出库! "
            ];
        }
        if (!array_key_exists($checkSKU, $SKUARR)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},SKU,{$sku} 捡货之后发生修改!禁止出库! "
            ];
        }
        /**
         * 开始验证是否有人正在操作相同订单
         * @author Shawn
         * @date 2018-05-28
         */
        $currStoreId    = C("CURRENT_STORE_ID");
        $key = "is_working_pack_{$currStoreId}:".$ordersn.$ebay_id;
        $redis = new Redis();
        $workData = $redis->get($key);
        $userName = session('truename');
        if($workData){
            if($workData['username'] != $userName){
                return [
                    'status' => 0,
                    'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},SKU,{$sku} 扫描失败，请重新扫描! "
                ];
            }
        }else{
            $redis->set($key,['username'=>$userName,'value'=>1],86400);
            $PickOrderDetailModel->where(['id'=>$RR[0]['id']])->save(["scaning"=>1,'scan_user'=>$userName]);
        }

        //TODO 要改一下 这里 订单号 是写死的
        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&ttt=" . time();
        return ['data' => $SKUARR, 'len' => count($SKUARR), 'status' => 1, 'ebay_id' => $ebay_id, 'print_mod' => $print_mod, 'print_url' => $print_url, 'sku' => $sku, 'ebay_carrier' => $ebay_carrier];
    }


    /**
    *测试人员谭 2018-08-03 00:09:22
    *说明:新版本 不要拣货单
    */
    //TODO: 新的  多品多见 专用，根据sku 系列找到订单找到最老的订单咯
    //获取一个订单 多品多货的
    public function GetOrderBySkuStrings($SKUARR_SCAN,$lableType,$deep=false){

        ksort($SKUARR_SCAN);
        $str = json_encode($SKUARR_SCAN);
        //$PickOrderDetailModel = new PickOrderDetailModel('','',C('DB_CONFIG2'));
        $PickOrderDetailSkustrModel = new PickOrderDetailSkustrModel();

        $PickOrder=new PickOrderModel();
        $PickOrderDetail=new PickOrderDetailModel();

        // 先找到没有完结的 多品多货-----------------
        $map=[];
        //TODO:===== 可能以后会出现跨仓
        $map['type']=3;
        $map['isprint']=2;
        //判断10*10还是10*15的拣货单
        $map['carrier_company'] = $lableType;


        $lablemsg= '物流渠道分组id：'.$lableType;

        $ordersns=$PickOrder->where($map)->getField('ordersn',true);

        if(empty($ordersns)){
            return [
                'status'  => 0,
                'msg'     => '当前没有正在【待包装】多品多件('.$lablemsg.')拣货单!!!',
                'ebay_id' => 0
            ];
        }


        /**
        *测试人员谭 2018-08-03 00:20:58
        *说明:开始第一个阶段找订单 粗略且不管订单状态
        */
        $map=[];
        $map['ordersn']=['in',$ordersns]; // 没有被扫
        $map['skustr']=$str; // 没有被扫
        $map['scaning']=0; // 没有被扫
        /*
         * 这里可能会产生性能问题
         * */
        //

        //print_r($map);
        $Orders=$PickOrderDetailSkustrModel->where($map)->field('ebay_id,ordersn')->select();

        if(empty($Orders)){
            return [
                'status'  => 0,
                'msg'     => '当前SKU组合--多品多件('.$lablemsg.')找不到订单....',
                'ebay_id' => 0
            ];
        }

        // 注意 查到这里的时候 肯定可以排除一大堆的 拣货单了
        $ebay_ids=[];

        $ordersns=[];

        foreach($Orders as $list){
            $ordersns[$list['ordersn']]=$list['ordersn'];
            $ebay_ids[$list['ebay_id']]=$list['ebay_id'];
        }
        $ordersns=array_keys($ordersns);
        $ebay_ids=array_keys($ebay_ids);



        //第二步 拿这些订单号 去找 pickorderdetail
        $map=[];
        $map['ordersn'] = ['in', $ordersns];
        $map['ebay_id'] = ['in', $ebay_ids];
        $map['is_delete'] = 0;
        $map['is_baled']  = 0;
        $map['isjump']    = 0;


        $Rs=$PickOrderDetail->where($map)->field('ebay_id,id,ordersn')->order('ebay_id')->find();

        if(empty($Rs)){
            return [
                'status'  => 0,
                'msg'     => '当前SKU组合找不到订单..请换一个篮子扫描',
                'ebay_id' => 0
            ];
        }


       // 开始尝试锁定我得订单

        $scanuser=$_SESSION['truename'];
        $ebay_id = $Rs['ebay_id'];
        $ordersn = $Rs['ordersn'];

        $map=[];
        $map['scaning']=0;
        $map['ordersn']=$ordersn;
        $map['ebay_id']=$ebay_id;

        // ordersn + ebay_id 是唯一索引，可以放心大胆食用!
        $rs=$PickOrderDetailSkustrModel->where($map)
            ->limit(1)
            ->save(['scaning'=>1,'scan_user'=>$scanuser,'scan_time'=>time()]);

        if($rs!==1){

            if($deep==false){
                return $this->GetOrderBySkuStrings($SKUARR_SCAN,$lableType,1);
            }

            return [
                'status'  => 0,
                'msg'     => '当前SKU组合查找订单失败..请重试',
                'ebay_id' => 0
            ];

        }

        //锁定之后 马上开始干 拣货单的包装员啊

        $where=[];
        $where['ordersn']=$ordersn;
        $where['ebay_id']=$ebay_id;
        $PickOrderDetail->where($where)->save(['scaning'=>1,'scan_user'=>$scanuser,'scan_time'=>time()]);


        /**
        *测试人员谭 2018-08-03 15:46:21
        *说明: 开始验证订单了啊
        */
        $OrderModel = new OrderModel();
        $TopMenu    = $this->GetTopMenu();
        $LayerModel=new BeltLayerModel();  // 传送带什么层

        // 这里要查询一下 神奇的 订单类型====
        $Orders       = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        $ebay_status  = $Orders['ebay_status'];
        $ebay_carrier = trim($Orders['ebay_carrier']);
        if (!in_array($ebay_status,$this->canScaningStatus)) {
            return [
                'status'  => 0,
                'msg'     => "拣货单{$ordersn}中 订单号{$ebay_id}在 【" . $TopMenu[$ebay_status] . "】中，禁止出库",
                'ebay_id' => $ebay_id
            ];
        }
        $allowCarrier     = load_config('newerp/Application/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        $print_mod        = 1;
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2; // 打印类型  10*10 还是 15*10
        }

        $APIChecksku = new ApiCheckskuModel();
        $ss          = $APIChecksku->where("ebay_id='$ebay_id'")->field('id')->find();
        if($ss){

            //这里要把这个玩意 delete 掉
            $Orderslog=new OrderslogModel();

            $log="包装人员 检测到订单已经被其他人包装了，本次从拣货单中移除";
            $PickOrderDetail->where($where)->save(['is_delete'=>1]);
            $Orderslog->addordernote($ebay_id,$log);

            return [
                'status'  => 0,
                'msg'     => '订单已经被其他小伙伴包了!!',
                'ebay_id' => $ebay_id
            ];

        }

        // 获得楼层============
        $layer=$LayerModel->getLayerByCarrier($ebay_carrier);


        //TODO 要改一下 这里 订单号 是写死的
        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&ttt=" . time();


        return [
                'status' => 1,
                'data' => $SKUARR_SCAN,
                'len' => count($SKUARR_SCAN),
                'ebay_id' => $ebay_id,
                'print_mod' => $print_mod,
                'print_url' => $print_url,
                'layer' => $layer,
                'ebay_carrier' => $ebay_carrier,
                'ordersn' => $ordersn,
        ];



    }


    /**
     *测试人员谭 2017-05-31 10:31:06
     *说明: 多品类性的时候
     * 改成多品多见 专用
     */
    function GetSKUinfoByBoxid($ordersn, $Boxid) {
        // $PickOrderModel       = new PickOrderModel('','',C('DB_CONFIG2'));
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $OnHandleModel        = new EbayOnHandleModel();
        // 先找到订单ID
        $RR = $PickOrderDetailModel->where("ordersn='$ordersn' and combineid='$Boxid'")
            ->field('ebay_id,sku,qty,goods_name,pic')->select();
        //包材信息的缓存
        $Packingmaterial = $this->CachePackingmaterial();
        $SKUARR          = [];
        $print_mod       = 1;
        foreach ($RR as $List) {
            $sku = strtoupper(trim($List['sku']));
            //包装方式
            $picks       = $OnHandleModel->alias('a')->join("ebay_goods b using(goods_sn)")
                ->where("a.goods_sn='$sku'")->field('a.packingmaterial,b.accessories,b.isnopackaging')->find();
            $picksid     = $picks['packingmaterial'];
            $accessories = $picks['accessories'];
            if ($picksid) {
                $List['model']         = $Packingmaterial[$picksid]['model'];
                $List['modelid']       = $picksid;
                $List['model_note']    = $Packingmaterial[$picksid]['notes'];
                $List['accessories']   = $this->fomartAccessories($accessories);
                $List['isnopackaging'] = $picks['isnopackaging'] == 1 ? '去包装' : '';
            }
            $SKUARR[$sku] = $List;
        }
        if (!$RR) {
            return ['status' => 0, 'msg' => "拣货单{$ordersn}中 格子号{$Boxid} 不存在"];
        }
        $OrderModel = new OrderModel();
        $ebay_id    = $RR[0]['ebay_id'];
        $TopMenu    = $this->GetTopMenu();
        // 这里要查询一下 神奇的 订单类型====
        $Orders           = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        $ebay_status      = $Orders['ebay_status'];
        $ebay_ordersn     = $Orders['ebay_ordersn'];
        $ebay_carrier     = trim($Orders['ebay_carrier']);
        $allowCarrier     = load_config('newerp/Application/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2;
        }
        if ($ebay_status != $this->allowScanStstus) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id}在 【" . $TopMenu[$ebay_status] . "】中，禁止出库"
            ];
        }
        /**
         *测试人员谭 2017-06-02 21:44:11
         *说明: 有没有被扫描过
         */
        $APIChecksku = new ApiCheckskuModel('', '', C('DB_CONFIG2'));
        $ss          = $APIChecksku->where("ebay_id='$ebay_id'")->field('id')->find();
        if ($ss) {
            //当我扫描一个 已经扫描过的 包裹的时候 要考虑 这家伙的捡货单 是不是已经完成了
            /**
             *测试人员谭 2017-06-06 15:09:05
             *说明: 来考虑一下
             */
            $PickOrder = new PickOrderModel('', '', C('DB_CONFIG2'));
            $msg       = "拣货单{$ordersn}中 订单号{$ebay_id}在 已经扫描过请联系您的主管!";
            $Rs        = $PickOrder->where("ordersn='$ordersn'")->field('isprint,is_work')->find();
            $isprint   = $Rs['isprint'];
            if ($isprint == 3) {
                $msg = "拣货单{$ordersn} 已经完成了!还扫个啥";
            } elseif ($isprint != 2) {
                $msg = "拣货单{$ordersn} 状态异常!不允许扫描";
            }
            return [
                'status' => 0,
                'msg'    => $msg
            ];
        }
        /**
         *测试人员谭 2017-05-28 18:59:37
         *说明: 验证一下  sku 有没有被修改
         */
        /*        $SaleDetailModel=new GoodsSaleDetailModel();

                $skuArr=$SaleDetailModel->where("ebay_id='$ebay_id'")->field('sku,qty')->select();*/
        $skuArr    = [];
        $skuArrTmp = $OrderModel->OrderResolve('', $ebay_ordersn);
        foreach ($skuArrTmp as $checksku => $Arrs) {
            $skuArr[] = ['sku' => strtoupper(trim($checksku)), 'qty' => $Arrs[0]];
        }
        //debug($skuArr);
        //debug($SKUARR);
        foreach ($skuArr as $List) {
            $sku = strtoupper(trim($List['sku']));
            $qty = $List['qty'];
            if (!array_key_exists($sku, $SKUARR)) {
                return [
                    'status' => 0,
                    'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},SKU,{$sku} 捡货之后发生修改!禁止出库! "
                ];
            }
            if ($qty != $SKUARR[$sku]['qty']) {
                return [
                    'status' => 0,
                    'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id},SKU,{$sku} 捡货之后发生数量修改!禁止出库! "
                ];
            }
        }
        //TODO 要改一下 这里 订单号 是写死的
        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&ttt=" . time();
        //$print_url="t.php?s=/Transport/Print/PrintAllCarrier&bill=6278701&mod=".$print_mod."&ttt=".time();
        return ['data' => $SKUARR, 'len' => count($SKUARR), 'status' => 1, 'ebay_id' => $ebay_id, 'print_mod' => $print_mod, 'print_url' => $print_url, 'ebay_carrier' => $ebay_carrier];
    }

    //包材信息
    function getPackingmater($id) {
        $Packingmaterial    = $this->CachePackingmaterial();
        $List['model']      = $Packingmaterial[$id]['model'];
        $List['modelid']    = $id;
        $List['model_note'] = $Packingmaterial[$id]['notes'];
        return $List;
    }

    function getPrintUrl($ebay_id) {
        $print_mod  = 1;
        $OrderModel = new OrderModel();
        $Orders     = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        if (!$Orders) {
            return ['status' => 0, 'msg' => '查不到订单号:' . $ebay_id];
        }
        $ebay_carrier     = trim($Orders['ebay_carrier']);
        $ebay_ordersn     = trim($Orders['ebay_ordersn']);
        $allowCarrier     = load_config('newerp/Application/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2;
        }
        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&reprint=1";
        return ['status' => 1, 'data' => $print_url];
    }

    function GetTopMenu() {
        if (S('TopMenu')) {
            $topMenus = S('TopMenu');
            $topMenus[2] = '已出货';
            return S('TopMenu');
        }
        $TopMenuM = new TopMenuModel();
        $RR       = $TopMenuM->field('id,name')->select();
        $Arr      = [];
        foreach ($RR as $List) {
            $Arr[$List['id']] = $List['name'];
        }
        $Arr[0] = '未付款';
        $Arr[1] = '等待处理';
        $Arr[2] = '已经发货';
        S('TopMenu', $Arr, 3600);
        return $Arr;
    }

    /**
     *测试人员谭 2017-05-31 22:11:27
     *说明: 获取本拣货单  已经over 的订单  5
     */
    function GetOrderHadPrint($ordersn, $limit = 0) {
        $OrderPackage         = new OrderPackageModel();
        $PickOrderDetailModel = new PickOrderDetailModel();
        $goodsModel           = new ErpEbayGoodsModel();
        $EbayOrder            = new OrderModel();
        $map['ordersn']       = $ordersn;
        $map['status']        = 1;
        $desc                 = 'id desc';
        $field                = 'baletime,baleuser,ebay_id';
        if ($limit == 0) {
            $OrdersData = $OrderPackage->where($map)->field($field)->order($desc)->select();
        } else {
            $OrdersData = $OrderPackage->where($map)->field($field)->order($desc)->limit($limit)->select();
        }
        for ($i = 0; $i < count($OrdersData); $i++) {
            //订单号
            $ebay_id = $OrdersData[$i]['ebay_id'];
            //产品信息
            $RR   = $PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")
                ->field('sku,qty,goods_name,combineid')->select();
            $skus = [];
            foreach ($RR as $List) {
                $isNoPackaging           = $goodsModel->where(['goods_sn' => ['eq', $List['sku']]])->getField('isnopackaging');
                $List['is_no_packaging'] = $isNoPackaging;
                $skus[]                  = $List;
            }
            $OrdersData[$i]['skus'] = $skus;
            // 跟踪号
            $RR                                 = $EbayOrder->where("ebay_id='$ebay_id'")->field('pxorderid,ebay_tracknumber,ebay_carrier')->find();
            $OrdersData[$i]['pxorderid']        = $RR['pxorderid'];
            $OrdersData[$i]['ebay_tracknumber'] = $RR['ebay_tracknumber'];
        }
        return $OrdersData;
    }

    /**
     *测试人员谭 2017-06-05 21:47:44
     *说明: 这个拣货单 还有哪些订单没有包装  无法包装的
     */
    function GetCanNotBaleOrder($ordersn) {
        $PickOrderDetailModel = new PickOrderDetailModel();
        $EbayOrder            = new OrderModel();
        $map['ordersn']       = $ordersn;
        $map['is_baled']      = 0;
        $PickOrder            = new PickOrderModel();
        $RR                   = $PickOrder->where("ordersn='$ordersn'")->field('isprint')->find();
        //没有完成打包的 状态
        if ($RR['isprint'] == 2) {
            $map['is_delete'] = 0; // 没有删掉/取消订单 但是 我决定 结束捡货单
        } else {
            // 已经完成打包的状态
            $map['status'] = 3; // 需要退回的 订单sku Detail 状态
        }
        if ($RR['isprint'] == 3) {
            // return -3;
        }
        //没有包装的 订单号
        $Orders    = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();
        $ArrOrders = [];
        foreach ($Orders as $Order) {
            //订单号
            $ebay_id                        = $Order['ebay_id'];
            $ArrOrders[$ebay_id]['ebay_id'] = $ebay_id;
            //产品信息
            $RR   = $PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")
                ->field('sku,qty,goods_name,combineid,pic')->select();
            $skus = [];
            foreach ($RR as $List) {
                $skus[] = $List;
            }
            $ArrOrders[$ebay_id]['skus'] = $skus;
            // 跟踪号
            $RR                                      = $EbayOrder->where("ebay_id='$ebay_id'")->field('pxorderid,ebay_tracknumber')->find();
            $ArrOrders[$ebay_id]['pxorderid']        = $RR['pxorderid'];
            $ArrOrders[$ebay_id]['ebay_tracknumber'] = $RR['ebay_tracknumber'];
        }
        return $ArrOrders;
    }


    // 要退回去，因为包装的环节 要退回去的sku
    /**
     *测试人员谭 2017-06-05 22:13:02
     *说明: 参数是  GetCanNotBaleOrder
     * 获取Location 排序的
     */
    public function GetBaledReturnList($ArrOrders) {
        if (count($ArrOrders) == 0) {
            return [];
        }
        $SKU        = [];
        $ReturnSKU  = [];
        $GoodsModel = new EbayGoodsModel();
        foreach ($ArrOrders as $List) {
            $skus = $List['skus'];
            foreach ($skus as $skuArr) {
                $sku = $skuArr['sku'];
                $qty = $skuArr['qty'];
                if (!array_key_exists($sku, $SKU)) {
                    $SKU[$sku]['sku']        = $sku;
                    $SKU[$sku]['qty']        = $qty;
                    $SKU[$sku]['pic']        = $skuArr['pic'];
                    $SKU[$sku]['goods_name'] = $skuArr['goods_name'];
                    /**
                     *测试人员谭 2017-06-05 22:19:19
                     *说明: 要获取最新的 location
                     */
                    $GoodsArr              = $GoodsModel->getGoodsInfo($sku);
                    $SKU[$sku]['location'] = $GoodsArr['goods_location'];
                } else {
                    $SKU[$sku]['qty'] += $qty;
                }
            }
        }
        //debug($SKU);
        foreach ($SKU as $List) {
            $ReturnSKU[$List['location'] . '_' . $List['sku']] = $List;
        }
        ksort($ReturnSKU); // 库位顺序 退回
        return $ReturnSKU;
    }

    /**
     *测试人员谭 2017-06-05 22:24:11
     *说明: 设置捡货单为已经 TODO
     */
    function SetOrderOver($ordersn) {
        $PickOrder            = new PickOrderModel();
        $OrderModel           = new OrderModel();
        $PickOrderModel       = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();
        $RR                   = $PickOrderModel->where("ordersn='$ordersn'")->field('id,type,isprint,is_work')->find();
        if (!$RR) {
            return ['status' => 0, 'msg' => "严重错误:拣货单【{$ordersn}】，不存在"];
        }
        $isprint = $RR['isprint']; // 只能等于2
        $id      = $RR['id'];
        $is_work = $RR['is_work']; // 只能等于2
        if ($isprint != 2) {
            return ['status' => 0, 'msg' => "拣货单【{$ordersn}】,不在已拣货已确认状态!"];
        }
        if ($is_work != 1) {
            return ['status' => 0, 'msg' => "拣货单【{$ordersn}】，不在[正在作业中]!"];
        }
        // 退回的订单号 哪些玩意 TODO
        $map['ordersn']   = $ordersn;
        $map['is_baled']  = 0;
        $map['is_delete'] = 0; // 没有删掉/取消订单 但是 我决定 结束捡货单
        //没有包装的 订单号
        $Orders            = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();
        $CreatePickService = new CreatePickService();
        $file              = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.over.txt';
        $filebd            = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.bale_delete.txt';
        /**
         *测试人员谭 2017-06-05 23:35:10
         *说明: 先将线上的结束掉
         */
        $CancelNote = '';
        foreach ($Orders as $List) {
            $ebay_id = $List['ebay_id'];
            $RR      = $CreatePickService->setOrderCancelCreated($ebay_id, 2);
            if (!$RR['status']) {
                $Log = "拣货单【{$ordersn}】$ebay_id，取消已经捡货状态失败请立马联系IT!";
                writeFile($file, $CancelNote . '----' . $Log . date('Y-m-d H:i:s') . "\n\n\n");
                return ['status' => 0, 'msg' => $Log];
            } else {
                $CancelNote .= $ordersn . '->' . $ebay_id . ",取消成功\n";
            }
        }
        writeFile($file, $CancelNote . '------' . date('Y-m-d H:i:s') . "\n\n\n");
        /**
         *测试人员谭 2017-06-05 23:35:36
         *说明: 再将该删掉的删掉
         */
        foreach ($Orders as $List) {
            $ebay_id            = $List['ebay_id'];
            $saved              = [];
            $saved['status']    = 3; // 这个就是 打包的时候 删除的
            $saved['is_delete'] = 1;
            /**
             *--------------Start
             *测试人员谭 2017-07-03 17:36:43
             *说明: 检查一下 这个订单为什么而结束，如果订单状态没有发生改变！则视为订单正常！ 则认为包装员的退回责任在 捡货员
             */
            $Orders      = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status')->find();
            $ebay_status = $Orders['ebay_status'];
            if ($ebay_status != $this->allowScanStstus && $ebay_status != 2009 && $ebay_status != 2) {
                $saved['is_normal'] = 0;
                // 这里表示 这个订单 是由于 业务原因  在包装的时候退回的 否则 这些退回的锅 就给捡货的人背
            }
            /**
             *-----End
             */
            $RR = $PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")->save($saved);
            if ($RR) {
                $log = $ordersn . '->' . $ebay_id . '  ' . date('Y-m-d H:i:s') . "\n\n\n";
            } else {
                $log = 'Error:' . $ordersn . '->' . $ebay_id . '  ' . date('Y-m-d H:i:s') . "\n\n\n";
            }
            writeFile($filebd, $log);
        }
        $save             = [];
        $save['is_work']  = 0; // 结束 正在作业的状态
        $save['work_end'] = time(); // 记录下结束时间
        $save['isprint']  = 3; // 3 就是结束状态改  完成
        $RR               = $PickOrder->where("id='$id'")->save($save);
        if ($RR) {
            return ['status' => 1, 'msg' => ''];
        }
        return ['status' => 0, 'msg' => '数据操作失败'];
    }

    /**
     *测试人员谭 2017-06-05 21:42:50
     *说明: 进度是什么
     */
    function getBaleProcess($ordersn) {
        $Arr                  = $this->GetOrderHadPrint($ordersn); // 所有的已经包装的 已完成的部分
        $Len                  = count($Arr); //同学 你包好了多少
        $PickOrder            = new PickOrderModel('', '', C('DB_CONFIG2'));
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG2'));
        $map['ordersn']       = $ordersn;
        $POrder               = $PickOrder->where($map)->find();
        $work_start           = $POrder['work_start']; // 开始时间
        // echo $work_start;
        $CostHours        = round((time() - $work_start) / 3600, 1);
        $map['is_delete'] = 0;
        $RR               = $PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();
        $Total            = count($RR); // 一个捡货单 有多少个 订单
        return [
            'work_start' => date('y-m-d H:i:s', $work_start),
            'CostHours'  => $CostHours,
            'ordersn'    => $ordersn,
            'Total'      => $Total,
            'Len'        => $Len,
            'Arr'        => $Arr
        ];
    }

    /**
     *测试人员谭 2017-06-05 21:42:05
     *说明: 检查 包裹是不是 ok了
     */
    function OverPickOrder() {
    }

    //格式化该死的 包装材料辅料
    function fomartAccessories($accessories) {
        if($accessories){
            return '带辅料';
        }else{
            return '';
        }
        $accessories     = str_replace('|', '', $accessories);
        $accessories_arr = explode(',', $accessories);
        $str             = '<br>';
        for ($i = 0; $i < count($accessories_arr); $i++) {
            if (!$accessories_arr[$i]) {
                continue;
            }
            // 层 圈 个 卷 片 条
            $accessories_str = $accessories_arr[$i];
            //  层 圈 个 卷 片 条 变成红色
            $accessories_str = preg_replace("/\*(\d+[\x80-\xff]+$)/", '*<b style="color:red">' . "$1" . '</b>', $accessories_str);
            $str .= ($i + 1) . '.' . $accessories_str . '<br>';
        }
        return $str;
    }

    /**
     * 根据sku找到拣货单(单品单货)
     * @param $sku
     * @return array
     * @author Shawn
     * @date 2018/7/11
     */
    public function getPickOrderBySku($sku,$type,$deep=false)
    {
        //判断10*10还是10*15的拣货单
        $map['carrier_company'] = $type;
        $filebd            = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.bale_delete.txt';
        $carrier_company = "物流渠道分组id：".$type;
        $type_info = "单品单货";
        //首先根据sku查出未完成拣货单
        $pickOrder = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();
        $userName = session('truename');
        $sku = strtoupper(trim($sku));
        $map['isprint'] = array('lt',3);

        /**
         *测试人员谭 2018-11-22 10:43:38
         *说明: 江 要求改成 只考虑待包装
         */
        $map['isprint'] = 2; // 待包装

        $map['type'] = 1;
        $ordersns = $pickOrder->where($map)->getField("ordersn",true);
        if(empty($ordersns)){
            return ['status' => 0, 'msg' => "未找到未完成的".$type_info.$carrier_company."拣货单"];
        }



        //开始查等待我包装的订单
        $pickMap['ordersn'] = array("in",$ordersns);
        $pickMap['sku']         = $sku;
        $pickMap['is_delete']   = 0;    //没有删除的
        $pickMap['is_baled']    = 0;     //没有包装好的
        $pickMap['isjump']      = 0;  //没有被跳过的
        $pickMap['scaning']     = 0; //没有被扫第一枪的


        $pickDetailData = $PickOrderDetailModel
            ->where($pickMap)
            ->field('ebay_id,sku,qty,goods_name,pic,id,ordersn')
            ->order('sortorder asc')
            ->find();

        if(empty($pickDetailData)){
            return ['status' => 0, 'msg' =>"没有包含sku:{$sku} 的".$type_info."拣货单" ];
        }


        /**
        *测试人员谭 2018-07-23 16:43:32
        *说明: 这里开始标记我抢到的订单 就是我的
        */

        $detailId = $pickDetailData['id'];
        $ebay_id = $pickDetailData['ebay_id'];
        $ordersn = $pickDetailData['ordersn'];


        $rows=$PickOrderDetailModel->where(['id'=>$detailId,'scaning'=>0])
            ->limit(1)
            ->save(['scaning'=>1,'scan_time'=>time(),'scan_user'=>$userName]);


        //数据库操作失败
        if($rows===false){
            return [
                'status' => 0,
                'msg'    => "订单锁定失败，数据操作失败 ",
                'ordersn'=>$ordersn
            ];
        }

        // 影响了0行表示 这个订单被其他人抢走了 再给一次机会
        if($rows===0){

            if($deep==false){
                $log=$userName.',扫描SKU单品单货'.$sku."，被抢走，重新尝试";
                writeFile($filebd,$log);
                return $this->getPickOrderBySku($sku,$type,true);
            }

            return [
                'status' => 0,
                'msg'    => "订单锁定失败，请重新扫描此SKU ",
                'ordersn'=>$ordersn
            ];

        }


        //锁定成功！



        $handleModel     = new \Task\Model\EbayHandleModel();
        $goodsModel      = new \Common\Model\ErpEbayGoodsModel();
        $Packingmaterial = $this->CachePackingmaterial();
        $skuData = [];
        $print_mod = 1;
        //查询sku商品信息
        $currStoreId    = C("CURRENT_STORE_ID");
        $packageMaterialId = $handleModel->table($handleModel->getPartitionTableName(['store_id' => $currStoreId]))
            ->where(['goods_sn' => $sku])->getField('packingmaterial');

        $goodsInfo         = $goodsModel->where(['goods_sn' => $sku])->find();
        $picksid           = $packageMaterialId;
        $accessories       = $goodsInfo['accessories'];
        if ($picksid) {
            $pickDetailData['model']       = $Packingmaterial[$picksid]['model'];
            $pickDetailData['modelid']     = $picksid;
            $pickDetailData['model_note']  = $Packingmaterial[$picksid]['notes'];
            $pickDetailData['accessories'] = $this->fomartAccessories($accessories);
            $pickDetailData['isnopackaging'] = '';//$goodsInfo['ispacking'] == 1 ? '带包装' : '裸装';
        }
        $skuData[$sku] = $pickDetailData;
        $OrderModel = new OrderModel();
       // $TopMenu    = $this->GetTopMenu();
        // 这里要查询一下 神奇的 订单类型====
        $Orders           = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        if (empty($Orders)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id}不存在，请重新扫描 ",
                'ordersn'=>$ordersn
            ];
        }
        
        if(!in_array($Orders['ebay_status'],$this->canScaningStatus)){
			return [
				'status' => 0,
				'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id} 状态异常,请跳过 <!-- {$Orders['ebay_status']} --> ",
				'ordersn'=>$ordersn
			];
		}
        
        $ebay_ordersn     = $Orders['ebay_ordersn'];
        $ebay_carrier     = trim($Orders['ebay_carrier']);
        $allowCarrier     = load_config(APP_PATH.'/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2;
        }
        $interceptModel = new \Api\Model\OrderInterceptRecordModel();
        $interceptInfo  = $interceptModel->where(['ebay_id' => $ebay_id, 'status' => 0])->find();
        if (!empty($interceptInfo)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id} 已被拦截，禁止出库",
                'ordersn'=>$ordersn
            ];
        }
        //检查下是否已经扫描过，进入了同步表的
        $APIChecksku = new ApiCheckskuModel();
        $ss          = $APIChecksku->where("ebay_id='$ebay_id'")->field('id')->find();


        if(!empty($ss)){
            $msg       = " 订单号{$ebay_id} 已经扫描过请联系您的主管!";
            return [
                'status' => 0,
                'msg'    => $msg,
                'ordersn'=>$ordersn
            ];
        }




        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&ttt=" . time();
        return ['data' => $skuData, 'len' => count($skuData), 'status' => 1, 'ebay_id' => $ebay_id, 'print_mod' => $print_mod, 'print_url' => $print_url, 'sku' => $sku, 'ebay_carrier' => $ebay_carrier,'ordersn'=>$ordersn];
    }

    /**
     * 根据sku找到拣货单(单品多货)
     * @param $sku
     * @param bool $deep
     * @return array
     * @author Shawn
     * @date 2018/7/23
     */
    public function getPickOrderBySkuMore($sku,$type,$deep=false)
    {
        //判断10*10还是10*15的拣货单
        $map['carrier_company'] = $type;
        $carrier_company =  "物流渠道分组id：".$type;;
        $filebd            = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.bale_delete.txt';
        $type_info = "单品多货";
        //首先根据sku查出未完成拣货单
        $pickOrder = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();
        $userName = session('truename');
        $sku = strtoupper(trim($sku));

        //先查询所有拣货单
        $map['isprint'] = array('lt',3);
        /**
        *测试人员谭 2018-09-27 10:43:38
        *说明: 江 要求改成 只考虑待包装
        */
        $map['isprint'] = 2; // 待包装
        $map['type'] = 2; // 多品多货
        $ordersns = $pickOrder->where($map)->getField("ordersn",true);
        if(empty($ordersns)){
            return ['status' => 0, 'msg' => "未找到未完成的".$type_info.$carrier_company."拣货单"];
        }




        /**
        *测试人员谭 2018-08-07 16:01:21
        *说明: 由于有了 自动解锁程序 不考虑 我之前扫描过的订单 这个逻辑
        */
        $pickMap=[];
        $pickMap['sku']         = $sku;
        $pickMap['is_delete']   = 0;  //没有删除的
        $pickMap['is_baled']    = 0;  //没有包装好的
        $pickMap['isjump']      = 0;  //没有被跳过的
        $pickMap['ordersn'] = array("in",$ordersns);
        $pickMap['scaning']     = 0; // 只考虑 没有扫过的
        $pickDetailData = $PickOrderDetailModel
            ->where($pickMap)
            ->field('ebay_id,sku,qty,goods_name,pic,id,ordersn')
            ->order('qty asc')  // 江鹏程 经理要求 按照少的sku 先出单子
            ->find();

        $is_new=1;

        if(empty($pickDetailData)){
            return ['status' => 0, 'msg' =>"没有包含sku:{$sku} 的".$type_info."拣货单" ];
        }



        /**
        *测试人员谭 2018-07-23 17:10:37
        *说明: 找到了 一个订单
        */
        $detailId = $pickDetailData['id'];
        $ebay_id  = $pickDetailData['ebay_id'];
        $ordersn  = $pickDetailData['ordersn'];


        /**
        *测试人员谭 2018-07-23 17:21:30
        *说明: 开始锁定我的订单
        */
        if($is_new==1){
            //尝试锁定 本次我找到的订单
            $rows=$PickOrderDetailModel->where(['id'=>$detailId,'scaning'=>0])
                ->limit(1)
                ->save(['scaning'=>1,'scan_time'=>time(),'scan_user'=>$userName]);


            //数据库操作失败
            if($rows===false){
                return [
                    'status' => 0,
                    'msg'    => "单品多货锁定失败，数据操作失败 ",
                    'ordersn'=>$ordersn
                ];
            }

            // 影响了0行表示 这个订单被其他人抢走了 再给一次机会
            if($rows===0){

                if($deep==false){
                    $log=$userName.',扫描SKU单品多货'.$sku."，被抢走，重新尝试";
                    writeFile($filebd,$log);
                    return $this->getPickOrderBySkuMore($sku,$type,true);
                }

                return [
                    'status' => 0,
                    'msg'    => "单品多货锁定失败，请重新扫描此SKU ",
                    'ordersn'=>$ordersn
                ];

            }
        }

        // 锁定我的订单  END


        $handleModel     = new \Task\Model\EbayHandleModel();
        $goodsModel      = new \Common\Model\ErpEbayGoodsModel();
        $Packingmaterial = $this->CachePackingmaterial();
        $skuData = [];
        $print_mod = 1;
        //查询sku商品信息
        $currStoreId    = C("CURRENT_STORE_ID");
        $packageMaterialId = $handleModel->table($handleModel->getPartitionTableName(['store_id' => $currStoreId]))
            ->where(['goods_sn' => $sku])->getField('packingmaterial');
        $goodsInfo         = $goodsModel->where(['goods_sn' => $sku])->find();
        $picksid           = $packageMaterialId;
        $accessories       = $goodsInfo['accessories'];
        if ($picksid) {
            $pickDetailData['model']       = $Packingmaterial[$picksid]['model'];
            $pickDetailData['modelid']     = $picksid;
            $pickDetailData['model_note']  = $Packingmaterial[$picksid]['notes'];
            $pickDetailData['accessories'] = $this->fomartAccessories($accessories);
            $pickDetailData['isnopackaging'] = '';//$goodsInfo['ispacking'] == 1 ? '带包装' : '裸装';
        }
        $skuData[$sku] = $pickDetailData;
        $OrderModel = new OrderModel();
        //$TopMenu    = $this->GetTopMenu();
        // 这里要查询一下 神奇的 订单类型====
        $Orders           = $OrderModel->where("ebay_id='$ebay_id'")->field('ebay_status,ebay_carrier,ebay_ordersn')->find();
        if (empty($Orders)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id}不存在，请重新扫描 ",
                'ordersn'=>$ordersn
            ];
        }

        if(!in_array($Orders['ebay_status'],$this->canScaningStatus)){
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id} 状态异常,请跳过 <!-- {$Orders['ebay_status']} --> ",
                'ordersn'=>$ordersn
            ];
        }

       // $ebay_ordersn     = $Orders['ebay_ordersn'];
        $ebay_carrier     = trim($Orders['ebay_carrier']);
        $allowCarrier     = load_config(APP_PATH.'/Transport/Conf/config.php');
        $CARRIER_TEMPT_15 = $allowCarrier['CARRIER_TEMPT_15'];
        if (array_key_exists($ebay_carrier, $CARRIER_TEMPT_15)) {
            $print_mod = 2;
        }
        $interceptModel = new \Api\Model\OrderInterceptRecordModel();
        $interceptInfo  = $interceptModel->where(['ebay_id' => $ebay_id, 'status' => 0])->find();
        if (!empty($interceptInfo)) {
            return [
                'status' => 0,
                'msg'    => "拣货单{$ordersn}中 订单号{$ebay_id} 已被拦截，禁止出库",
                'ordersn'=>$ordersn
            ];
        }
        //检查下是否已经扫描过，进入了同步表的
        $APIChecksku = new ApiCheckskuModel();
        $ss          = $APIChecksku->where("ebay_id='$ebay_id'")->field('id')->find();

        if(!empty($ss)){
            $msg       = " 订单号{$ebay_id} 已经扫描过请联系您的主管!";
            return [
                'status' => 0,
                'msg'    => $msg,
                'ordersn'=>$ordersn
            ];
        }

        $print_url = "t.php?s=/Transport/Print/PrintAllCarrier&bill=" . $ebay_id . "&mod=" . $print_mod . "&ttt=" . time();
        return ['data' => $skuData, 'len' => count($skuData), 'status' => 1, 'ebay_id' => $ebay_id, 'print_mod' => $print_mod, 'print_url' => $print_url, 'sku' => $sku, 'ebay_carrier' => $ebay_carrier,'ordersn'=>$ordersn];
    }





    /**
     * 获取自己已经包装好的产品
     * @param int $limit
     * @return mixed
     * @author Shawn
     * @date 2018/7/11
     */
    function getMyselfOrderHadPrint($limit = 5) {
        $PickOrderDetailModel = new PickOrderDetailModel('', '', C('DB_CONFIG_READ'));
        $EbayOrder            = new OrderModel('', '', C('DB_CONFIG_READ'));
        $map['scan_user']        = session('truename');
        $map['is_baled']         = 1;
        $desc                 = 'scan_time desc';
        $field                = 'ebay_id,sku,qty,goods_name,combineid,scan_time';
        $ordersData = $PickOrderDetailModel->where($map)->field($field)->order($desc)->limit($limit)->select();

        $uniquArr=[];

        foreach ($ordersData as &$v){
            $ebay_id = $v['ebay_id'];
            /**
            *测试人员谭 2018-08-03 17:50:46
            *说明: 多品多货 的有重复要干掉
            */
            if(array_key_exists($ebay_id,$uniquArr)){
                 continue;
            }

            $uniquArr[$ebay_id]=1;

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
        return $ordersData;
    }
}
