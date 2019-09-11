<?php
namespace Package\Service;

use Common\Model\EbayOrderExtModel;
use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Mid\Service\TransferOrderService;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;

/**
 *测试人员谭 2017-12-12 18:38:31
 *说明: 分拣分区 跨仓的 服务
 */
class OrderCrossSortingService
{
    private $current_storeid = 0;
    private $store_names = [];
    private $PickOrderModel = null;
    private $PickOrderDetailModel = null;
    private $PickOrderConfirmModel = null;
    private $PickOrderDetailStrModel = null;
    private $InterNalModel = null;
    private $OrderModel = null;
    private $EbayOrderExtModel = null;

    function __construct() {
        $this->PickOrderConfirmModel   = new PickOrderConfirmModel();
        $this->PickOrderDetailModel    = new PickOrderDetailModel();
        $this->PickOrderModel          = new PickOrderModel();
        $this->PickOrderDetailStrModel = new PickOrderDetailSkustrModel();
        $this->transferOrderService    = new TransferOrderService();
        $this->OrderModel               = new OrderModel();
        $this->EbayOrderExtModel        = new EbayOrderExtModel();
        $this->InterNalModel            = new InternalStoreSkuModel();
        $this->current_storeid         = C('CURRENT_STORE_ID');
        $this->store_names             = C('STORE_NAMES');
    }

    // 是否已经初始化了
    function isInit($ordersn) {
        $rr     = $this->PickOrderConfirmModel->where(['ordersn' => $ordersn])->field('id')->find();
        $rr_str = $this->PickOrderDetailStrModel->where(['ordersn' => $ordersn])->field('id')->find();
        //debug($rr_str);
        //debug($rr);die();
        $i      = 0;
        if (!empty($rr)) {
            $i++;
        }
        if (!empty($rr_str)) {
            $i++;
        }
        if ($i == 1) {
            return ['status' => 0, 'msg' => '当前状态只有一半初始化了! 初始化有误!'];
        }
        if ($i == 0) {
            return ['status' => 1, 'msg' => '没有初始化!'];
        }
        if ($i == 2) {
            return ['status' => 2, 'msg' => '已经初始化过了!'];
        }
        return ['status' => 0, 'msg' => '未知的错误!'];
    }

    //初始化 确认表
    function InitOrderConfirm($ordersn) {
        $RR       = $this->PickOrderDetailModel->where(['ordersn' => $ordersn])
            ->field('sku,sum(qty) as cc')
            ->group('sku')->select();
        $user     = $_SESSION['truename'];
        $real_qty = 0;
        $addData  = [];
        foreach ($RR as $List) {
            $storeid=$this->InterNalModel->getOrderSkuStore($List['sku'],$this->current_storeid);
            $addData[] = array(
                'ordersn'      => $ordersn,
                'sku'          => $List['sku'],
                'qty'          => $List['cc'],
                'real_qty'     => $real_qty,
                'confirm_user' => $user,
                'addtime'      => time(),
                'storeid'      => $storeid
            );
        }
        $this->PickOrderConfirmModel->addAll($addData);
    }

    //初始化多品多货的缓存
    function InitOrderDetailSkustr($ordersn) {
        $RR = $this->PickOrderDetailModel->where(['ordersn' => $ordersn])
            ->field('ebay_id')
            ->group('ebay_id')->select();
        foreach ($RR as $Lis) {
            $map            = [];
            $map['ordersn'] = $ordersn;
            $map['ebay_id'] = $Lis['ebay_id'];
            $Arr            = $this->PickOrderDetailModel->where($map)->field('sku,qty')->select();
            if (count($Arr) < 2) { // 单品单货有什么好的
                //return false;
            }
            $skuArr = [];
            foreach ($Arr as $List) {
                $skuArr[strtoupper($List['sku'])] = $List['qty'];
            }
            ksort($skuArr);
            $str           = json_encode($skuArr);
            $map['skustr'] = $str;
            @$this->PickOrderDetailStrModel->add($map);
        }
    }

