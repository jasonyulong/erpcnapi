<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name CanWeightFailureLog.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/12/27
 * @Time: 10:18
 * @Description 称重扫描渠道错误失败日志
 */
namespace Order\Model;

use Think\Model;

class CanWeightFailureLogModel extends Model
{
    protected $tableName = "can_weight_failure_log";


    /**
     * 记录日志
     * @param $realCarrier
     * @param $scanCarrier
     * @param $ebayId
     * @param int $type
     * @return mixed
     * @author Shawn
     * @date 2018/12/27
     */
    public function addLog($realCarrier,$scanCarrier,$ebayId,$type=1){
        $data['real_carrier']   = trim($realCarrier);
        $data['scan_carrier']   = trim($scanCarrier);
        $data['ebay_id']        = trim($ebayId);
        $data['type']           = $type;
        $data['operationtime']  = time();
        $data['operationuser']  = session("truename");
        $data['notes']          = '订单真实物流：'.$realCarrier.',称重扫描物流：'.$scanCarrier.'，称重失败！';
        $result = $this->add($data);
        return $result;
    }
}