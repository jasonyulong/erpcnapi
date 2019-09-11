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
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Think\Controller;

class TempController extends Controller{


    function fixedLocation(){
	
    	$storeid=C('CURRENT_STORE_ID');
    	
		$map['ordersn']=I('ordersn');
		$map['store_id']=C('CURRENT_STORE_ID');
		
        print_r($map);
        
        $PickOrderdetai=new PickOrderDetailModel();
        $RR=$PickOrderdetai->where($map)->field('id,sku')->select();

        print_r($PickOrderdetai->_sql());
        
        //  $Handle=new EbayOnHandleModel();

        foreach($RR as $List){
            $id=$List['id'];
            $sku=$List['sku'];
            $locatin=M('ebay_onhandle_'.$storeid)->where(['goods_sn'=>$sku])->getField('g_location');

            echo $sku.",".$locatin."\n";
            if(!empty($locatin)){
                $PickOrderdetai->where(['id'=>$id])->save(['location'=>$locatin]);
            }
        }

    }


    function fixedLocation2(){
        $map['b.isprint']=1;

        $PickOrderdetai=new PickOrderDetailModel();
        
		$storeid=C('CURRENT_STORE_ID');

        $RR=$PickOrderdetai->alias('a')
            ->join('pick_order b using(ordersn)')
            ->where($map)->field('a.id,a.sku')->select();

        //debug($PickOrderdetai->_sql());die();
        //  $Handle=new EbayOnHandleModel();

        $Cache=[];

        foreach($RR as $List){
            $id=$List['id'];
            $sku=$List['sku'];
            $locatin=$Cache[$sku];
            if(empty($locatin)){
                $locatin=M('ebay_onhandle_'.$storeid)->where(['goods_sn'=>$sku])->getField('g_location');
                $Cache[$sku]=$locatin;
            }

            echo $sku.",".$locatin."\n";
            if(!empty($locatin)){
                $PickOrderdetai->where(['id'=>$id])->save(['location'=>$locatin]);
            }
        }

    }



    /**
     *测试人员谭 2017-12-10 17:33:49
     *说明: 更新 库位
     */
    function updateLocationByordersn(){
        $ordern=I('ordersn');
        echo $ordern."\n\n";

        if(empty($ordern)){
            echo "空!";
            die();
        }
	
		$storeid=C('CURRENT_STORE_ID');
        //die();

        $PickOrderdetai=new PickOrderDetailModel();
        $RR=$PickOrderdetai->where(['ordersn'=>$ordern])->field('id,sku')->select();
        foreach($RR as $List){
            $id=$List['id'];
            $sku=$List['sku'];
            $locatin=M('ebay_onhandle_'.$storeid)->where(['goods_sn'=>$sku])->getField('g_location');

            echo $sku.",".$locatin."\n";
            if(!empty($locatin)){
                $PickOrderdetai->where(['id'=>$id])->save(['location'=>$locatin]);
            }
        }

    }


    function checkOrder1745(){
        $RR=M('erp_ebay_order');
        $sql="SELECT a.ebay_id FROM erp_ebay_order a JOIN erp_order_type b USING(ebay_id)
WHERE a.ebay_status=1745
AND b.pick_status=1";

        $Rs=$RR->query($sql);


        echo '<meta charset="utf-8">';
        echo '<h1>1745状态 erp_ebay_order.pick_status=1 检查</h1>';
        foreach($Rs as $List){

            $ebay_id=$List['ebay_id'];
            $SS=$RR->query("SELECT ordersn FROM pick_order_detail WHERE ebay_id='".$ebay_id."' AND is_delete=0 GROUP BY ordersn");

            $ordersns='';
            foreach($SS as $lis){
                $ordersns.=$lis['ordersn'].',';
            }


            if(count($SS)>=2){
                echo '<div>'.$ebay_id.',<span style="color:#911">有重单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==1){
                // echo '<div>'.$ebay_id.',<span style="color:#191">正常单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==0){
                echo '<div>'.$ebay_id.',<span style="color:#981">无重单,但是无法打印</span></div>';
                //$up= "update erp_ebay_order set ebay_status=1723 where ebay_status=1745 and ebay_id='$ebay_id' limit 1";
                //echo $up."<br>";
                // $RR->execute($up);



            }

        }

    }

