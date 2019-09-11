<?php
namespace Mid\Service;

/**
 * carrier相关同步
 * brady
 * 2017/11/07 
 */
class EbayCarrierService extends BaseService{

    /**
     * 同步CarrierLocations
     * @Author   brady
     * @DateTime 2017-11-07
     */
    public function syncCarrierLocations(){
        $carrierLocations = new \Transport\Model\CarrierLocationsModel();
        $start=0;$limit=500;
        $action = 'Carrier/getCarrierLocationsList/';
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            
            $list = $this->getErpData($requestData,$action);
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists   =$carrierLocations
                                ->where("id=".$v['id'])
                                ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierLocations->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }
        
    }
    
    
    /**
     * 同步CarrierWeightFee数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncCarrierWeightFee(){
        $carrierWeightFeeModel = new \Transport\Model\CarrierWeightFeeModel();
        
        $start=0;$limit=500;
        
        $action = 'Carrier/getCarrierWeightFeeList/';
        while(true){
            $requestData = ['start' =>$start];
            $requestData['limit']=$limit;
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$carrierWeightFeeModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierWeightFeeModel->add($v);
                }
                return true;
            }
            else{
                echo $list['msg'];
                break;
            }
            $start +=$limit;
            
        }

    }

    /**
     * 同步CarrierWeightFee数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncCarrierWeightZone(){
        $carrierWeightZoneModel = new \Transport\Model\CarrierWeightZoneModel();
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Carrier/getCarrierWeightZoneList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$carrierWeightZoneModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierWeightZoneModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    /**
     * 同步ebay_carrier_group_items数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncCarrierGroupItems(){
        $carrierGroupItemsModel = new \Transport\Model\EbayCarrierGroupItemsModel();

        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Carrier/getCarrierGroupItemsList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$carrierGroupItemsModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierGroupItemsModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    /**
     * 同步ebay_carrier_group_items数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncCarrierGroup(){
        $carrierGroupModel = new \Transport\Model\EbayCarrierGroupModel();

        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Carrier/getCarrierGroupList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$carrierGroupModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierGroupModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }
    }

    /**
     * 同步ZipCodeGroup数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncCarrierZipCodeGroup(){
        $carrierZipCodeGroupModel = new \Transport\Model\EbayCarrierZipCodeGroupModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Carrier/getCarrierZipCodeGroupList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$carrierZipCodeGroupModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $carrierZipCodeGroupModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    /**
     * [syncCarrierCompany 数据同步]
     * @Author   brady
     * @DateTime 2017-11-08
     * @return   [type]     [description]
     */
    public  function  syncCarrierCompany(){
        $CarrierCompanyModel = new \Transport\Model\CarrierCompanyModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Carrier/getCarrierCompanyList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$CarrierCompanyModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $CarrierCompanyModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }
   
}