<?php
/**
 *测试人员谭 2017-05-02 19:59:00
 *说明: 打印面单的玩意
 */
//TODO 打印的人 请将域名 帮到 hosts ： 218.17.38.218  erpcnapi.com

namespace Transport\Service;

use Common\Model\EbayCurrencyModel;
use Common\Model\OrderDetailModel;
use Common\Model\OrderModel;
use Task\Model\EbayHandleModel;
use Transport\Model\CarrierModel;
use Transport\Model\CountriesModel;

class PrintService extends PrintBaseService{

    public $AllCarrier = null;
    public $CarrierHouseName = null;
    public $CarrierModel = null;
    public $OrderModel = null;
    public $OrderDetailModel = null;
    public $EbayHandle = null;
    public $CountriesModel = null;
    public $CurrencyModel = null;


    public function __construct(){
        $this->AllCarrier       = C('CARRIER_TEMPT'); // 常规模板
        $this->CarrierHouseName = C('CARRIER_WHOUSE_NAME'); //仓库名称 速卖通线上包用的
        $this->Carrier15        = C('CARRIER_TEMPT_15'); // 10*15 的运输方式
        $this->Carrier20        = C('CARRIER_TEMPT_20'); // 10*20 的运输方式
        $this->PdfPath        = C('CARRIER_TEMPLATE_PDF_PATH');// pdf 运输方式
        $this->ChangeTEMPT    = C('CARRIER_MORE_TEMPLATE'); //一个运输方式还要求用8种面单
        $this->CarrierModel     = new CarrierModel();
        $this->OrderModel       = new OrderModel();
        $this->OrderDetailModel = new OrderDetailModel();
        $this->EbayHandle       = new EbayHandleModel();
        $this->CountriesModel   = new CountriesModel();
        $this->CurrencyModel   = new EbayCurrencyModel();
    }


    //分辨出来哪些是10×10  那些是 15×10

    function Splite2Label($orders){
        $OrderModel=$this->OrderModel;
        $bill15=[];
        $bill10=[];


        foreach($orders as $ebay_id){
            $carriers=$OrderModel->where("ebay_id='$ebay_id'")->field('ebay_carrier')->find();
            //如果这个是 15 × 15 的标签
            if(array_key_exists($carriers['ebay_carrier'],$this->Carrier15)){
                $bill15[]=$ebay_id;
            }
            else if(array_key_exists($carriers['ebay_carrier'],$this->Carrier20))
            {
                $bill20[] = $ebay_id;
            }
            else{
                $bill10[]=$ebay_id;
            }
        }

        return ['bill10'=>$bill10,'bill15'=>$bill15, 'bill20' => $bill20];
    }


    /**
     * @desc   创建面单
     * @access 
     * @author
     * @param  
     * @return
     */
    /**
    *测试人员谭 2018-07-25 18:16:57
    *说明: 方法 此时被修改：
     * 修改的内容是 根据参数 返回不同结果  为了让这个 方法 可以供其他程序使用!
     *
    */
    function getOrderInfo($ebay_id,$is_check=false) {

        $debug=(int)$_GET['debug'];
        $OrderModel = $this->OrderModel;
        $orderinfo = $OrderModel->getOrderinfo($ebay_id);  // 获取订单详情
        if(!$orderinfo){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.$ebay_id.' 找不到订单<br>';
                if($is_check){
                    return ' <!--failure-->'.$ebay_id.' 找不到订单<br>';
                }
            }
            return false;
        }
        $orderinfo = $orderinfo[0];

        $skuInfo = $OrderModel->OrderResolve($ebay_id);

        //debug($skuInfo);
        if (strtolower($orderinfo['ebay_phone']) == 'invalid request') {
            $orderinfo['ebay_phone'] = '';
        }
        if (strtolower($orderinfo['ebay_phone1'] == 'invalid request')) {
            $orderinfo['ebay_phone1'] = '';
        }
        $carrier          = trim($orderinfo['ebay_carrier']);
        $ebay_postcode    = trim($orderinfo['ebay_postcode']);
        $storeid          = trim($orderinfo['ebay_warehouse']);
        $ebay_countryname = trim($orderinfo['ebay_countryname']);
        $ebay_tracknumber = trim($orderinfo['ebay_tracknumber']);
        $ebay_ordersn     = trim($orderinfo['ebay_ordersn']);

        // 判断是否是测试信息
        $testTemplates = C('CARRIER_TEMPLATE_TEST');
        if (array_key_exists($carrier, $testTemplates) && $_GET['test'] == '') {
            if($_GET['debug'] == 1){
                echo ' '.$ebay_id.' 测试中...<br>';
            }
            return false;
        }

