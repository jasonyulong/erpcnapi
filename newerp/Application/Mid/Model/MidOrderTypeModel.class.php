<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 10:11
 */

namespace Mid\Model;


use Think\Model;

class MidOrderTypeModel extends BaseModel
{
    protected $tableName = 'mid_order_type';

    /**
     * 保存定单类型数据
     * @author crazytata
     * @since 2017 10 26 10:10
     */
    public function saveOrderTypeData($data){
        $ebay_id = $data['ebay_id'];
        $row = $this->where("ebay_id='{$ebay_id}'")->find();
        if ($row) {
            return $this->where("ebay_id='{$ebay_id}'")->save($data);
        }else{
            return $this->add($data);
        }
    }
}