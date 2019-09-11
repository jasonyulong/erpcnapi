<?php
namespace Package\Service;
use Common\Model\EbayUserModel;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderLogModel;
use Package\Model\PickOrderModel;

/**
 * Class CreatePickService
 * @package Package\Service
 * 确认捡货单 服务层
 */
class ConfirmService extends CreatePickService{


    /**
    *测试人员谭 2017-05-25 16:50:05
    *说明: 删除某一个 捡货单，整个地删除
    */
    function deleteOnePickOrder($ordersn){
        $PickOrderModel       = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();

        //第一步 验证这个捡货单
        $OrderRs=$PickOrderModel->where("ordersn='$ordersn'")->field('isprint')->find();
        if(!$OrderRs){
            return ['status'=>0,'msg'=>'拣货单'.$ordersn.'不存在!'];
        }

        $isprint = $OrderRs['isprint'];

        if($isprint!=1){
            return ['status'=>0,'msg'=>'拣货单'.$ordersn.'不是[已打印未确认]状态，不允许操作!'];
        }

        $DetailArr=$PickOrderDetailModel->where("ordersn='$ordersn' and status=1")
            ->field('ebay_id')->group('ebay_id') -> select();

        $data['status']=1;
        $data['msg']='删除拣货单成功';
        foreach($DetailArr as $List){
            $ebay_id=$List['ebay_id'];
            $rs=$this->deleteOneOrder($ordersn,$ebay_id);
            if(!$rs['status']){
                return ['status'=>0,'msg'=>'移除拣订单中订单:'.$ebay_id." 失败，请中断操作联系IT,".$rs['msg']];
            }
        }

        return $data;

    }

    /**
    *测试人员谭 2017-05-25 16:36:02
    *说明: 删除某一个捡货单的详情
    */
    function deleteOneOrder($ordersn,$ebay_id){
        $file=dirname(dirname(THINK_PATH)).'/log/package/'.date('YmdH').'.info.txt';
        $PickOrderModel       = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();

        //第一步 验证这个捡货单
        $OrderRs=$PickOrderModel->where("ordersn='$ordersn'")->field('isprint')->find();
        if(!$OrderRs){
            return ['status'=>0,'msg'=>'拣货单'.$ordersn.'不存在!'];
        }

        $isprint = $OrderRs['isprint'];



        if($isprint!=1){
            return ['status'=>0,'msg'=>'拣货单'.$ordersn.'不是[已打印未确认]状态，不允许操作!'];
        }

        //这里是可能有多个数据的!但是订单删除必须是一致的 所以取一个 也可以
        $OrderDetailRs=$PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id='$ebay_id'")
            ->field('status,is_delete')->find();

        if(!$OrderDetailRs){
            return ['status'=>0,'msg'=>'拣货单明细'.$ordersn.','.$ebay_id.'不存在!'];
        }

        $detailstatus = $OrderDetailRs['status'];
        $is_delete    = $OrderDetailRs['is_delete'];

        if($is_delete>0){
            return ['status'=>0,'msg'=>'拣货单明细'.$ordersn.','.$ebay_id.' 已经删除了!'];
        }

        if($detailstatus!=1){
            return ['status'=>0,'msg'=>'拣货单明细'.$ordersn.','.$ebay_id.' 状态 已经分配了 不允许移除!'];
        }

        $rs=$this->setOrderCancelCreated($ebay_id);

        if(!$rs['status']){
            return ['status'=>0,'msg'=>'订单'.$ebay_id.' 设置为未拣货状态失败：'.$rs['msg']];
        }

        // 已经成功地 设置为未拣货状态
        // 开始修Order数据
        $data=[];
        $data['is_delete']=1;

        // 注意 这里可能有多个数据
        $rs=$PickOrderDetailModel->where("ordersn='$ordersn' and ebay_id='$ebay_id'")->save($data);

        if($rs!==false){
            //日志
            $PickOrderLog=new PickOrderLogModel('','',C('DB_CONFIG2'));
            $log='拣货单的订单'.$ebay_id."被移除了";
            $PickOrderLog->addOneLog($ordersn,$log);
            //检查主单子 要不要 设置为删除态
            $this->PickOrderNeedDelete($ordersn);
            return ['status'=>1,'msg'=>'拣货单的订单'.$ebay_id.'被移除成功'];
        }

        return ['status'=>0,'msg'=>'拣货单的订单'.$ebay_id.'被移除失败'];

    }