    // 扫描一个sku
    function ScanOneSKU($ordersn, $sku) {
        $Rs = $this->isInit($ordersn);
        if ($Rs['status'] != 2) {
            $Rs['status']=0;
            return $Rs;//$Rs['msg'];
        }
        $sku            = strtoupper($sku);
        $map['ordersn'] = $ordersn;
        $map['sku']     = $sku;
        $Rr             = $this->PickOrderConfirmModel->where($map)->field('real_qty,storeid,status,qty,id')->find();
        if (empty($Rr)) {
            /**
             *测试人员谭 2017-12-13 16:07:05
             *说明: 如果属于,则初始化失败了
             */
            return ['status' => 0, 'msg' => $sku . ',不属于捡货单' . $ordersn . '!如果确定属于,请联系IT!'];
        }
        $real_qty  = $Rr['real_qty'];
        $qty       = $Rr['qty'];
        $Confirmid = $Rr['id'];
        $status    = $Rr['status'];
        $storeid   = $Rr['storeid'];
        if ($status == 2) {
            $msg = $this->store_names[$storeid];
            return ['status' => 0, 'msg' => '捡货单' . $ordersn . ',中sku: ' . $sku . ',来自[' . $msg . ']您已经确认了这条sku的数量,再找出来也无法参与发货了!'];
        }
        if ($qty == $real_qty) {
            return ['status' => 0, 'msg' => '捡货单' . $ordersn . ',中sku一共' . $qty . '您已经全部扫描过了,请检查是否多扫描!'];
        }
        $RR      = $this->PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint,is_work,cross_status')->find();
        $isprint = $RR['isprint'];
        $type = $RR['type'];
        $is_work = $RR['is_work'];
        $cross_status = $RR['cross_status'];

        if ($type == 1) {
            return ['status' => 0, 'msg' => '单品单件的 拣货单 不需要二次分拣'];
        }
        if ($type == 2) {
            return ['status' => 0, 'msg' => '单品多件的 拣货单 不需要二次分拣'];
        }
        if ($is_work) {
            return ['status' => 0, 'msg' => '拣货单 已经开始打包了，不能二次分拣'];
        }
        if ($isprint != 1) {
            return ['status' => 0, 'msg' => '拣货单必须在已经确认状态下才能二次分拣'];
        }
        /**
         *测试人员谭 2017-12-13 16:32:46
         *说明:
         */
        //先假设 这个sku 分给了一个 已经带有【分拣位置号】 或者，这个已经带有分拣号的订单
        $map['ordersn']   = $ordersn;
        $map['sku']       = $sku;
        $map['combineid'] = ['gt', 0]; // 大于0 就是 已经 分拣了
        $map['is_stock']  = 0;
        $map['is_delete'] = 0;
        $map['_string']   = " (qty-qty_com)>0 ";
        $field            = 'id,ebay_id,combineid';
        $RR               = $this->PickOrderDetailModel->where($map)
            ->field($field)->order('order_addtime asc')->find();


        if ($RR) {
            $id        = $RR['id'];
            $combineid = $RR['combineid'];
            $this->PickOrderDetailModel->where(['id' => $id])->setInc('qty_com');
            $this->PickOrderConfirmModel->where(['id' => $Confirmid])->limit(1)->setInc('real_qty');
            return ['status' => 1, 'data' => $combineid, 'msg' => ''];
        }
        //再到 没有分配过 分拣位置 的订单里面查找
        $map['combineid'] = 0;
        $RR               = $this->PickOrderDetailModel->where($map)->field($field)->order('order_addtime asc')->find();
        //没有分拣过的定单 没有找到
        if (!$RR) {
            // 这里要检查一下所有的sku 是不是都完毕了
            unset($map['sku']);
            $map['combineid'] = ['gt', 0];
            $RS               = $this->PickOrderDetailModel->where($map)->field('id')->find();
            if (!$RS) {
                return ['status' => 0, 'data' => '', 'msg' => '跨仓拣货单：' . $ordersn . ' 已经二次分拣完毕!'];
            }
            return ['status' => 0, 'data' => '', 'msg' => '未分拣的订单中找不到' . $sku . ',请确认是否是前面已经扫描,或者SKU不属于本次拣货单'];
        }
        $ebay_id = $RR['ebay_id'];
        $id      = $RR['id'];
        $this->PickOrderDetailModel->where(['id' => $id])->setInc('qty_com');
        $this->PickOrderConfirmModel->where(['id' => $Confirmid])->limit(1)->setInc('real_qty');
        $map            = [];
        $map['ordersn'] = $ordersn;
        $rs             = $this->PickOrderDetailModel->where($map)->field('max(combineid) as cc')->find();
        $newcombineid   = $rs['cc'] + 1;
        $map['ebay_id'] = $ebay_id;
        // 注意 这里可能多个数据
        $Rs = $this->PickOrderDetailModel->where($map)->save(['combineid' => $newcombineid]);
        if ($Rs) {
            //SKU和个数按照顺序排好
            return ['status' => 1, 'data' => $newcombineid, 'msg' => ''];
        }
        return ['status' => 0, 'data' => '', 'msg' => '未知原因失败了'];
    }

