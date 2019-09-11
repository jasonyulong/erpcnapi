<?php
/**
 * CarrierStatistics Api
 * @author 	leo
 * @since 	2018年8月31日09:48:13
 */
namespace Api\Controller;

use Order\Model\ApiCarrierStatisticsModel;
use Think\Page;

class CarrierStatisticsController {

    /**
     * sendCarrierStatistics
     * @author 	leo
     * @since 	2018年8月31日09:48:13
     * @link    /t.php?s=/Api/CarrierStatistics/sendCarrierStatistics
     */
    public function sendCarrierStatistics() {
        $carrierStatisticsModel = new ApiCarrierStatisticsModel();
        $to_time = I('time');
        $pageNo =  I('pageNo');
        $name   =  I('name');
        $completedTime   =  I('completedTime');
        if(empty($to_time)){
            $to_time    = date("Ymd", strtotime("-1 day"));
        }
        if($name){
            $nameArr = explode(',',trim($name));
            $where['carrier_name'] = ['in',$nameArr];
        }

        if($completedTime){
            $where['completed_time'] = trim($completedTime);
        }
        $where['to_time'] = ['eq',$to_time];
        $where['total_order_count'] = ['gt',0];
        $count    = $carrierStatisticsModel->where($where)->count();
        $prePage  = 500;

        $pageServer    = new Page($count, $prePage);
        if($pageNo){
            $pageServer->firstRow = ($pageNo - 1)*$prePage;
        }
        $dataInfo        = $carrierStatisticsModel
            ->where($where)
            ->limit($pageServer->firstRow . ',' . $pageServer->listRows)
            ->order('total_order_count_2/total_order_count asc')
            ->select();
        $dataArr['info'] = $dataInfo;
        $dataArr['page'] = $pageServer;
        echo json_encode($dataArr);
    }

    /**
     * sendAllCompletedTime
     * @author 	leo
     * @since 	2018年11月7日15:22:41
     * @link    /t.php?s=/Api/CarrierStatistics/sendAllCompletedTime
     */
    public function sendAllCompletedTime()
    {
        $carrierStatisticsModel = new ApiCarrierStatisticsModel();
        $dataArr  = $carrierStatisticsModel->field('DISTINCT(completed_time)')->select();
        echo json_encode($dataArr);
    }
}

?>