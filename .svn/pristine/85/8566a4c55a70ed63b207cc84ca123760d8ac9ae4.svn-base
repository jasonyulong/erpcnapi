<?php
/**
 * Report Api
 * @author 	Rex
 * @since 	2017-11-15 18:13:00
 */
namespace Api\Controller;

class ReportController {

    /**
     * reprot board
     * @author 	Rex
     * @since 	2017-11-15 18:16:00
     * @link    http://local.erpcnapi.com/t.php?s=/Api/Report/reportBoard
     */
    public function reportBoard() {
    	$orderCountService = new \Report\Service\OrderCountService();
        $dataInfo = $orderCountService->getData();
        $retList = array();
        foreach ($dataInfo as $key => $value) {
        	foreach ($value as $key2 => $value2) {
                if (in_array($key2, array('id','to_date','add_time','update_time'))) {
                    continue;
                }
                $retList[$value['to_date']]['wms_'.$key2] = $value2;
        	}
        }
        echo json_encode($retList);
    }

}

?>