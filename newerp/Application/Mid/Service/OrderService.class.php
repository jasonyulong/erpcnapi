<?php
namespace Mid\Service;

use Mid\Model\MidEbayOrderModel;
use Order\Model\EbayOrderModel;

/**订单 服务
 * @author 王模刚
 * @since  2017 10 24 18:00
 */
class OrderService extends BaseService
{
    private $_stime = '2017-11-06 17:30:00';     #订单默认同步订单 1509960600

    /*
     * 获取并存储订单列表
     * @author 王模刚
     * @since 2017 10 25 17:22
     */
    public function getSyncOrderList($request) {
//        ini_set('memory_limit','200M');
        $requestData = ['stime' => $this->_stime];
        !empty($request['ebay_id']) && $requestData['ebay_id'] = $request['ebay_id'];
        $action      = 'Order/getOrderList/wid/'.$this->currentid.'/status/1723/limit/2000';
        if($request['supervip']){
            $requestData['supervip']=1;
        }

        //print_r($requestData);

        $orderList = $this->getErpData($requestData, $action);

        if(I('debug')){
            print_r($orderList);
        }

        if ($orderList['ret'] != 100) {
            exit('No data!');
        }
        $orderListData      = $orderList['data'];
        $midEbayOrder       = new \Mid\Model\MidEbayOrderModel();
        $midEbayOrderDetail = new \Mid\Model\MidEbayOrderDetailModel();
        $midOrderEditLogModel = new \Mid\Model\MidOrderEditLogModel();
        $idsStr             = '';
        $idsArr             = [];
        if (count($orderListData) == 0) {
            exit('No data2!');
        }
        echo '共获取订单:'.count($orderListData)."\n";
        echo '开始插入新订单'.date('Y-m-d H:i:s')."\n";
        $insertOrderTime = 0;
        $insertOrderDetailTime = 0;
        foreach ($orderListData as $k => $v) {
            $date                 = date('Y-m-d H:i:s');
            echo $v["ebay_id"]."\n";
            //如果ebay_id为空就是一场数据就不许要存储。ebay_warehouse也不能为空且必须在仓库常量数组中
            if (empty($v["ebay_id"]) || empty($v["ebay_ordersn"]) || empty($v['ebay_tracknumber']) || !in_array($v["ebay_warehouse"], C('W_ID'))) {
                //此处需写日志
                $saveLog = array(
                    'ebay_id'       => $v["ebay_id"] ?: '',
                    'new_content'   => json_encode($v),
                    'type'          => 2,
                    'reason'        => '订单中，ebay_id为空，或者ebay_ordersn为空，或者ebay_tracknumber为空，或者ebay_warehouse不在仓库常量数组中！',
                    'w_update_time' => $date
                );
                $midOrderEditLogModel->add($saveLog);
                continue;
            }
            foreach ($v['order_details'] as $key => $value) {
                if (empty($value['ebay_id']) || empty($value['sku']) || empty($value['ebay_amount'])) {
                    //此处需写日志
                    $saveLog2 = array(
                        'ebay_id'       => $value["ebay_id"] ?: '',
                        'new_content'   => json_encode($value),
                        'type'          => 3,
                        'reason'        => '订单详情中，ebay_id为空，或者recordnumber为空，或者sku为空，或者ebay_amount不在仓库常量数组中！',
                        'w_update_time' => $date
                    );
                    $midOrderEditLogModel->add($saveLog2);
                    continue 2;
                }
            }
            $orderData = array(
                "ebay_id"                   => $v["ebay_id"],
                "ebay_ordersn"              => $v["ebay_ordersn"],
                "ebay_orderqk"              => $v["ebay_orderqk"],
                "ebay_paystatus"            => $v["ebay_paystatus"],
                "recordnumber"              => $v["recordnumber"],
                "ebay_tid"                  => $v["ebay_tid"],
                "ebay_ptid"                 => $v["ebay_ptid"],
                "ebay_orderid"              => $v["ebay_orderid"],
                "ebay_createdtime"          => $v["ebay_createdtime"],
                "ebay_paidtime"             => $v["ebay_paidtime"],
                "ebay_userid"               => $v["ebay_userid"],
                "ebay_username"             => $v["ebay_username"],
                "ebay_usermail"             => $v["ebay_usermail"],
                "ebay_street"               => $v["ebay_street"],
                "ebay_street1"              => $v["ebay_street1"],
                "ebay_city"                 => $v["ebay_city"],
                "ebay_state"                => $v["ebay_state"],
                "ebay_couny"                => $v["ebay_couny"],
                "ebay_countryname"          => $v["ebay_countryname"],
                "ebay_postcode"             => $v["ebay_postcode"],
                "ebay_phone"                => $v["ebay_phone"],
                "ebay_currency"             => $v["ebay_currency"],
                "ebay_total"                => $v["ebay_total"],
                "ebay_status"               => $v["ebay_status"],
                "ebay_user"                 => $v["ebay_user"],
                "ebay_addtime"              => $v["ebay_addtime"],
                "ebay_shipfee"              => $v["ebay_shipfee"],
                "ebay_combine"              => $v["ebay_combine"],
                "market"                    => $v["market"],
                "ebay_account"              => $v["ebay_account"],
                "ebay_note"                 => $v["ebay_note"],
                "ebay_noteb"                => $v["ebay_noteb"],
                "is_reg"                    => $v["is_reg"],
                "ordertype"                 => $v["ordertype"],
                "status"                    => $v["status"],
                "mailstatus"                => $v["mailstatus"],
                "templateid"                => $v["templateid"],
                "postive"                   => $v["postive"],
                "ebay_carrier"              => $v["ebay_carrier"],
                "ebay_carrierstyle"         => $v["ebay_carrierstyle"],
                "ebay_warehouse"            => $v["ebay_warehouse"],
                "ebay_markettime"           => $v["ebay_markettime"],
                "ebay_tracknumber"          => $v["ebay_tracknumber"],
                "ebay_site"                 => $v["ebay_site"],
                "location"                  => $v["location"],
                "ebaypaymentstatus"         => $v["ebaypaymentstatus"],
                "paypalemailaddress"        => $v["paypalemailaddress"],
                "shippedtime"               => $v["shippedtime"],
                "refundamount"              => $v["refundamount"],
                "resendreason"              => $v["resendreason"],
                "refundreason"              => $v["refundreason"],
                "resendtime"                => $v["resendtime"],
                "refundtime"                => $v["refundtime"],
                "canceltime"                => $v["canceltime"],
                "cancelreason"              => $v["cancelreason"],
                "ebay_feedback"             => $v["ebay_feedback"],
                "ebay_sdsn"                 => $v["ebay_sdsn"],
                "isprint"                   => $v["isprint"],
                "ebay_ordertype"            => $v["ebay_ordertype"],
                "profitstatus"              => $v["profitstatus"],
                "orderweight"               => $v["orderweight"],
                "orderweight2"              => $v["orderweight2"],
                "ordershipfee"              => $v["ordershipfee"],
                "ordercopst"                => $v["ordercopst"],
                "scantime"                  => $v["scantime"],
                "accountid"                 => $v["accountid"],
                "ishide"                    => $v["ishide"],
                "packingtype"               => $v["packingtype"],
                "packinguser"               => $v["packinguser"],
                "packagingstaff"            => $v["packagingstaff"],
                "order_no"                  => $v["order_no"],
                "ebay_phone1"               => $v["ebay_phone1"],
                "ismodifive"                => $v["ismodifive"],
                "totalprofit"               => $v["totalprofit"],
                "ebay_case"                 => $v["ebay_case"],
                "paypal_case"               => $v["paypal_case"],
                "moneyback"                 => $v["moneyback"],
                "moneyback_total"           => $v["moneyback_total"],
                "orders_pcase_time"         => $v["orders_pcase_time"],
                "orders_ecase_time"         => $v["orders_ecase_time"],
                "erp_op_id"                 => $v["erp_op_id"],
                "custom_paypalemailaddress" => $v["custom_paypalemailaddress"],
                "pxorderid"                 => $v["pxorderid"],
                "pxordertime"               => $v["pxordertime"],
                "cky_orderid"               => $v["cky_orderid"],
                "cky_item"                  => $v["cky_item"],
                "ebay_company"              => $v["ebay_company"],
                "zencartid"                 => $v["zencartid"],
                "updateprofittime"          => $v["updateprofittime"],
                "smturl"                    => $v["smturl"],
                'wms_flag'                  => self::WMS_FLAG_DEFAULT,
                'wms_add_time'              => $date,
                'wms_update_time'           => $date
            );

            /**
            *测试人员谭 2018-08-13 19:05:07
            *说明: 不管线上是什么状态，进入到 wms 一律是 1723 !
            */
            $orderData['ebay_status']=1723; // 这里的1723 和那里的不一样啊

            try {
                $midEbayOrder->startTrans();
                $date     = date('Y-m-d H:i:s');
                $time1 = array_sum(explode(' ',microtime()));
                $resOrder = $midEbayOrder->saveOrderData($orderData);
                $time2 = array_sum(explode(' ',microtime()));
                $insertOrderDetailTime += $time2-$time1;
                echo '插入一条订单用时:'.($time2-$time1)."\n";
                if ((int)$resOrder < 1) {
                    echo 'Error 失败'."0 \n";
                    $midEbayOrder->rollback();
                    continue;
                }

                /**
                *测试人员谭 2018-10-11 15:30:47
                *说明: 保持 mid_ebay_order_detail 中永远只有一份数据--Start----------------------
                */
                echo '删除detail'."\n";
                $midEbayOrderDetail->where(['ebay_ordersn'=>$v["ebay_ordersn"]])->delete();
                // 保持 mid_ebay_order_detail 中永远只有一份数据-----END--------------------------

                $countDetails = count($v['order_details']);
                $detailIds    = array();
                foreach ($v['order_details'] as $kk => $vv) {
                    $orderDetailData = array(
                        'mid_order_id'       => $resOrder,
                        'ebay_id'            => $vv['ebay_id'],         #对应erp里面的明细 ebay_id
                        'recordnumber'       => $vv["recordnumber"],
                        'ebay_ordersn'       => $vv["ebay_ordersn"],
                        'ebay_itemid'        => $vv["ebay_itemid"],
                        'ebay_itemtitle'     => $vv["ebay_itemtitle"],
                        'ebay_itemurl'       => $vv["ebay_itemurl"],
                        'sku'                => $vv["sku"],
                        'ebay_itemprice'     => $vv["ebay_itemprice"],
                        'ebay_amount'        => $vv["ebay_amount"],
                        'ebay_createdtime'   => $vv["ebay_createdtime"],
                        'ebay_shiptype'      => $vv["ebay_shiptype"],
                        'ebay_user'          => $vv["ebay_user"],
                        'shipingfee'         => $vv["shipingfee"],
                        'ebay_account'       => $vv["ebay_account"],
                        'ebay_site'          => $vv["ebay_site"],
                        'addtime'            => $vv["addtime"],
                        'storeid'            => $vv["storeid"],
                        'finalvaluefee'      => $vv["finalvaluefee"],
                        'feeorcreditamount'  => $vv["feeorcreditamount"],
                        'attribute'          => $vv["attribute"],
                        'sourceorder'        => $vv["sourceorder"],
                        'listingtype'        => $vv["listingtype"],
                        'istrue'             => $vv["istrue"],
                        'ebay_tid'           => $vv["ebay_tid"],
                        'notes'              => $vv["notes"],
                        'goods_location'     => $vv["goods_location"],
                        'orderlineitemid'    => $vv["orderlineitemid"],
                        'paypalemailaddress' => $vv["paypalemailaddress"],
                        'combine_orderid'    => $vv["combine_orderid"],
                        'goods_cost'         => $vv["goods_cost"],
                        'smturl'             => $vv["smturl"],
                        'firstfee'           => $vv["firstfee"],
                        'feedbacktype'       => $vv["feedbacktype"],
                        'wms_flag'           => self::WMS_FLAG_DEFAULT,
                        'wms_add_time'       => $date,
                        'wms_update_time'    => $date
                    );
                    $time3 = array_sum(explode(' ',microtime()));
                    $resDetail       = $midEbayOrderDetail->saveOrderDetailData($orderDetailData, $resOrder);
                    $time4 = array_sum(explode(' ',microtime()));
                    $insertOrderDetailTime += $time4-$time3;
                    echo '插入一条详情用时:'.($time4-$time3)."\n";
                    if ((int)$resDetail < 1) {
                        echo 'Error 失败'."1 \n";
                        $midEbayOrder->rollback();
                        continue 2;
                    }
                    $detailIds[] = $resDetail;
                }
                if ($countDetails != count($detailIds)) {
                    echo 'Error 失败'."2 \n";
                    $midEbayOrder->rollback();
                    continue;
                }
                $idsArr[] = $v['ebay_id'];
                $midEbayOrder->commit();
            } catch (\Exception $e) {
                echo 'Error 失败'."3 \n";
                $midEbayOrder->rollback();
            }
        }
        echo '插入新订单结束'.date('Y-m-d H:i:s')."\n";
        echo '插入订单用时:'.$insertOrderTime."\n";
        echo '插入订单详情用时:'.$insertOrderDetailTime."\n";
        if (!empty($idsArr)) {
            $idsStr      = implode(',', $idsArr);
            echo '返回订单总数:'.count($idsArr)."\n";
            echo '返回的订单id'.$idsStr."\n";
            $action      = 'Order/updateOrderSyncStatus/wid/'.$this->currentid;
            $requestData = array('ebay_ids' => $idsStr);
            $msgs        = '修改订单同步状态';
            echo '开始返回'.date('Y-m-d H:i:s')."\n";
            $this->getErpData($requestData, $action);
            echo '返回结束'.date('Y-m-d H:i:s')."\n";
        }
    }



