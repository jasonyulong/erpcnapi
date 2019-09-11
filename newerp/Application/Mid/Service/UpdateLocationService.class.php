<?php
namespace Mid\Service;
//aleb  2018 -01 -30
use Package\Model\EbayOnHandleModel;

class UpdateLocationService  extends BaseService{
	
	function getNewLocation($limit,$current_storeid){
		$action='GoodsLocation/getGoodsLocationByApi';
		$list       = $this->getErpData(['limit'=>$limit,'storeid'=>$current_storeid], $action);
		
		
		$onhandleModel=new EbayOnHandleModel();
		
		//ebay_onhandle_196
		
		$ids=[];
		if($list['status']==0){
			return ;
		}
		
		
		$Data=$list['data'];
		
		foreach ($Data as $item){
			$storeid  = $item['storeid'];
			$sku      = $item['sku'];
			$location = $item['new_location'];
			$id       = $item['id'];

			echo "$storeid \n";
			
			$rr=$onhandleModel->table('ebay_onhandle_'.$storeid)
				->where("goods_sn='$sku'")
				->limit(1)
				->save(['g_location'=>$location]);
			
			if(false!==$rr){
				$ids[]=$id;
			}
			
		}
		
		
		return $ids;
	}
	
	function updateDone($ids,$storeid){
		$action='GoodsLocation/setGoodsLocationByApi';
		$Arr      = $this->getErpData(['limit'=>0,'history_id'=>$ids,'storeid'=>$storeid], $action);
		print_r($Arr);
	}
	
}


