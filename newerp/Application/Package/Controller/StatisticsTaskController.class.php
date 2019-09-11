<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name StatisticsTaskController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/2
 * @Time: 15:57
 * @Description 执行统计任务（cli模式）
 */
namespace Package\Controller;
use Common\Model\EbayUserModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\EbayGoodsModel;
use Package\Model\OneSkuPackLogModel;
use Package\Model\OrderslogModel;
use Package\Model\PackerStatisticsModel;
use Package\Model\PackSubsidyStatisticsModel;
use Package\Model\PickOrderModel;
use Package\Model\StatisticsModel;
use Package\Service\CreateSingleOrderService;
use Think\Controller;
use Common\Model\ErpOrderTypeModel;
use Order\Model\EbayOrderModel;
use Package\Model\CheckScanOrderModel;
use Package\Model\GoodsSaleDetailModel;
use Package\Model\PickerStatisticsModel;
use Package\Model\PickOrderDetailModel;
class StatisticsTaskController extends Controller{

    /**
     * 当前仓库id
     * @var null
     */
    private $currStoreId = null;

    /**
     * 初始化
     * @author Shawn
     * @date 2018/8/2
     */
    public function _initialize()
    {
        if(!IS_CLI){
            echo 'Must run in cli'."\n\n";
            die();
        }else{
            ini_set('memory_limit','2048M');
        }
        $this->currStoreId = C("CURRENT_STORE_ID");
    }
    /**
     * 拣货统计程序
     * @author Shawn
     * @date 2018/8/2
     */
    public function pickerStatistics()
    {
        $logPath = dirname(dirname(THINK_PATH)) . '/log/statistics_task/' . date('Ymd') . '_picker.error.txt';
        $log = '['.date("Y-m-d H:i:s").']';
        //没有传时间，默认查3天以内订单
        if(isset($_SERVER['argv'][2])){
            $startTime = strtotime($_SERVER['argv'][2]);
        }else{
            $startTime = strtotime('-3 days');
        }
        $endTime = time();
        //根据时间找到已出库订单,查询丛库
        $ebayOrderModel = new EbayOrderModel("","",C('DB_CONFIG_READ'));
        $goodsSaleDetailModel = new GoodsSaleDetailModel("","",C('DB_CONFIG_READ'));
        $checkScanModel = new CheckScanOrderModel("","",C('DB_CONFIG_READ'));
        $orderTypeModel = new ErpOrderTypeModel("","",C('DB_CONFIG_READ'));
        //查找拣货人
        $singleOrderService = new CreateSingleOrderService();
        //no 发货 no money
        $statisticModel = new PickerStatisticsModel();
        $orderMap['scantime'] = array("between",[$startTime,$endTime]);
        $orderMap['ebay_status'] = 2;
        //找到所有出库成功的订单
        $orderData = $ebayOrderModel->where($orderMap)
            ->field("ebay_id,scantime")
            ->select();
        if(!empty($orderData)){
            //查找sku信息
            foreach ($orderData as $v){
                $ebay_id = $v['ebay_id'];
                $scan_time = $v['scantime'];
                $goodsDetailData = $goodsSaleDetailModel->where(['ebay_id'=>$ebay_id])
                    ->field("sku,qty,storeid")
                    ->select();
                //没有数据直接跳过
                if(empty($goodsDetailData)){
                    $log .= "没有找到产品信息，sql语句 \r\n".$goodsSaleDetailModel->_sql()."\r\n";
                    writeFile($logPath,$log);
                    continue;
                }else{
                    foreach ($goodsDetailData as $value){
                        //默认拣货人为江鹏程
                        $sku = $value['sku'];
                        $saveMap['ebay_id'] = $ebay_id;
                        $saveMap['sku'] = $sku;
                        //根据仓库id找到数据表，虽然现在只有一个仓了，但是公司扩展之后肯定要加仓
                        $store_id = is_null($value['storeid']) ? $this->currStoreId : $value['storeid'];
                        $table_name = "ebay_onhandle_".$store_id;
                        $qty = $value['qty'];
                        //判断是否统计过了
                        $isSave = $statisticModel->where($saveMap)->getField("id");
                        if(!empty($isSave)){
                            continue;
                        }else{
                            //查找库位信息，根据库位找到拣货人，没有的话默认拣货人
                            $goods_location = M($table_name,"",C('DB_CONFIG_READ'))
                                ->where(['goods_sn'=>$sku])
                                ->getField('g_location');
                            if(empty($goods_location)){
                                continue;
                            }else{
                                //根据库位找到对应拣货人
                                $picker = $singleOrderService->getPicker($goods_location);
                            }
                            //确认订单类型,先找下是不是验货扫描的，不是的话就去找类型
                            $checkMap['status'] = 1;
                            $checkMap['ebay_id'] = $ebay_id;
                            $checkId = $checkScanModel->where($checkMap)->getField('id');
                            if(!empty($checkId)){
                                $type = 4;
                            }else{
                                $type = $orderTypeModel->where(['ebay_id'=>$ebay_id])->getField("type");
                            }
                            //开始插入数据到统计表
                            $statisticData['ebay_id'] = $ebay_id;
                            $statisticData['addtime'] = $endTime;
                            $statisticData['picktime'] = $scan_time;
                            $statisticData['picker'] = $picker;
                            $statisticData['qty'] = (int)$qty;
                            $statisticData['sku'] = $sku;
                            $statisticData['type'] = (int)$type;
                            $result = $statisticModel->add($statisticData);
                            //失败了，记录下日志
                            if(!$result){
                                $log .= "添加数据失败，sql语句 \r\n".$statisticModel->_sql()."\r\n";
                                writeFile($logPath,$log);
                            }
                        }

                    }
                }
            }
        }else{
            $log .= "没有找到订单，sql语句 \r\n".$ebayOrderModel->_sql()."\r\n";
            writeFile($logPath,$log);
        }
    }

