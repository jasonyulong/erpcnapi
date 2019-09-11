<?php
namespace Mid\Service;

/**
 * carrier相关同步
 * brady
 * 2017/11/07 
 */
class EbayCountriesService extends BaseService{

    
    
    /**
     * 同步ZipCodeGroup数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayCountriesAlias(){
        $CountriesAliasModel = new \Transport\Model\CountriesAliasModel();

        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Countries/getEbayCountriesAliasList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $isExists=$CountriesAliasModel
                            ->where("alias='".$v['alias']."'")
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $CountriesAliasModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }


    /**
     * 同步countries数据
     * @Author   brady
     * @DateTime 2017-11-07
     * @return   [type]     [description]
     */
    public  function  syncEbayCountries(){
        $CountriesModel = new \Transport\Model\CountriesModel();

        $start=0;$limit=500;
        while(true){
            $requestData = ['limit' => $limit];
            $requestData['start']=$start;
            $action = 'Countries/getEbayCountriesList/';
            $list = $this->getErpData($requestData,$action);
            
            if($list['ret']==100 && !empty($list['data'])){
                foreach($list['data'] as $v){
                    $where=array('name'=>$v['name']);
                    $where['char_code']=$v['char_code'];
                    $isExists=$CountriesModel
                            ->where($where)
                            ->find();
                    if(!empty($isExists)){
                         continue;
                    }
                    $CountriesModel->add($v);
                }
            }else{
                echo $list['msg'];
                break;
            }
            $start+=$limit;
        }

    }
   
}