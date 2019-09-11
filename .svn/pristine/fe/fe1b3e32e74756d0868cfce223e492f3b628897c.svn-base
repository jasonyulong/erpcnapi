<?php
namespace Mid\Service;

/**
 * carrier相关同步
 * brady
 * 2017/11/07 
 */
class EbayShippingService extends BaseService{

    
    
    /**
     * 同步EbayOverseasWarehouse数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayOverseasWarehouseSetting(){
        $OverseasWarehouseSettingModel = new \Transport\Model\OverseasWarehouseSettingModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Shipping/getEbayOverseasWarehouseSettingList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists= $OverseasWarehouseSettingModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                     $OverseasWarehouseSettingModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }


    /**
     * 同步shipfee_calc_group数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayShipfeeSetGroup(){
        $ShipfeeSetGroupModel = new \Transport\Model\ShipfeeSetGroupModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Shipping/getEbayShipfeeSetGroupList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$ShipfeeSetGroupModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $ShipfeeSetGroupModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }


    /**
     * 同步shipfee_calc_set数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayShipfeeSet(){
        $ShipfeeSetModel = new \Transport\Model\ShipfeeSetModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Shipping/getEbayShipfeeSetList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$ShipfeeSetModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $ShipfeeSetModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }


    /**
     * [Systemshipfee 同步]
     * @Author   brady
     * @DateTime 2017-11-08
     * @return   [type]     [description]
     */
    public function  syncSystemshipfee(){
        $SystemshipfeeModel = new \Transport\Model\SystemshipfeeModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Shipping/getSystemshipfeeList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$SystemshipfeeModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $SystemshipfeeModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    

    /**
     * [Systemshipfee 同步]
     * @Author   brady
     * @DateTime 2017-11-08
     * @return   [type]     [description]
     */
    public function  syncTransportUsers(){
        $TransportUsersModel = new \Transport\Model\TransportUsersModel();
        
        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Shipping/getTransportUsersList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$TransportUsersModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $TransportUsersModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    

    /**
     * [Systemshipfee 同步]
     * @Author   brady
     * @DateTime 2017-11-08
     * @return   [type]     [description]
     */
    public function  syncProfitForAccount(){
        $ProfitForAccountModel = new \Transport\Model\ProfitForAccountModel();
        
        $start=0;$limit=500;
        $action = 'Shipping/getProfitForAccountList/';
        while(true){

            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            
            $list = $this->getErpData($requestData,$action);            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists= $ProfitForAccountModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $ProfitForAccountModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    
}