    public function getSyncOrderLists($request){
//        ini_set('memory_limit','200M');
        $requestData = ['stime' => $this->_stime];
        !empty($request['ebay_id']) && $requestData['ebay_id'] = $request['ebay_id'];
        $action      = 'Order/getOrderLists/wid/'.$this->currentid.'/status/1723/limit/2000';

        $orderList = $this->getErpData($requestData, $action);
        //print_r($orderList);
        if ($orderList['ret'] != 100) {
            exit('No data!');
        }

        $orderListData      = $orderList['data'];



        $midEbayOrder       = new \Mid\Model\MidEbayOrderModel();
        $midOrderEditLogModel = new \Mid\Model\MidOrderEditLogModel();
        $idsStr             = '';
        $idsArr             = [];
        if (count($orderListData) == 0) {
            exit('No data2!');
        }
        echo '共获取订单:'.count($orderListData)."\n";
        echo '开始插入新订单'.date('Y-m-d H:i:s')."\n";
        $insertOrderTime = 0;
        $insertOrderDetailTime = 0;
        foreach ($orderListData as $k => $v) {
            $date                 = date('Y-m-d H:i:s');
            echo $v["ebay_id"]."\n";
            //如果ebay_id为空就是一场数据就不许要存储。ebay_warehouse也不能为空且必须在仓库常量数组中
            if (empty($v["ebay_id"]) || empty($v["ebay_ordersn"]) || empty($v['ebay_tracknumber']) || !in_array($v["ebay_warehouse"], C('W_ID'))) {
                //此处需写日志
                $saveLog = array(
                    'ebay_id'       => $v["ebay_id"] ?: '',
                    'new_content'   => json_encode($v),
                    'type'          => 2,
                    'reason'        => '订单中，ebay_id为空，或者ebay_ordersn为空，或者ebay_tracknumber为空，或者ebay_warehouse不在仓库常量数组中！',
                    'w_update_time' => $date
                );
                $midOrderEditLogModel->add($saveLog);
                continue;
            }

            $orderData = array(
                "ebay_id"                   => $v["ebay_id"],
                "ebay_ordersn"              => $v["ebay_ordersn"],
                "ebay_orderqk"              => $v["ebay_orderqk"],
                "ebay_paystatus"            => $v["ebay_paystatus"],
                "recordnumber"              => $v["recordnumber"],
                "ebay_tid"                  => $v["ebay_tid"],
                "ebay_ptid"                 => $v["ebay_ptid"],
                "ebay_orderid"              => $v["ebay_orderid"],
                "ebay_createdtime"          => $v["ebay_createdtime"],
                "ebay_paidtime"             => $v["ebay_paidtime"],
                "ebay_userid"               => $v["ebay_userid"],
                "ebay_username"             => $v["ebay_username"],
                "ebay_usermail"             => $v["ebay_usermail"],
                "ebay_street"               => $v["ebay_street"],
                "ebay_street1"              => $v["ebay_street1"],
                "ebay_city"                 => $v["ebay_city"],
                "ebay_state"                => $v["ebay_state"],
                "ebay_couny"                => $v["ebay_couny"],
                "ebay_countryname"          => $v["ebay_countryname"],
                "ebay_postcode"             => $v["ebay_postcode"],
                "ebay_phone"                => $v["ebay_phone"],
                "ebay_currency"             => $v["ebay_currency"],
                "ebay_total"                => $v["ebay_total"],
                "ebay_status"               => $v["ebay_status"],
                "ebay_user"                 => $v["ebay_user"],
                "ebay_addtime"              => $v["ebay_addtime"],
                "ebay_shipfee"              => $v["ebay_shipfee"],
                "ebay_combine"              => $v["ebay_combine"],
                "market"                    => $v["market"],
                "ebay_account"              => $v["ebay_account"],
                "ebay_note"                 => $v["ebay_note"],
                "ebay_noteb"                => $v["ebay_noteb"],
                "is_reg"                    => $v["is_reg"],
                "ordertype"                 => $v["ordertype"],
                "status"                    => $v["status"],
                "mailstatus"                => $v["mailstatus"],
                "templateid"                => $v["templateid"],
                "postive"                   => $v["postive"],
                "ebay_carrier"              => $v["ebay_carrier"],
                "ebay_carrierstyle"         => $v["ebay_carrierstyle"],
                "ebay_warehouse"            => $v["ebay_warehouse"],
                "ebay_markettime"           => $v["ebay_markettime"],
                "ebay_tracknumber"          => $v["ebay_tracknumber"],
                "ebay_site"                 => $v["ebay_site"],
                "location"                  => $v["location"],
                "ebaypaymentstatus"         => $v["ebaypaymentstatus"],
                "paypalemailaddress"        => $v["paypalemailaddress"],
                "shippedtime"               => $v["shippedtime"],
                "refundamount"              => $v["refundamount"],
                "resendreason"              => $v["resendreason"],
                "refundreason"              => $v["refundreason"],
                "resendtime"                => $v["resendtime"],
                "refundtime"                => $v["refundtime"],
                "canceltime"                => $v["canceltime"],
                "cancelreason"              => $v["cancelreason"],
                "ebay_feedback"             => $v["ebay_feedback"],
                "ebay_sdsn"                 => $v["ebay_sdsn"],
                "isprint"                   => $v["isprint"],
                "ebay_ordertype"            => $v["ebay_ordertype"],
                "profitstatus"              => $v["profitstatus"],
                "orderweight"               => $v["orderweight"],
                "orderweight2"              => $v["orderweight2"],
                "ordershipfee"              => $v["ordershipfee"],
                "ordercopst"                => $v["ordercopst"],
                "scantime"                  => $v["scantime"],
                "accountid"                 => $v["accountid"],
                "ishide"                    => $v["ishide"],
                "packingtype"               => $v["packingtype"],
                "packinguser"               => $v["packinguser"],
                "packagingstaff"            => $v["packagingstaff"],
                "order_no"                  => $v["order_no"],
                "ebay_phone1"               => $v["ebay_phone1"],
                "ismodifive"                => $v["ismodifive"],
                "totalprofit"               => $v["totalprofit"],
                "ebay_case"                 => $v["ebay_case"],
                "paypal_case"               => $v["paypal_case"],
                "moneyback"                 => $v["moneyback"],
                "moneyback_total"           => $v["moneyback_total"],
                "orders_pcase_time"         => $v["orders_pcase_time"],
                "orders_ecase_time"         => $v["orders_ecase_time"],
                "erp_op_id"                 => $v["erp_op_id"],
                "custom_paypalemailaddress" => $v["custom_paypalemailaddress"],
                "pxorderid"                 => $v["pxorderid"],
                "pxordertime"               => $v["pxordertime"],
                "cky_orderid"               => $v["cky_orderid"],
                "cky_item"                  => $v["cky_item"],
                "ebay_company"              => $v["ebay_company"],
                "zencartid"                 => $v["zencartid"],
                "updateprofittime"          => $v["updateprofittime"],
                "smturl"                    => $v["smturl"],
                'wms_flag'                  => self::WMS_FLAG_DEFAULT,
                'wms_add_time'              => $date,
                'wms_update_time'           => $date
            );

            /**
             *测试人员谭 2018-08-13 19:05:07
             *说明: 不管线上是什么状态，进入到 wms 一律是 1723 !
             */
            $orderData['ebay_status']=1723; // 这里的1723 和那里的不一样啊
            $resOrder = $midEbayOrder->saveOrderData($orderData);


        }
    }

