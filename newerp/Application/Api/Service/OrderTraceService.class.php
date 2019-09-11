<?php
namespace Api\Service;


use Common\Model\OrderModel;
use Mid\Model\MidEbayOrderModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\ApiOrderWeightModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderLogModel;
use Package\Model\PickOrderModel;

class OrderTraceService{

    function show(){
        $ebay_id=$_POST['ebay_id'];
        if($ebay_id==''){
            echo json_encode(['status'=>0,'msg'=>'订单号为空,无法查询!']);
            return ;
        }

        // 订单进入 WMS 的时间
        $OrderModel=new OrderModel();
        $RR=$OrderModel->where(compact('ebay_id'))
            ->field('from_unixtime(ebay_addtime) as e_addtime,w_add_time')->find();


        /**
        *测试人员谭 2017-11-17 16:39:25
        *说明: 如果只存在中间表中
        */
        if(empty($RR)){
            $OrderMidModel=new MidEbayOrderModel();
            $RR=$OrderMidModel->where(compact('ebay_id'))
                ->field('from_unixtime(ebay_addtime) as e_addtime,wms_add_time as w_add_time')->find();

            if(empty($RR)){
                echo json_encode(['status'=>0,'msg'=>'订单号未进入WMS']);
                return ;
            }

            $w_add_time=$RR['w_add_time'];

            $data[]='['.$w_add_time.'] 订单进入WMS--等待进入捡货流程';
            echo json_encode(['status'=>1,'msg'=>'查询成功','data'=>$data]);
            return ;
        }



        $w_add_time=$RR['w_add_time'];
        $data[]='['.$w_add_time.'] 订单进入WMS--等待捡货';


        //开始查询生成 捡货单的记录
        $PickOrderDetail=new PickOrderDetailModel();
        $SS=$PickOrderDetail->where(compact('ebay_id'))
            ->field('ordersn,ebay_id')->group('ordersn')
            ->order('id desc')->limit(1)->select();

        $PickOrder=new PickOrderModel();
        $PickOrderLog=new PickOrderLogModel();

       // debug($PickOrderDetail->_sql());

        foreach($SS as $List){

/*            $ordersn = $List['ordersn'];
            $addtime = $PickOrder->where(['ordersn' => $ordersn])->getField('addtime');
            $data[]  = '[' . date('Y-m-d H:i:s', $addtime) . '] 订单正在捡货,捡货单:'.$ordersn;
            $RR=$PickOrderLog->where(
                ['ordersn'=>$ordersn,
                  'note'=>['like',"%{$ebay_id}%"],
                    'addtime'=>['gt',$addtime]
            ])->field('note,addtime')->find();

            if($RR){
                $note=$RR['note'];
                $n_addtime=$RR['addtime'];
                if(strstr($note,'除')){  // 删除,移除
                    $data[]  = '[' . date('Y-m-d H:i:s', $n_addtime) . '] 订单捡货后货不够--退回等待捡货';
                }

            }else{

                $RR=$PickOrderLog->where(
                    ['ordersn'=>$ordersn,
                     'note'=>['like',"拣货单被确认了"],
                     'addtime'=>['gt',$addtime]
                    ])->field('note,addtime')->find();
                if($RR){
                    $data[]  = '[' . date('Y-m-d H:i:s', $RR['addtime']) . '] 订单----进入打包流程';
                }

            }*/

            $ordersn = $List['ordersn'];
            $addtime = $PickOrder->where(['ordersn' => $ordersn])->getField('addtime');
            $data[]  = '[' . date('Y-m-d H:i:s', $addtime) . '] 订单正在捡货,捡货单:'.$ordersn;

            $RR=$PickOrderLog->where(
                ['ordersn'=>$ordersn,
                    'note'=>['like',"拣货单被确认了"],
                    'addtime'=>['gt',$addtime]
                ])->field('note,addtime')->find();
            if($RR){
                 $data[]  = '[' . date('Y-m-d H:i:s', $RR['addtime']) . '] 订单----进入打包流程';
            }

        }



        //最后查 一下包装

        $OrderApisku=new ApiCheckskuModel();

        $RR=$OrderApisku->where(compact('ebay_id'))->find();
        if($RR){
            $addtime=$RR['addtime'];
            $data[]  = '[' . date('Y-m-d H:i:s', $addtime) . '] 订单验货包装完等待称重';
        }


        $OrderWeight=new ApiOrderWeightModel();
        // 最后最后 查一下 称重
        $RR=$OrderWeight->where(compact('ebay_id'))->find();
        if($RR){
            $scantime=$RR['scantime'];
            $data[]  = '[' . date('Y-m-d H:i:s', $scantime) . '] 订单称重完毕待同步重量';
        }


        echo json_encode(['status'=>1,'msg'=>'查询成功','data'=>$data]);
    }
}