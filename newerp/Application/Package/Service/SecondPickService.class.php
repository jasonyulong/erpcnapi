<?php
namespace Package\Service;
use Order\Model\TwoPickCountModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailQrcodeModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;
use Package\Model\PickRecordModel;

/**
 * Class CreatePickService
 * @package Package\Service
 * 捡货单 2下次 分拣服务层
 */
class SecondPickService{


    /**
    *测试人员谭 2017-05-26 22:57:10
    *说明: 给当前的 拣货单子里面的 sku，分配一个  二次分拣的 小储物柜号
    */
    function getSecondPickLocation($ordersn,$sku,$qrcode,$sorting3=false){
        $PickOrderModel       = new PickOrderModel();

        $PickOrderDetailModel = new PickOrderDetailModel();
        $pickRecordModel      = new PickRecordModel();
        $pickOrderQrcodeModel = new PickOrderDetailQrcodeModel();
        $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint,is_work,sorting_status')->find();

        $isprint=$RR['isprint'];//
        $type=$RR['type'];   // 只针对 2 和 3
        $is_work=$RR['is_work'];
        $sorting_status = $RR['sorting_status'];

        if($sorting_status==0 && $sorting3){
            return ['status'=>0,'msg'=>'当前 拣货单二次分拣未结束 请先去二次分拣'];
        }

        if($sorting_status == 1 && !$sorting3){
            return ['status'=>0,'msg'=>'当前 拣货单二次分拣已结束 请去三次分拣'];
        }
        if($type==1){
            return ['status'=>0,'msg'=>'单品单件的 拣货单 不需要二次分拣'];
        }

        if($type==2){
            return ['status'=>0,'msg'=>'单品多件的 拣货单 不需要二次分拣'];
        }

        if($is_work){
            return ['status'=>0,'msg'=>'拣货单 已经开始打包了，不能二次分拣'];
        }

        if($isprint!=2){
            return ['status'=>0,'msg'=>'拣货单必须在待包装状态下才能二次分拣'];
        }


        /**
        *测试人员谭 2017-05-26 23:18:45
        *说明:
        */
        //先假设 这个sku 分给了一个 已经带有【分拣位置号】 或者，这个已经带有分拣号的订单
        $map['ordersn']   = $ordersn;
        $map['sku']       = $sku;
        $map['combineid'] = ['gt', 0];
        $map['is_stock']  = 0;
        $map['is_delete'] = 0;
        $map['_string'] = " (qty-qty_com)>0 ";

        $pickRecordAdd = [
            'adduser' => session('truename'),
            'addtime' => time(),
            'sku'     => $sku,
            'ordersn' => $ordersn
        ];

        $field='id,ebay_id,combineid';

        $RR=$PickOrderDetailModel->where($map)->field($field)->order('order_addtime asc')->find();

        if($RR){
            $id        = $RR['id'];
            $combineid = $RR['combineid'];
            $ebay_id = $RR['ebay_id'];
            $PickOrderDetailModel->where("id=$id")->setInc('qty_com');

            //检查如果扫描的是订单的最后一个sku往二次分拣表统计加一条数据
            $success = $this->saveTwoPick($ordersn,$ebay_id);

            //分拣记录
            $pickRecordAdd['combineid'] = $combineid;
            $pickRecordModel->add($pickRecordAdd);

            if(true==$success){
                $this->saveSKUString($ordersn,$ebay_id);
            }

            //返回剩余的
            $fieldd = "sku,(qty-qty_com) as is_notover";
            $returnRR=$PickOrderDetailModel->where(['ebay_id'=>$ebay_id,'(qty-qty_com)>0'])->field($fieldd)->order('order_addtime asc')->select();
            //记录二次分拣扫描的二维码 Shawn 2018-09-04
            if(!empty($qrcode) && strpos($qrcode,'$') !== false){
                $pickOrderQrcodeData['ebay_id'] = $ebay_id;
                $pickOrderQrcodeData['ordersn'] = $ordersn;
                $pickOrderQrcodeData['qrcode'] = $qrcode;
                $pickOrderQrcodeModel->addScanQrcodeData($pickOrderQrcodeData);
            }

            return ['status'=>1,'data'=>$combineid,'msg'=>'','success'=>$success,'returnRR'=>$returnRR];
        }


        /**
        *测试人员谭 2017-05-26 23:18:49
        *说明:
        */
        //再到 没有分配过 分拣位置 的订单里面查找
        $map['combineid'] = 0 ;
        $RR=$PickOrderDetailModel->where($map)->field($field)->order('order_addtime asc')->find();

        //没有分拣过的定单 没有找到
        if(!$RR){
            // 这里要检查一下所有的sku 是不是都完毕了
            unset($map['sku']);
            $map['combineid'] = ['gt', 0];
            $RS=$PickOrderDetailModel->where($map)->field('id')->find();
            if(!$RS){
                return ['status'=>0,'data'=>'','msg'=>'拣货单：'.$ordersn.' 已经分拣完毕!'];
            }

            return ['status'=>0,'data'=>'','msg'=>'未分拣的订单中找不到'.$sku.',请确认是否是前面已经扫描,或者SKU不属于本次拣货单'];
        }

        $ebay_id = $RR['ebay_id'];
        $id      = $RR['id'];

        $PickOrderDetailModel->where("id=$id")->setInc('qty_com');

        $map=[];
        $map['ordersn']   = $ordersn;
        $rs=$PickOrderDetailModel->where($map)->field('max(combineid) as cc')->find();
        $newcombineid=$rs['cc']+1;

        $map['ebay_id']   = $ebay_id;
        $Rs=$PickOrderDetailModel->where($map)->save(['combineid'=>$newcombineid]);

        //检查如果扫描的是订单的最后一个sku往二次分拣表统计加一条数据//
        //这里不可能是最后一个 sku 因为本次扫描只会增加一个sku，程序执行到这里是 因为没有分配好  combineid

        if($Rs){
            //分拣记录
            $pickRecordAdd['combineid'] = $newcombineid;
            $pickRecordModel->add($pickRecordAdd);

            //返回剩余的
            $fieldd = "sku,(qty-qty_com) as is_notover";
            $returnRR=$PickOrderDetailModel->where(['ebay_id'=>$ebay_id,'(qty-qty_com)>0'])->field($fieldd)->order('order_addtime asc')->select();
            //记录二次分拣扫描的二维码 Shawn 2018-09-04
            if(!empty($qrcode) && strpos($qrcode,'$') !== false ){
                $pickOrderQrcodeData['ebay_id'] = $ebay_id;
                $pickOrderQrcodeData['ordersn'] = $ordersn;
                $pickOrderQrcodeData['qrcode'] = $qrcode;
                $pickOrderQrcodeModel->addScanQrcodeData($pickOrderQrcodeData);
            }
            return ['status'=>1,'data'=>$newcombineid,'msg'=>'','returnRR'=>$returnRR];
        }

        return ['status'=>0,'data'=>'','msg'=>'未知原因失败了'];

    }

