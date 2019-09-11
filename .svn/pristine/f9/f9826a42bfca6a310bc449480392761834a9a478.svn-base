<?php
/**
 * User: barady
 * Date: 2017/11/7
 * Time: 15:58
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayAccountController extends Controller
{   
    /**
     * 同步ebay_account 到erp_ebay_account
     * @Author   brady
     * @DateTime 2017-11-07
     * @link http://local.erpanapi.com/t.php?s=/Sync/SyncEbayAccount/syncEbayAccount
     */
    public function syncEbayAccount(){
        $ebayAccountService = new \Mid\Service\EbayAccountService();
        $start=0;$limit=500;
        while(true){
            $list=$ebayAccountService->syncAccount($start,$limit);
            if(empty($list['data'])){
                break;
            }
            $ebayAccountService->addAccount($list['data']);
            $start+=$limit;
        }    
        die('ebay_account 同步到 erp_ebay_account，成功！');

    }
    
   
}