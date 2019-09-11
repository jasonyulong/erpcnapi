<?php
/**
 * User: barady
 * Date: 2017/11/7
 * Time: 15:58
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayZoneDatailController extends Controller
{   
    /**
     * 同步ebay_zone_detail 到ebay_zone_detail
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayZoneDatail/syncEbayZoneDatail
     */
    public function syncEbayZoneDatail(){
        $ebayZoneDatailService = new \Mid\Service\EbayZoneDetailService();
        $ebayZoneDatailService->syncEbayZoneDetail();
        die('ebay_zone_detail 同步到 ebay_zone_detail，成功！');

    }


    /**
     * 同步ebay_zone 到ebay_zone
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayZoneDatail/syncEbayZoneDatail
     */
    public function syncEbayZone(){
        $ebayZoneDatailService = new \Mid\Service\EbayZoneDetailService();
        $ebayZoneDatailService->syncEbayZone();
        die('ebay_zone 同步到 ebay_zone，成功！');

    }
    
   
}