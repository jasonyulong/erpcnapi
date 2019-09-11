<?php
namespace Api\Model;

use Think\Model;

//民治仓库 最后一枪有没有扫描
class OrderWeightModel extends Model
{
	protected $tableName = 'api_orderweight';

    function isExsit($ebay_id){
        $map['ebay_id']=$ebay_id;
        $rr=$this->where($map)->find();
        if($rr){
            return true;
        }

        return false;
    }

}