        // 判断是否有跟踪号
        if(empty($ebay_tracknumber)){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.$ebay_id.' 没有跟踪号<br>';
                if($is_check){
                    return ' <!--failure-->'.$ebay_id.' 没有跟踪号<br>';
                }
            }
            return false;
        }

        // 查询国家二字码
        $countrycode = $this->CountriesModel->getCountryCodeByCountryname($ebay_countryname);
        $orderinfo['countrycode'] = $countrycode;

        if($orderinfo['countrycode'] == ''){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.' '.$ebay_id.',订单没有国家二字码:<br>';
                if($is_check){
                    return ' <!--failure-->'.$ebay_id.' 订单没有国家二字码<br>';
                }
            }
            return false;
        }

        // 有一些特殊的 国家 2字 码 要变化一下
        $this->changeCountryCode($orderinfo);
        // 根据国家二字码查询中文国家名称
        $countrycn = $this->CountriesModel->where("char_code='$countrycode'")->field('name')->find();
        $orderinfo['countrycn'] = $countrycn['name'];

        // 根据物流渠道、国家二字码查询创建面单方式
        $orderinfo['template'] = $this->getTemplate($carrier, $countrycode);

        // 仓库名称
        $orderinfo['warehousename'] = $this->getWarehouseName($carrier); 

        if($orderinfo['template']==false){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.$ebay_id.'运输方式【'.$carrier.'】没有支持的物流模板:<br>';
                if($is_check){
                    return ' <!--failure-->'.$ebay_id.'运输方式【'.$carrier.'】没有支持的物流模板:<br>';
                }
            }
            return false;
        }



        // TODO  各种分区代码 ，都写在这里 避免变量 定义不当 -------START-----
        /***测试人员谭 2017-05-05 17:23:52
        *说明: 关于分拣分区 拒绝打印的条件是 === false  如果不是 EUB 才会强制做这个限制
        //TODO: 如果有其他的 运输方式 必须要 分拣分区的话 请在 方法fenjianCode 后面接着 写  return false
         */
        $orderinfo['postnum']=$this->fenjianCode($ebay_postcode,$carrier); // 分拣分区

        $CarrierInfo= $this->getCarrierInfo($carrier); // 根据运输方式查询订单回邮地址
        $orderinfo['carrier_mark_code'] = $CarrierInfo['mark_code'];
        $orderinfo['backAddress']=$CarrierInfo['address']; // 回邮地址
        // hank 2018/3/23 9:32  添加联系电话
        $orderinfo['tel']=$CarrierInfo['tel'];
        $orderinfo['postnum'] = SortingCode::getInstance()->getSortingCode($orderinfo);
        $orderinfo['zipcode']= substr($ebay_postcode,0,5); // 这里比较特殊 是邮编的 前 5
        if($carrier=='俄速通'){
            $zonecode=$this->getZoneNum($ebay_postcode);
            if(!$zonecode){
                if($debug==1||$is_check){
                    echo ' <!--failure-->'.' 俄速通,'.$ebay_id.' 邮编分区有误<br>';
                    if($is_check){
                        return ' <!--failure-->'.' 俄速通,'.$ebay_id.' 邮编分区有误<br>';
                    }
                }
                return false;
            }
            //TODO 分拣分区
            $orderinfo['zonecode']=$zonecode;
        }

        if($carrier=='英国直飞专线-安骏'){
            $zonecode=$this->getUKZoneNum($ebay_postcode);
            if(!$zonecode){
                if($debug==1||$is_check){
                    echo ' <!--failure-->'.' 英国直飞专线-安骏,'.$ebay_id.' 邮编分区有误<br>';
                    if($is_check){
                        return ' <!--failure-->'.' 英国直飞专线-安骏,'.$ebay_id.' 邮编分区有误<br>';
                    }
                }
                return false;
            }
            $orderinfo['zonecode']=$zonecode;
        }

        if($carrier == '香港平邮-ZTO')
        {
            $zonecode = $this->getZhongtongZonecode($orderinfo['countrycode'], $orderinfo['ebay_postcode']);
            if(!$zonecode)
            {
                echo ' <!--failure-->' . ' 香港平邮-ZTO,' . $ebay_id . ' 国家分拣码为空<br>';
                if($is_check)
                {
                    return ' <!--failure-->' . ' 香港平邮-ZTO,' . $ebay_id . ' 邮编分区有误<br>';
                }
                return false;
            }
            $orderinfo['zonecode'] = $zonecode;
        }

        if(in_array($carrier, ['比利时小包非欧盟','比利时小包'])){
            $zonecode = $this->getEmsCode($orderinfo['countrycode']);
            if(!$zonecode)
            {
                echo ' <!--failure-->' .$carrier . $ebay_id . ' 国家分拣码为空<br>';
                if($is_check)
                {
                    return ' <!--failure-->' . $carrier . $ebay_id . ' 邮编分区有误<br>';
                }
                return false;
            }
            $orderinfo['zonecode'] = $zonecode;
        }

        if (strpos($carrier, '出口易小包') !== false) {

            $orderinfo['cky_data'] = $this -> getChuKouYiCode($orderinfo['countrycode'], $orderinfo['ebay_postcode']);
//             TOD 先假设一个pxorderid = 123456
            //$orderinfo['pxorderid'] = $orderinfo['pxorderid'] ? $orderinfo['pxorderid'] : 123456789;


            if (!$orderinfo['pxorderid']) {
                if($debug==1||$is_check){
                    echo ' <!--failure-->'."订单:{$ebay_id}; 运输方式: {$carrier} 没有获取pxid数据，无法打印.<br>";
                    if($is_check){
                        return ' <!--failure-->'."订单:{$ebay_id}; 运输方式: {$carrier} 没有获取 Px ID 数据，无法打印.<br>";
                    }
                }
                return false;
            }
        }


        /**
        *测试人员谭 2017-07-17 17:54:36
        *说明: 可能没有交运
        */
         if($carrier=='EUB'||stristr($carrier,'UBI')){
             if (!$orderinfo['pxorderid']) {

                 if($debug==1||$is_check){
                     echo ' <!--failure-->'."订单:{$ebay_id}; 运输方式: {$carrier} 可能没有交运，无法打印.<br>";
                     if($is_check){
                         return ' <!--failure-->'."订单:{$ebay_id}; 运输方式: {$carrier} 可能没有交运，无法打印.<br>";
                     }
                 }

                 return false;
             }

         }



        // 重要国家 的分拣代码 有很多国家会需要
        $orderinfo['importantcode']=$this->ImportantCountryCode($countrycode);
        // hank 2018/3/26 15:32 厦门仓都需要这个美国分区
        if(strpos($carrier,'厦门') !== false){
            $orderinfo['fenqucode']=$this->DingliFenqu($ebay_postcode,$countrycode);
        }

        //ToDO  各种分区代码 ，都写在这里 避免变量 定义不当 --------------------END
        $orderinfo['sku_count'] = count($skuInfo);
        $orderinfo['skustr']= $this->getLocationstr($skuInfo,$storeid,$carrier);
        if ($carrier == 'lazada物流') {
            $result = $this -> getLazadaData($ebay_id, $orderinfo);

            if (!$result['status'] ) {
                if($debug==1||$is_check){
                    echo ' <!--failure-->'.$result['data'];
                    if($is_check){
                        return ' <!--failure-->'.$result['data'];
                    }
                }
                return false;
            }
        } elseif ($carrier == '圆通-shopee') {
            $this -> getYuanTongShopEData($orderinfo['ebay_account'], $orderinfo['ebay_id'], $orderinfo);
        } elseif ($carrier == '新加坡小包平邮' || $carrier == '新加坡小包挂号') {
            $orderinfo['singapore_data'] = $this -> getSingaporeFenJanCode($orderinfo['countrycode'], $orderinfo['ebay_postcode']);
        }

        // 西班牙专线平邮获取分区码
        if ($carrier === '西班牙专线平邮') {
            $orderinfo['fenqu'] = $this -> getSpainAreaCode($orderinfo['ebay_postcode']);
        }

        $shenbao=explode('..',$orderinfo['skustr']);

        $orderinfo['shenbao']=$shenbao[0];




        //debug($skuInfo);
        if($orderinfo['skustr']==''){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.$ebay_id.' SKU信息异常:<br>';
                if($is_check){
                    return ' <!--failure-->'.$ebay_id.' SKU信息异常:<br>';
                }
            }
            return false;
        }

        if($orderinfo['postnum']===false){
            if($debug==1||$is_check){
                echo ' <!--failure-->'.'分拣分区异常:<br>';
                if($is_check){
                    return ' <!--failure-->'.'分拣分区异常:<br>';
                }
            }
            return false;
        }

        /**
        *测试人员谭 2017-05-10 18:39:53
        *说明: 最后执行 获取pdf  这样的话 debug 的时候 会快很多
        */
        //dump($carrier);
        // hank 2017-11-08 如果模板带有img的  就请求图片
        if(strstr($orderinfo['template'],'img')){ // pdf 转图片的 干活
            $orderinfo['pdfimg']=$this->getPdf2imgUrl($ebay_id, $carrier,$is_check);

            if($orderinfo['pdfimg']==false){
                if($debug==1||$is_check){
                    echo ' <!--failure-->'." {$ebay_id}  PDF面单获取失败，请手动重新获取面单！<br> ";
                    if($is_check){
                        return ' <!--failure-->'." {$ebay_id}  PDF面单获取失败，请手动重新获取面单！<br> ";
                    }
                }
                return false;
            }

            /**
             *测试人员谭 2018-03-08 20:28:15
             *说明: 万易通 澳大利亚 这个垃圾有一个特殊情况  部分国家 自动变成 2章 pdf //TODO ===
             */

            if(!is_array($orderinfo['pdfimg'])&&$carrier=='香港平邮易递宝（万邑通）'){
                $orderinfo['template']='img1'; // 系统自动切换成  1 个 pdf 的模板
            }

            /**
             *测试人员谭 2017-06-03 20:28:15
             *说明: 顺丰这个垃圾有一个特殊情况  部分国家 自动变成 2章 pdf //TODO ===
             */

            if(is_array($orderinfo['pdfimg'])&&$carrier=='顺丰国际小包'){
                $orderinfo['template']='img2'; // 系统自动切换成  2 个 pdf 的模板
            }

            /**
            *测试人员谭 2017-06-03 20:38:24
            *说明: 顺达宝平邮 这个狗屎 也跟着凑热闹  时不时闹出 2章 pdf 去你大爷的 //TODO==
            */
            if(is_array($orderinfo['pdfimg'])&&$carrier=='顺达宝平邮'){
                // 系统自动取地一个
                $orderinfo['pdfimg']=$orderinfo['pdfimg'][0];
            }
        }
        /**
         *测试人员谭 2017-06-07 20:41:03
         *说明: 云图特殊情况
         */
        if($carrier=='华南小包挂号'){
            if($countrycode=='FR'){
                $orderinfo['template']='hnxbgh_FR';
            }

            if($countrycode=='DE'){
                $orderinfo['template']='hnxbgh_DE';
            }

            if($countrycode=='NZ'){
                $orderinfo['template']='hnxbgh_NZ';
            }

            if(in_array($countrycode,array('HR','PL','SI','RT'))){
                $orderinfo['template']='hnxbgh_VPG';
            }

        }


        /**
        *测试人员谭 2017-08-11 22:24:17
        *说明: shopee 的字画面但 需要知道是普货 还是 特货，由订单API 里面的返回值，存储于 ebay_orderdetail.listingType
         * Start
         * hank 2017-11-06 jumia 平台面单需要产品描述 ebay_orderdetail.ebay_itemtitle
        */
        $goods_to_declare=$this->OrderDetailModel
            ->field('ListingType,ebay_itemtitle')
            ->where(['ebay_ordersn'=>$ebay_ordersn])
            ->find();

        $orderinfo['goods_to_declare']=trim($goods_to_declare['ListingType']);
        $orderinfo['ebay_itemtitle']=trim($goods_to_declare['ebay_itemtitle']);

        /*-----END------测试人员谭2017-08-11 22:31:39---END---*/

        // ran  根据币种转成美元
        $ebay_currency = strtoupper(trim($orderinfo['ebay_currency']));
        if($ebay_currency != 'USD'){
            $rateInfo = $this->CurrencyModel->getRates();
            $rate = $rateInfo[$ebay_currency];
            $orderinfo['ebay_total'] = $rate*$orderinfo['ebay_total'];
        }
        // hank 2017/12/5 19:35 小包统一申报价值
        $sbjz = $this->sbjz($orderinfo['ebay_total']);
        $orderinfo['sbjz'] = $sbjz;

        // hank 2018/1/23 11:58 e速宝 地区代码 分拣码 中英文申报名
        $orderinfo['esbzonecode'] = $this->getZoneCode($countrycode,$ebay_postcode);
        $orderinfo['esbfenjiancode'] = $this->getFenJianMa($countrycode,$ebay_postcode);
        foreach($skuInfo as $item){
            $orderinfo['yingwen'] = $item['5'];
            $orderinfo['zhongwen'] = $item['10'];
            $orderinfo['price'] = $item[2];
            $orderinfo['sb_weight'] = $item[8];
            break;
        }

        // hank 2018/2/2 17:28 线下EUB广州-YLD  线下EUB广州(带电)-YLD 专用分拣码
        $orderinfo['eubfenjian'] = $this->eubfenjianCode($ebay_postcode,$countrycode);

        return $orderinfo;
    }

    /**
     * hank
     * 2017-12-05
     * @param $ebay_total
     * @return float|int
     * 各种小包统一申报价值
     * 按美元计算
     * 蔡秋君要求的
     * （新）5美金以内的订单，按照实际销售金额的来申报; 5-10美金 申报5美金; 超过10美金以上8-10美金随机 hank 2018/1/5 14:32
     */
    private function sbjz($ebay_total){
        // hank 2018/2/2 17:30 如果为0则按0.1计算
        if($ebay_total == 0 || empty($ebay_total)){
            $ebay_total = 0.1;
        }
        $declare = 0;
        $min     = 8;
        $max     = 10;

        if ($ebay_total <= 5) {
            return $ebay_total;
        }

        if ($ebay_total >5 && $ebay_total <= 10) {
            return 5;
        }

        $declare = round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);

        return $declare;
    }


    // 获取 库位 以及 捡货信息
    function getLocationstr($skuInfo,$storeid,$carrier){
        //return false;
        // 要填写申报名么
        $shenbao_name=false;
        $index=0;
        if(strstr($carrier,'EUB')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'速卖通线上')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'东莞小包')){
            $shenbao_name=true;
        }
        if(strstr($carrier,'荷兰邮政')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'厦门小包')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'土耳其')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'福州小包')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'顺友通')){
            $shenbao_name=true;
        }
        if(strstr($carrier,'顺邮宝')){
            $shenbao_name=true;
        }
        if(strstr($carrier,'华南小包平邮')){
            $shenbao_name=true;
        }
        if(strstr($carrier,'lazada物流')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'欧速通')){
            $shenbao_name=true;
        }

        if(strstr($carrier,'顺邮宝')){
            $shenbao_name=true;
        }

        if (strstr($carrier, '新加坡小包')) {
            $shenbao_name=true;
        }
        if (strstr($carrier, '易通')) {
            $shenbao_name=true;
        }

        if (strstr($carrier, '圆通-shopee')) {
            $shenbao_name=true;
        }

        if (strstr($carrier, '出口易小包')) {
            $shenbao_name = true;
        }

        if (strstr($carrier, '美国专线-安骏')) {
            $shenbao_name = true;
        }

        if (strstr($carrier, 'DHl小包挂号')) {
            $shenbao_name = true;
        }

        $EbayHandle=$this->EbayHandle;
        $str='';
        foreach($skuInfo as $key=>$Val){
            $rr=$EbayHandle->table("ebay_onhandle_{$storeid}")->where("goods_sn='$key'")
                ->field('g_location as local')->find();
            $local=$rr['local'];
            if(!$rr){
                if($_GET['debug']==1){
                    echo '找不到SKU:'.$key;
                }

                return false;
            }
            if($shenbao_name&&$index==0){
                $index=1;
                $str.=$Val[5].'.. '.$Val[0].'*'.$key.'('.$local.'),';
            }else{
                $str.=$Val[0].'*'.$key.'('.$local.'),';
            }
        }

        return trim($str,',');
    }

    // 按照库位排序的干活

    function SortOrder($Orders){
        $OrderModel=$this->OrderModel;
        $NewSortOrder=[];
        foreach($Orders as $ebay_id){
            $local=$OrderModel->getOrderMainSKULocal($ebay_id,C("CURRENT_STORE_ID"));
            $NewSortOrder[$ebay_id] = $local;
        }

        // debug($NewSortOrder);
        natsort($NewSortOrder);

        $arraysss = array();

        foreach ($NewSortOrder as $key => $v) {
            $arraysss[] = $key;
        }

        unset($NewSortOrder);
        return $arraysss;

    }

    // 分拣代码  EUB 系列的 会用到
    function fenjianCode($postCode,$carrier){

        $postCode=substr(trim($postCode),0,3);
        $arr=array(
            '1F'=>array('000','001','002','003','004','005','006','007','008','009','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028','029','030','031','032','033','034','035','036','037','038','039','040','041','042','043','044','045','046','047','048','049','050','051','052','053','054','055','056','057','058','059','060','061','062','063','064','065','066','067','068','069','074','075','076','077','078','080','081','082','083','084','085','086','087','090','091','092','093','094','095','096','097','098','099','105','106','107','108','109','115','117','118','119','120','121','122','123','124','125','126','127','128','129','130','131','132','133','134','135','136','137','138','139','140','141','142','143','144','145','146','147','148','149','150','151','152','153','154','155','156','157','158','159','160','161','162','163','164','165','166','167','168','169','170','171','172','173','174','175','176','177','178','179','180','181','182','183','184','185','186','187','188','189','190','191','192','193','194','195','196','197','198','199','200','201','202','203','204','205','206','207','208','209','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','270','271','272','273','274','275','276','277','278','279','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299'),
            '1P'=>array('103','110','111','112','113','114','116'),
            '1Q'=>array('070','071','072','073','079','088','089'),
            '1R'=>array('100','101','102','104'),
            '3F'=>array('400','401','402','403','404','405','406','407','408','409','410','411','412','413','414','415','416','417','418','419','420','421','422','423','424','425','426','427','428','429','430','431','432','433','437','438','439','450','451','452','453','454','455','456','457','458','459','470','471','475','476','477','480','483','484','485','490','491','493','494','495','496','497','500','501','502','503','504','505','506','507','508','509','510','511','512','513','514','515','516','517','518','519','520','521','522','523','524','525','526','527','528','529','533','536','540','546','547','548','550','551','552','553','554','555','556','557','558','559','560','561','562','563','564','565','566','567','568','569','570','571','572','573','574','575','576','577','578','579','580','581','582','583','584','585','586','587','588','589','590','591','592','593','594','595','596','597','598','599','600','601','602','603','604','605','606','607','608','609','612','617','618','619','621','624','632','635','640','641','642','643','644','645','646','647','648','649','650','651','652','653','654','655','656','657','658','659','660','661','662','663','664','665','666','667','668','669','670','671','672','673','674','675','676','677','678','679','680','681','682','683','684','685','686','687','688','689','690','691','692','693','694','695','696','697','698','699','740','741','742','743','744','745','746','747','748','749','750','751','752','753','754','755','756','757','758','760','761','762','763','764','765','766','767','768','769','770','771','772','785','786','787','789','790','791','792','793','794','795','796','797','798','799'),
            '3P'=>array('460','461','462','463','464','465','466','467','468','469','472','473','474','478','479'),
            '3Q'=>array('498','499','530','531','532','534','535','537','538','539','541','542','543','544','545','549','610','611'),
            '3R'=>array('759','773','774','775','776','777','778'),
            '3U'=>array('613','614','615','616','620','622','623','625','626','627','628','629','630','631','633','634','636','637','638','639'),
            '3C'=>array('434','435','436','481','482','486','487','488','489','492'),
            '3D'=>array('779','780','781','782','783','784','788'),
            '3H'=>array('440','441','442','443','444','445','446','447','448','449'),
            '4F'=>array('813','814','815','816','817','818','819','820','821','822','823','824','825','826','827','828','829','830','831','832','833','834','835','836','837','838','839','840','841','842','843','844','845','846','847','848','849','854','856','857','858','861','862','864','865','866','867','868','869','870','871','872','873','874','875','876','877','878','879','880','881','882','883','884','885','886','887','888','889','890','891','892','893','894','895','896','897','898','899','906','909','910','911','912','913','914','915','916','917','918','926','927','928','929','930','931','932','933','934','935','936','937','938','939'),
            '4P'=>array('900','901','902','903','904','905','907','908'),
            '4Q'=>array('850','851','852','853','855','859','860','863'),
            '4R'=>array('919','920','921'),
            '4U'=>array('922','923','924','925'),
            '2F'=>array('942','950','951','952','953','956','957','958','959','960','961','962','963','964','965','966','967','968','969','970','971','972','973','974','975','976','977','978','979','986','987','988','989','990','991','992','993','994','995','996','997','998','999'),
            '2P'=>array('980','981','982','983','984','985'),
            '2Q'=>array('800','801','802','803','804','805','806','807','808','809','810','811','812'),
            '2R'=>array('945','946','947','948'),
            '2U'=>array('940','941','943','944','949','954','955'),
            '5F'=>array('300','301','302','303','304','305','306','307','308','309','310','311','312','313','314','315','316','317','318','319','320','322','323','324','325','326','334','335','336','337','338','339','341','342','343','344','345','346','348','349','350','351','352','353','354','355','356','357','358','359','360','361','362','363','364','365','366','367','368','369','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','390','391','392','393','394','395','396','397','398','399','700','701','702','703','704','705','706','707','708','709','710','711','712','713','714','715','716','717','718','719','720','721','722','723','724','725','726','727','728','729','730','731','732','733','734','735','736','737','738','739'),
            '5P'=>array('330','331','332','333','340'),
            '5Q'=>array('321','327','328','329','347')
        );

        foreach($arr as $k=>$v){
            if(in_array($postCode,$v)){
                return $k;
            }
        }

        if(strstr($carrier,'EUB')&&!strstr($carrier,'加拿大')&&!strstr($carrier,'线下EUB广州')&&!strstr($carrier,'Wsh线上')&&!strstr($carrier,'北京EUB')&& !strstr($carrier,'wish线上') && $carrier !='越南EUB-HYT'){
            return false;
        }
        //TODO: 如果有其他的 运输方式 必须要 分拣分区的话 请下后面接着 写  return false

        return '';
    }

    /**
     * hank
     * @param        $postCode
     * @param string $countrycode
     * @return bool|int|string
     * 线下EUB广州-YLD  线下EUB广州(带电)-YLD 专用分拣码
     */
    function eubfenjianCode($postCode,$countrycode){
        $fenqu = [
            'AU'=> [
                '1'=>[
                    '1','2','4','9'
                ],
                '2'=>[
                    '3','5','6','7','8'
                ]
            ],
            'CA'=>[
                '1'=>[
                    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'
                ],
                '2'=>[
                    'S','T','U','V','W','X','Y','Z'
                ]
            ],
            'RU'=>[
                '1'=>[
                    '101','102','103','104','105','106','107','108','109','110','111',
                    '112','113','114','115','116','117','118','119','120','121','122',
                    '123','124','125','126','127','128','129','130','131','132','133',
                    '134','135','136','137','138','139','140','141','142','143','144',
                    '145','146','147','148','149','150','151','152','153','154','155',
                    '156','157','170','171','172','210','211','212','213','214','215',
                    '216','217','218','219','220','221','222','223','224','225','226',
                    '227','228','229','230','231','232','233','234','235','236','237',
                    '238','239','240','241','242','243','244','245','246','247','248',
                    '249','250','251','252','253','254','255','256','257','258','259',
                    '260','261','262','263','264','265','266','267','268','269','270',
                    '271','272','273','274','275','276','277','278','279','280','281',
                    '282','283','284','285','286','287','288','289','290','291','292',
                    '293','294','295','296','297','298','299','300','301','302','303',
                    '304','305','306','307','308','309','346','347','352','353','354',
                    '355','356','357','358','359','390','391','629','689'
                ],
                '2'=>[
                    '630','632','633','634','636','640','641','644','646','647','648',
                    '649','650','651','652','653','654','655','656','658','659','660',
                    '662','663','664','665','666','667','668','669','670','671','672',
                    '673','674','675','676','677','678','679','680','681','682','683',
                    '684','685','686','687','688','690','692','693','694'
                ],
                '3'=>[
                    '160','161','162','163','164','165','166','167','168','169','173',
                    '174','175','180','181','182','183','184','185','186','187','188',
                    '190','191','192','193','194','195','196','197','198','199'
                ],
                '4'=>[
                    '344','350','360','361','362','363','364','366','367','368','369',
                    '370','371','372','373','374','375','376','377','378','379','380',
                    '381','382','383','384','385','386','392','393','394','395','396',
                    '397','398','399','400','401','403','405','406','407','408','409',
                    '410','412','413','414','416','420','421','422','423','424','425',
                    '426','427','428','429','430','431','432','433','440','442','443',
                    '444','445','446','450','452','453','454','455','456','457','460',
                    '461','462','610','612','613','614','617','618','619','620','622',
                    '623','624','626','627','628'
                ],
            ],
            'US'=>[
                '1F'=>['000','001','002','003','004','005','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028','029','030','031','032','033','034','035','036','037','038','039','040','041','042','043','044','045','046','047','048','049','050','051','052','053','054','055','056','057','058','059','060','061','062','063','064','065','066','067','068','069','074','075','076','077','078','080','081','082','083','084','085','086','087','090','091','092','093','094','095','096','097','098','099','105','106','107','108','109','115','117','118','119','120','121','122','123','124','125','126','127','128','129','130','131','132','133','134','135','136','137','138','139','140','141','142','143','144','145','146','147','148','149','150','151','152','153','154','155','156','157','158','159','160','161','162','163','164','165','166','167','168','169','170','171','172','173','174','175','176','177','178','179','180','181','182','183','184','185','186','187','188','189','190','191','192','193','194','195','196','197','198','199','200','201','202','203','204','205','206','207','208','209','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','270','271','272','273','274','275','276','277','278','279','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299'],
                '1P'=>['103','110','111','112','113','114','116'],
                '1Q'=>['070','071','072','073','079','088','089'],
                '1R'=>['100','101','102','104'],
                '3F'=>['400','401','402','403','404','405','406','407','408','409','410','411','412','413','414','415','416','417','418','419','420','421','422','423','424','425','426','427','428','429','430','431','432','433','437','438','439','450','451','452','453','454','455','456','457','458','459','470','471','475','476','477','480','483','484','485','490','491','493','494','495','496','497','500','501','502','503','504','505','506','507','508','509','510','511','512','513','514','515','516','517','518','519','520','521','522','523','524','525','526','527','528','529','533','536','540','546','547','548','550','551','552','553','554','555','556','557','558','559','560','561','562','563','564','565','566','567','568','569','570','571','572','573','574','575','576','577','578','579','580','581','582','583','584','585','586','587','588','589','590','591','592','593','594','595','596','597','598','599','600','601','602','603','604','605','606','607','608','609','612','617','618','619','621','624','632','635','640','641','642','643','644','645','646','647','648','649','650','651','652','653','654','655','656','657','658','659','660','661','662','663','664','665','666','667','668','669','670','671','672','673','674','675','676','677','678','679','680','681','682','683','684','685','686','687','688','689','690','691','692','693','694','695','696','697','698','699','740','741','742','743','744','745','746','747','748','749','750','751','752','753','754','755','756','757','758','760','761','762','763','764','765','766','767','768','769','770','771','772','785','786','787','789','790','791','792','793','794','795','796','797','798','799'],
                '3P'=>['460','461','462','463','464','465','466','467','468','469','472','473','474','478','479'],
                '3Q'=>['498','499','530','531','532','534','535','537','538','539','541','542','543','544','545','549','610','611'],
                '3R'=>['759','773','774','775','776','777','778'],
                '3U'=>['613','614','615','616','620','622','623','625','626','627','628','629','630','631','633','634','636','637','638','639'],
                '3C'=>['434','435','436','481','482','486','487','488','489','492'],
                '3D'=>['779','780','781','782','783','784','788'],
                '3H'=>['440','441','442','443','444','445','446','447','448','449'],
                '4F'=>['813','814','815','816','817','818','819','820','821','822','823','824','825','826','827','828','829','830','831','832','833','834','835','836','837','838','839','840','841','842','843','844','845','846','847','848','849','854','856','857','858','861','862','864','865','866','867','868','869','870','871','872','873','874','875','876','877','878','879','880','881','882','883','884','885','886','887','888','889','890','891','892','893','894','895','896','897','898','899','906','909','910','911','912','913','914','915','916','917','918','926','927','928','929','930','931','932','933','934','935','936','937','938','939'],
                '4P'=>['900','901','902','903','904','905','907','908'],
                '4Q'=>['850','851','852','853','855','859','860','863'],
                '4R'=>['919','920','921'],
                '4U'=>['922','923','924','925'],
                '2F'=>['942','950','951','952','953','956','957','958','959','960','961','962','963','964','965','966','967','968','969','970','971','972','973','974','975','976','977','978','979','986','987','988','989','990','991','992','993','994','995','996','997','998','999'],
                '2P'=>['980','981','982','983','984','985'],
                '2Q'=>['800','801','802','803','804','805','806','807','808','809','810','811','812'],
                '2R'=>['945','946','947','948'],
                '2U'=>['940','941','943','944','949','954','955'],
                '5F'=>['300','301','302','303','304','305','306','307','308','309','310','311','312','313','314','315','316','317','318','319','320','322','323','324','325','326','334','335','336','337','338','339','341','342','343','344','345','346','348','349','350','351','352','353','354','355','356','357','358','359','360','361','362','363','364','365','366','367','368','369','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','390','391','392','393','394','395','396','397','398','399','700','701','702','703','704','705','706','707','708','709','710','711','712','713','714','715','716','717','718','719','720','721','722','723','724','725','726','727','728','729','730','731','732','733','734','735','736','737','738','739'],
                '5P'=>['330','331','332','333','340'],
                '5Q'=>['321','327','328','329','347'],
                '5R'=>['006','007','008','009'],
            ]
        ];

        if($countrycode == 'US' || $countrycode == 'RU') {
            // 美国和俄罗斯都是以邮编前三位区分
            $postCode = substr(trim($postCode), 0, 3);

        }
        // 澳大利亚和加拿大都是以第一位区分
        if($countrycode == 'AU' || $countrycode == 'CA'){
            $postCode = substr(trim($postCode),0,1);

        }
        $fenjianma = '';
        foreach ($fenqu[$countrycode] as $k => $v) {
            if (in_array($postCode, $v)) {
                $fenjianma =  $k;
                return $fenjianma;
            }
        }
        if(empty($fenjianma)){
            return 1;
        }
    }


    // 获取 运输方式相关的 需要 显示在 面单上的东西
    function getCarrierInfo($ebay_carrier){
        $map['status']=1;
        $map['name']=$ebay_carrier;
        $Rs=$this->CarrierModel->where($map)->field('address,tel,mark_code')->find();
        //unset($Carrier);
        return $Rs;
    }

    //获取 模板
    function getTemplate($carrier, $countryCharCode)
    {

        if(array_key_exists($carrier,$this->ChangeTEMPT))
        {
            $change_tempt=$this->ChangeTEMPT[$carrier];
            if(array_key_exists($countryCharCode,$change_tempt)){
                $carrier=$change_tempt[$countryCharCode];
            }
        }

/*        if ($carrier == '福州小包挂号') {
            $countryCharCode == 'FR' ? $carrier = '福州小包挂号_FR' : null;
            $countryCharCode == 'NZ' ? $carrier = '福州小包挂号_NZ' : null;
            $countryCharCode == 'DE' ? $carrier = '福州小包挂号_DE' : null;
            in_array($countryCharCode, ['HR','PL','SI','RT']) ? $carrier = '福州小包挂号_group1' : null;
        }

        if (strtoupper(trim($carrier)) == 'LWE') {
            if (strtoupper($countryCharCode) == 'MY') {
                return $this -> AllCarrier['LWE_MY'];
            } elseif (strtoupper($countryCharCode) == 'SG') {
                return $this -> AllCarrier['LWE_SG'];
            } elseif (strtoupper($countryCharCode) == 'TH') {
                return $this -> AllCarrier['LWE_TH'];
            } elseif (strtoupper($countryCharCode) == 'ID') {
                return $this -> AllCarrier['LWE_ID'];
            }
        }

        if($carrier=='顺友通平邮'&&$countryCharCode=='UA'){
            $carrier = '顺友通平邮_UA';
        }

        if($carrier=='顺邮宝平邮'&&in_array($countryCharCode,['BR','CA','US'])){
            $carrier = '顺邮宝平邮_1';
        }

        if($carrier=='顺邮宝挂号'&&in_array($countryCharCode,['BR','CA','DK'])){
            $carrier = '顺邮宝挂号_1';
        }

        if($carrier=='顺邮宝挂号'&&in_array($countryCharCode,['AU','DE','RU','SG','US','NL','GB','HU','NZ','PL','ES','FR','NO','CH','IN','JP','TR','AT','BE','IE','CZ','PT','IT'])){
            $carrier = '顺邮宝挂号_2';
        }
*/

        if(array_key_exists($carrier,$this->AllCarrier)){
            return $this->AllCarrier[$carrier];
        }

        return false;
    }

    // 东莞仓 还是  莆田仓
    function getWarehouseName($carrier){
        if(array_key_exists($carrier,$this->CarrierHouseName)){
            return $this->CarrierHouseName[$carrier];
        }
        return '';
    }


    // pdf 转 url 的时候  pdf 的路径 是什么
    //TODO 注意 如果 运输方式和现有的 运输方方式 有包含关系 要警惕
    function getPdf2imgUrl($ebay_id,$carrier,$is_check=false){

        $path=$this->PdfPath[$carrier];
        if($_GET['debug']==2){
            debug($this->PdfPath);
            debug($carrier);
        }
        if(!$path){
            if($_GET['debug']==1){

            }
            return false;
        }

        $path=$path.$ebay_id.'.pdf';


        /**
         *测试人员谭 2018-07-31 17:08:59
         *说明: 如果是 chek 就不会去真的把图片转化，直接 检查pdf是否存在  就好了
         */
        if($is_check){
            $fullPath     = dirname(dirname(THINK_PATH)).'/'.$this->PdfPath[$carrier].'/'.$ebay_id.'.pdf';
            if(file_exists($fullPath)){
                return ['status'=>1];
            }else{
                return false;
            }
        }



        $data=$this->pdf2png($path);

        if($_GET['debug']==1){
            print_r($data);
            print_r($path);
        }

        if(empty($data)){
            if($_GET['debug']==1){
                echo "{$ebay_id} 订单获取物流面单失败，请手动重新获取面单！<br>";
            }
            return false;
        }

        if(!$data['status']){
            if($_GET['debug']==1){
                echo $data['msg']."<br>";
            }
            return false;
        }

        $urls=$data['data'];
        // 如果是多张图片就是数组，如果是1张图片就直接 返回
        if(count($urls)==1){
            return $urls[0];
        }
        return $urls;
    }


    /**
     * Lazada 打单特有数据的获取
     * $param $ebayId ;
     * @param $orderinfo
     * @return boolean
     */
    public function getLazadaData($ebayId, &$orderinfo)
    {
        $ebay_ordersn=$orderinfo['ebay_ordersn'];
        $sellerNameArr = [ 'vigo', 'burstore', 'versea'];
        $sellerAddressArr = [
            'vigo'=>'2031,Bldg36,Wanzhongshenghuo Vil.,Minzhi St.,Longhua',
            'burstore'=>'Room 601, unit 3, block 33, wanzhong village, minzhi street, longhua district',
            'versea'=>'futian district red tail building, 1007 south street'
        ];
        $sellerPhoneArr = [
            'vigo'=>'13392417851',
            'burstore'=>'18127031045',
            'versea'=>'13313466517'
        ];
        $name = explode('_', $orderinfo['ebay_account'])[0];
        if(in_array($name, $sellerNameArr)) {
            $orderinfo['seller_name'] = $name;
            $orderinfo['seller_address'] = $sellerAddressArr[$name];
            $orderinfo['seller_contact'] = $sellerPhoneArr[$name];
        }

        //获取分拣代码
        if($orderinfo['countrycode'] == 'MY'){
            $orderinfo['sort_code'] = 'LZDMY';
        }elseif ($orderinfo['countrycode'] == 'SG'){
            $orderinfo['sort_code'] = 'LZDSG';
        }elseif ($orderinfo['countrycode'] == 'PH'){
            $orderinfo['sort_code'] = 'LZDPH';
        }elseif ($orderinfo['countrycode'] == 'TH'){
            $orderinfo['sort_code'] = 'LZDTH';
        }elseif ($orderinfo['countrycode'] == 'ID'){
            $orderinfo['sort_code'] = 'LZDID';
        }elseif ($orderinfo['countrycode'] == 'VN'){
            $orderinfo['sort_code'] = 'LZDID';
        }else{
            return ['status' => false, 'data' => 'Lazada 不支持国家.'];
        }

        $skuTitleArr = $this->OrderDetailModel -> where(['ebay_ordersn'=> $ebay_ordersn])
            -> getField('sku, ebay_itemtitle', true);

        $orderinfo['lazadaItemTitle'] = $skuTitleArr;
        return ['status' => true, 'data' => ''];
    }


    /**
     * 获取圆通-shopee 运输方式特有的字段信息
     * @param $account
     * @param $ebayId
     * @param $orderInfo
     */
    public function getYuanTongShopEData($account, $ebayId, &$orderInfo)
    {
        // $shopeeController = new \Order\Controller\ShopeeController();
        // $info = $shopeeController ->getSingleOrderInfo($account, $ebayId);
        // $orderInfo['yuantong_shopee']['carrier'] = $info['orders'][0]['shipping_carrier'];
        // $orderInfo['yuantong_shopee']['cod'] = $info['orders'][0]['cod'] ? '- 貨到付款' : '';
        // $orderInfo['yuantong_shopee']['goods_to_declare'] = $info['orders'][0]['goods_to_declare'] ? '特貨' : '普貨';
        // hank 2017-11-08 保存历史订单的 几个面单信息
        // ebay_site     cod
        // feedbacktype  goods_to_declare
        // ebay_shiptype shipping_carrier

        // $cod = $info['orders'][0]['cod'] ? '- 貨到付款' : '';
        // $goods_to_declare = $info['orders'][0]['goods_to_declare'] ? '特貨' : '普貨';
        // $data = [
        //     'ebay_shiptype'    => $info['orders'][0]['shipping_carrier'],
        //     'ebay_site' => $cod,
        //     'feedbacktype' => $goods_to_declare
        // ];
        //
        // $OrderDetailModel = new  \Common\Model\OrderDetailModel();
        // $resss = $OrderDetailModel->where(['ebay_ordersn'=>$orderInfo['ebay_ordersn']])->save($data);

        // hank 2017-11-08 今天之后的面单都是 使用详情表的数据
        $orderDetailInfo = $this->OrderDetailModel->where(['ebay_ordersn'=>$orderInfo['ebay_ordersn']])->find();
        $orderInfo['yuantong_shopee']['carrier'] = $orderDetailInfo['ebay_shiptype'];
        $orderInfo['yuantong_shopee']['cod'] = $orderDetailInfo['ebay_site'];
        $orderInfo['yuantong_shopee']['goods_to_declare'] = $orderDetailInfo['feedbacktype'];
    }


    /**
     * 获取新加坡小包平邮挂号分拣码和分拣分区的 方法
     * @param $countryCode : 国家二字码
     * @param $postCode    : 邮编
     * @return array       : 返回
     */
    public function getSingaporeFenJanCode($countryCode, $postCode = null)
    {

        $arr = [
            'AD' =>	['AD', '005'], 'AE' => ['AE', '006'], 'AF' => ['AF','006'], 'AG' =>	['AG' ,'006'], 'AI' => ['AI' ,'006'],
            'AL' =>	['AL', '098'], 'AM' => ['AM', '099'], 'AN' => ['AN','006'], 'AO' =>	['AO' ,'006'], 'AR' => ['AR' ,'103'],
            'AS' =>	['AS', '003'], 'AT' => ['AT', '063'], 'AW' => ['AW','006'], 'AZ' =>	['AZ' ,'005'], 'BA' => ['BA' ,'005'],
            'BE' =>	['BE', '065'], 'BF' => ['BF', '006'], 'BG' => ['BG','066'], 'BH' =>	['BH' ,'006'], 'BI' => ['BI' ,'006'],
            'BJ' =>	['BJ', '006'], 'BM' => ['BM', '006'], 'BN' => ['BN','043'], 'BO' =>	['BO' ,'006'], 'BR' => ['BR' ,'030'],
            'BS' =>	['BS', '006'], 'BT' => ['BT', '003'], 'BV' => ['BV','006'], 'BW' =>	['BW' ,'006'], 'BY' => ['BY' ,'064'],
            'BZ' =>	['BZ', '006'], 'CA' => ['CA', '067'], 'CC' => ['CC','004'], 'CD' =>	['CD' ,'006'], 'CF' => ['CF' ,'006'],
            'CG' =>	['CG', '006'], 'CH' => ['CH', '094'], 'CI' => ['CI','006'], 'CK' =>	['CK' ,'003'], 'CL' => ['CL' ,'104'],
            'CM' =>	['CM', '006'], 'CO' => ['CO', '107'], 'CR' => ['CR','108'], 'CU' =>	['CU' ,'006'], 'CV' => ['CV' ,'006'],
            'CX' =>	['CX', '004'], 'CY' => ['CY', '005'], 'CZ' => ['CZ','069'], 'DE' =>	['DE' ,'073'], 'DJ' => ['DJ' ,'006'],
            'DK' =>	['DK', '070'], 'DM' => ['DM', '006'], 'DO' => ['DO','006'], 'DZ' =>	['DZ' ,'006'], 'EC' => ['EC' ,'109'],
            'EE' =>	['EE', '100'], 'EG' => ['EG', '006'], 'EH' => ['EH','006'], 'ER' =>	['ER' ,'006'], 'ES' => ['ES' ,'092'],
            'ET' =>	['ET', '006'], 'FI' => ['FI', '071'], 'FJ' => ['FJ','049'], 'FK' =>	['FK' ,'006'], 'FM' => ['FM' ,'003'],
            'FO' =>	['FO', '005'], 'FR' => ['FR', '072'], 'FX' => ['FX','006'], 'GA' =>	['GA' ,'006'], 'GB' => ['GB' ,'097'],
            'GD' =>	['GD', '006'], 'GE' => ['GE', '005'], 'GF' => ['GF','006'], 'GG' =>	['GG' ,'005'], 'GH' => ['GH' ,'110'],
            'GI' =>	['GI', '005'], 'GL' => ['GL', '005'], 'GM' => ['GM','006'], 'GN' =>	['GN' ,'006'], 'GP' => ['GP' ,'006'],
            'GQ' =>	['GQ', '006'], 'GR' => ['GR', '074'], 'GS' => ['GS','006'], 'GT' =>	['GT' ,'006'], 'GU' => ['GU' ,'003'],
            'GW' =>	['GW', '006'], 'GY' => ['GY', '006'], 'HK' => ['HK','044'], 'HM' =>	['HM' ,'004'], 'HN' => ['HN' ,'006'],
            'HR' =>	['HR', '068'], 'HT' => ['HT', '006'], 'HU' => ['HU','075'], 'IC' =>	['IC' ,'005'], 'ID' => ['ID' ,'041'],
            'IE' =>	['IE', '077'], 'IL' => ['IL', '111'], 'IN' => ['IN','050'], 'IO' =>	['IO' ,'005'], 'IQ' => ['IQ' ,'006'],
            'IR' =>	['IR', '006'], 'IS' => ['IS', '076'], 'IT' => ['IT','078'], 'JE' =>	['JE' ,'005'], 'JM' => ['JM' ,'112'],
            'JO' =>	['JO', '006'], 'JP' => ['JP', '061'], 'JU' => ['JU','006'], 'KE' =>	['KE' ,'113'], 'KG' => ['KG' ,'005'],
            'KH' =>	['KH', '003'], 'KI' => ['KI', '003'], 'KM' => ['KM','006'], 'KN' =>	['KN' ,'006'], 'KP' => ['KP' ,'003'],
            'KR' =>	['KR', '046'], 'KV' => ['KV', '005'], 'KW' => ['KW','006'], 'KY' =>	['KY' ,'006'], 'KZ' => ['KZ' ,'079'],
            'LA' =>	['LA', '051'], 'LB' => ['LB', '114'], 'LC' => ['LC','006'], 'LI' =>	['LI' ,'005'], 'LK' => ['LK' ,'058'],
            'LR' =>	['LR', '006'], 'LS' => ['LS', '115'], 'LT' => ['LT','081'], 'LU' =>	['LU' ,'082'], 'LV' => ['LV' ,'080'],
            'LY' =>	['LY', '116'], 'MA' => ['MA', '119'], 'MC' => ['MC','005'], 'MD' =>	['MD' ,'102'], 'ME' => ['ME' ,'005'],
            'MG' =>	['MG', '117'], 'MH' => ['MH', '003'], 'MK' => ['MK','101'], 'ML' =>	['ML' ,'006'], 'MM' => ['MM' ,'054'],
            'MN' =>	['MN', '003'], 'MO' => ['MO', '052'], 'MP' => ['MP','003'], 'MQ' =>	['MQ' ,'006'], 'MR' => ['MR' ,'006'],
            'MS' =>	['MS', '006'], 'MT' => ['MT', '005'], 'MU' => ['MU','118'], 'MV' =>	['MV' ,'053'], 'MW' => ['MW' ,'006'],
            'MX' =>	['MX', '105'], 'MY' => ['MY', '040'], 'MZ' => ['MZ','006'], 'NA' =>	['NA' ,'120'], 'NC' => ['NC' ,'003'],
            'NE' =>	['NE', '006'], 'NF' => ['NF', '004'], 'NG' => ['NG','121'], 'NI' =>	['NI' ,'006'], 'NL' => ['NL' ,'083'],
            'NO' =>	['NO', '084'], 'NP' => ['NP', '055'], 'NR' => ['NR','003'], 'NU' =>	['NU' ,'003'], 'NZ' => ['NZ' ,'062'],
            'OM' =>	['OM', '122'], 'PA' => ['PA', '006'], 'PE' => ['PE','124'], 'PF' =>	['PF' ,'059'], 'PG' => ['PG' ,'056'],
            'PH' =>	['PH', '045'], 'PK' => ['PK', '003'], 'PL' => ['PL','085'], 'PM' =>	['PM' ,'006'], 'PN' => ['PN' ,'003'],
            'PR' =>	['PR', '125'], 'PT' => ['PT', '086'], 'PW' => ['PW','003'], 'PY' =>	['PY' ,'123'], 'QA' => ['QA' ,'006'],
            'RE' =>	['RE', '006'], 'RO' => ['RO', '088'], 'RS' => ['RS','087'], 'RW' =>	['RW' ,'006'], 'SA' => ['SA' ,'126'],
            'SB' =>	['SB', '057'], 'SC' => ['SC', '006'], 'SD' => ['SD','006'], 'SE' =>	['SE' ,'093'], 'SG' => ['SG' ,'131'],
            'SH' =>	['SH', '006'], 'SI' => ['SI', '091'], 'SJ' => ['SJ','006'], 'SK' =>	['SK' ,'090'], 'SL' => ['SL' ,'006'],
            'SM' =>	['SM', '005'], 'SN' => ['SN', '006'], 'SO' => ['SO','006'], 'SR' =>	['SR' ,'006'], 'SS' => ['SS' ,'006'],
            'ST' =>	['ST', '006'], 'SV' => ['SV', '006'], 'SY' => ['SY','006'], 'SZ' =>	['SZ' ,'006'], 'TA' => ['TA' ,'006'],
            'TC' =>	['TC', '006'], 'TD' => ['TD', '006'], 'TF' => ['TF','006'], 'TG' =>	['TG' ,'006'], 'TH' => ['TH' ,'042'],
            'TJ' =>	['TJ', '005'], 'TK' => ['TK', '003'], 'TL' => ['TL','003'], 'TM' =>	['TM' ,'005'], 'TN' => ['TN' ,'006'],
            'TO' =>	['TO', '003'], 'TR' => ['TR', '095'], 'TT' => ['TT','129'], 'TV' =>	['TV' ,'003'], 'TW' => ['TW' ,'002'],
            'TZ' =>	['TZ', '128'], 'UA' => ['UA', '096'], 'UG' => ['UG','130'], 'UM' =>	['UM' ,'006'], 'UY' => ['UY' ,'006'],
            'VA' =>	['VA', '005'], 'VC' => ['VC', '006'], 'VE' => ['VE','006'], 'VG' =>	['VG' ,'006'], 'VI' => ['VI' ,'006'],
            'VN' =>	['VN', '047'], 'VU' => ['VU', '060'], 'WF' => ['WF','006'], 'WS' =>	['WS' ,'003'], 'XB' => ['XB' ,'006'],
            'XC' =>	['XC', '006'], 'XD' => ['XD', '006'], 'XE' => ['XE','006'], 'XG' =>	['XG' ,'006'], 'XH' => ['XH' ,'006'],
            'XI' =>	['XI', '006'], 'XJ' => ['XJ', '006'], 'XK' => ['XK','006'], 'XM' =>	['XM' ,'006'], 'XN' => ['XN' ,'006'],
            'XY' =>	['XY', '006'], 'YE' => ['YE', '006'], 'YT' => ['YT','006'], 'ZA' =>	['ZA' ,'127'], 'ZM' => ['ZM' ,'006'],
            'ZR' =>	['ZR', '006'], 'ZW' => ['ZW', '006'], 'BB' => ['BB','106'], 'BD' =>	['BD' ,'048'], 'UZ' => ['UZ' ,'005'],
        ];

        // 澳大利亚的情况
        if (strtoupper(trim($countryCode)) == 'AU') {
            if (is_between($postCode, '3000', '3999') || is_between($postCode, '7000', '8999')) {
                return ['MEL','012'];
            } elseif (is_between($postCode, '6000', '6999')) {
                return ['PER','013'];
            } elseif (is_between($postCode, '0000', '2999')||is_between($postCode, '4000', '5999')||is_between($postCode, '9000', '9999')) {
                return ['SYD','011'];
            } else {
                return ['AU','010'];
            }
        }

        // 俄罗斯的情况
        if (strtoupper(trim($countryCode)) == 'RU') {

            if(
                is_between($postCode, '675000', '686999') || is_between($postCode,'688000','688999') ||
                is_between($postCode, '690000','690999') || is_between($postCode, '692000','694999')
            ) {
                return ['VVO','411'];
            } elseif (
                is_between($postCode, '173000','173999') || is_between($postCode, '180000','180999') ||
                is_between($postCode, '186000','188999') || is_between($postCode, '190000','190999') ||
                is_between($postCode,'195000','195999')
            ) {
                return ['LED','412'];
            } elseif (
                is_between($postCode, '630000','630999') || is_between($postCode, '632000','634999') ||
                is_between($postCode, '636000','636999') || is_between($postCode, '644000','644999') ||
                is_between($postCode, '646000','650999') || is_between($postCode, '652000','656999') ||
                is_between($postCode, '658000','660999') || is_between($postCode, '662000','674999') ||
                is_between($postCode, '687000','687999')
            ) {
                return ['OVB','413'];
            } elseif(
                is_between($postCode, '344000','344999') || is_between($postCode, '346000','347999') ||
                is_between($postCode, '392000','393999') || is_between($postCode, '410000','410999') ||
                is_between($postCode, '403000','405999') || is_between($postCode, '400000','400999') ||
                is_between($postCode, '416000','416999') || is_between($postCode, '412000','414999') ||
                is_between($postCode, '425000','433999') || is_between($postCode, '420000','423999') ||
                is_between($postCode, '445000','446999') || is_between($postCode, '442000','443999') ||
                is_between($postCode, '440000','440999') || is_between($postCode, '460000','462999') ||
                is_between($postCode, '452000','457999') || is_between($postCode, '450000','450999') ||
                is_between($postCode, '617000','620999') || is_between($postCode, '612000','614999') ||
                is_between($postCode, '610000','610999') || is_between($postCode, '640000','641999') ||
                is_between($postCode, '626000','628999') || is_between($postCode, '622000','624999')
            ) {
                return ['EKA','414'];
            } else {
                return ['MOW','410'];
            }
        }

        // 美国的情况
        if (strtoupper(trim($countryCode)) == 'US') {
            if (is_between($postCode, '96700', '96899') || is_between($postCode, '85000', '93599')) {
                return ['LAX','022'];
            } elseif (is_between($postCode,'40000', '79999')) {
                return ['ORD','025'];
            } elseif (is_between($postCode, '00000', '29999')) {
                return ['JFK','021'];
            } elseif (
                is_between($postCode, '80000', '84900') || is_between($postCode, '93600', '96699') ||
                is_between($postCode, '96900', '99999')
            ) {
                return ['SFO','023'];
            } elseif (is_between($postCode, '30000','39999')) {
                return ['MIA','024'];
            } else {
                return ['US','020'];
            }
        }

        return $arr[$countryCode];
    }


    /**
     * 根据国家二字码和邮编，获取分区隔口号
     * @param $countryCode
     * @param $postcode
     * @return mixed
     */
    public function getChuKouYiCode($countryCode, $postcode)
    {
        $arr = [
            'CF' => 5,   'CL' => 33,  'GI' => 60,'TD' => 1,  'ZR' => 1,  'ZM' => 1,  'VN' => 51,
            'JO' => 58,   'ID'=> 50,  'IN' => 1, 'IT' => 7,  'IL' => 19,  'IR' => 1,  'IQ' => 1,
            'YE' => 1,   'XH'=> 1,  'AM' => 1, 'JM' => 1,  'SY' => 1,  'HU' => 39,  'NC' => 64,
            'SG' => 24,  'CI'=> 1, 'GR' => 37, 'EH' => 1, 'ES' => 22, 'UZ' => 54,  'UY' => 1,
            'UA' => 26,  'UG'=> 1, 'BN' => 24, 'VE' => 1, 'GT' => 66, 'VU' => 64,  'ER' => 5,
            'TM' => 54,  'TR'=> 42, 'TV' => 64,'TN' => 1, 'TT' => 1, 'TO' => 64,   'TZ' => 1,
            'TH' => 48,  'TW'=> 63, 'TJ' => 54,'SO' => 1, 'SR' => 1, 'SD' => 1,    'SZ' => 60,
            'SI' => 1,  'SK' => 35, 'LK' => 56,'VC' => 63, 'SM' => 7, 'LC' => 60,  'ST' => 1,
            'CX' => 14,  'SA'=> 44, 'CY' => 1, 'SN' => 1, 'RS' => 1, 'SV' => 66,   'CH' => 8,
            'SE' => 20,  'JP'=> 10, 'NO' => 18,'NU' => 63, 'NG' => 1, 'NE' => 1,   'DM' => 1,
            'NI' => 66,  'NP'=> 1, 'NR' => 64, 'ZA' => 45, 'NA' => 62, 'MX' => 43, 'MC' => 5,
            'MA' => 1,  'MD' => 1, 'MM' => 61, 'FM' => 63, 'PE' => 1, 'BD' => 1,   'MN' => 1,
            'MR' => 1,  'MU' => 46, 'YT' => 5, 'MQ' => 5, 'MK' => 8, 'ML' => 1,    'MY' => 53,
            'MW' => 45,  'MT'=> 1, 'MV' => 24, 'MG' => 1, 'RO' => 1, 'RW' => 1,    'LU' => 1,
            'RE' => 45,  'LI'=> 8, 'LY' => 1,  'LR' => 1, 'LT' => 1, 'LB' => 1,    'LA' => 1,
            'LS' => 60,  'LV'=> 27, 'CK' => 64,'KE' => 1, 'HR' => 30, 'KW' => 1,   'KM' => 5,
            'CC' => 14,  'KY'=> 60, 'QA' => 1, 'CM' => 1, 'ZW' => 1, 'RT' => 36,   'KH' => 1,
            'GA' => 1,  'GH' => 1, 'IC' => 4,  'GW' => 1, 'GN' => 1, 'KG' => 54,   'DJ' => 1,
            'HN' => 66,  'ME'=> 1, 'KR' => 47, 'HT' => 1, 'KZ' => 54, 'GY' => 1,   'GU' => 16,
            'CU' => 1,  'GE' => 1, 'GD' => 60, 'CR' => 1, 'CO' => 1, 'CD' => 1,    'GM' => 1,
            'FK' => 1,  'CV' => 5, 'FI' => 38, 'FJ' => 52, 'PH' => 49, 'VA' => 23, 'EC' => 1,
            'TG' => 1,  'TL' => 14, 'DE' => 6, 'GQ' => 1, 'KP' => 59, 'BI' => 1,   'BF' => 1,
            'BT' => 48,  'BW'=> 1, 'BZ' => 60, 'BO' => 1, 'BA' => 1, 'PL' => 23,   'PR' => 17,
            'IS' => 1,  'BE' => 41, 'BJ' => 1, 'BG' => 29, 'BY' => 25, 'BR' => 3,   'PA' => 1,
            'BH' => 1,  'PY' => 33, 'PK' => 57, 'BS' => 60, 'BB' => 60, 'AT' => 31, 'AG' => 60,
            'AO' => 60,  'AD'=> 5, 'EE' => 28, 'IE' => 32, 'ET' => 1, 'EG' => 1,    'AZ' => 1,
            'AR' => 21,  'AF'=> 57, 'DZ' => 1, 'AL' => 1, 'GB' => 60, 'PG' => 1,    'AU' => 13,
            'RU' => 2,  'FR' => 63, 'DK' => 34, 'NL' => 63, 'CA' => 15, 'MH' => 16, 'US' => 63,
            'SC' => 45,  'PT'=> 40, 'WS' => 63, 'NZ' => 12, 'PW' => 16, 'BL' => 62, 'TC' => 60,
            'VI' => 63,  'KN'=> 60, 'SL'   => 1, 'PF'  => 63, 'KIR' => 14,'DO' => 1, 'AN' => 63
        ];


        if ($countryCode === 'US') {
            if (is_between($postcode[0], 0, 3)) {
                return 17;
            } elseif (is_between($postcode[0], 4, 9)) {
                return 16;
            } else {
                return 63;
            }
            //    '美国' => 17,   // 需邮编筛选（0-3）
            //    '美国' => 63,   // 其他
            //    '美国' => 16,   // 需邮编筛选（4-9）
        }

        if ($countryCode === 'RU') {
            $first = $postcode[0];
            $firstTwo = substr($postcode, 0, 2);

            if (is_between($firstTwo, 63, 67)) {
                return 11;
            } elseif (is_between($firstTwo, 60, 62) || is_between($first, 3, 4)) {
                return 67;
            } elseif (is_between($firstTwo, 68, 69)) {
                return 68;
            } else {
                return 2;
            }
            //    '俄罗斯' => 2,    // 需邮编筛选（3、4、6除外）
            //    '俄罗斯' => 11,   // 需邮编筛选（63-67）（邮编前两位）
            //    '俄罗斯' => 67,   // 需邮编筛选（3、4、60-62）
            //    '俄罗斯' => 68,   // 需邮编筛选（68-69）（即邮编前两位）
        }

        if ($countryCode === 'AU') {
            $first = $postcode[0];
            if (in_array($first, [0,1,2,4,9])) {
                return 14;
            } elseif (is_between($first, 3, 8)) {
                return 15;
            }
            //    '澳大利亚' => 13,   // 需邮编筛选（3 4 5 6 7 8）
            //    '澳大利亚' => 14,   // 需邮编筛选（0 1 2 4 9）
        }

        return $arr[$countryCode];
    }


    /**
     * 获取西班牙的分区码
     * @param int $postcode
     * @return int
     */
    public function getSpainAreaCode($postcode)
    {
        if(preg_match('/17[0-9]{3}/',$postcode)||preg_match('/08[0-9]{3}/',$postcode)||preg_match('/25[0-9]{3}/',$postcode)||preg_match('/43[0-9]{3}/',$postcode)){
            return 1;
        }elseif (preg_match('/02[0-9]{3}/',$postcode)||preg_match('/13[0-9]{3}/',$postcode)||preg_match('/16[0-9]{3}/',$postcode)||preg_match('/19[0-9]{3}/',$postcode)||preg_match('/28[0-9]{3}/',$postcode)||preg_match('/45[0-9]{3}/',$postcode)){
            return 2;
        }elseif (preg_match('/11[0-9]{3}/',$postcode)||preg_match('/14[0-9]{3}/',$postcode)||preg_match('/21[0-9]{3}/',$postcode)||preg_match('/41[0-9]{3}/',$postcode)||preg_match('/51[0-9]{3}/',$postcode)){
            return 3;
        }else{
            return 4;
        }
    }

}