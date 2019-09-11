<?php

namespace Mid\Model;
use Think\Model;

class MidEbayGoodsModel Extends BaseModel {
	protected $tableName = 'mid_ebay_goods';

	public function saveGoodsData($data) {
		$goods_sn = $data['goods_sn'];
		$row = $this->where("goods_sn='{$goods_sn}'")->find();
		if ($row) {
			$id = $row['id'];
			$data['id'] = $id;
			return $this->save($data);
		}else{
			return $this->add($data);
		}
	}

}