    function checkOrder1745_2(){
        $RR=M('erp_ebay_order');
        $sql="SELECT a.ebay_id FROM erp_ebay_order a JOIN erp_order_type b USING(ebay_id)
WHERE a.ebay_status=1745
AND b.pick_status=2";

        $Rs=$RR->query($sql);


        echo '<meta charset="utf-8">';
        echo '<h1>1745状态 erp_ebay_order.pick_status=2 检查</h1>';
        foreach($Rs as $List){

            $ebay_id=$List['ebay_id'];
            $SS=$RR->query("SELECT ordersn FROM pick_order_detail WHERE ebay_id='".$ebay_id."' AND is_delete=0 GROUP BY ordersn");

            $ordersns='';
            foreach($SS as $lis){
                $ordersns.=$lis['ordersn'].',';
            }


            if(count($SS)>=2){
                echo '<div>'.$ebay_id.',<span style="color:#911">有重单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==1){
                // echo '<div>'.$ebay_id.',<span style="color:#191">正常单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==0){
                echo '<div>'.$ebay_id.',<span style="color:#981">无单,但是无法打印</span></div>';
                $up= "update erp_ebay_order set ebay_status=1723 where ebay_status=1745 and ebay_id='$ebay_id' limit 1";
                echo $up."<br>";
                //如果此处开始执行这个修改操作,请告诉下王模刚 因为更新到1723是需要同步更新时间的
                // $RR->execute($up);

                $up="update erp_order_type set pick_status=1 where ebay_id='$ebay_id' and pick_status=2 limit 1";
                echo $up."<br>";
                //$RR->execute($up);
            }

        }

    }

    function checkOrder1723(){
        $RR=M('erp_ebay_order');
        $sql="SELECT a.ebay_id FROM erp_ebay_order a JOIN erp_order_type b USING(ebay_id)
WHERE a.ebay_status=1723
AND b.pick_status=1";

        $Rs=$RR->query($sql);


        echo '<meta charset="utf-8">';
        echo '<h1>1723状态erp_ebay_order.pick_status=1检查</h1>';
        foreach($Rs as $List){

            $ebay_id=$List['ebay_id'];
            $SS=$RR->query("SELECT ordersn FROM pick_order_detail WHERE ebay_id='".$ebay_id."' AND is_delete=0 GROUP BY ordersn");

            $ordersns='';
            foreach($SS as $lis){
                $ordersns.=$lis['ordersn'].',';
            }


            if(count($SS)>=2){
                echo '<div>'.$ebay_id.',<span style="color:#911">有重单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==1){
                echo '<div>'.$ebay_id.',<span style="color:#991">不应该有单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==0){
                // echo '<div>'.$ebay_id.',<span style="color:#981">无重单,无异常</span></div>';
            }

        }

    }

    function checkOrder1723_2(){
        $RR=M('erp_ebay_order');
        $sql="SELECT a.ebay_id FROM erp_ebay_order a JOIN erp_order_type b USING(ebay_id)
WHERE a.ebay_status=1723
AND b.pick_status=2";

        $Rs=$RR->query($sql);


        echo '<meta charset="utf-8">';
        echo '<h1>1723状态erp_ebay_order.pick_status=2检查</h1>';
        foreach($Rs as $List){

            $ebay_id=$List['ebay_id'];
            $SS=$RR->query("SELECT ordersn FROM pick_order_detail WHERE ebay_id='".$ebay_id."' AND is_delete=0 GROUP BY ordersn");

            $ordersns='';
            foreach($SS as $lis){
                $ordersns.=$lis['ordersn'].',';
            }


            if(count($SS)>=2){
                echo '<div>'.$ebay_id.',<span style="color:#911">有重单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==1){
                echo '<div>'.$ebay_id.',<span style="color:#191">异常单：'.trim($ordersns,',').'</span></div>';
            }elseif(count($SS)==0){
                echo '<div>'.$ebay_id.',<span style="color:#981">无重单,但是是异常!!</span></div>';
            }

        }

    }

