<?php
/**
 * User: 王模刚
 * Date: 2017/10/27
 * Time: 17:23
 */

namespace Common\Model;


use Think\Model;

class ErpOrderTypeModel extends Model
{
    protected $tableName = 'erp_order_type';

    /**
     * 保存定单类型数据
     * @author crazytata
     * @since 2017 10 26 10:10
     */
    public function saveOrderTypeData($data){
        $ebay_id = $data['ebay_id'];
        $row = $this->where("ebay_id='{$ebay_id}' and wms_flag =0")->find();
        if ($row) {
            return false;
        }else{
            return $this->add($data);
        }
    }
}