    //检查 是不是要删掉 捡货单
    function PickOrderNeedDelete($ordersn){
        $PickOrderDetailModel = new PickOrderDetailModel();


        // 查没有被删除的有几多？
        $rs=$PickOrderDetailModel->where("ordersn='$ordersn' and is_delete=0")->field('id')->select();

        /**
        *测试人员谭 2017-05-25 16:43:04
        *说明: 一个捡货单的 有效详情 已经没有了，这个单活着也没什么意思了
        */
        if(count($rs)>0){
            return false;
        }

        $data['isprint']=100;

        $PickOrderModel       = new PickOrderModel();

        $rs=$PickOrderModel->where("ordersn='$ordersn'")->save($data);

        if($rs==1){
            $PickOrderLog=new PickOrderLogModel();
            $log='捡货单 检测到已经没有明细数据了，被系统删除';
            $PickOrderLog->addOneLog($ordersn,$log);
        }

        return true;
    }


    //根据确认缓存表来分配数据
    //分配之后 需要立即
    function allocationInventoryBYConfirm($ordersn){
        $pick_order_confirm=new PickOrderConfirmModel();
        $Checks=$pick_order_confirm->checkStatus($ordersn);

        if(!$Checks){
            return ['status'=>0,'msg'=>"无法分配库存,请将拣货单{$ordersn}中所有sku全部填写完毕(即使sku为0)"];
        }


        $PickOrderModel       = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();

        //检查捡货单 是不是处于 可以分配的状态
        $OrderRs=$PickOrderModel->where("ordersn='$ordersn'")->field('isprint')->find();
        $isprint = $OrderRs['isprint'];
        if($isprint!=1){
            return ['status'=>0,'msg'=>"无法分配库存,拣货单{$ordersn}不在【已打印未确认】"];
        }


        // 缓存大战!  Pick_order_detail  缓存和 PickOrderConfirm 缓存大战
        $ErpOrderSKU=$PickOrderDetailModel->getErpOrderDetail($ordersn);
        //真实捡到的 sku数量
        $CacheSKU=$pick_order_confirm->getRealQty($ordersn);



        //就像订单分配库存逻辑一样，可怜的订单 好不容易分配到了库存
        //结果到了拣货的时候 发现库存少了，
        //还不知道能不能真实地分配到

        $SuccesOrder=[]; // 成功分配的单子
        $OutStockOrder=[]; // 缺货的单子
        //开始分配
        foreach($ErpOrderSKU as $ebay_id=>$Order){

            $is_out_stock=0; // 是不是
            foreach($Order as $SkuNeed){
                 $sku=$SkuNeed['sku'];
                 $qty=$SkuNeed['qty'];

                if(!array_key_exists($sku,$CacheSKU)){
                    return ['status'=>0,'msg'=>"严重错误,拣货单{$ordersn}确认信息里居然没有这个SKU:{$sku}"];
                }

                if($CacheSKU[$sku]<$qty){
                    $is_out_stock=1;
                }
            }

            if($is_out_stock==1){
                $OutStockOrder[]=$ebay_id;
                continue;
            }


            $SuccesOrder[]=$ebay_id;
            //假装吧库存占用
            foreach($Order as $SkuNeed){
                $sku=$SkuNeed['sku'];
                $qty=$SkuNeed['qty'];
                $CacheSKU[$sku]-=$qty;
            }


        }


        // 该标记为缺货的订单标记为缺货
        foreach($OutStockOrder as $val){
            $PickOrderDetailModel->setOutStock($ordersn,$val);
        }

        return ['status'=>1,'msg'=>'分配成功','data'=>$SuccesOrder];

    }


    // 删除 分配不到库存的 订单
    function DeleteStockOrder($ordersn){
        $PickOrderDetailModel = new PickOrderDetailModel('','',C('DB_CONFIG2'));
        $map['ordersn']=$ordersn;
        $map['is_delete']=0; // 没有删掉的
        $map['is_stock']=1;  // 已经缺货的

        $DetailArr=$PickOrderDetailModel->where($map)->field('ebay_id')->group('ebay_id')->select();

        $data['status']=1;
        $data['msg']='删除拣货单 缺货订单成功';
        foreach($DetailArr as $List){
            $ebay_id=$List['ebay_id'];
            $rs=$this->deleteOneOrder($ordersn,$ebay_id);
            if(!$rs['status']){
                return ['status'=>0,'msg'=>'移除拣订单中订单:'.$ebay_id." 失败，请中断操作联系IT,".$rs['msg']];
            }
        }

        return $data;
    }


