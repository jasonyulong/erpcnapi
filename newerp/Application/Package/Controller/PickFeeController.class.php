<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Package\Model\EbayGoodsModel;
use Package\Model\PickFeeModel;
use Package\Model\PickFeeRuleModel;

/**
 * Class CreatePickController
 * @package Package\Controller
 *
 */
class PickFeeController extends CommonController{

    public function _initialize()
    {
        if (!can('order_package_statistic')) {
            echo "<h1 style='color: #911'> 您没有权限查看订单包装统计费用的权限. </h1>";
            exit(-1);
        }

        parent::_initialize();
    }


    function index(){
        $pmodel=new PickFeeRuleModel('', '', C('DB_CONFIG2'));
        $RR1=$pmodel->order('type asc')->select();
       // debug($RR1);
        $typeName=array(
            '1'=>'单品单货',
            '2'=>'单品多货',
            '3'=>'多品多货'
        );

        $this -> assign('TypeName', $typeName);
        $this -> assign('Data', $RR1);
        $this -> display('rule');

    }

    function saveRule(){
        $str=urldecode($_POST['str']);
        $str=trim($str,'@');
        $strArr=explode('@',$str);

       // debug($strArr);die();

        $pmodel=new PickFeeRuleModel('', '', C('DB_CONFIG2'));

        $data['status']=1;
        $data['msg']='';

        $i=0;
        $j=0;
        foreach($strArr as $Lis){
            $i++;
            $Data=explode(':',$Lis);
            $id=$Data[0];
            $val=$Data[1];

            if($id>0&&$val>0){
                $RR=$pmodel->save(['id'=>$id,'value'=>$val]);
                if($RR===false){
                    $j++;
                }
            }else{
                $j++;
            }
        }

        if($j==0){
            $data['msg']='更新成功!';

        }elseif($j==$i){
            $data['msg']='全部失败!';  $data['status']=0;
        }elseif($j<$i){
            $data['msg']='部分成功!';$data['status']=0;
        }

        echo json_encode($data);
    }



    function ZreoFeeSKU(){
        $PickFee= new PickFeeModel('', '', C('DB_CONFIG2'));

        $RR=$PickFee->where("fee=0")->group('sku')->select();

        $Gname=[];
        $Goods=new EbayGoodsModel();
        foreach($RR as $List){
            $sku=$List['sku'];
            $Gname[$sku]=$Goods->where("goods_sn='$sku'")->getField('goods_name');
        }

        $this -> assign('Gname', $Gname);
        $this -> assign('Data', $RR);
        $this -> display('zerofeesku');
    }
}