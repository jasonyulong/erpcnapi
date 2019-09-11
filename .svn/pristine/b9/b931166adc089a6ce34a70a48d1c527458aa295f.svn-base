<?php
namespace Package\Model;

use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *
 */
class PickOrderConfirmModel extends Model {
    protected $tableName = 'pick_order_confirm';

    // 检查 有没有确认完全
    function checkStatus($ordersn){
        $map['ordersn']=$ordersn;
        $map['status']=1;

        $RR=$this->where($map)->field('id')->select();

        if(count($RR)>0){
            return false;
        }

        return true;
    }


    //真实地 已经确认了或者填写了 捡货实际数量的 sku 数据
    function getRealQty($ordersn){
        $map['ordersn']=$ordersn;
        $Arr=$this->where($map)->field('sku,real_qty')->select();

        $arr=[];
        foreach($Arr as $List){
            $arr[$List['sku']]=$List['real_qty'];
        }
        return $arr;
    }


}