    /**
     * 包装统计程序
     * @author Shawn
     * @date 2018/8/2
     */
    public function packerStatistics()
    {
        $logPath = dirname(dirname(THINK_PATH)) . '/log/statistics_task/' . date('Ymd') . '_packer.error.txt';
        $log = '['.date("Y-m-d H:i:s").']';
        //没有传时间，默认查3天以内订单
        if(isset($_SERVER['argv'][2])){
            $startTime = strtotime($_SERVER['argv'][2]);
        }else{
            $startTime = strtotime('-3 days');
        }
        $endTime = time();
        //根据时间找到已出库订单,查询丛库
        $ebayOrderModel = new EbayOrderModel("","",C('DB_CONFIG_READ'));
        $goodsSaleDetailModel = new GoodsSaleDetailModel("","",C('DB_CONFIG_READ'));
        $checkScanModel = new CheckScanOrderModel("","",C('DB_CONFIG_READ'));
        $orderTypeModel = new ErpOrderTypeModel("","",C('DB_CONFIG_READ'));
        $apiCheckModel = new ApiCheckskuModel("","",C('DB_CONFIG_READ'));
        //no 发货 no money
        $statisticModel = new PackerStatisticsModel();
        $orderMap['scantime'] = array("between",[$startTime,$endTime]);
        $orderMap['ebay_status'] = 2;
        //找到所有出库成功的订单
        $orderData = $ebayOrderModel->where($orderMap)
            ->field("ebay_id,scantime")
            ->select();
        if(!empty($orderData)){
            //查找sku信息
            foreach ($orderData as $v){
                $ebay_id = $v['ebay_id'];
                $scan_time = $v['scantime'];
                $goodsDetailData = $goodsSaleDetailModel->where(['ebay_id'=>$ebay_id])
                    ->field("sku,qty")
                    ->select();
                //没有数据直接跳过
                if(empty($goodsDetailData)){
                    $log .= "没有找到产品信息，sql语句 \r\n".$goodsSaleDetailModel->_sql()."\r\n";
                    writeFile($logPath,$log);
                    continue;
                }else{
                    foreach ($goodsDetailData as $value){
                        //默认包装人为江鹏程
                        $packer = "江鹏程";
                        $sku = $value['sku'];
                        $qty = $value['qty'];
                        $saveMap['ebay_id'] = $ebay_id;
                        $saveMap['sku'] = $sku;
                        //判断是否统计过了
                        $isSave = $statisticModel->where($saveMap)->getField("id");
                        if(!empty($isSave)){
                            continue;
                        }else{
                            //确认订单类型,先找下是不是验货扫描的，不是的话就去找类型
                            $checkMap['status'] = 1;
                            $checkMap['ebay_id'] = $ebay_id;
                            $checkData = $checkScanModel->where($checkMap)->getField('scan_user');
                            if(!empty($checkData)){
                                $type = 4;
                                $packer = $checkData;
                            }else{
                                //同步表找包装人
                                $pickData = $apiCheckModel->where(['ebay_id'=>$ebay_id])->getField("packinguser");
                                if (!empty($pickData)) {
                                    $packer = $pickData;
                                }
                                $type = $orderTypeModel->where(['ebay_id'=>$ebay_id])->getField("type");
                            }
                            //开始插入数据到统计表
                            $statisticData['ebay_id'] = $ebay_id;
                            $statisticData['addtime'] = $endTime;
                            $statisticData['packtime'] = $scan_time;
                            $statisticData['packer'] = $packer;
                            $statisticData['qty'] = (int)$qty;
                            $statisticData['sku'] = $sku;
                            $statisticData['type'] = (int)$type;
                            $result = $statisticModel->add($statisticData);
                            //失败了，记录下日志
                            if(!$result){
                                $log .= "添加数据失败，sql语句 \r\n".$statisticModel->_sql()."\r\n";
                                writeFile($logPath,$log);
                            }
                        }

                    }
                }
            }
        }else{
            $log .= "没有找到订单，sql语句 \r\n".$ebayOrderModel->_sql()."\r\n";
            writeFile($logPath,$log);
        }
    }