    /**
     * get old order list before online 
     * @author Rex
     * @since  2017-11-08 19:43
     */
    public function getOldSyncOrderList($request) {
        $requestData = ['stime' => $this->_stime];
        !empty($request['ebay_id']) && $requestData['ebay_id'] = $request['ebay_id'];
        $action      = 'Order/getOldOrderList/wid/'.$this->currentid.'/limit/500';
        $orderList = $this->getErpData($requestData, $action);
//        dump($orderList);
        if ($orderList['ret'] != 100) {
            exit('No data!');
        }
        $orderListData      = $orderList['data'];
        $midEbayOrder       = new \Mid\Model\MidEbayOrderModel();
        $midEbayOrderDetail = new \Mid\Model\MidEbayOrderDetailModel();
        $idsStr             = '';
        $idsArr             = [];
        if (count($orderListData) == 0) {
            exit('No data2!');
        }
        echo count($orderListData);
        echo '<br/>';
        foreach ($orderListData as $k => $v) {
            $midOrderEditLogModel = new \Mid\Model\MidOrderEditLogModel();
            $date                 = date('Y-m-d H:i:s');
            //如果ebay_id为空就是一场数据就不许要存储。ebay_warehouse也不能为空且必须在仓库常量数组中
            if (empty($v["ebay_id"]) || empty($v["ebay_ordersn"]) || empty($v['ebay_tracknumber']) || !in_array($v["ebay_warehouse"], C('W_ID'))) {
                //此处需写日志
                $saveLog = array(
                    'ebay_id'       => $v["ebay_id"] ?: '',
                    'new_content'   => json_encode($v),
                    'type'          => 2,
                    'reason'        => '订单中，ebay_id为空，或者ebay_ordersn为空，或者ebay_tracknumber为空，或者ebay_warehouse不在仓库常量数组中！',
                    'w_update_time' => $date
                );
                $midOrderEditLogModel->add($saveLog);
                continue;
            }
            foreach ($v['order_details'] as $key => $value) {
                if (empty($value['ebay_id']) || empty($value['sku']) || empty($value['ebay_amount'])) {
                    //此处需写日志
                    $saveLog2 = array(
                        'ebay_id'       => $value["ebay_id"] ?: '',
                        'new_content'   => json_encode($value),
                        'type'          => 3,
                        'reason'        => '订单详情中，ebay_id为空，或者recordnumber为空，或者sku为空，或者ebay_amount不在仓库常量数组中！',
                        'w_update_time' => $date
                    );
                    $midOrderEditLogModel->add($saveLog2);
                    continue 2;
                }
            }
//            $v["ebay_addtime"] = strtotime('2017-11-06 17:30:00');
            $orderData = array(
                "ebay_id"                   => $v["ebay_id"],
                "ebay_ordersn"              => $v["ebay_ordersn"],
                "ebay_orderqk"              => $v["ebay_orderqk"],
                "ebay_paystatus"            => $v["ebay_paystatus"],
                "recordnumber"              => $v["recordnumber"],
                "ebay_tid"                  => $v["ebay_tid"],
                "ebay_ptid"                 => $v["ebay_ptid"],
                "ebay_orderid"              => $v["ebay_orderid"],
                "ebay_createdtime"          => $v["ebay_createdtime"],
                "ebay_paidtime"             => $v["ebay_paidtime"],
                "ebay_userid"               => $v["ebay_userid"],
                "ebay_username"             => $v["ebay_username"],
                "ebay_usermail"             => $v["ebay_usermail"],
                "ebay_street"               => $v["ebay_street"],
                "ebay_street1"              => $v["ebay_street1"],
                "ebay_city"                 => $v["ebay_city"],
                "ebay_state"                => $v["ebay_state"],
                "ebay_couny"                => $v["ebay_couny"],
                "ebay_countryname"          => $v["ebay_countryname"],
                "ebay_postcode"             => $v["ebay_postcode"],
                "ebay_phone"                => $v["ebay_phone"],
                "ebay_currency"             => $v["ebay_currency"],
                "ebay_total"                => $v["ebay_total"],
                "ebay_status"               => $v["ebay_status"],
                "ebay_user"                 => $v["ebay_user"],
                "ebay_addtime"              => $v["ebay_addtime"],
                "ebay_shipfee"              => $v["ebay_shipfee"],
                "ebay_combine"              => $v["ebay_combine"],
                "market"                    => $v["market"],
                "ebay_account"              => $v["ebay_account"],
                "ebay_note"                 => $v["ebay_note"],
                "ebay_noteb"                => $v["ebay_noteb"],
                "is_reg"                    => $v["is_reg"],
                "ordertype"                 => $v["ordertype"],
                "status"                    => $v["status"],
                "mailstatus"                => $v["mailstatus"],
                "templateid"                => $v["templateid"],
                "postive"                   => $v["postive"],
                "ebay_carrier"              => $v["ebay_carrier"],
                "ebay_carrierstyle"         => $v["ebay_carrierstyle"],
                "ebay_warehouse"            => $v["ebay_warehouse"],
                "ebay_markettime"           => $v["ebay_markettime"],
                "ebay_tracknumber"          => $v["ebay_tracknumber"],
                "ebay_site"                 => $v["ebay_site"],
                "location"                  => $v["location"],
                "ebaypaymentstatus"         => $v["ebaypaymentstatus"],
                "paypalemailaddress"        => $v["paypalemailaddress"],
                "shippedtime"               => $v["shippedtime"],
                "refundamount"              => $v["refundamount"],
                "resendreason"              => $v["resendreason"],
                "refundreason"              => $v["refundreason"],
                "resendtime"                => $v["resendtime"],
                "refundtime"                => $v["refundtime"],
                "canceltime"                => $v["canceltime"],
                "cancelreason"              => $v["cancelreason"],
                "ebay_feedback"             => $v["ebay_feedback"],
                "ebay_sdsn"                 => $v["ebay_sdsn"],
                "isprint"                   => $v["isprint"],
                "ebay_ordertype"            => $v["ebay_ordertype"],
                "profitstatus"              => $v["profitstatus"],
                "orderweight"               => $v["orderweight"],
                "orderweight2"              => $v["orderweight2"],
                "ordershipfee"              => $v["ordershipfee"],
                "ordercopst"                => $v["ordercopst"],
                "scantime"                  => $v["scantime"],
                "accountid"                 => $v["accountid"],
                "ishide"                    => $v["ishide"],
                "packingtype"               => $v["packingtype"],
                "packinguser"               => $v["packinguser"],
                "packagingstaff"            => $v["packagingstaff"],
                "order_no"                  => $v["order_no"],
                "ebay_phone1"               => $v["ebay_phone1"],
                "ismodifive"                => $v["ismodifive"],
                "totalprofit"               => $v["totalprofit"],
                "ebay_case"                 => $v["ebay_case"],
                "paypal_case"               => $v["paypal_case"],
                "moneyback"                 => $v["moneyback"],
                "moneyback_total"           => $v["moneyback_total"],
                "orders_pcase_time"         => $v["orders_pcase_time"],
                "orders_ecase_time"         => $v["orders_ecase_time"],
                "erp_op_id"                 => $v["erp_op_id"],
                "custom_paypalemailaddress" => $v["custom_paypalemailaddress"],
                "pxorderid"                 => $v["pxorderid"],
                "pxordertime"               => $v["pxordertime"],
                "cky_orderid"               => $v["cky_orderid"],
                "cky_item"                  => $v["cky_item"],
                "ebay_company"              => $v["ebay_company"],
                "zencartid"                 => $v["zencartid"],
                "updateprofittime"          => $v["updateprofittime"],
                "smturl"                    => $v["smturl"],
                'wms_flag'                  => self::WMS_FLAG_DEFAULT,
                'wms_add_time'              => $date,
                'wms_update_time'           => $date
            );
            try {
                $midEbayOrder->startTrans();
                $date     = date('Y-m-d H:i:s');
                $resOrder = $midEbayOrder->saveOrderData($orderData);
                if ((int)$resOrder < 1) {
                    $midEbayOrder->rollback();
                    continue;
                }
                $countDetails = count($v['order_details']);
                $detailIds    = array();
                foreach ($v['order_details'] as $kk => $vv) {
                    $orderDetailData = array(
                        'mid_order_id'       => $resOrder,
                        'ebay_id'            => $vv['ebay_id'],         #对应erp里面的明细 ebay_id
                        'recordnumber'       => $vv["recordnumber"],
                        'ebay_ordersn'       => $vv["ebay_ordersn"],
                        'ebay_itemid'        => $vv["ebay_itemid"],
                        'ebay_itemtitle'     => $vv["ebay_itemtitle"],
                        'ebay_itemurl'       => $vv["ebay_itemurl"],
                        'sku'                => $vv["sku"],
                        'ebay_itemprice'     => $vv["ebay_itemprice"],
                        'ebay_amount'        => $vv["ebay_amount"],
                        'ebay_createdtime'   => $vv["ebay_createdtime"],
                        'ebay_shiptype'      => $vv["ebay_shiptype"],
                        'ebay_user'          => $vv["ebay_user"],
                        'shipingfee'         => $vv["shipingfee"],
                        'ebay_account'       => $vv["ebay_account"],
                        'ebay_site'          => $vv["ebay_site"],
                        'addtime'            => $vv["addtime"],
                        'storeid'            => $vv["storeid"],
                        'finalvaluefee'      => $vv["finalvaluefee"],
                        'feeorcreditamount'  => $vv["feeorcreditamount"],
                        'attribute'          => $vv["attribute"],
                        'sourceorder'        => $vv["sourceorder"],
                        'listingtype'        => $vv["listingtype"],
                        'istrue'             => $vv["istrue"],
                        'ebay_tid'           => $vv["ebay_tid"],
                        'notes'              => $vv["notes"],
                        'goods_location'     => $vv["goods_location"],
                        'orderlineitemid'    => $vv["orderlineitemid"],
                        'paypalemailaddress' => $vv["paypalemailaddress"],
                        'combine_orderid'    => $vv["combine_orderid"],
                        'goods_cost'         => $vv["goods_cost"],
                        'smturl'             => $vv["smturl"],
                        'firstfee'           => $vv["firstfee"],
                        'feedbacktype'       => $vv["feedbacktype"],
                        'wms_flag'           => self::WMS_FLAG_DEFAULT,
                        'wms_add_time'       => $date,
                        'wms_update_time'    => $date
                    );
                    $resDetail       = $midEbayOrderDetail->saveOrderDetailData($orderDetailData, $resOrder);
                    if ((int)$resDetail < 1) {
                        $midEbayOrder->rollback();
                        continue 2;
                    }
                    $detailIds[] = $resDetail;
                }
                if ($countDetails != count($detailIds)) {
                    $midEbayOrder->rollback();
                    continue;
                }
                $idsArr[] = $v['ebay_id'];
                $midEbayOrder->commit();
            } catch (Exception $e) {
                $midEbayOrder->rollback();
            }
        }
        if (!empty($idsArr)) {
            $idsStr      = implode(',', $idsArr);
            $action      = 'Order/updateOrderSyncStatus/wid/'.$this->currentid;
            $requestData = array('ebay_ids' => $idsStr);
            $msgs        = '修改订单同步状态';
            $this->getErpData($requestData, $action);
        }
    }