    //扫描的是否是订单的最后一个sku
    public function saveTwoPick($ordersn,$ebayid)
    {

        $towPickModel = new TwoPickCountModel();
        $map['ordersn']   = $ordersn;
        $map['combineid'] = ['gt', 0];
        $map['is_stock']  = 0;
        $map['is_delete'] = 0;
        $map['ebay_id'] = $ebayid;
        $map['_string'] = " (qty-qty_com)>0 ";
        $PickOrderDetailModel = new PickOrderDetailModel();
        $RS=$PickOrderDetailModel->where($map)->field('id')->find();

        // 没有找到 就说明真的是最后一个！！！！
        if (!$RS) {
            $towPickModel->where(['ebay_id' => $ebayid])->save(['status' => 1]);
            $saveDate = [
                'ebay_id'       => $ebayid,
                'ordersn'       => $ordersn,
                'pick_user'     => session('truename'),
                'end_pick_time' => time(),
            ];
            $towPickModel->add($saveDate);

            return true;
        }

        return false;

    }
     // 获取当前的 捡货单子
     function getSecondPickStatus($ordersn){
         $PickOrderModel       = new PickOrderModel();

         $PickOrderDetailModel = new PickOrderDetailModel();

         $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint')->find();

         $isprint=$RR['isprint'];//
         $type=$RR['type'];   // 只针对 2 和 3

         if($type==1){
             return ['status'=>0,'msg'=>'单品单件的 拣货单 不需要二次分拣'];
         }

         if($isprint!=2){
             return ['status'=>0,'msg'=>'拣货单必须在待包装状态下才能二次分拣'];
         }



         $map['ordersn']   = $ordersn;
         $map['is_stock']  = 0; // 不缺货
         $map['is_delete'] = 0; // 不删除
         $DetailRs=$PickOrderDetailModel->where($map)->field('ebay_id,sku,qty,location,goods_name,pic,order_addtime,combineid,qty_com,(qty-qty_com) as is_notover')
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


    /**
    *测试人员谭 2017-06-01 20:37:11
    *说明: 重新打单
    */
    function RePick($ordersn){

        $file=dirname(dirname(THINK_PATH)).'/log/package/'.date('YmdH').'.repick.txt';

        $PickOrderModel       = new PickOrderModel();
        $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint,is_work')->find();

        $isprint=$RR['isprint'];//
        $type=$RR['type'];   // 只针对 2 和 3
        $is_work=$RR['is_work'];

        if($type==1){
            return ['status'=>0,'msg'=>'单品单件的 拣货单 不需要二次分拣'];
        }

        if($isprint!=2){
            return ['status'=>0,'msg'=>'拣货单必须在已经确认状态下才能二次分拣'];
        }

        if($is_work){
            return ['status'=>0,'msg'=>'拣货单 已经开始打包了，不能二次分拣'];
        }

        //qty_com  combineid

        $PickOrderDetailModel = new PickOrderDetailModel();

        $combineid=0;
        $qty_com=0;

        $save['combineid'] = $combineid;
        $save['qty_com']   = $qty_com;

        $rr=$PickOrderDetailModel->where("ordersn='$ordersn'")->save($save);

        if($rr){
            return ['status'=>1,'msg'=>'<span style="color:#191;">重置二次分拣成功!</span>'];
        }else{
            return ['status'=>1,'msg'=>'<span style="color:#911;">重置二次分拣失败!</span>'];
        }


    }


    /**
    *测试人员谭 2017-07-05 12:00:14
    *说明: 二次分拣的时候 吧订单的sku 分解出来
    */

    function saveSKUString($ordersn,$ebay_id){
        $PickOrderDetailSkustrModel=new PickOrderDetailSkustrModel();
        $map['ordersn']=$ordersn;
        $map['ebay_id']=$ebay_id;
        $PickOrderDetailSkustrModel->where($map)->limit(1)->delete();

        $PickOrderDetailModel=new PickOrderDetailModel();

        $Arr=$PickOrderDetailModel->where($map)->field('sku,qty')->select();
        if(count($Arr)<2){
            return false;
        }


        $skuArr=[];
        foreach($Arr as $List){
            $skuArr[strtoupper($List['sku'])]=$List['qty'];
        }

        ksort($skuArr);
        $str=json_encode($skuArr);
        $map['skustr']=$str;
        $PickOrderDetailSkustrModel->add($map);

    }
}