    /**
     * 第一次执行统计程序之前，先修复下核对扫描数据(默认把8月份的数据补入数据表)
     * @author Shawn
     * @date 2018/8/3
     */
    public function repairCheckScanData(){
        //没有传时间，默认补齐8月份数据
        if(isset($_SERVER['argv'][2])){
            $startTime = strtotime($_SERVER['argv'][2]);
        }else{
            $startTime = strtotime("2018-08-01 00:00:00");
        }
        $endTime = time();
        //找到核对扫描出库的数据
        $filed = 'operationuser,operationtime,ebay_id';
        $map['notes'] = array("like","%验货扫描成功%");
        $map['operationtime'] = array("between",array($startTime,$endTime));
        $orderLogModel = new OrderslogModel('','',C('DB_CONFIG_READ'));
        $checkScanModel = new CheckScanOrderModel();
        $orderData = $orderLogModel->where($map)
            ->field($filed)
            ->select();
        foreach ($orderData as $v){
            $data['scan_user']  = $v['operationuser'];
            $data['scan_time']  = $v['operationtime'];
            $data['ebay_id']    = $v['ebay_id'];
            $save = $checkScanModel->where(['ebay_id'=>$v['ebay_id']])->find();
            if(!empty($save)){
                continue;
            }else{
                $result = $checkScanModel->add($data);
                if($result){
                    echo "订单：".$v['ebay_id']."加入扫描表成功\r\n";
                }else{
                    echo "订单：".$v['ebay_id']."加入扫描表失败！\r\n";
                }
            }
        }

    }

