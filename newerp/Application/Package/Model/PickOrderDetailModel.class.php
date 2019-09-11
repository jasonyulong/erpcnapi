<?php
namespace Package\Model;

use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  订单共用的一些操作   by  谭联星
 */
class PickOrderDetailModel extends Model {
    protected $tableName = 'pick_order_detail';

    // 订单明细
    // 作用是 分配库存之用 可以重新 分配 所以 要考虑原先缺货的 也就是 不要设置 is_stock
    function getErpOrderDetail($ordersn){

        $Arr=[];

        // 要靠路没有删除的 部分
        $RR=$this->where("ordersn='$ordersn' and is_delete=0")->field('ebay_id,sku,qty,order_addtime')
            ->order('order_addtime asc')->select();

        foreach($RR as $List){
            $Arr[$List['ebay_id']][]=array(
                'sku'           => $List['sku'],
                'qty'           => $List['qty'],
                'order_addtime' => $List['order_addtime']
            );
        }

        return $Arr;
    }


    //设置哪些订单有货 没货
    function setOutStock($ordersn,$ebay_id){
        $map['is_stock']=1;
        return $this->where("ordersn='$ordersn' and ebay_id='$ebay_id'")->save($map);
    }

    /**
     * 统计sku种类和数量
     * @param $where 查询条件
     * @return array
     * @author xiao
     * @date 2018-04-06
     */
    public function getSkuTotal($where){
        $data = $this->where($where)
            ->field('count(distinct `sku`) as skudiscount,sum(`qty`) as skucount')
            ->find();
        return $data;
    }

}