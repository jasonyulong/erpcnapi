<?php
/**
 * User: 王模刚
 * Date: 2017/10/27
 * Time: 18:33
 */
namespace Products\Model;

use Think\Model;

class GoodsSaleDetailModel extends Model
{
    protected $tableName = 'erp_goods_sale_detail';

    /**
     * 保存产品销售详情
     * @author crazytata
     * @since  2017 10 26 10:10
     */
    public function saveData($data) {
        $ebay_id = $data['ebay_id'];
        $sku     = $data['sku'];
        $row     = $this->where(['sku' => $sku, 'ebay_id' => $ebay_id])->find();
        if ($row) {
            return $this->where(['sku' => $sku, 'ebay_id' => $ebay_id])->save($data);
        } else {
            return $this->add($data);
        }
    }

    /**
     * 获取订单类型
     * @author Simon 2017/11/2
     */
    public function getOrderType($ebay_id) {
        $map['ebay_id'] = $ebay_id;
        $field          = "qty,sku";
        $items          = $this->where($map)->field($field)->limit(2)->select();
        if(count($items)==0){
            return [false, false];
        }
        $items          = !empty($items) ? $items : array();
        if (count($items) > 1) {
            return [3, ''];
        }
        if ($items[0]['qty'] > 1) {
            return [2, $items[0]['sku']];
        }
        return [1, $items[0]['sku']];
    }
}