    /**
     * 修改了账号名称，导致找不到用户id，修复下数据
     * @author Shawn
     * @date 2018/8/7
     */
    public function repairUserData()
    {
        $packModel = new PackerStatisticsModel();
        if(isset($_SERVER['argv'][2])){
            //找到修改的所有账户名
            $allUser = array('3163', '2252', '3166', '3167', '3075', '3169', '3170', '3171', '3172', '3174', '3175', '3176',
                '3177', '3178', '3179', '3180', '3181', '3182', '3183', '3184', '3185', '3186', '3187', '3188', '3189', '3190',
                '3191', '3192', '3193', '3233', '3234', '3235', '3236','3237','3238', '3239','3240','3241','3242','3243','3244',
                '3245','3246','3247', '3248', '3249', '3250', '3251', '3218', '3219', '3220', '3221', '3222', '3223', '3224', '3225',
                '3226', '3227', '3228', '3229', '3230', '3231', '3194', '3195', '3196', '3197', '3198', '3199', '3200', '3201', '3202', '3203',
                '3204', '3205', '3206', '3207', '3208', '3209', '3210', '3211', '3212', '3213', '3214', '3215', '3217', '3252',
                '3253', '3254', '3255', '3256', '3257');
            $map['packer'] = array("in",$allUser);
            $userModel = new EbayUserModel('','',C('DB_CONFIG_READ'));
            $data = $packModel->where($map)->field("packer")->group("packer")->select();
            if(empty($data)){
                echo "没有找到需要修复的用户数据";exit;
            }else{
                foreach ($data as $value){
                    $packer = $value['packer'];
                    $username = $userModel->where(['id'=>$packer])->getField("username");
                    if(empty($username)){
                        echo "没有找到用户\r\n";
                        continue;
                    }else{
                        $result = $packModel->where(['packer'=>$packer])->save(['packer'=>$username]);
                        echo $result."\r\n";
                    }
                }
            }
            echo '账号数据修复完毕！';exit;
        }else{
            //找到江鹏程的包装数据进行修复
            $packer = "江鹏程";
            $apiCheckModel = new ApiCheckskuModel("","",C('DB_CONFIG_READ'));
            $packData = $packModel->where(['packer'=>$packer])->field("id,ebay_id")->select();
            if(empty($packData)){
                echo "没有找到需要修复的数据";exit;
            }else{
                foreach ($packData as $v){
                    //找到包装人
                    $id = $v['id'];
                    $ebay_id = $v['ebay_id'];
                    $user = $apiCheckModel->where(['ebay_id'=>$ebay_id])->getField("packinguser");
                    if(empty($user)){
                        echo "没有找到".$ebay_id."这个订单的包装人\r\n";
                        continue;
                    }else{
                        $result = $packModel->where(['id'=>$id])->save(['packer'=>$user]);
                        echo $result."\r\n";
                    }
                }
            }
            echo '包装人员数据修复完毕！';exit;
        }
    }