    /**
     * 获取并存储订单类型
     * @author 王模刚
     * @since  2017 10 26 9:45   此方法通过getSyncOrderList方法调用  此方法废用，通过自动任务生成
     */
    public function getSyncOrderTypeList() {
        $midOrderTypeModel = new \Common\Model\ErpOrderTypeModel();
        $requestData       = ['stime' => '2017-10-26 00:00:00'];
        $action            = 'Order/getOrderTypeList/wid/'.$this->currentid.'/limit/200';
        $orderTypeList     = $this->getErpData($requestData, $action);
        if ($orderTypeList['ret'] != 100) {
            die('No data!');
        }
        $orderTypeListData = $orderTypeList['data'];
        $idStr             = '';
        $idArr             = [];
        foreach ($orderTypeListData as $k => $v) {
            $date     = date('Y-m-d H:i:s', time());
            $saveData = array(
                'ebay_id'      => $v["ebay_id"],
                'ebay_ordersn' => $v["ebay_ordersn"] ?: '',
                'ebay_addtime' => $v["ebay_addtime"] ?: 0,
                'type'         => $v["type"],
                'pick_status'  => $v["pick_status"] ?: 1,
                'qty'          => $v["qty"],
                'floor'        => $v["floor"]
//            ,'wms_add_time'    => $date,
//                'wms_update_time' => $date
            );
            $res      = $midOrderTypeModel->saveOrderTypeData($saveData);
            if ($res !== false) {
                $idArr[] = $v["ebay_id"];
            }
        }
        if (!empty($idArr)) {
            $idStr        = implode(',', $idArr);
            $updateAction = 'Order/updateOrderTypeSyncStatus/wid/'.$this->currentid.'/ebay_ids/' . $idStr;
            $msg          = '修改订单类型同步状态';
            $this->updateStatus($updateAction, $msg);
        }
    }

