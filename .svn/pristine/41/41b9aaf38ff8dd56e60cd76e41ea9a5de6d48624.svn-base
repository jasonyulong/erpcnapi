<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name OneSkuPackLogModel.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/12/27
 * @Time: 16:28
 * @Description 单品多货包装日志
 */
namespace Package\Model;

use Think\Model;

class OneSkuPackLogModel extends Model {
    protected $tableName = 'onesku_pack_log';

    /**
     * 添加单品多货扫描日志信息
     * @param $ebay_id
     * @param $sku
     * @return mixed
     * @author Shawn
     * @date 2018/12/27
     */
    public function addLog($ebay_id,$sku){
        $data['ebay_id']        = trim($ebay_id);
        $data['sku']            = trim($sku);
        $data['operationuser']  = session("truename");
        $data['operationtime']  = time();
        $result = $this->add($data);
        return $result;
    }
}