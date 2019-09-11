<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\ErpEbayGoodsModel;
use Common\Model\OrderModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\EbayGoodsModel;
use Package\Model\OrderPackageModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Package\Model\WhiteOrderModel;
use Package\Service\MakeBaleService;

/**
 * Class CreatePickController
 * @package Package\Controller
 * 白单的标记
 */
class WhiteOrderController extends CommonController{
	
	/**
	*测试人员谭 2018-06-22 14:19:14
	*说明: 标记白单
	*/
	public function markOrder(){
		$ebay_id=(int)$_POST['ebayid'];
		
		$adduser=$_POST['baozhuangUser'];
		
		if(empty($adduser) || empty($ebay_id)){
			echo json_encode(['status'=>0,'msg'=>'参数有误']);
			return false;
		}
		
		/**
		*测试人员谭 2018-06-22 14:22:39
		*说明: 包状台 的界面返回的 json 在这里插入数据 记录下 该死的白单
		*/
		
		$add=[];
		$add['ebay_id']=$ebay_id;
		$add['adduser']=$adduser;
		$add['addtime']=time();
		$WhiteOrder=new WhiteOrderModel();
		$rs=$WhiteOrder->add($add);
		
		if($rs){
			echo json_encode(['status'=>1,'msg'=>'添加成功']);
		}else{
			echo json_encode(['status'=>0,'msg'=>'添加失败了']);
		}
		
		return false;
	}

	
	public function testpage(){
		$this->display();
	}
}