    /**
     * 根据获取的返回值，来改变v3-all中的状态，避免更新过的数据重复更新
     * @author 王模刚
     * @since  2017 10 28
     */
    private function updateStatus($action, $returnmsg) {
        $requestData = ['stime' => '2017-10-28 00:00:00'];
        $return      = $this->getErpData($requestData, $action);
        if ($return['ret'] != 100) {
            echo $returnmsg . ' 失败!失败原因：' . $return['msg'] . '<br>';
        } else {
            echo $returnmsg . ' 成功！' . '<br>';
        }
    }

    /**
     * 订单从中间表同步到erp表
     * 王模刚
     * 2017 11 6
     */
    public function syncOrder() {
        $midEbayOrderModel       = new \Mid\Model\MidEbayOrderModel();
        $midEbayOrderDetailModel = new \Mid\Model\MidEbayOrderDetailModel();
        $erpEbayOrderModel       = new \Common\Model\OrderModel();
        $erpEbayOrderDetailModel = new \Common\Model\OrderDetailModel();
        $midOrderEditLogModel    = new \Mid\Model\MidOrderEditLogModel();
        $EbayOrderExtModel       = new \Common\Model\EbayOrderExtModel();
        //获取所有待同步订单
        $where['wms_flag'] = self::WMS_FLAG_DEFAULT;
        $midOrders         = $midEbayOrderModel->where($where)->order('wms_add_time asc')->limit(1000)->select();
        //echo count($midOrders);
//        dump($midOrders);
        echo count($midOrders);
        echo '<br/>';
        foreach ($midOrders as $midOrder) {
            $date = date('Y-m-d H:i:s');
            try {
                $erpEbayOrderModel->startTrans();
                $existErpOrder = $erpEbayOrderModel->where(['ebay_id' => $midOrder['ebay_id']])->find();
                if (!empty($existErpOrder)) {
                    //如果是存在的数据就不允许修改，回收站的除外
                    if ($existErpOrder['ebay_status'] == 1731) {
                        $midOrder['w_update_time'] = $date;
                        $ret                       = $erpEbayOrderModel->where(['ebay_id' => $midOrder['ebay_id']])->save($midOrder);
                        if (!$ret) {
                            $erpEbayOrderModel->rollback();
                            continue;
                        }
                        //要写日志,因为是修改
                        $saveLog = array(
                            'ebay_id'       => $midOrder['ebay_id'],
                            'old_content'   => json_encode($existErpOrder),
                            'new_content'   => json_encode($midOrder),
                            'type'          => 1,
                            'reason'        => '原数据再回收站中的修改操作！',
                            'w_update_time' => $date
                        );
                        $midOrderEditLogModel->add($saveLog);
                    } else {
                        $midOrder['wms_flag']      = self::WMS_ABNORMAL;
                        $midOrder['w_update_time'] = $date;
                        $time1 = array_sum(explode(' ',microtime()));
                        $ret                       = $midEbayOrderModel->where(['id' => $midOrder['id']])->save($midOrder);

                        // 如果是拦截的订单 就修改除了状态以外的其他单子
                        unset($midOrder['ebay_status']);

                        $erpEbayOrderModel->where(['ebay_id' => $midOrder['ebay_id']])->save($midOrder);

                        $time2 = array_sum(explode(' ',microtime()));
                        echo '更新订单数据时间:'.($time2 - $time1)."\n";
                        if (false === $ret) {
                            $erpEbayOrderModel->rollback();
                            continue;
                        }
                    }
                } else {
                    $midOrder['w_add_time']    = $date;
                    $midOrder['w_update_time'] = $date;
                    $ret                       = $erpEbayOrderModel->add($midOrder);
                    if (!$ret) {
                        $erpEbayOrderModel->rollback();
                        continue;
                    }
                }

                //获取待同步订单详情,根据 mid_order_id 获取订单详情
                $midOrderDetails = $midEbayOrderDetailModel->where(['ebay_ordersn' => $midOrder['ebay_ordersn']])->select();
                echo $midOrder['ebay_id']."\n";

                if (empty($midOrderDetails)) {
                    $erpEbayOrderModel->rollback();
                    continue;
                }


                $time1 = array_sum(explode(' ',microtime()));
                $retDelDetail = $erpEbayOrderDetailModel->where(['ebay_ordersn' => $midOrder['ebay_ordersn']])->delete();
                $time2 = array_sum(explode(' ',microtime()));
                echo '删除订单详情的语句执行时间:'.($time2 - $time1)."\n";
//                dump($retDelDetail);
//                dump($erpEbayOrderDetailModel->_sql());
                if (false === $retDelDetail) {
                    $erpEbayOrderModel->rollback();
                    continue;
                }
                $countDetails = count($midOrderDetails);
                $detailIds    = array();
                foreach ($midOrderDetails as $midOrderDetail) {
                    $date                            = date('Y-m-d H:i:s');
                    $midOrderDetail['w_update_time'] = $date;
                    //判断是否存在
                    $existErpOrderDetail = $erpEbayOrderDetailModel->where(['id' => $midOrderDetail['ebay_id']])->find();
                    if (!empty($existErpOrderDetail)) {
                        $erpEbayOrderModel->rollback();
                        continue 2;
                    } else {
                        $midOrderDetail['id']            = $midOrderDetail['ebay_id'];
                        $midOrderDetail['w_add_time']    = $date;
                        $midOrderDetail['w_update_time'] = $date;
                        $ret                             = $erpEbayOrderDetailModel->add($midOrderDetail);
                        if (!$ret) {
                            $erpEbayOrderModel->rollback();
                            continue 2;
                        }
                        $detailIds[] = $ret;
                    }
                }
                if ($countDetails != count($detailIds)) {
                    $erpEbayOrderModel->rollback();
                    continue;
                }
                $midEbayOrderModel->where(['ebay_id' => $midOrder['ebay_id']])->setField('wms_flag', self::WMS_FLAG_OK);
                $EbayOrderExtModel->saveToExt($midOrder['ebay_id'],1723);
                $erpEbayOrderModel->commit();
            } catch (Exception $e) {
                echo $e->getMessage();
                $erpEbayOrderModel->rollback();
            }
        }
    }

