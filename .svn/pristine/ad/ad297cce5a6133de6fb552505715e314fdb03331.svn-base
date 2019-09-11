<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 10:11
 */

namespace Mid\Model;


use Think\Model;

class MidGoodsSaleDetailModel extends BaseModel
{
    protected $tableName = 'mid_goods_sale_detail';

    /**
     * 保存产品销售详情
     * @author crazytata
     * @since 2017 10 26 10:10
     */
    public function saveData($data){
        $ebay_id = $data['ebay_id'];
        $sku = $data['sku'];
        $row = $this->where(['sku'=>$sku,'ebay_id'=>$ebay_id])->find();
        if ($row) {
            return $this->where(['sku'=>$sku,'ebay_id'=>$ebay_id])->save($data);
        }else{
            return $this->add($data);
        }
    }
}