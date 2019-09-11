<?php

namespace Mid\Model;
use Think\Model;
/**订单模型
 * @author 王模刚
 * @since 2017 10 24 18:00
 */
class MidEbayOrderModel Extends BaseModel {
	protected $tableName = "mid_ebay_order";

	/**保存订单主表数据
	 * @author 王模刚
	 * @since 2017 10 24 18:00
	 */
	public function saveOrderData($data) {
//		$ebay_ordersn = $data['ebay_ordersn'];
        $ebay_id = $data['ebay_id'];
		$row = $this->where("ebay_id='{$ebay_id}' and wms_flag=0")->find();
		if ($row) {
			return $row['id'];
		}else{
			return $this->add($data);
		}
	}

}