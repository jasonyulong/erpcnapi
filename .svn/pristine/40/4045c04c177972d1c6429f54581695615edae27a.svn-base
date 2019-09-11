<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 *
 * 预估运费的时候 采用啥运输方式的 啥区
 */

namespace Transport\Model;

use Think\Model;

class ShipfeeSetModel extends Model
{

    protected $tableName = "shipfee_calc_set";

    public $typeName=['1'=>'开发用','2'=>'刊登用','3'=>'通用'];

    /**
     * @param $id
     * @return mixed
     *  获取 一个设置好的运费规则的全部 信息
     */
    function getOneBudget($id){
        $rs= $this->where("id='$id'")->find();
        if(empty($rs)){
            return [];
        }
        $usezone=$rs['usezone'];
        $shipid=$rs['shipid'];
        $SHIPFee=new SystemshipfeeModel();
        $Carrier=new CarrierModel();
        $rs['carrier']=$Carrier->getCarrierByid($shipid);
        $rs['shipfee']=$SHIPFee->where("id='$usezone'")->find();
        return $rs;
    }

    // 多个方案
    /**
    *测试人员谭 2017-04-14 11:59:48
    *说明: $countrycode 如果指定了 国家 应该叫做【特定国家专线】 则 只查找专线
     * 方案已经废除
    */
    function getSomeBudget($where,$countrycode,$attr){
        $List=$this->where($where)->select();

        $SHIPFee=new SystemshipfeeModel();
        $Carrier=new CarrierModel();
        foreach($List as $k=>$list){
            $usezone=$list['usezone'];
            $carrierid=$list['shipid'];
            $whereStr="id=$carrierid and encounts like '%,$countrycode,%' and uncontain not like '%,$countrycode,%'";
            if($attr!=1&&$attr!=''){
                $whereStr.= "and allowed_attribute like '%,$attr,%'";
            }
            $r=$Carrier->where($whereStr)->field('name')->select();
            if(empty($r)){
                continue;
            }
            $List[$k]['carriername'] = $r['name'];
            $List[$k]['shipfee'] = $SHIPFee->where("id='$usezone'")->find();
        }
        return $List;
    }

    public function getCount($where){
        $count=$this->where($where)->count();
        return $count;
    }

    public function getData($where,$limit,$order){
        if(!$order){
            $order = ' name desc ';
        }
        $field='*';
        $List = $this->field($field)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();

        $SHIPFee=new SystemshipfeeModel();
        $Carrier=new CarrierModel();
        foreach($List as $k=>$list){
            $usezone=$list['usezone'];
            $r=$Carrier->getCarrierByid($list['shipid']);
            $List[$k]['carriername']=$r['name'];
            $List[$k]['shipfee']=$SHIPFee->where("id='$usezone'")->find();
        }
        return $List;
    }
}