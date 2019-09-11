<?php
namespace Task\Service;

use Common\Model\ConfigsModel;
use Common\Model\OrderModel;
use Common\Model\PickLocationModel;
use Order\Model\EbayOrderModel;
use Order\Model\OrderTypeModel;
use Common\Model\GoodsSaleDetailModel;
use Task\Model\EbayHandleModel;

class SetOrderTypeService
{

    private $current_storeid=0;

    private $SaleDetailRead = null;

    public function __construct() {
        $this->current_storeid=C('CURRENT_STORE_ID');
        $this->initLocation();
        $this->onhandle    = new EbayHandleModel();
        $this->Location196 = $this->initLocation();
        $this->SaleDetailRead = new GoodsSaleDetailModel();
    }



    //这里是 erp_order_type
    public function setOrderType() {
        $ebayOrderModel       = new EbayOrderModel();
        $OrderTypeModel       = new OrderTypeModel();
        $goodsSaleDetailModel = new GoodsSaleDetailModel();
        $configModel          = new ConfigsModel();
        $configArr            = $configModel->find();
        // 参与拣货单的状态
        $pick_order_status = trim($configArr['pick_order_status'], ',');
        $pick_order_status = explode(',', $pick_order_status);
        echo 'setOrderType check order from ' . "<br>\n";
		
        $_string='b.type is null';
	
		$limit=7000;
		

	
		$map['ebay_status']=1723;
		
		if(I('ebay_id')){
			$map['a.ebay_id']=I('ebay_id');
		}

		
	
		if(I('limit')){
			$limit=intval(I('limit'));
		}
		
	
		/**
		*测试人员谭 2018-07-11 11:32:52
		*说明: 这些订单一定要是
		 * erp_goods_sale_detail 存在
		 * erp_ebay_order        存在
		 * erp_order_type        不存在  目的是为了初始化
		*/
		
		
		$sql=" SELECT aa.ebay_id FROM 
 (
 SELECT `ebay_id` FROM erp_ebay_order a INNER JOIN erp_goods_sale_detail b USING(ebay_id)  
 WHERE `ebay_status` = 1723 GROUP BY ebay_id) aa
 LEFT JOIN erp_order_type dd USING(ebay_id)
 WHERE dd.id IS NULL LIMIT 19999 ";
		
		$ebay_ids=$ebayOrderModel->query($sql);
	
		$ebay_ids = array_column($ebay_ids, 'ebay_id');
		
		print_r($ebayOrderModel->_sql()."\n\n");
        print_r(count($ebay_ids)."\n\n");
		
        $log=$ebayOrderModel->_sql()."\n\n";
        $log.=count($ebay_ids)."\n\n";
        $log.=date('H:i:s')."\n\n\n\n";
		$file=dirname(dirname(THINK_PATH)).'/log/ordertype/'.date('Ymd').'.txt';
		echo $file."\n";
        writeFile($file,$log);
        
        if(count($ebay_ids)==0){
        	echo '没找到要处理的订单'."\n";
        	return '';
		}


        $map['ebay_status']    = ['in', [1723, 1724, 1745,1724,2009]];
        $map['ebay_warehouse'] = $this->current_storeid;
        $map['accountid']      = ['gt', 0];
        $map['ebay_id']        = ['in', $ebay_ids];

        $orders = $ebayOrderModel->where($map)
            ->field('ebay_id,ebay_addtime,ebay_ordersn,ebay_combine')
            ->select();

        echo 'find orders: ' . "<br>\n";
        echo count($orders) . "<br>\n\n";//die();

        $log=$ebayOrderModel->_sql()."\n";
        $log .= "过滤后的订单数量:".count($orders)."\n\n";
        writeFile($file,$log);
		sleep(3);
        // check order
        foreach ($orders as $List) {
            $ebay_id      = $List['ebay_id'];
            $ebay_addtime = $List['ebay_addtime'];
            $ebay_ordersn = $List['ebay_ordersn'];
            $ebay_combine = $List['ebay_combine'];
            if ($ebay_combine == 1) {  // 被合并订单 理论上 不会出现
                continue;
            }

            /**
            *测试人员谭 2017-12-11 10:13:25
            *说明: 计算订单是 多品还是单品
            */
            $OrdertypeArr = $goodsSaleDetailModel->getOrdertype($ebay_id);
            $Ordertype    = $OrdertypeArr[0];
            $sku          = $OrdertypeArr[1];
            if ($Ordertype === false) {
            	 $log=$ebay_id."多品 单品判断失败 ".date('H:i:s')."\n";
            	 echo $log;
                writeFile($file,$log);
                continue;
            }

            $floor = 0; // 多品多货物的 默认是0 还是0

            /**
            *测试人员谭 2017-12-21 13:51:52
            *说明: 这里比较蛋疼---- T开头库位的SKU 不能生成捡货单，现在捡货单 只能生成在 floor=1（混合）  3  5
             *  6 楼 就是  T 开头的玩意。  列表在 特殊库位订单 列表中
            */

            $floor=$goodsSaleDetailModel->isTlocationOrder($ebay_id);

            /**
             *测试人员谭 2017-09-16 16:00:00
             *说明: 订单是属于哪一楼层
             */
            if ($Ordertype < 3&&$floor==0) { //多品多货的 不判断
                $floor = $this->getMinzhiFloor($sku);
            }

            //echo $ebay_id,',',$Ordertype,',',$sku,',',$floor."\n";


            $rr = $OrderTypeModel->where("ebay_id='$ebay_id'")->field('id')->find();

            if ($rr) {
                $id            = $rr['id'];
                $data          = [];
                $data['type']  = $Ordertype;
                $data['floor'] = $floor;
                $OrderTypeModel->where("id=$id")->limit(1)->save($data);
            } else {
                $data                 = [];
                $data['type']         = $Ordertype;
                $data['ebay_ordersn'] = $ebay_ordersn;
                $data['ebay_id']      = $ebay_id;
                $data['floor']        = $floor;
                $data['ebay_addtime'] = $ebay_addtime;
                $OrderTypeModel->add($data);
            }
        }

        //TODO 执行到这里的时候 多品多货的 floor=0 在这里 要执行一个玩意： 是否是 跨仓单子
        //TODO 也就是检查 type=3 and  floor=0  and is_cross=0 的订单 一旦跨仓， set floor=1,is_cross=1 , 这样的话，setMoreSKUFloor 就做自己的事情了

        $this->isCrossOrder();
        
        //TODO   这里开始解决 多屏的 楼成问题
        $this->setMoreSKUFloor();
    }