    /**
     * 订单类型 从中间表同步到erp主表
     * 王模刚
     * 2017 11 6
     */
    public function syncOrderType() {
        $midEbayOrderTypeModel = new \Mid\Model\MidOrderTypeModel();
        $erpEbayOrderTypeModel = new \Common\Model\ErpOrderTypeModel();
        $orderTypeInfo         = $midEbayOrderTypeModel->select();
//        dump($orderDetailInfo);
        $return = '';
        foreach ($orderTypeInfo as $k => $v) {
            $saveData = array(
                'ebay_id'      => $v["ebay_id"],
                'ebay_ordersn' => $v["ebay_ordersn"] ?: '',
                'ebay_addtime' => $v["ebay_addtime"] ?: 0,
                'type'         => $v["type"],
                'pick_status'  => $v["pick_status"] ?: 1,
                'qty'          => $v["qty"],
                'floor'        => $v["floor"]
            );
            $row      = $erpEbayOrderTypeModel->where(['ebay_id' => $v['ebay_id']])->find();
            if ($row) {
                $res = $erpEbayOrderTypeModel->where(['ebay_id' => $v['ebay_id']])->save($saveData);
                if ($res === false) {
                    $return = $v['ebay_id'] . '修改订单类型失败！';
                } else {
                    $return = $v['ebay_id'] . '修改订单类型成功！';
                }
            } else {
                $res = $erpEbayOrderTypeModel->add($saveData);
                if ($res === false) {
                    $return = $v['ebay_id'] . '保存到订单类型失败！';
                } else {
                    $return = $v['ebay_id'] . '保存到订单类型成功！';
                }
            }
            dump($return);
        }
    }

