<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ErpEbayGoodsModel;
use Common\Model\OrderModel;
use Order\Model\EbayCarrierModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\BeltLayerModel;
use Package\Model\EbayGoodsModel;
use Package\Model\OrderPackageModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\WhiteOrderModel;
use Package\Service\MakeBaleService;

/**
 * Class CreatePickController
 * @package Package\Controller
 * 传送带设置
 */
class BeltController extends CommonController{

    /**
    *测试人员谭 2018-07-26 15:29:45
    *说明: 传送带上面的物流方式是啥子
    */

    public function index(){
        $BeltLayer=new BeltLayerModel();
        $Layers=$BeltLayer->order('id desc')->getField('id,carrier');

       // print_r($Layers);

        $Carrier=new EbayCarrierModel();

        $Carriers=$Carrier->getInlandCarrier('');

        $this->assign('Carriers',$Carriers);
        $this->assign('Layer',$Layers);
        $this->display();
    }


    /**
    *测试人员谭 2018-07-26 17:33:51
    *说明: 修改的页面
    */
    public function showCarrier(){
        $carriers = load_config('newerp/Application/Transport/Conf/config.php');
        $carriers = array_keys($carriers['CARRIER_TEMPT']);

        $trueCarrier = [];
        foreach ($carriers as $val) {
            $trueCarrier[] = strpos($val, '_') === false ? $val : explode('_', $val)[0];
        }

        $Carriers = array_unique($trueCarrier);
        $this->assign('carriers',$Carriers);



        $id=(int)$_GET['id'];
        $BeltLayer=new BeltLayerModel();
        $beltCarrier=$BeltLayer->where("id=$id")->getField('carrier');
        $beltCarrier=trim($beltCarrier,',');
        $beltCarrier=explode(',',$beltCarrier);



        $this->assign('beltCarrier',$beltCarrier);
        $this->assign('id',$id);
        $this->display();


    }

    // 保存设置
    public function saveSetting(){
        $id=(int)$_POST['id'];
        $carrier=trim($_POST['carrier']);

        $user=$_SESSION['truename'];

        // 这里十年八年 改不聊几次，且改的人 十年八年 变不了几次，写死
        $AllowUser=[
            '章涛',
            '江鹏程',
            '测试人员谭',
            '谢露',
            '李晓明',
            '肖华明',
            '赖运英'
        ];


        if(!in_array($user,$AllowUser)){
            echo '您没有权限修改传送带物流!请联系IT！';
            return;
        }

        if(empty($id) || empty($carrier)){
            echo '提交的数据参数有误!';            return;
        }

        $carrier=','.$carrier.',';

        $BeltLayer=new BeltLayerModel();
        $rr=$beltCarrier=$BeltLayer->where("id=$id")->save(['carrier'=>$carrier]);

        if($rr===false){
            echo '修改失败！请联系IT!';            return;
        }


        echo '<div style="color:#0f0">修改成功!</div>';
        return;
    }
}