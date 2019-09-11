<?php
namespace Package\Model;

use Think\Model;

/**
*测试人员谭 2018-06-22 14:22:27
*说明: 运输方式层
*/
class BeltLayerModel extends Model {

    protected $tableName = "belt_layer";






    /**
    *测试人员谭 2018-07-27 10:06:00
    *说明: 获取层数 用运输方式
    */
    public function getLayerByCarrier($carrier){

        $key='Layer:Carrier';
        $Layers=S($key);

        if(empty($Layers)){
            $Layers=$this->order('id desc')->getField('id,carrier');
            S($key,$Layers,3600); // 缓存一个小时
        }

        //$carriers 的存储结构是  ,EUB,小宝平邮,小宝挂号,

        foreach($Layers as $k=>$carriers){
            if(strstr($carriers,','.$carrier.',')){
                return $k;
            }
        }

        return 1;

    }
}