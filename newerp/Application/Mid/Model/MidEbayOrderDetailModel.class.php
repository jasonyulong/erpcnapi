<?php

namespace Mid\Model;
use Think\Model;
/**订单详情模型
 * @author 王模刚
 * @since 2017 10 24 18:00
 */
class MidEbayOrderDetailModel Extends BaseModel {
	protected $tableName = "mid_ebay_order_detail";

	/**保存订单详情表数据
	 * @author 王模刚
	 * @since 2017 10 24 18:00
	 */
	public function saveOrderDetailData($data,$mid_order_id) {
		if (empty($data) || empty($mid_order_id)) {
			return false;
		}
		$ebay_id = $data['ebay_id'];
		$row = $this->where("mid_order_id = {$mid_order_id} and ebay_id={$ebay_id}")->find();
		if ($row) {
			return false;
		}else{
			return $this->add($data);
		}
	}

}