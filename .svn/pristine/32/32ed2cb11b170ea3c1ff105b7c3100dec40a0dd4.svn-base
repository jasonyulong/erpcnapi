<?php
namespace Mid\Service;

/**
 * 账号表同步
 * brady
 * 2017/11/07 
 */
class EbayAccountService extends BaseService{

    /**
     * 同步ebay_account
     * @Author   brady
     * @DateTime 2017-11-07
     */
    public function syncAccount($start=0,$limit=500){
        $list=$this->getAccountList($start,$limit);
        
        
        if($list['ret']!=100){
            $this->addAccount($list['data']);
            echo $list['msg'];exit;
        }
        return $list;
        
        

    }
    
    /**获取账户列表
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public function getAccountList($start=0,$limit=500){
        //$midGoodsSaleDetailModel = new \Transport\Model\EbayAccountModel();

        $requestData = ['start' => $start];
        $requestData['limit'] =$limit;
        $action = 'Account/getAccountList/';
        $accountList = $this->getErpData($requestData,$action);
        return $accountList;
    }

    /**
     * 添加进去
     * @Author   brady
     * @DateTime 2017-11-07
     */
    public function  addAccount($list){
        $ebayAccountModel = new \Transport\Model\EbayAccountModel();
        foreach($list as $v){
            $isExists   =$ebayAccountModel
                        ->where("id=".$v['id'])
                        ->find();
            if(!empty($isExists)){
                 continue;
            }
            $ebayAccountModel->add($v);
        }
        return  true;

    }
   
}