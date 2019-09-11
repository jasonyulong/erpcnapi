<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Transport\Model;

use Think\Model;

class SystemshipfeeModel extends Model{

    protected $tableName = "ebay_systemshipfee";

    /**
     * @param $CarrierId
     *  根据运输方式 获取 运费规则
     */
    function getShipfeeByCarrierId($CarrierId){
        //$filed="name,shippingid,aweightstart,aweightend,bfirstweight,bnextweight,
        //bfirstweightamount,bnextweightamount";
        $rs=$this->where("shippingid='$CarrierId'")->select();
        return $rs;
    }

    function getShipfeeNameByid($id){
        $rs=$this->where("id='$id'")->getField('name');
        return $rs;
    }
}