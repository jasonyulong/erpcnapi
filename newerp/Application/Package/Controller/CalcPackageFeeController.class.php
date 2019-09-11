<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 20:51
 */

namespace Package\Controller;


use Common\Model\OrderModel;
use Package\Model\EbayGoodsModel;
use Package\Model\EbayOnHandleModel;
use Package\Model\OrderPackageModel;
use Package\Model\PackingMaterialModel;
use Package\Model\PickerOrderModel;
use Package\Model\PickFeeModel;
use Package\Model\PickFeeRuleModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Think\Controller;

/**
 |-------------------------------------------------------------------------------------
 |
 |  计算订单包装的费用
 |
 |  1、查找要计算的订单
 |  2、循环计算订单的包装费用
 |  3、将订单计算出来的包装费用填写到数据表中
 |-------------------------------------------------------------------------------------
 * Class CalcPackageFeeController
 * @package Package\Controller
 */
class CalcPackageFeeController extends Controller{



    public $FeeRule=[];
    public $PickFeeModel=null;

    private $reCalcDays=16;
    //private $reCalcDays=15;


    public function _initialize(){
        ini_set('memory_limit','500M');
        $this->PickFeeModel=new PickFeeModel();

        // 规则存表 中
        $PickFeeRule=new PickFeeRuleModel();

        $FeeRuleTemp=[];


        $RR=$PickFeeRule->order('type asc')->select();

        foreach($RR as $List){
            if($List['type']==1){
                $FeeRuleTemp[1][$List['name']]=$List['value'];
            }else{
                if($List['type']==2){
                    $name='单品多货';
                }else{
                    $name='多品多货';
                }
                $FeeRuleTemp[$List['type']]=['name'=>$name,'value'=>$List['value']];
            }
        }

        $this->FeeRule=$FeeRuleTemp;

        //print_r($FeeRuleTemp);die();
        unset($FeeRuleTemp);

    }






    /**
    *测试人员谭 2017-07-01 09:58:24
    *说明: 计算结果为0 的 补1补
    */

    function FixPickFee(){

        if(!IS_CLI){
            echo 'Must run in cli'."\n\n";
            die();
        }


        $OnHandle=new EbayOnHandleModel(); //包材藏在这里

        //ebay_packingmaterial  goods_sn,packingmaterial



        $map['fee']=0;
        $map['endtime']=['gt',strtotime('-'.$this->reCalcDays.' days')];

        $RR=$this->PickFeeModel->where($map)->field('id,ebay_id,sku,type,pkname')->select();

        echo 'Fixd:'.count($RR)."\n";

        foreach($RR as $List){
            $sku=$List['sku'];
            /**
            *测试人员谭 2017-07-01 10:31:03
            *说明: 重新查一遍 这个 包装材料是什么
            */
            $fff=$OnHandle->alias('a')->join("ebay_packingmaterial b on a.packingmaterial=b.id")
                ->where("a.goods_sn='$sku'")->field('b.model')->find();
           // echo $OnHandle->_sql()."\n";


            $pkname=$fff['model'];


            $fee=$this->getFee($List['type'],$pkname);

            $FeeGroup=$this->getFeeGroup($List['type'],$pkname);

            if(empty($pkname)){
                $pkname='';
            }

            echo $fee."\n";

            $add=[];

            $add['ebay_id']=$List['ebay_id'];

            $add['fee']=$fee;

            $add['pkname']=$pkname;

            $add['fee_group']=$FeeGroup;

            $add['id']=$List['id'];

            $this->PickFeeModel->save($add);
        }



    }


    /**
    *测试人员谭 2017-06-29 23:11:51
    *说明: 计算包装费用
    */

    //php tcli.php Package/CalcPackageFee/CalcBaledFee