    function getsku(){
        $intersku=new InternalStoreSkuModel();
        $PickOrderDetailModel=new PickOrderDetailModel();
        $skus=$intersku->where(['sku'=>['neq','T0025']])->field('sku')->select();
        //debug($intersku->_sql());
        //debug($skus);
        foreach($skus as $lis){
            $sku=$lis['sku'];
            $count=$PickOrderDetailModel->alias('a')->join('inner join pick_order b using(ordersn)')
                ->where([
                    'a.sku'=>$sku,
                    // 'a.is_normal'=>1,
                    'a.is_delete'=>0,
                    'b.isprint'=>['in',[1,2]], //0=没打印， 1=已打印未确认 2=已经确认 3=完成 100=删除
                ])->field('sum(a.qty) as cc')->find();

            $count=(int)$count['cc'];

            echo $sku,',',$count.'<br>';
        }
    }


    /**
     *测试人员谭 2018-01-03 15:19:11
     *说明: 不在捡货打回，无法生成 订单的
     */
    function checkOrders(){
        $OrderModel=new OrderModel();

        $OrderType=new OrderTypeModel();
        $ApiCheckskuModel=new ApiCheckskuModel();

        $map['ebay_status']=['in',[1723,1724,1745]];
        $RR=$OrderModel->where($map)->field('ebay_id,ebay_status')->select();


        $PickOrderDetail=new PickOrderDetailModel();
        $PickOrderModel=new PickOrderModel();


        $str1='';
        $str2='';
        $str3='';
        $str4='';
        foreach($RR as $List){
            $ebay_id=$List['ebay_id'];
            $ebay_status=$List['ebay_status'];


            $Rss=$ApiCheckskuModel->where(['ebay_id'=>$ebay_id])->field('id')->find();
            if($Rss){
                continue;
            }
            //正常的捡货单有木有
            $ddd=$PickOrderDetail
                ->where(['is_delete'=>0,'is_normal'=>1,'ebay_id'=>$ebay_id])
                ->field('ebay_id,ordersn')->find();

            $Rs=$OrderType->where(['ebay_id'=>$ebay_id])->getField('pick_status');


            if ($Rs == 3) {
                continue;
            }


            if($ddd){
                $ordersn=$ddd['ordersn'];
                $ss=$PickOrderModel->where(['ordersn'=>$ordersn])->getField('isprint');
                if($ss>=3){
                    //echo $ebay_id.'---error'."\n\n";
                    $str3.= $ebay_id." 没法生成捡货单也不在打回(捡货单不是处理中) \n";
                    continue;
                }else{
                    // 这里是正在捡货  正常的------
                    continue;
                }

            }




            if($Rs==''){
                $str1.= $ebay_id." 没初始化订单 \n";
                continue;
            }



            if($Rs==2){ // 这个表示 已经生成了 捡货单子
                $str3.= $ebay_id." 没法生成捡货单也不在打回 （type是已经生成了捡货单）\n";
            }


            if($Rs==1){
                if($ebay_status==1745||$ebay_status==1724){
                    $str3.= $ebay_id." 没法生成捡货单也不在打回 （1724 or 1745 但是 type=1）\n";
                    continue;
                }
            }
        }


        echo $str1;
        echo $str2;
        echo $str3;
        echo $str4;

    }


    function  test(){
        $this->PickOrderDetailModel=new PickOrderDetailModel();

        $pickDetail = $this->PickOrderDetailModel->alias('a')
            ->join('inner join pick_order b using(ordersn)')
            ->where([
                'a.ebay_id' => 9573852,
                'a.is_delete' => 0,
                'a.is_normal' =>1,
                'b.isprint'   =>['in',[0,1,2]] // 加入a表的目的是 如果捡货单 不是正常的处理状态  还是当作 已经生成捡货单这个事情不存在
            ])
            ->field('a.id')
            ->order('a.order_addtime desc')
            ->find();

         echo $this->PickOrderDetailModel->_sql();
    }
}