    /**
     * 包装补贴统计程序
     * @author Shawn
     * @date 2018/8/15
     */
    public function packSubsidyStatistics()
    {
        $logPath = dirname(dirname(THINK_PATH)) . '/log/statistics_task/' . date('Ymd') . '_subsidy.error.txt';
        $log = '['.date("Y-m-d H:i:s").']';
        //没有传时间，默认查3天以内订单
        if(isset($_SERVER['argv'][2])){
            $startTime = strtotime($_SERVER['argv'][2]);
        }else{
            $startTime = strtotime('-3 days');
        }
        $endTime = time();
        //根据时间找到已出库订单,查询丛库
        $ebayOrderModel = new EbayOrderModel("","",C('DB_CONFIG_READ'));
        $goodsSaleDetailModel = new GoodsSaleDetailModel("","",C('DB_CONFIG_READ'));
        $apiCheckModel = new ApiCheckskuModel("","",C('DB_CONFIG_READ'));
        $goodsModel = new EbayGoodsModel("","",C('DB_CONFIG_READ'));
        //no 发货 no money
        $statisticModel = new PackSubsidyStatisticsModel();
        $orderMap['scantime'] = array("between",[$startTime,$endTime]);
        $orderMap['ebay_status'] = 2;
        //找到所有出库成功的订单
        $orderData = $ebayOrderModel->where($orderMap)
            ->field("ebay_id,scantime")
            ->select();
        if(!empty($orderData)){
            //查找sku信息
            foreach ($orderData as $v){
                $ebay_id = $v['ebay_id'];
                $scan_time = $v['scantime'];
                $goodsDetailData = $goodsSaleDetailModel->where(['ebay_id'=>$ebay_id])
                    ->field("sku,qty")
                    ->select();
                //没有数据直接跳过
                if(empty($goodsDetailData)){
                    $log .= "没有找到产品信息，sql语句 \r\n".$goodsSaleDetailModel->_sql()."\r\n";
                    writeFile($logPath,$log);
                    continue;
                }else{
                    foreach ($goodsDetailData as $value){
                        //默认包装人为江鹏程
                        $packer = "江鹏程";
                        $sku = $value['sku'];
                        $qty = $value['qty'];
                        $saveMap['ebay_id'] = $ebay_id;
                        $saveMap['sku'] = $sku;
                        //找到sku对应产品名称
                        $goods_name = $goodsModel->where(['goods_sn'=>$sku])->getField("goods_name");
                        if(empty($goods_name)){
                            $log .= "没有找到产品名，sql语句 \r\n".$goodsModel->_sql()."\r\n";
                            writeFile($logPath,$log);
                            continue;
                        }else{
                            $subsidy_g_name = '钢化';
                            $subsidy_g_result = strstr($goods_name, $subsidy_g_name);
                            $subsidy_z_name = '总成';
                            $subsidy_z_result = strstr($goods_name, $subsidy_z_name);
                            if(!$subsidy_g_result && !$subsidy_z_result){
                                continue;
                            }else{
                                $type = $subsidy_g_result ? 1 : 2;
                            }

                        }
                        //判断是否统计过了
                        $isSave = $statisticModel->where($saveMap)->getField("id");
                        if(!empty($isSave)){
                            continue;
                        }else{
                            //同步表找包装人
                            $pickData = $apiCheckModel->where(['ebay_id'=>$ebay_id])->getField("packinguser");
                            if (!empty($pickData)) {
                                $packer = $pickData;
                            }
                            //开始插入数据到统计表
                            $statisticData['ebay_id'] = $ebay_id;
                            $statisticData['addtime'] = $endTime;
                            $statisticData['packtime'] = $scan_time;
                            $statisticData['packer'] = $packer;
                            $statisticData['qty'] = (int)$qty;
                            $statisticData['sku'] = $sku;
                            $statisticData['type'] = (int)$type;
                            $statisticData['sku_name'] = $goods_name;
                            $result = $statisticModel->add($statisticData);
                            //失败了，记录下日志
                            if(!$result){
                                $log .= "添加数据失败，sql语句 \r\n".$statisticModel->_sql()."\r\n";
                                writeFile($logPath,$log);
                            }
                        }

                    }
                }
            }
        }else{
            $log .= "没有找到订单，sql语句 \r\n".$ebayOrderModel->_sql()."\r\n";
            writeFile($logPath,$log);
        }
    }