    /**
     * 前期等待扫描订单 [临时用，不得用作他处]
     * @author  Rex
     * @since   2017-11-07 18:51
     **/
    public function getWaitScanOrderList($ebay_id = '') {

    }

    /**
     * 抽查订单
     * 王模刚  2017 11 8
     */
    public function tmpGetOrder(){
        $action      = 'Order/tmpGetOrder/wid/';
        $orderList   = $this->getErpData(['limit'=>500], $action);
        if ($orderList['ret'] != 100) {
            exit('No data!');
        }
        if(!$orderList['data']){
            exit('No data2!');
        }
        dump($orderList['data']) ;
    }

    /**
     * 临时同步 2017-11-06 17:30 之前订单的 进系统时间
     * @author Simon 2017/11/11
     */
    public function tmpSyncOrderTime(){
        $action      = 'Order/getOrderByEbayId/wid/'.$this->currentid;
        $orderModel  = new \Mid\Model\MidEbayOrderModel();
        $ebay_ids = $orderModel->where(['ebay_addtime'=>strtotime('2017-11-06 17:30:00')])->limit(1000)->getField('ebay_id',true);
        foreach($ebay_ids as $ebay_id){
            $order   = $this->getErpData(['ebay_id'=>$ebay_id], $action);
            if ($order['ret'] != 100) {
                exit('No data!');
            }
            if(empty($order['data'])){
                exit('No data2!');
            }
            dump($order);
            $orderModel->where(['ebay_id'=>$order['data']['ebay_id']])->setField('ebay_addtime',$order['data']['ebay_addtime']);
        }
    }

