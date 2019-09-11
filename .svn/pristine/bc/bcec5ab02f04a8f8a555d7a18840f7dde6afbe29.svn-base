<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/9
 * Time: 11:56
 */
namespace Api\Model;

use Think\Model;

class OrderInterceptRecordModel extends Model
{
    protected $tableName = 'order_intercept_record';

    /**
     * 异常是否处理
     * @author Simon 2017/11/9
     */
    public function isControl($ebay_id) {
        $this->where()->find();
    }

    /**
     * 是否存在未处理的订单拦截的记录
     * @author Simon 2017/11/9
     */
    public function isExistNotControlInterceptInfo($ebay_id) {
        $data = $this->where(['ebay_id' => $ebay_id, 'status' => 0])->find();
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }
    }
}