    /**
     * 拣货、包装、补贴数据统计
     * @author Shawn
     * @date 2018/8/18
     */
    public function statistics(){
        //改了账号名称，防止统计数据异常
        $allUser = array('3163', '2252', '3166', '3167', '3075', '3169', '3170', '3171', '3172', '3174', '3175', '3176',
            '3177', '3178', '3179', '3180', '3181', '3182', '3183', '3184', '3185', '3186', '3187', '3188', '3189', '3190',
            '3191', '3192', '3193', '3233', '3234', '3235', '3236','3237','3238', '3239','3240','3241','3242','3243','3244',
            '3245','3246','3247', '3248', '3249', '3250', '3251', '3218', '3219', '3220', '3221', '3222', '3223', '3224', '3225',
            '3226', '3227', '3228', '3229', '3230', '3231', '3194', '3195', '3196', '3197', '3198', '3199', '3200', '3201', '3202', '3203',
            '3204', '3205', '3206', '3207', '3208', '3209', '3210', '3211', '3212', '3213', '3214', '3215', '3217', '3252',
            '3253', '3254', '3255', '3256', '3257');
        $logPath = dirname(dirname(THINK_PATH)) . '/log/statistics_task/' . date('Ymd') . '.error.txt';
        $log = '['.date("Y-m-d H:i:s").']';
        //没有传时间，默认查3天以内订单
        if(isset($_SERVER['argv'][2])){
            $startTime = strtotime($_SERVER['argv'][2]);
        }else{
            $startTime = strtotime('-3 days');
        }
        $endTime = time();
        //根据时间找到已出库订单,查询丛库
        $ebayOrderModel = new EbayOrderModel("","",C('DB_CONFIG_READ'));
        $goodsSaleDetailModel = new GoodsSaleDetailModel("","",C('DB_CONFIG_READ'));
        $checkScanModel = new CheckScanOrderModel("","",C('DB_CONFIG_READ'));
        $orderTypeModel = new ErpOrderTypeModel("","",C('DB_CONFIG_READ'));
        $apiCheckModel = new ApiCheckskuModel("","",C('DB_CONFIG_READ'));
        $goodsModel = new EbayGoodsModel("","",C('DB_CONFIG_READ'));
        $userModel = new EbayUserModel('','',C('DB_CONFIG_READ'));
        $pickOrderDetailModel = new PickOrderDetailModel('','',C('DB_CONFIG_READ'));
        $pickOrderModel  = new PickOrderModel('','',C('DB_CONFIG_READ'));
        //查找拣货人
        $singleOrderService = new CreateSingleOrderService();
        //no 发货 no money
        $statisticModel = new StatisticsModel();
        $orderMap['scantime'] = array("between",[$startTime,$endTime]);
        $orderMap['ebay_status'] = 2;
        //找到所有出库成功的订单
        $orderData = $ebayOrderModel->where($orderMap)
            ->field("ebay_id,scantime")
            ->order("scantime asc")
            ->select();
        if(!empty($orderData)){
            //查找sku信息
            foreach ($orderData as $v){
                $ebay_id = $v['ebay_id'];
                $scan_time = $v['scantime'];
                $goodsDetailData = $goodsSaleDetailModel->where(['ebay_id'=>$ebay_id])
                    ->field("sku,qty,storeid")
                    ->select();
                //没有数据直接跳过
                if(empty($goodsDetailData)){
                    $log .= "没有找到产品信息，sql语句 \r\n".$goodsSaleDetailModel->_sql()."\r\n";
                    writeFile($logPath,$log);
                    continue;
                }else{
                    foreach ($goodsDetailData as $value){
                        //默认拣货人为江鹏程
                        $picker = "江鹏程";
                        $packer = "江鹏程";
                        $sku = $value['sku'];
                        $saveMap = [];
                        $saveMap['ebay_id'] = $ebay_id;
                        $saveMap['sku'] = $sku;
                        //sku数量
                        $qty = $value['qty'];
                        //判断是否统计过了
                        $isSave = $statisticModel->where($saveMap)->getField("id");
                        if(!empty($isSave)){
                            continue;
                        }else{
                            //找下补贴类型，默认为0
                            $sub_type = 0;
                            //找到sku对应产品名称
                            $goods_name = $goodsModel->where(['goods_sn'=>$sku])->getField("goods_name");
                            if(empty($goods_name)){
                                $goods_name = '';
                                $log .= "没有找到产品名，sql语句 \r\n".$goodsModel->_sql()."\r\n";
                                writeFile($logPath,$log);
                            }else{
                                $subsidy_g_name = '钢化';
                                $subsidy_g_result = strstr($goods_name, $subsidy_g_name);
                                $subsidy_z_name = '总成';
                                $subsidy_z_result = strstr($goods_name, $subsidy_z_name);
                                if($subsidy_g_result || $subsidy_z_result){
                                    $sub_type = $subsidy_g_result ? 1 : 2;
                                }

                            }
                            //确认订单类型,先找下是不是验货扫描的，不是的话就去找类型
                            $checkMap['status'] = 1;
                            $checkMap['ebay_id'] = $ebay_id;
                            $checkData = $checkScanModel->where($checkMap)->getField('scan_user');
                            if(!empty($checkData)){
                                $type = 4;
                                $packer = $checkData;
                            }else{
                                //同步表找包装人
                                $pickData = $apiCheckModel->where(['ebay_id'=>$ebay_id])->getField("packinguser");
                                if (!empty($pickData)) {
                                    $packer = $pickData;
                                }
                                $type = $orderTypeModel->where(['ebay_id'=>$ebay_id])->getField("type");
                            }
                            //如果是多品多货需要单独找下拣货人
                            if($type == 3){
                                $saveMap['is_baled'] = 1;
                                $saveMap['is_delete'] = 0;
                                $ordersn = $pickOrderDetailModel->where($saveMap)->order("id desc")->getField("ordersn");
                                if(!empty($ordersn)){
                                    $pickData = $pickOrderModel->where(['ordersn'=>$ordersn])->getField("pickuser");
                                    if(!empty($pickData)){
                                        $picker = $pickData;
                                    }
                                }
                            }else{
                                //根据仓库id找到数据表，虽然现在只有一个仓了，但是公司扩展之后肯定要加仓
                                $store_id = is_null($value['storeid']) ? $this->currStoreId : $value['storeid'];
                                $table_name = "ebay_onhandle_".$store_id;
                                //查找库位信息，根据库位找到拣货人，没有的话默认拣货人
                                $goods_location = M($table_name,"",C('DB_CONFIG_READ'))
                                    ->where(['goods_sn'=>$sku])
                                    ->getField('g_location');
                                if(!empty($goods_location)){
                                    //根据库位找到对应拣货人
                                    $picker = $singleOrderService->getPicker($goods_location);
                                }
                            }
                            if(in_array($picker,$allUser)){
                                $username = $userModel->where(['id'=>$picker])->getField("username");
                                if(!empty($username)){
                                    $picker = $username;
                                }
                            }
                            if(in_array($packer,$allUser)){
                                $username = $userModel->where(['id'=>$packer])->getField("username");
                                if(!empty($username)){
                                    $packer = $username;
                                }
                            }
                            //开始插入数据到统计表
                            $statisticData['ebay_id'] = $ebay_id;
                            $statisticData['addtime'] = $endTime;
                            $statisticData['ptime'] = $scan_time;
                            $statisticData['picker'] = $picker;
                            $statisticData['packer'] = $packer;
                            $statisticData['qty'] = (int)$qty;
                            $statisticData['sku'] = $sku;
                            $statisticData['type'] = (int)$type;
                            $statisticData['sub_type'] = (int)$sub_type;
                            $statisticData['sku_name'] = $goods_name;
                            $result = $statisticModel->add($statisticData);
                            //失败了，记录下日志
                            if(!$result){
                                $log .= "添加数据失败，sql语句 \r\n".$statisticModel->_sql()."\r\n";
                                writeFile($logPath,$log);
                            }
                        }

                    }
                }
            }
        }else{
            $log .= "没有找到订单，sql语句 \r\n".$ebayOrderModel->_sql()."\r\n";
            writeFile($logPath,$log);
        }
    }

