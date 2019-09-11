<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 18:01
 */

namespace Order\Model;

use Think\Model;

class EbayStoreModel extends Model
{

    protected $tableName = 'ebay_store';

    public function getStores()
    {
        $data = S('storelist');
        if(empty($data)){
            $data = $this->field('id,store_name')
                -> where('store_name not like "%备货仓%" and store_name not like "%FBA%"')
                ->select();
            S('storelist',$data,60);
        }
        return $data;
    }

    public function getStoresIndexId(){
        $data = $this->field('id,store_name')
            -> where('store_name not like "%备货仓%"')
            ->select();
        $ss=[];
        foreach($data as $list){
            $ss[$list['id']]=$list['store_name'];
        }
        return $ss;
    }

    // 仓库名字 用 id 索引
    public function getAllStoreNameIndexID(){
        $rr=$this->field('id,location,store_name')->select();
        $arrname=array();
        foreach($rr as $list){
            $arrname[$list['id']]=$list['store_name'];
        }
        return $arrname;
    }


    //location索引， id 为值
    public function getAllStoreIndexLocation(){
        $rr=$this->field('id,location,store_name')->select();
        $arr=array();
        foreach($rr as $list){
            $location=unserialize($list['location']);
            $storeid=$list['id'];// 仓库id
            if(count($location)>0){
                foreach($location as $item_location){
                    if(empty($item_location)){
                        continue;
                    }
                    $arr[$item_location]=$storeid;
                }
            }
        }
        return $arr;
    }
}