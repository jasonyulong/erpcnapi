<?php
/**
 * User: 王模刚
 * Date: 2017/10/27
 * Time: 15:58
 */

namespace Mid\Controller;


use Mid\Service\UpdateLocationService;
use Think\Controller;

class UpdateLocationController extends Controller
{
    /**
     * mid_ebay_user 同步到 ebay_user  此方法可以废弃，因为ebay_user不用走中间表
     * @author 王模刚
     * @since 2017 10 27
     * @link
     */
    public function UpLocation(){
	
		$limit = 1000;
		if (I('limit')) {
			$limit = (int)I('limit');
		}
		$storeid = C('CURRENT_STORE_ID');
	
	
		$UpdateLocationService = new UpdateLocationService();
	
		$ids = $UpdateLocationService->getNewLocation($limit,$storeid);
		
		if(empty($ids)){
			return ;
		}
		
	
		$ids = implode(',', $ids);
	
		print_r($ids);
		echo "\n";
		print_r($storeid);
		echo "\n";
	
		if(empty($ids)){
			return ;
		}
		
		$UpdateLocationService->updateDone($ids,$storeid);
    }
}