    /**
    *测试人员谭 2017-06-06 14:54:17
    *说明: TODO  这个方法 作用是 捡货回来之后 确认了数量，然后，确认的数量（真正捡到的sku库存） 分配给捡货单包含的订单。
    */
    // 确认之后 还有结余的 库存 列表
    function getBackSKU($ordersn){
        $PickOrderModel       = new PickOrderModel();
        $PickOrderDetailModel = new PickOrderDetailModel();

        $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('isprint')->find();

        $isprint=$RR['isprint'];

        if($isprint!=2){
            //已经确认
            return ['status'=>0,'msg'=>'拣货单必须是【已经确认】才能查看应退回库存'];
        }


        // 查询每一个sku 要多少
        $pick_order_confirm=new PickOrderConfirmModel();
        $CacheSKU=$pick_order_confirm->getRealQty($ordersn);

        $map['is_stock']  = 0; //不缺货
        $map['is_delete'] = 0; //没删掉
       // $map['status']    = 1; //正常状态
        $map['ordersn']   = $ordersn;

        $filed='sku,sum(qty) as cc,goods_name,location,pic';
        $RR=$PickOrderDetailModel->where($map)->field($filed)->group('sku')->select();
        //echo $PickOrderDetailModel->_sql();
        $BackSKU=[];



        foreach($RR as $List){
            $cc       = $List['cc'];
            $sku      = $List['sku'];
            $location = $List['location'];
            $backqty=$CacheSKU[$sku]-$cc; // 总共 捡回来的个数 减去 拼凑完整的订单 sku 综合

            unset($CacheSKU[$sku]);

            if($backqty>0){
                $List['backqty']=$backqty;
                $BackSKU[$location.'_'.$sku]=$List;
            }

        }
        //debug($BackSKU);

        //要退回的  这些查不到的 有点订单删掉了哦怎么搞 有意思
        foreach($CacheSKU as $sku=>$qty){
            $RR=$PickOrderDetailModel->where("ordersn='$ordersn' and sku='$sku'")->field('sku,goods_name,location,pic')->find();
            $RR['backqty']=$qty;
            if($qty==0){
                continue;
            }
            $location=$RR['location'];
            $BackSKU[$location.'_'.$sku]=$RR;
        }

        ksort($BackSKU); // 按照库位排序
        return ['status'=>1,'msg'=>'查询成功','data'=>$BackSKU];

    }


    /**
    *测试人员谭 2017-05-25 20:13:58
    *说明: 确认拣货单据  重要操作 必须要严重提醒
    */
    function ConFirmPickOrder($ordersn){

        // 还是要分配一遍
        $rs=$this->allocationInventoryBYConfirm($ordersn);
        if(!$rs['status']){
            $rs['msg']='确认失败:'.$rs['msg'];
            return $rs;
        }

        /**
        *测试人员谭 2017-05-31 18:09:26
        *说明: 拣货单的--- 分配到库存的单子 全部转为 等待扫描
        */
        $rs=$this->setOrderToWriteScan($rs['data']);
//        dump($rs);die;
        if(!$rs['status']){
            $rs['msg']='确认失败:'.$rs['msg'];
            return $rs;
        }

        /**
        *测试人员谭 2017-05-25 20:26:28
        *说明: 删掉 分配失败的角色
        */
        $rs=$this->DeleteStockOrder($ordersn);

        if(!$rs['status']){
            return ['status'=>0,'msg'=>'确认失败:'.$rs['msg']];
        }

        $piuserid=$_REQUEST['userid'];
        $User=new EbayUserModel();
        $UserName=$User->getusername($piuserid);

        $data=[];
        $data['isprint']=2;
        $data['pickuser']=$UserName;
        $data['pick_end']=time();


        $PickOrderModel       = new PickOrderModel();

        $rs=$PickOrderModel->where("ordersn='$ordersn'")->save($data);

        if($rs){
            $PickOrderLog=new PickOrderLogModel();
            $log='拣货单被确认了';
            $PickOrderLog->addOneLog($ordersn,$log);

            return ['status'=>1,'msg'=>'确认成功了'];
        }

        return ['status'=>0,'msg'=>'确认失败了,请停止操作联系IT'];

    }
}