    // 确认本仓库的sku 都ok 不ok
    function ConfirmCurrentStore($ordersn) {
        $Rs = $this->isInit($ordersn);
        if ($Rs['status'] != 2) {
            $Rs['status']=0;
            return $Rs;//$Rs['msg'];
        }

        $storeid         = $this->current_storeid;
        $Cmap['ordersn'] = $ordersn;
        $Cmap['storeid'] = $storeid;
        $Cmap['status']  = 2;
        $RR              = $this->PickOrderConfirmModel->where($Cmap)->find();
        if ($RR) {
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',当前仓库的SKU部分已经确认过了!'];
        }
        $map['ordersn']  = $ordersn;
        $map['store_id'] = $storeid;
        // $map['is_stock']  = 0;
        $map['_string']    = " (qty-qty_com)>0 ";
        $RR                = $this->PickOrderDetailModel->where($map)
            ->field('combineid,ebay_id')->group('ebay_id')->select();

        $stock_ebay_id     = [];
        $CreatePickService = new CreatePickService();
        foreach ($RR as $List) {
            $ebay_id         = $List['ebay_id'];
            $Lmap            = [];
            $Lmap['ordersn'] = $ordersn;
            $Lmap['ebay_id'] = $ebay_id;
            $this->PickOrderDetailModel->where($Lmap)->save(['is_stock' => 1, 'is_delete' => 1]); // 记为缺货 山掉
            $stock_ebay_id[] = $ebay_id;
            $CreatePickService->setOrderCancelCreated($ebay_id, 3); // 集货的时候挂掉了
        }
        $Cmap            = [];
        $Cmap['ordersn'] = $ordersn;
        $Cmap['storeid'] = $storeid;
        $tt              = time();
        // 当前仓的 先给确认掉!
        $rr = $this->PickOrderConfirmModel->where($Cmap)->save(['status' => 2,'confirm_time' => $tt]);


