<?php
namespace Mid\Service;

/**
 * carrier相关同步
 * brady
 * 2017/11/07 
 */
class EbayZoneDetailService extends BaseService{

    
    
    /**
     * 同步ZipCodeGroup数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayZoneDetail(){
        $EbayZoneDetailModel = new \Transport\Model\EbayZoneDetailModel();
        
        $start=0;$limit=500;
         $action = 'ZoneDetail/getEbayZoneDetailList/';
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$EbayZoneDetailModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                   $EbayZoneDetailModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }

    /**
     * 同步ebay_zone数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayZone(){
        $EbayZoneModel = new \Transport\Model\EbayZoneModel();

        $start=0;$limit=500;
        $action = 'ZoneDetail/getEbayZoneList/';
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$EbayZoneModel
                            ->where("id=".$v['id'])
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $EbayZoneModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }


   
   
}