<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 21:02
 */

namespace Package\Model;

use Think\Model;

/**
 * Class OrderPackageModel
 * @package Package\Model
 */
class OrderPackageModel extends Model
{

    protected $tableName = 'order_package';


    /**
     * 保存包裹的费用
     * @param $ebayId
     * @param $fee
     * @return bool
     */
    public function setPackageFee($ebayId, $fee)
    {
        return $this -> where(['ebay_id' => $ebayId]) -> setField('package_type', $fee);
    }


}