        if ($rr) {

            $this->PickOrderModel->where(['ordersn'=>$ordersn])->limit(1)->save(['cross_status'=>2]);
            //这里发送一个 调拨单子！
            //TODO 这里发送一个 调拨单子！
            $arr = $this->store_names;
            unset($arr[$this->current_storeid]);
            $arr           = array_keys($arr);
            $other_storeid = $arr[0];//获取另一个仓库
            // 没删掉的订单----
            $SkuArr        = $this->PickOrderDetailModel
                ->where(['ordersn' => $ordersn, 'is_delete' => 0, 'store_id' => $other_storeid])
                ->field('sku,sum(qty) as qty')->group('sku')->select();

            $from_store_id = $other_storeid;
            $to_store_id   = $this->current_storeid;
            /**
             *测试人员谭 2017-12-13 20:22:38
             *说明: TODO:  simon 这里就交给你了----------------
             */
            //创建调拨单 simon 2017-12-13
            $rs=$res = $this->transferOrderService->createTransferOrder(
                json_encode(['from_store' => $from_store_id, 'to_store' => $to_store_id, 'contains' => $SkuArr, 'pick_ordersn' => $ordersn])
            );

            $msg='';
            if($rs['status']!=100){
                $msg='请注意！您的调拨单生成失败了，请点击重新生成调拨单!';
            }
            /**
            *测试人员谭 2017-12-13 23:18:07
            *说明:TODO: 不要看这里调拨单子生成失败了! 如果最终调拨单没有入库 ,永远无法进行 第二次分拣操作
            */
            //TODO: 这里发送一个 调拨单子！ END---------------------

            return ['status' => 1, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',当前仓库的SKU确认成功,请注意转移多余的sku到异常区!','msg1'=>$msg];
        } else {
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',当前仓库的SKU确认失败了,一个数据都没有修改!','msg1'=>''];
        }
    }



    /**
     *测试人员谭 2017-12-13 17:38:33
     *说明: 确认其他仓的,
     */
    function ConfirmOtherStore($ordersn) {
        $Rs = $this->isInit($ordersn);
        if ($Rs['status'] != 2) {
            $Rs['status']=0;
            return $Rs;//$Rs['msg'];
        }

        $RR=$this->PickOrderModel->where(['ordersn'=>$ordersn])->find();

        $isprint      = $RR['isprint'];
        $cross_status = $RR['cross_status'];
        $cross        = $RR['is_cross'];
        if($isprint!=1){
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',必须在等待确认才能操作!'];
        }

        if($cross_status!=3){
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',的捡货单必须确认入库才能操作，请联系您的主管点击【检查调拨单】<!--'.$cross_status.'-->!'];
        }

        if($cross!=1){
            return ['status' => 0, 'data' => '', 'msg' => $ordersn . ',不是一个跨仓单！ 你逗窝?'];
        }

        $arr = $this->store_names;
        unset($arr[$this->current_storeid]);
        $arr             = array_keys($arr);
        $storeid         = $arr[0];//获取另一个仓库
        $Cmap['ordersn'] = $ordersn;
        $Cmap['storeid'] = $storeid;
        $Cmap['status']  = 2;
        $RR              = $this->PickOrderConfirmModel->where($Cmap)->find();
        if ($RR) {
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',集货仓库的SKU部分已经确认过了!'];
        }
        $map['ordersn']    = $ordersn;
        $map['store_id']   = $storeid;
        $map['is_stock']   = 0;
        $map['is_delete']  = 0;
        $map['_string']    = " (qty-qty_com)>0 ";
        $RR                = $this->PickOrderDetailModel->where($map)
            ->field('combineid,ebay_id')->group('ebay_id')->select();
        $stock_ebay_id     = [];
        $CreatePickService = new CreatePickService();
        foreach ($RR as $List) {
            $ebay_id         = $List['ebay_id'];
            $Lmap            = [];
            $Lmap['ordersn'] = $ordersn;
            $Lmap['ebay_id'] = $ebay_id;
            $this->PickOrderDetailModel->where($Lmap)->save(['is_stock' => 1, 'is_delete' => 1]); // 记为缺货 山掉
            $stock_ebay_id[] = $ebay_id;
            $CreatePickService->setOrderCancelCreated($ebay_id, 4); // 集货的时候挂掉了
        }
        $Cmap            = [];
        $Cmap['ordersn'] = $ordersn;
        $Cmap['storeid'] = $storeid;
        $tt              = time();
        $rr              = $this->PickOrderConfirmModel->where($Cmap)->save(['status' => 2, 'confirm_time' => $tt]);
        if (!$rr) {
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',' . $this->store_names[$storeid] . '的SKU确认失败了!'];
        }
        //检查捡货单子 是不是要直接干掉
        $ConfirmService = new ConfirmService();
        $rr             = $ConfirmService->PickOrderNeedDelete($ordersn); // 极端情况下 一个都没有捡到货
        //订单要转到 等待打包
        if ($rr == true) {
            return ['status' => 1, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',' . $this->store_names[$storeid] . '的SKU确认成功,但是全部是异常!!!捡货单转到回收站了!'];
        }
        $save['isprint'] = 2;
        $this->PickOrderModel->where(['ordersn' => $ordersn])->limit(1)->save($save);

        /**
        *测试人员谭 2017-12-15 11:55:32
        *说明: 这里转到等待扫描 所有没有异常的订单 都转到等待扫描
        */
        $ebay_ids=$this->PickOrderDetailModel
            ->where(['is_delete'=>0,'ordersn' => $ordersn])
            ->group('ebay_id')->getField('ebay_id',true);

        //$CreatePickService->setOrderToWriteScan($ebay_ids);
        $this->setOrderToWriteScan($ebay_ids);
        // 订单转到
        return ['status' => 1, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',' . $this->store_names[$storeid] . '的SKU确认成功,请注意转移多余的sku到异常区!'];
    }

    function setOrderToWriteScan($ebay_ids){
            //允许转入等待扫描的订单状态 [等待打印,等待扫描] 朱诗萌 2017/11/4
            $allowOrderStatus = [1745, 1724,1723];
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

                $this->EbayOrderExtModel->saveToExt($ebay_id,1724);
            }

            return $Error_Order;

    }



    // 可以做自动任务 去检查调拨单有没有完结
    function CheckTransferOrder($ordersn=''){


        $map=[];
        $one=0;
        if($ordersn){
            $one=1;
            $map['ordersn']=$ordersn;
        }

        $map['isprint']=1;

        $map['cross_status']=2;

        $RR=$this->PickOrderModel->where($map)->field('ordersn')->select();

        foreach($RR as $List){
            $ordersn=$List['ordersn'];
            /**
            *测试人员谭 2017-12-13 23:33:09
            *说明: smith 期望返回值是
             * ['status'=>3,'msg'=>'入库了']
             * 0 没创建
             * 1 草稿
             * 2 出库了
             * 3 入库了
            */
            $rr=$this->transferOrderService->getTransferOrderStatus($ordersn);

            $error=[
                0=>'没创建',
                1=>'草稿',
                2=>'调出仓库已出库',
                3=>'调入仓库已入库',
                100=>'检查调拨单网络失败'
            ];



            if(3==$rr){
                /**
                *测试人员谭 2017-12-13 23:35:26
                *说明: 这玩意操作之后，才能进行 订单的另一个仓库的sku 分拣 确认！ 否则啥也别想干！！！！！
                */
                $this->PickOrderModel->where(['ordersn'=>$ordersn])->limit(1)->save(['cross_status'=>3]);
            }

            if($one){
                if($rr==3){
                    return ['status'=>1,'msg'=>'调拨单已经入库您可以操作集货仓了'];
                }else{
                    return ['status'=>0,'msg'=>'调拨单没有完成,您还不能开始 扫描另一个仓库的sku,当前状态是:'.$error[$rr]];
                }
            }

        }


        return ['status'=>0,'msg'=>0];
    }


    //如果失败了 要单独生成
    function CreateTransferOrder($ordersn){

        $Rs = $this->isInit($ordersn);
        if ($Rs['status'] != 2) {
            $Rs['status']=0;
            return $Rs;//$Rs['msg'];
        }

        $RR=$this->PickOrderModel->where(['ordersn'=>$ordersn])->find();

        $isprint      = $RR['isprint'];
        $cross_status = $RR['cross_status'];
        $cross        = $RR['is_cross'];
        if($isprint!=1){
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',必须在等待确认才能操作!'];
        }

        if($cross_status!=2){
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',的捡货单必须是完成了当前仓库的分拣<!--'.$cross_status.'-->!'];
        }

        if($cross!=1){
            return ['status' => 0, 'data' => '', 'msg' => $ordersn . ',不是一个跨仓捡货单！ 你逗窝?'];
        }


        $arr = $this->store_names;
        unset($arr[$this->current_storeid]);
        $arr           = array_keys($arr);
        $other_storeid = $arr[0];//获取另一个仓库
        // 没删掉的订单----
        $SkuArr        = $this->PickOrderDetailModel
            ->where(['ordersn' => $ordersn, 'is_delete' => 0, 'store_id' => $other_storeid])
            ->field('sku,sum(qty) as qty')->group('sku')->select();

        $from_store_id = $other_storeid;
        $to_store_id   = $this->current_storeid;
        /**
         *测试人员谭 2017-12-13 20:22:38
         *说明: TODO:  simon 这里就交给你了----------------
         */
        //创建调拨单 simon 2017-12-13
        $rs=$res = $this->transferOrderService->createTransferOrder(
            json_encode(['from_store' => $from_store_id, 'to_store' => $to_store_id, 'contains' => $SkuArr, 'pick_ordersn' => $ordersn])
        );

        //debug($rs);
        $data['status']=1;
        $msg='调拨单生成成功了请尽快联系主管调拨，审核';
        if($rs['status']!=100){
            $data['status']=0;
            $msg='请注意！您的调拨单生成失败了!';
            if($rs['status']==102){
                $msg='您已经生成了调拨单了！请通知主管检查 ERP调拨单 ';
            }
        }
        $data['msg']=$msg;
        return $data;
    }

    // 获取当前的 捡货单子 分拣进度
    function getOrderSortStatus($ordersn,$isAll=false){

        $RR=$this->PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint')->find();

        $isprint=$RR['isprint'];//

        $type=$RR['type'];   // 只针对 2 和 3


        $map['ordersn']   = $ordersn;
        //$map['is_stock']  = 0; // 不缺货
        //$map['is_delete'] = 0; // 不删除
        $DetailRs=$this->PickOrderDetailModel->where($map)->field('ebay_id,sku,qty,location,goods_name,pic,order_addtime,combineid,qty_com,store_id as storeid')
            ->order('(qty_com-qty) desc')
            ->select();

        $MainArr=[];

        foreach($DetailRs as $List){
            $combineid=$List['combineid'];
            if($combineid==0){
                $combineid='999';
            }
            $MainArr[$combineid.'.'.$List['ebay_id']][]=$List;  // 一个订单 一个数组
        }

        ksort($MainArr,SORT_NUMERIC);

        unset($DetailRs);
        return ['status'=>1,'msg'=>'查询成功','data'=>$MainArr];

    }


    // 2次分拣   3次分拣之后
    function ViewBadOrder($ordersn){
        $RR=$this->PickOrderModel->where(['ordersn'=>$ordersn])->find();

        $isprint      = $RR['isprint'];
        $cross_status = $RR['cross_status'];
        $cross        = $RR['is_cross'];
        if($isprint<2){
            return ['status' => 0, 'data' => '', 'msg' => '跨仓单' . $ordersn . ',必须在完成了 本仓分拣，集货仓分拣之后才能查看退回！！!'];
        }


        if($cross!=1){
            return ['status' => 0, 'data' => '', 'msg' => $ordersn . ',不是一个跨仓捡货单！ 你逗窝?'];
        }

        $map=[];
        $map['ordersn']=$ordersn;
        $map['is_delete']=1;

        $RR=$this->PickOrderDetailModel->where($map)->field('combineid,ebay_id')->group('ebay_id')->order('combineid asc')->select();

        $OrderBack=[];
        foreach($RR as $Order){
            $ebay_id=$Order['ebay_id'];
            $OrderBack[$Order['ebay_id']]=$this->PickOrderDetailModel->where(['ordersn'=>$ordersn,'ebay_id'=>$ebay_id])
                ->field('sku,qty')->select();
        }


        return ['status'=>1,'data'=>$RR,'skus'=>$OrderBack];

    }
}