    function CalcBaledFee(){

        if(!IS_CLI){
            echo 'Must run in cli'."\n\n";
            die();
        }

        ini_set('memory_limit','500M');



        $OrderPick       = new PickOrderModel();
        $OrderPickDetail = new PickOrderDetailModel();
        $PickFee         = new PickFeeModel();


       // $Goods=new EbayGoodsModel(); //产品辅助

        $OnHandle=new EbayOnHandleModel(); //包材藏在这里

        $PackMater=new PackingMaterialModel(); // 包材


        $PackMaterArr       = $PackMater->getField('id,model', true);
        $AllSKUPackMaterArr = $OnHandle->getField('goods_sn,packingmaterial', true);

        // debug($PackMaterArr);

       // die();

        $tt=strtotime('-7 days');

        $map['isprint']=3;
        $map['work_end']=['gt',$tt];
        $map['type'] = 3;

        $Porder=$OrderPick->where($map)->field('ordersn,baleuser,work_end,type')->select();

        $where['is_delete']=0;

        foreach($Porder as $Lis){
            $ordersn=$Lis['ordersn'];
            $rr=$PickFee->where("ordersn='$ordersn'")->find();
            if($rr){
                continue;
            }

            $baleuser = $Lis['baleuser'];
            $work_end = $Lis['work_end'];
            $type     = $Lis['type'];

            $where['ordersn']=$ordersn;

            $Detail= $OrderPickDetail->where($where)->field('sku,ebay_id')->group('ebay_id')->select();

            foreach($Detail as $List){

                $ebay_id=$List['ebay_id'];

                $sku=strtoupper($List['sku']);

                $packmodelid=$AllSKUPackMaterArr[$sku];

                $pacmname=$PackMaterArr[$packmodelid];

                if($pacmname==''&&$type==1){
                    $fee=0;
                }else{
                    $fee=$this->getFee($type,$pacmname);
                }

                $FeeGroup=$this->getFeeGroup($type,$pacmname);

                echo $FeeGroup."\n";
                if(empty($pacmname)){
                    $pacmname='';
                }

                $add=[];
                $add['ordersn']=$ordersn;
                $add['ebay_id']=$ebay_id;
                $add['pkuser']=$baleuser;
                $add['endtime']=$work_end;
                $add['fee']=$fee;
                $add['type']=$type;
                $add['sku']=$sku;
                $add['pkname']=$pacmname;
                $add['fee_group']=$FeeGroup;
                $this->saveFee($add);

            }

        }


    }



    private function saveFee($add){

        $ebay_id=$add['ebay_id'];

        $ss=$this->PickFeeModel->where("ebay_id=$ebay_id")->field('id')->find();

        if($ss){
            return ;
        }

        $this->PickFeeModel->add($add);

    }



    private function getFee($type,$pacmodelname){

        if($type==2||$type==3){
            return $this->FeeRule[$type]['value'];
        }

        $arr=$this->FeeRule[1];
        //echo $pacmodelname."\n";
        $val=0;
        foreach($arr as $k=>$List){
            $name=$k;
            $value=$List;
            if(strstr($pacmodelname,$name)!==false){
                return $value;
            }
        }
        return $val;
    }

    private function getFeeGroup($type,$pacmodelname){
        if($type==2||$type==3){
            return $this->FeeRule[$type]['name'];
        }


        $arr=$this->FeeRule[1];
        //echo $pacmodelname."\n";
        $str='异常';

        if($pacmodelname==''){
            return $str;
        }

        foreach($arr as $k=>$List){
            $name=$k;
            if(strstr($pacmodelname,$name)!==false){
                return $name;
            }
        }
        return $str;
    }




    /**
     *测试人员谭 2017-07-03 17:00:46
     *说明: 拣货统计
     */
    function pickerStatistic(){
        ini_set('memory_limit','512M');
        //picker_order
        $PickerOrderModel = new  PickerOrderModel();
        $PickOrderModel   = new  PickOrderModel();
        $PickOrderDetail  = new  PickOrderDetailModel();

        $tt=strtotime('-16 days');
        $PickOrderRs=$PickOrderModel->where("isprint=3 and work_end>$tt")
            ->field('pickuser,ordersn,type,work_end')->select();
        foreach($PickOrderRs as $List){
            $pickuser = $List['pickuser'];
            $ordersn  = $List['ordersn'];
            $type     = $List['type'];
            $work_end = $List['work_end'];
            $rs=$PickerOrderModel->where("ordersn='$ordersn'")->find();
            if($rs){
                continue;
            }

            $Detail=$PickOrderDetail->where("ordersn='$ordersn' and (is_delete=0 or (is_normal=0 and is_delete=1))")
                ->field('ebay_id,is_delete')->select();

            foreach($Detail as $vlist){
                $ebay_id   = $vlist['ebay_id'];
                $is_delete = $vlist['is_delete'];
                $ss=$PickerOrderModel->where("ordersn='$ordersn' and ebay_id=$ebay_id")->field('id')->find();
                if($ss){
                    continue;
                }

                $add=[];
                $add['picktime'] = $work_end;
                $add['ordersn'] = $ordersn;
                $add['ebay_id'] = $ebay_id;
                $add['type'] = $type;
                $add['pickuser'] = $pickuser;
                $add['is_delete'] = $is_delete;

                $PickerOrderModel->add($add);

            }



        }



    }

