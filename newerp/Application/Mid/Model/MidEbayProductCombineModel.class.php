<?php

namespace Mid\Model;
use Think\Model;
/**组合商品模型
 * @author 王模刚
 * @since 2017 10 24 18:00
 */
class MidEbayProductCombineModel Extends BaseModel {
	protected $tableName = "mid_ebay_productscombine";

	public function saveCombineProduct($data) {
		$goods_sn = $data['goods_sn'];
		$row = $this->where("goods_sn='{$goods_sn}'")->find();
		if ($row) {
			return $this->where("goods_sn='{$goods_sn}'")->save($data);
		}else{
			return $this->add($data);
		}
	}

}