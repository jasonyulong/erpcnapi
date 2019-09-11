<?php
/**
 * User: barady
 * Date: 2017/11/7
 * Time: 15:58
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayCountriesController extends Controller
{   
    /**
     * 同步ebay_countries_alias 到ebay_countries_alias
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Mid/SyncEbayCountries/syncEbayCountriesAlias
     */
    public function syncEbayCountriesAlias(){
        $EbayCountriesService = new \Mid\Service\EbayCountriesService();
        $EbayCountriesService->syncEbayCountriesAlias();
        die('ebay_countries_alias 同步到 ebay_countries_alias，成功！');

    }

    

    /**
     * 同步ebay_countries 到erp_ebay_countries
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Sync/SyncEbayCountries/syncEbayCountries
     */
    public function syncEbayCountries(){
        $EbayCountriesService = new \Mid\Service\EbayCountriesService();
        $EbayCountriesService->syncEbayCountries();
        die('ebay_countries 同步到 ebay_countries，成功！');

    }
    
   
}