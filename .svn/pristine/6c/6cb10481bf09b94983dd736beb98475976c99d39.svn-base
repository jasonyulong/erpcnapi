<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 20:51
 */

namespace Package\Controller;

use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\EbayOnHandleModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;
use Think\Controller;

class UnLockController extends Controller{

    /**
    *测试人员谭 2018-07-30 17:07:55
    *说明: 自动解锁
    */
    function unlockOrders(){
         if(!IS_CLI){
             echo 'must run in cli';
             return ;
         }


        //===============

        $_time=180; // 180s 之后  你还不包装！！！！ 那这个订单 就归别人了

        $PickModel=new PickOrderModel('','',C('DB_CONFIG_READ'));

        $PickDetailModel       = new PickOrderDetailModel('', '', C('DB_CONFIG_READ'));
        $PickDetailWrite       = new PickOrderDetailModel();
        $logMolde              = new OrderslogModel();
        $PickOrderDetailSkustr = new PickOrderDetailSkustrModel();

        $map=[];
        //先查询所有拣货单
        $map['isprint'] = array('lt',3);
        $map['type'] = array('in',[1,2,3]);

        $ordersns = $PickModel->where($map)->getField("ordersn",true);
        if(empty($ordersns)){
            echo "未找到未完成的 多品拣货单\n\n";
        }


        $pickMap['scan_time']    = ['lt',time()-$_time];  //没有包装好的
        $pickMap['is_baled']    = 0;  //没有包装好的
        $pickMap['is_delete']    = 0;  //没有移除
        //$pickMap['isjump']    = 0;  //没有跳过的
        $pickMap['scaning'] = 1;     //锁定扫描过的
        $pickMap['ordersn'] = array('in',$ordersns);

        $Rs=$PickDetailModel->where($pickMap)->field('id,scan_time,scan_user,ebay_id,ordersn,qty')->select();


        //print_r($Rs);
        //return;
        foreach($Rs as $List){
            $tt=time()-$_time; // 当前时间之前的 180 秒
            $id=$List['id'];
            $ebay_id=$List['ebay_id'];
            $scan_time=$List['scan_time'];
            $scan_user=$List['scan_user'];
            $ordersn=$List['ordersn'];
            $qty=$List['qty'];


            $scan_time=date('Y-m-d H:i:s',$scan_time);


            $map=[];
            $map['id']=$id;
            $map['scan_time']=['lt',$tt]; // 第一次扫描时间 已经过了 180 秒
            $map['scaning']=1; // 第一枪已经扫描了
            $map['is_delete']=0; // 没删掉


            if($qty>100 && $tt < 600){
                echo '订单有100pcs 3分钟不够用'."\n";
                continue;
            }


            $save=[];
            $save['scan_time']=0;
            $save['scan_user']='';
            $save['scaning']=0;




            $type=$PickModel->where(['ordersn'=>$ordersn])->getField('type');

            echo $ebay_id,',',$ordersn,"\n";

            if(3==$type){
                $rr=$PickDetailWrite->where($map)->save($save);
            }else{
                $rr=$PickDetailWrite->where($map)->limit(1)->save($save);
            }



            /**
            *测试人员谭 2018-08-04 13:11:16
            *说明: type=3 真正的解锁是这里！！！！！！
            */
            if($type==3){
                $where=[];
                $where['ordersn']=$ordersn;
                $where['ebay_id']=$ebay_id;
                $PickOrderDetailSkustr->where($where)->limit(1)->save($save);
            }



            if($rr>0){ // 表示 实实在在地 影响了 一行
                $log="订单 {$ebay_id} 拣货单{$ordersn}， 由于超过 {$_time}秒没动静 系统自动清理掉包装员 原来包装员：{$scan_user},锁定时间:{$scan_time}" ;
                $logMolde->addordernote($ebay_id,$log);
                //return;
            }
        }

    }
}