    /**
     *测试人员谭 2018-07-27 16:10:12
     *说明: 单个订单数据啊
     */
    public function getOrderByEbayId($request){
        $action      = 'Order/getOrderByEbayId/wid/'.C('W_ID');
        $requestData['ebay_id'] = $request['ebay_id'];
        $orderList = $this->getErpData($requestData, $action);
        return $orderList;
    }


    /**
    *测试人员谭 2018-11-11 15:05:46
    *说明:TODO 检查 已入wms 是不是不正常
    */

    public function checkInWhouseOrder($request){
//        ini_set('memory_limit','200M');
        $requestData = ['stime' => $this->_stime];
        !empty($request['ebay_id']) && $requestData['ebay_id'] = $request['ebay_id'];

        $storeid=$this->currentid;

        $action      = 'Order/checkInWhouseOrder/wid/'.$storeid;

        $orderList = $this->getErpData($requestData, $action);
        //print_r($orderList);
        if ($orderList['ret'] != 100) {
            exit('No data!');
        }

        $orderListData      = $orderList['data'];

        //print_r($action."\n\n");
        //print_r($orderListData);

        $OrderModel=new EbayOrderModel('','',C('DB_CONFIG_READ'));
        $MidOrderModel=new MidEbayOrderModel('','',C('DB_CONFIG_READ'));

        $oredrids=[];

        foreach($orderListData as $ebay_id){
            if(!is_numeric($ebay_id)){
                continue;
            }

            $ebay_status=$OrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');

            echo $ebay_id.','.$ebay_status."\n";

            if(empty($ebay_status)||1731==$ebay_status){
                $ebay_status=$MidOrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');
                if(empty($ebay_status)){
                    echo "严重异常订单:".$ebay_status."\n\n";
                    $oredrids[]=$ebay_id;
                }
            }
        }


        return $oredrids;
    }
}