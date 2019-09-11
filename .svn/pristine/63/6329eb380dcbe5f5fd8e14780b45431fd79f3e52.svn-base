<?php
/**
 * 订单统计报表
 * User: 王模刚
 * Date: 2017/11/4
 * Time: 16:15
 */

namespace Report\Controller;


use Think\Controller;

class ReportOrderBoardController  extends Controller
{
    protected $stauts = array(
        1723=>'可打印',
        1745=>'等待打印',
        1724=>'虚拟仓订单',
        2009=>'已出库待称重',
        1733=>'等待打印贰'
    );
    /**
     * 王模刚
     * 2017 11 4
     * @link  http://local.erpcnapi.com/t.php?s=/Report/ReportOrderBoard/orderCount
     */
    public function orderCount(){
        $today = date('Y-m-d');
        $yesterday2 = date('Y-m-d',strtotime('-30 days'));

        echo '<pre>';
        $toDays = [$today];
        $i = 1;
        while ($i < 30) {
            $toDays[] = date('Y-m-d',strtotime("-$i days"));
            $i++;
        }

        foreach ($toDays as $value) {
            $orderCountService = new \Report\Service\OrderCountService();
            $ret = $orderCountService->createReportBoardData($value);
            //var_dump($ret);
        }

        echo 'End';

    }

    /**
     * 按进WMS 时间时间统计
     * @author  Rex
     * @since   2017-11-17 11:28:00
     * @link  http://local.erpcnapi.com/t.php?s=/Report/ReportOrderBoard/orderCount2
     */
    public function orderCount2() {
        $today = date('Y-m-d');
        echo '<pre>';
        $toDays = [$today];
        $i = 1;
        while ($i < 30) {
            $toDays[] = date('Y-m-d',strtotime("-$i days"));
            $i++;
        }

        foreach ($toDays as $key => $value) {
            $orderCountService = new \Report\Service\OrderCountService();
            $ret = $orderCountService->createReportBoardData2($value);
        }

        echo 'End';
    }

    /**
     * 显示信息report_order_board中的数据
     * 王模刚  2017 11 10
     */
    public function index(){
        $orderCountService = new \Report\Service\OrderCountService();
        $dataInfo = $orderCountService->getData();
        $this->assign('data',$dataInfo);
        $this->display();
    }

    /**
     * show report_order_board_2 data list
     * @author  Rex
     * @since   2017-11-17 12:00:00
     */
    public function index2(){
        $reportOrderBoard2Model = new \Report\Model\ReportOrderBoard2Model();
        $dataInfo = $reportOrderBoard2Model->getDataList();
        $this->assign('data',$dataInfo);
        $this->display();
    }

}