<?php
/**
 * @copyright Copyright (c) 2016
 * @version   Beta 1.0
 * @author    kevin
 */

namespace Mid\Controller;

use Think\Controller;

class SyncAvgWeightController extends Controller
{
    /**
     * 从ERP拉取sku历史重量平均值, 表明：avg_weight_statistics
     * @link http://192.168.1.57/t.php?s=/Order/OrderWeight/getAvgWeight
     */
    public function sync()
    {
        $argv = $_SERVER['argv'];
        $time = isset($argv[2]) ? date('Y-m-d 00:00:00', strtotime($argv[2])) : date('Y-m-d 00:00:00', strtotime("-1 days"));

        echo $time.PHP_EOL;

        $avgWeightService = new \Mid\Service\AvgWeightService();

        echo $avgWeightService->getAvgWeight($time);
        return;
    }
}
