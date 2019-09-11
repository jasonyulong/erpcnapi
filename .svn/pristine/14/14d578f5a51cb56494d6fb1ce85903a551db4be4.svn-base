<?php
namespace Package\Model;

use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  订单共用的一些操作   by  谭联星
 */
class PickerOrderModel extends Model {
    protected $tableName = 'picker_order';

    /**
     * 获取所有未结束的拣货单
     * @author Simon 2017/11/27
     */
    public function getNotEndOrderSn(){
        $orderSnS = $this->where(['isprint'=>['neq',3]])->getField('ordersn,isprint');
        return $orderSnS;
    }
}