    /**
     * 单品多货包装日志统计
     * @author Shawn
     * @date 2018/12/27
     * @desc  php tcli.php Package/StatisticsTask/packLogStatistics
     */
    public function packLogStatistics()
    {
        $packLogModel = new OneSkuPackLogModel();
        $pickDetailModel = new PickOrderDetailModel('','',C('DB_CONFIG_READ'));
        $field = 'id,operationuser,ebay_id,sku';
        //找到所有没有统计的日志
        $p_map['type'] = 0;
        $count      = $packLogModel->where($p_map)->count();
        if(empty($count) || $count == 0){
            exit("暂无未处理日志信息");
        }
        $page       = 3000;
        $pCount     = ceil($count / $page);
        p("开始单品多货日志统计，未统计总数：{$count}, 总页数： {$pCount} ，每页： {$page}");
        for ($curr = 0; $curr <= $pCount; $curr++) {
            if ($curr <= 0) {
                $curr = 1;
            }
            p("当前页码：{$curr}");
            $pageSize  = ($curr - 1) * $page;
            $packLogData = $packLogModel->where($p_map)
                ->limit($pageSize, $page)
                ->field($field)
                ->select();
            if(empty($packLogData)){
                p("没有需要处理的日志信息：{$curr}");
                continue;
            }
            foreach ($packLogData as $value){
                //找到最后一条包装人员
                $ebay_id            = $value['ebay_id'];
                $map['ebay_id']     = $ebay_id;
                $map['sku']         = $value['sku'];
                $map['is_delete']   = 0;
                $map['is_baled']    = 1;
                $map['scaning']     = 2;
                $userName = $value['operationuser'];
                $upMap['id'] = $value['id'];
                $pickUser = $pickDetailModel->where($map)->order("scan_time desc")->getField('scan_user');
                if(empty($pickUser)){
                    p("订单号：{$ebay_id} 尚未完成包装，默认包装失败！");
                    $saveData = [];
                    $saveData['type'] = 1;
                    $saveData['notes']  = $userName.'包装订单'.$ebay_id.'失败';
                    $result = $packLogModel->where($upMap)->save($saveData);
                    if($result === false){
                        p("订单号：{$ebay_id} 数据库更新状态失败！");
                    }
                }else{
                    //如果最后一个包装人员是日志记录这个人，表示是他完成的
                    $saveData = [];
                    $saveData['type'] = 1;
                    if($userName == $pickUser){
                        $saveData['status'] = 1;
                        $saveData['notes']  = $userName.'包装订单'.$ebay_id.'成功';
                    }else{
                        $saveData['notes']  = $userName.'包装订单'.$ebay_id.'失败';
                    }
                    $result = $packLogModel->where($upMap)->save($saveData);
                    if($result === false){
                        p("订单号：{$ebay_id} 数据库更新状态失败！");
                    }
                }

            }
            p("当前页处理完成：limit($pageSize,$page)");
        }
        p("任务完成！");exit;
    }

