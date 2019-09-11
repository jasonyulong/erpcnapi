<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 */
namespace Package\Controller;

use Api\Model\OrderWeightModel;
use Common\Controller\CommonController;
use Order\Model\EbayCarrierModel;
use Order\Model\EbayConfigModel;
use Order\Model\EbayOrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\CarrierGroupItemModel;
use Package\Model\CarrierGroupModel;
use Package\Model\PickOrderDetailModel;
use Package\Service\CreatePickService;
use Think\Page;
use Transport\Model\CarrierModel;

/**
*测试人员谭 2017-12-11 23:43:50
*说明: 跨仓单 生成捡货单子
*/
class OrderTestController extends CommonController
{
    protected $pageSize = 50;
    /**
     * 用于生成拣货单的订单的状态
     * @var null
     */
    protected $pickAbleStatus = null;
    protected $allowCarriers = null;
    /**
     * @var EbayCarrierModel
     */
    protected $ebayCarrierModel = null;
    private   $current_storeid=0;
    private   $current_floor=1; //跨仓单 楼层固定死


    public function _initialize() {
        parent::_initialize();
        $this->pageSize = session('pagesize') ? session('pagesize') : 100;
        $allowCarrier = load_config('newerp/Application/Transport/Conf/config.php');
        // debug($allowCarrier);die();
        $carriers    = array_keys($allowCarrier['CARRIER_TEMPT']);
        $trueCarrier = [];
        foreach ($carriers as $val) {
            $trueCarrier[] = strpos($val, '_') === false ? $val : explode('_', $val)[0];
        }
        //debug($carriers);
        $this->allowCarriers = array_unique($trueCarrier);

        $this->current_storeid=C('CURRENT_STORE_ID');

    }

    /**
     *
     */
    public function index() {
        $ebay_id=trim($_POST['ebayid']);
        $ebay_id=trim($ebay_id,',');
        $ebay_id=str_replace('，',',',$ebay_id);
        $ebay_id=str_replace(" ",',',$ebay_id);
        $ebay_id=str_replace("",',',$ebay_id);

        $ebay_id=str_replace("/[^0-9,]/",'',$ebay_id);
        $RR=[];
        if($ebay_id){
            $OrderWeight=new OrderWeightModel('','',C('DB_CONFIG_READ'));
            $RR=$OrderWeight->where("ebay_id in($ebay_id)")->select();
            //print_r($OrderWeight->_sql());

            $html='<table border="1"><tr>';

            foreach($RR as $List){
                $html.='<tr>';
                $html.='<td>'.$List['ebay_id'].'</td>';
                $html.='<td>'.$List['weight'].'</td>';
                $html.='<td>'.$List['scan_user'].'</td>';
                $html.='<td>'.$List['transport'].'</td>';
                $html.='<td>'.date('Y-m-d H:i:s',$List['scantime']).'</td>';
                $html.='<tr>';
            }

            $html.='</table>';
            echo $html;
            die();
        }

        $this->display();
    }


}