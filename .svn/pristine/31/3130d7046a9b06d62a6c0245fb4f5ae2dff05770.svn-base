<?php
/**
 * @copyright Copyright (c) 2016
 * @version   Beta 1.0
 * @author    kevin
 */

namespace Mid\Service;

/**
 * Class AvgWeightStatistics
 * @package Mid\Service
 */
class AvgWeightService extends BaseService
{
    /**
     * 获取sku重量平均值
     * @param $time
     * @return bool
     */
    public function getAvgWeight($time)
    {
        $action      = 'Order/getAvgWeight';
        $requestData = [
            'time' => $time,
        ];

        $responce = $this->getErpData($requestData, $action);
        if (empty($responce) || $responce['status'] != 1 || empty($responce['data'])) {
            return 'error';
        }

        $responceData   = $responce['data'];
        $avgWeightModel = new \Mid\Model\AvgWeightStatisticsModel();
        foreach ($responceData as $val) {
            $has = $avgWeightModel->where(['md5_str' => $val['md5_str']])->getField('md5_str');

            echo json_encode($val).PHP_EOL;

            if (!empty($has)) {
                $avgWeightModel->where(['md5_str' => $val['md5_str']])->save($val);
            } else {
                $avgWeightModel->add($val);
            }
        }

        return 'success';
    }
}