    private function initLocation() {
        $Location    = new PickLocationModel();
        $Locations   = $Location->find();
        $LocationStr = $Locations['location'];
        $LocationStr = explode(',', $LocationStr);
        $arr2        = [];
        foreach ($LocationStr as $list) {
            if (!empty($list)) {
                $arr2[] = $list;
            }
        }
        return $arr2;
    }

    //多品楼成 问题 入库
    private function setMoreSKUFloor() {
        echo '多品的楼层问题 Start ' . "<br>\n";
        // $readLink = C('DB_CONFIG_READ');
        $OrderRead            = new OrderModel();
        $OrderTypeModel       = new OrderTypeModel();
        $Config               = new ConfigsModel();
        $configArr            = $Config->find();
        // 参与拣货单的状态
        $pick_order_status = trim($configArr['pick_order_status'], ',');
        $pick_order_status = explode(',', $pick_order_status);
        echo 'check order from ' . "<br>\n";
        $map['a.ebay_status']    = ['in', $pick_order_status];
        $map['a.ebay_warehouse'] = $this->current_storeid;
        $map['a.accountid']      = ['gt', 0];
        $map['b.type']           = 3;     // 多品多货的

        /**
        *测试人员谭 2017-12-11 10:31:25
        *说明: TODO: b.floor 的目的是 让 isCrossOrder 先排除掉 跨仓单，跨仓单只有1楼
         * TODO:   isCrossOrder 如果判断了 跨仓单  b.floor=1,is_cross=1 这样的话  本方法 处理的订单 就和 跨仓单 无关了
         */
        $map['b.floor']          = 0;     // 多品多货的 不确定楼层的玩意

        $RR                      = $OrderRead->alias('a')->where($map)
            ->join('inner join erp_order_type b using(ebay_id)')
            ->field('a.ebay_id,a.ebay_warehouse')->select();

        debug($OrderRead->_sql());
        //所有的多品多货
        foreach ($RR as $List) {
            $ebay_id        = $List['ebay_id'];
            $ebay_warehouse = $List['ebay_warehouse'];
            $floor          = $this->getFloorMoreSKU($ebay_id, $ebay_warehouse);
            //echo $ebay_id,',',$floor."\n";
            $OrderTypeModel->where(['ebay_id' => $ebay_id])->limit(1)->save(['floor' => $floor]);
        }
    }