    /**
     * 新的拣货单，单品单货和单品多货包装费用统计
     * @author Shawn
     * @date 2018/7/19
     */
    public function calcBaledFeeByNewPickOrder(){
        if(!IS_CLI){
            echo 'Must run in cli'."\n\n";
            die();
        }
        //创建拣货单时间，没有传时间默认取7天前的
        if(isset($_SERVER['argv'][2])){
            $updateTime = strtotime($_SERVER['argv'][2]);
        }else{
            $updateTime = strtotime('-7 days');
        }
        ini_set('memory_limit','2000M');
        set_time_limit(0);
        $orderPackage = new OrderPackageModel();
        $pickOrder       = new PickOrderModel();
        $pickOrderDetail = new PickOrderDetailModel();
        $pickFee         = new PickFeeModel();
        $ebayOnHandle = new EbayOnHandleModel(); //包材藏在这里
        $packingMaterial = new PackingMaterialModel(); // 包材
        //获取包材id和包材名称
        $PackMaterArr       = $packingMaterial->getField('id,model', true);
        //获取产品sku和对应的包材id
        $AllSKUPackMaterArr = $ebayOnHandle->getField('goods_sn,packingmaterial', true);
                //删除的拣货单不需要
        $map['isprint'] = array("in",[0,1,2,3]);
        $map['addtime'] = ['gt',$updateTime];
        //只查单品单货和单品多货
        $map['type']    = ['lt',3];
        //查询出拣货单号
        $pickOrderData = $pickOrder->where($map)
                    ->field("ordersn,type")
                    ->select();
        //拣货详情查找已经包装，并且没有删除的
        $where['is_baled'] = 1;
        $where['is_delete'] = 0;
        foreach($pickOrderData as $value){
            $ordersn = $value['ordersn'];
            $type = $value['type'];
            //根据拣货单查找拣货详情数据
            $where['ordersn'] = $ordersn;
            $pickDetailData = $pickOrderDetail->where($where)
                ->field("sku,ebay_id")
                ->group('ebay_id')
                ->select();
            if(empty($pickOrderData)){
                continue;
            }
            $feeMap['ordersn'] = $ordersn;
            foreach ($pickDetailData as $v){
                $ebay_id = $v['ebay_id'];
                $sku = strtoupper($v['sku']);
                //检查下这个拣货单里面的订单是不是已经计算过了
                $feeMap['ebay_id'] = $ebay_id;
                $isCalc = $pickFee->where($feeMap)->find();
                if(!empty($isCalc)){
                    continue;
                }
                //找下包装人和包装时间
                $baleData = $orderPackage->where($feeMap)->field("baleuser,baletime")->find();
                if(empty($baleData)){
                    continue;
                }
                //找到sku对应包材
                $packModelId = $AllSKUPackMaterArr[$sku];
                $modelName = $PackMaterArr[$packModelId];
                //@todo 这里不太懂
                if(!isset($modelName) && $type == 1){
                    $fee = 0;
                }else{
                    $fee = $this->getFee($type,$modelName);
                }
                $feeGroup = $this->getFeeGroup($type,$modelName);
                if(empty($modelName)){
                    $modelName = '';
                }
                $add = [];
                $add['ordersn']     = $ordersn;
                $add['ebay_id']     = $ebay_id;
                $add['pkuser']      = $baleData['baleuser'];
                $add['endtime']     = $baleData['baletime'];
                $add['fee']         = $fee;
                $add['type']        = $type;
                $add['sku']         = $sku;
                $add['pkname']      = $modelName;
                $add['fee_group']   = $feeGroup;
                $this->saveFee($add);
            }
        }
        echo "done";exit;
    }
    
}