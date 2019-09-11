<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 18:06
 */

namespace Mid\Controller;


use Think\Controller;

class GetInternalStoreSkuController extends Controller
{
    /*
     * 同步internal_store_sku
     * @author 王模刚
     * @since 2017 12 10
     * @link local.erpanapi.com/t.php?s=/Mid/GetInternalStoreSku/getInternalStore
     */
    public function getInternalStore(){
        $orderService = new \Mid\Service\InternalService();
        $request = $_REQUEST;
        $orderService->getSyncInternalList($request);
        echo '<br/>End';
    }
    
    public function getDelInternalStore(){
		$orderService = new \Mid\Service\InternalService();
		$orderService->getDelInternalList();
	}
}