<?php
/**
 * User: barady
 * Date: 2017/11/7
 * Time: 15:58
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayCarrierController extends Controller
{   
    /**
     * 同步syncCarrierLocations 到ebay_carrier_locations
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierLocations
     */
    public function syncCarrierLocations(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierLocations();
        die('ebay_carrier_locations 同步到 ebay_carrier_locations，成功！');

    }


    
    /**
     * 同步carrier_weight_fee 到carrier_weight_fee
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierWeightFee
     */
    public function syncCarrierWeightFee(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierWeightFee();
        die('carrier_weight_fee 同步到 carrier_weight_fee，成功！');

    }
    
   

   /**
     * 同步carrier_weight_zone 到carrier_weight_zone
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierWeightZone
     */
    public function syncCarrierWeightZone(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierWeightZone();
        die('carrier_weight_zone 同步到 carrier_weight_zone，成功！');

    }


    

    /**
     * 同步ebay_carrier_group_items 到ebay_carrier_group_items
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierGroupItems
     */
    public function syncCarrierGroupItems(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierGroupItems();
        die('ebay_carrier_group_items 同步到 ebay_carrier_group_items，成功！');

    }

    /**
     * 同步ebay_carrier_group 到ebay_carrier_group
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierGroup
     */
    public function syncCarrierGroup(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierGroup();
        die('ebay_carrier_group 同步到 ebay_carrier_group，成功！');

    }

    

    /**
     * 同步ebay_carrier_zipcode_groups 到ebay_carrier_zipcode_groups
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierZipCodeGroup
     */
    public function syncCarrierZipCodeGroup(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierZipCodeGroup();
        die('ebay_carrier_group 同步到 ebay_carrier_group，成功！');

    }

    /**
     * 同步ebay_carrier_company 到ebay_carrier_company
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCarrier/syncCarrierCompany
     */
    public function syncCarrierCompany(){
        $ebayCarrierService = new \Mid\Service\EbayCarrierService();
        $ebayCarrierService->syncCarrierCompany();
        die('ebay_carrier_company 同步到ebay_carrier_company，成功！');

    }
}