    /**
     * 获取sku所在的楼层
     * @author Simon 2017/11/2
     */
    private function getMinzhiFloor($sku = '', $location = false, $store_id = null) {
        if(is_null($store_id)){
            $store_id = C("CURRENT_STORE_ID");
        }
        if (false === $location) { // 如果这个 location 就是空怎么办
            $location = strtolower(trim($this->onhandle->table($this->onhandle->getPartitionTableName(['store_id' => $store_id]))->where(['goods_sn' => $sku])->getField('g_location')));
        }
        foreach ($this->Location196 as $list) {
            $len = count($list);
            if (substr($location, 0, $len) == strtolower($list)) {
                return 3;
            }
        }
        return 5;
    }

    //--各种--多品 的楼成问题
    private function getFloorMoreSKU($ebay_id, $storeid = null) {
        if(is_null($storeid)){
            $store_id = C("CURRENT_STORE_ID");
        }
        $RR = $this->SaleDetailRead->alias('a')
            ->join('inner join ebay_onhandle_' . $storeid . ' b on a.sku=b.goods_sn')
            ->where(['a.ebay_id' => $ebay_id])
            ->field('a.sku,b.g_location')
            ->select();
        //TODO 未来做两个 仓库的时候 这里的变量 必须要改一下
        $floor = 5;
        if ($storeid == $this->current_storeid) {
            foreach ($RR as $List) {
                $f = $this->getMinzhiFloor('', $List['g_location']); // 如果有一个 5楼 直接 就是 5 楼了
                if ($f == 5) {
                    return $f;
                }
                $floor = $f;
            }
            return $floor;
        }
        return $floor; // 默认是 5楼
    }

    // 批量检查 订单是不是 跨仓的订单
    private function isCrossOrder(){

        echo $this->current_storeid."\n\n";

        echo '订单是不是 跨仓的订单 Start ' . "<br>\n\n";

        $OrderRead            = new OrderModel();
        $OrderTypeModel       = new OrderTypeModel();
        $Config               = new ConfigsModel();
        $configArr            = $Config->find();

        // 参与拣货单的状态
        $pick_order_status = trim($configArr['pick_order_status'], ',');
        $pick_order_status = explode(',', $pick_order_status);
        echo 'check order from ' . "<br>\n";
        $map['a.ebay_status']    = ['in', $pick_order_status];
        $map['a.ebay_warehouse'] = $this->current_storeid;
        $map['b.type']           = 3;     // 多品多货的
        $map['b.floor']          = 0;     // 多品多货的 不确定楼层的玩意



        $RR = $OrderRead->alias('a')->where($map)
            ->join('inner join erp_order_type b using(ebay_id)')
            ->field('a.ebay_id,a.ebay_warehouse')->select();

        print_r($OrderRead->_sql()."\n\n");
        print_r(count($RR)."\n\n");

        //所有的多品多货
        foreach ($RR as $List) {
            $ebay_id        = $List['ebay_id'];
            $skus=$this->SaleDetailRead
                ->where(['ebay_id'=>$ebay_id])
                ->field('ebay_id,storeid')->select();

            foreach($skus as $list){
                if($this->current_storeid==$list['storeid']){
                    continue;
                }

                echo '找到一个跨仓单子'.$ebay_id.','.$list['storeid']."\n\n";
                $OrderTypeModel->where(['ebay_id'=>$ebay_id])->limit(1)->save([
                    'floor'=>1,
                    'is_cross'=>1
                ]);
                break;
            }


        }// end Foreach $RR


    }
}