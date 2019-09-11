<?php
namespace Order\Model;

use Think\Model;

class EbayCarrierModel extends Model
{
	protected $tableName = 'ebay_carrier';

	/**
	 * 获取国内仓物流方式
	 */
	public function getInlandCarrier($user){
        $currStoreId    = C("CURRENT_STORE_ID");
		$condition = array();
		$condition['status'] = 1;
		$condition['ebay_user'] = 'vipadmin';
		$condition['ebay_warehouse'] = $currStoreId;
		return $this->where($condition)->order('name asc')->getField('name', true);
	}

	/**
	 * 获取海外仓物流方式
	 */
	public function getForeignCarrier($user){
        $currStoreId    = C("CURRENT_STORE_ID");
		$condition = array();
		$condition['status'] = 1;
		$condition['ebay_user'] = 'vipadmin';
		$condition['ebay_warehouse'] = array('neq', $currStoreId);
		return $this->where($condition)->order('name asc')->getField('name', true);
	}

}