    /**
     * @desc 清理数据
     * @cli php tcli.php Package/StatisticsTask/clearData
     * @author Shawn
     * @date 2019/5/3
     */
    public function clearData()
    {
        //默认保留半年数据
        $day = (int)I("day");
        $day = ($day == 0) ? '180' : $day;
        p("开始清理程序，清理".$day."天前数据");
        $map['ptime'] = array("lt",strtotime("-{$day} days"));
        $statisticModel = new StatisticsModel();
        $count      = $statisticModel->where($map)->count();
        if(empty($count) || $count == 0){
            p("暂无需要清理数据");
            exit;
        }
        $page       = 5000;
        $pCount     = ceil($count / $page);
        p("需要清理数据总数：{$count}, 总页数： {$pCount} ，每次删除： {$page}");
        for ($curr = 1; $curr <= $pCount; $curr++) {
            p("当前页码：{$curr}");
            $pageSize  = ($curr - 1) * $page;
            $result = $statisticModel
                ->where($map)
                ->limit($page)
                ->getField("id",true);
            if(empty($result)){
                p("没有找到需要删除数据");
                continue;
            }
            foreach ($result as $v){
                $statisticModel->where(["id"=>$v])->delete();
                if($result === false){
                    p("id:".$v."清理失败");
                }
            }

        }
        p("done");
        exit;
    }
}