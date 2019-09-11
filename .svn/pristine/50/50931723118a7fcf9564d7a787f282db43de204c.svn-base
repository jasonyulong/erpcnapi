<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name UndatedStatisticalController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/3/1
 * @Time: 15:06
 * @Description wms未日清数据统计
 */

namespace Api\Controller;

use Order\Model\EbayOrderModel;
use Package\Model\ApiCheckskuModel;

class UndatedStatisticalController extends  BaseController
{

    /**
     * @desc 验证身份
     * @author Shawn
     * @date 2019/3/1
     */
    public function _initialize()
    {
        parent::checkAuth();
    }

    /**
     * @desc 获取未日清统计数据
     * @author Shawn
     * @date 2019/3/1
     */
    public function getUndatedStatistical()
    {
        $date = $_POST['date'];
        if(empty($date)){
            echo json_encode(['status'=>0,'msg'=>'时间为空,无法查询!']);
            return;
        }
        $orderModel = new EbayOrderModel('','','DB_CONFIG_READ');
        //需要统计订单状态
        $statusArr = array(
            '1745' => '待打印',
            '1724' => '待扫描（待包装）',
            '2009' => '待称重',
        );
        $data = array();
        foreach ($statusArr as $key=>$value)
        {
            $map['ebay_status'] = $key;
            $count = $orderModel->where($map)->count();
            $data[$key] = (int)$count;
        }
        //统计已称重订单
        $apiCheckSkuModel = new ApiCheckskuModel('','','DB_CONFIG_READ');
        $s_map['addtime'] = array("between",array(strtotime($date." 00:00:00"),strtotime($date." 23:59:59")));
        $count = $apiCheckSkuModel->where($s_map)->count();
        $data['count'] = (int)$count;
        echo json_encode(['status'=>1,'msg'=>'获取成功','data'=>$data]);
        return;
    }
}