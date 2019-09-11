<?php
@session_start();
error_reporting(0);
include "include/dbconf.php";
include "include/dbmysqli.php";
unset($dbcon);
$dbconLocal =new DBMysqli($LOCAL);
//$dbcon      =new DBMysqli($ERPCONF);
$user       = $_SESSION['user'];

if($user == ""){
    echo "<script>location.href='login.php'</script>";die();
}

date_default_timezone_set ("Asia/Chongqing");


# 写日志；
function writeFile($file, $str) {
    if(!empty($_SERVER['argv'])){// 命令行时候
        $file=str_replace('.txt','.cli.txt',$file);
    }
    $index = strripos($file, '/');
    if (!file_exists($file) && strripos($file, '/') !== false) {
        $fileDir = substr($file, 0, $index);
        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
            chmod($fileDir,0777); // 有写环境 mkdir 的 0777  无效
        }
    }
    file_put_contents($file, "\xEF\xBB\xBF" . $str, FILE_APPEND);
}

/**
*测试人员谭 2017-03-25 17:36:33
*说明: 顺友--那些玩意
*/

function ChangetrackNo($trackNo){
    global $dbconLocal;
    $ss="select ebay_tracknumber from erp_ebay_order where ebay_status=2009 ";
    $ss.=" and ebay_warehouse=196 AND pxorderid='$trackNo' limit 1";
    $ss=$dbconLocal->getResultArrayBySql($ss);
    if(count($ss)==1){
        return $ss[0]['ebay_tracknumber'];
    }

    return $trackNo;
}

/**
*测试人员谭 2017-02-07 14:00:47
*说明: 该死的瑞典小包子 变态的跟踪号
*/
function Handle_tracknumber($trackNo,$carrier=''){

    global $dbconLocal;



    if(strstr($carrier,'IB中美专线')&&strlen($trackNo)==8){
        $ss="select ebay_id,ebay_tracknumber from erp_ebay_order where ebay_id='$trackNo' limit 1";
        $ss=$dbconLocal->getResultArrayBySql($ss);
        return $ss[0]['ebay_tracknumber'];
    }

    if(preg_match("/^Q1[0-9A-Z]+X$/",$trackNo)){
        $jiequ=substr(substr($trackNo,2,30),0,-2);
        $trackNo='RE'.$jiequ.'SE';
    }

    if(preg_match("/^Q3[0-9A-Z]+X$/",$trackNo)){
        $jiequ=substr(substr($trackNo,2,30),0,-2);
        $trackNo='UF'.$jiequ.'SE';
    }

/*    // 顺友 通挂号
    if(preg_match("/^RN[0-9]{9}MY$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    // 顺友 通挂号
    if(preg_match("/^RQ[0-9]{9}MY$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    // 顺邮宝 挂号
    if(preg_match("/^RS[0-9]{9}MV$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    // 顺邮宝 挂号
    if(preg_match("/^RC[0-9]{9}MY$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    // 顺邮宝 挂号
    if(preg_match("/^RR[0-9]{9}KG$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }*/
    
    // 顺友 通平邮
    if(preg_match("/^SY[A-Z]{3}[0-9]{8}$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }


    // PMS
    if(preg_match("/^PMS[0-9]{8}$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    // 万欧专线
    if(preg_match("/^WNBAA\w+$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    /**
     *测试人员谭 2017-10-30 17:42:34
     *说明: 老挝的垃圾玩意
     */
    if(preg_match("/^LAO[A-Z]\w+YQ$/",$trackNo)){
        return ChangetrackNo($trackNo);
    }

    /**
     *测试人员谭 2017-11-17 20:44:09
     *说明: 其他物流方式
     */

    $ss="select ebay_id,ebay_tracknumber from erp_ebay_order where ebay_tracknumber='$trackNo' and ebay_status=2009 limit 1";
    $ss=$dbconLocal->getResultArrayBySql($ss);
    if(empty($ss)){
        $ss="select ebay_id,ebay_tracknumber from erp_ebay_order where pxorderid='$trackNo' and ebay_status=2009 limit 1";
        $ss=$dbconLocal->getResultArrayBySql($ss);
        if($ss[0]['ebay_tracknumber']!=''){
            return $ss[0]['ebay_tracknumber'];
        }

        return $trackNo;

    }

    return $ss[0]['ebay_tracknumber'];
}



/**
 * 计算各扫描员的扫描数量
 */
function getScanCount() {
    global $dbconLocal;
    $selectSql = <<<TEXT
SELECT transport, scan_user, count(id) as counts  FROM api_orderweight WHERE bag_mark = '' GROUP BY scan_user, transport
ORDER BY transport
TEXT;

    $resultSet = $dbconLocal -> getResultArrayBySql($selectSql);
    $formatTable = <<<TEXT
<table class="countTable" cellpadding='0' cellspacing='0'>
    <tr>
        <th>运输方式</th>
        <th>扫描人</th>
        <th>扫描数量</th>
        <th> -- </th>
    </tr>
TEXT;

    foreach ($resultSet as $key => $item) {
        $formatTable .= <<<TEXT
<tr>
    <td>{$item['transport']}</td>
    <td>{$item['scan_user']}</td>
    <td>{$item['counts']}</td>
    <td><a href="inBagPage.php" target="_blank" style="font-family:微软雅黑;background-color:orange;color:green;padding:2px 3px; border-radius:1px"> 去装袋 </a></td>
</tr>
TEXT;
    }
    $formatTable .= '</table>';
    return $formatTable;
}




// 验证重量--
/**
*测试人员谭 2017-03-03 18:16:21
*说明: goods_out_weight 表存在
*/
function CheckWeight($ebay_id){
    global $dbconLocal;
    //开始搞定计算重量的干活
    $sql="select sku,qty from erp_goods_sale_detail where ebay_id='$ebay_id'";
    $sql=$dbconLocal->getResultArrayBySql($sql);
    $gweight=0; // 包装后重量
    $checkWeight=0; // 是不是 需要强制 验重量
    $abs=0;         // 误差的值 上下多少g


    if(count($sql)==1&&$sql[0]['qty']==1){ // 只有一个sku的时候 并且sku 只有一个的时候
        $sku=$sql[0]['sku'];
        $ischecksql="select id from goods_out_weight where sku='$sku' limit 1";
        $ischecksql=$dbconLocal->getResultArrayBySql($ischecksql);
        if(count($ischecksql)==1){ // 在强制验重的数据表里面 需要强制 验重
            $checkWeight=1;
        }
    }
    foreach($sql as $list){
        $sku=$list['sku'];
        $qty=$list['qty'];

        // 包材部分
        // 优先获取 仓库表里面的 再获取 产品资料里面的
        $ss="select gross_weight from ebay_onhandle_196 where goods_sn='$sku' limit 1";
        $ss=$dbconLocal->getResultArrayBySql($ss);
        $pweight=$ss[0]['gross_weight'];
        $gweight+=$pweight*$qty;

    }

    $gweight=(int)$gweight;

    // 重量区间 和 计算误差---
    if($gweight<=20){
        $abs=8;
    }elseif($gweight<100){
        $abs=15;
    }elseif($gweight<300){
        $abs=20;
    }elseif($gweight<1000){
        $abs=40;
    }elseif($gweight<2000){
        $abs=50;
    }elseif($gweight<3000){
        $abs=40;
    }
    $checkWeight=0;
    // 计算重量 |  是不是要 强制重量  |  容许的误差是多少
    $str=$gweight.'g|'.$checkWeight.'|'.$abs;
    return $str;
}

/**
 *测试人员杨 2018-01-12
 *说明: 获取重量
 */
function GetWeight($ebay_id){
    global $dbconLocal;
    //记录一下日志,有权限设置的用户不用遵守规则
    $log = "用户：".$_SESSION['truename']."===GetWeight：".date("Y-m-d H:i:s",time())."===\n";
    $log .= "ebay_id===".$ebay_id."\n";
    $logPath = dirname(__FILE__).'/log/checkweight/'.date('YmdH').'.txt';
    //判断是否有权限
    $cpower = explode(",", $_SESSION['power']);
    //有权限用户直接通过
    if(in_array('view_pick_carrier_group', $cpower)){
        $log .= "===该用户有权限设置规则，无须遵守规则，直接通过===\n";
        writeFile($logPath,$log);
        return "5_".$ebay_id;
    }
    //开始搞定计算重量的干活
    $sql = "select ebay_ordersn from erp_ebay_order where ebay_id='$ebay_id' limit 1";
    $sql = $dbconLocal->getResultArrayBySql($sql);
    //根据ebay_ordersn查询sku信息
    $ebayOrderSn = $sql[0]['ebay_ordersn'];
    $log .= "ebay_ordersn===".$ebayOrderSn."\n";
    $sql = "SELECT sku,ebay_amount FROM `erp_ebay_order_detail` where ebay_ordersn='$ebayOrderSn' ORDER BY sku asc";
    $sql = $dbconLocal->getResultArrayBySql($sql);
    $str = '';
    //相同sku，数量+1
    $handleData = [];
    for($i=0;$i<count($sql);$i++){
        $sku = $sql[$i]['sku'];
        if(array_key_exists($sku,$handleData)){
            $handleData[$sku] = $handleData[$sku] + (int)$sql[$i]['ebay_amount'];
        }else{
            $handleData[$sku] = (int)$sql[$i]['ebay_amount'];
        }
    }
    //拼接字符串
    foreach ($handleData as $key=>$val){
        $str.=$key.'*'.$val.',';
    }
    $log .= "sku*ebay_amount===".$str."\n";
    $token = md5(trim($str,','));
    $sql = "SELECT avg_weight FROM `avg_weight_statistics` where md5_str='$token'";
    $log .= "===".$sql."===\n";
    $sql = $dbconLocal->getResultArrayBySql($sql);
    //没有找到平均重量
    if(empty($sql)){
        $log .= "===avg_weight没有找到平均重量===\n";
        writeFile($logPath,$log);
        return "5_".$ebay_id;
    }else{
        $weight = (int)$sql[0]['avg_weight'];
        $log .= "avg_weight找到平均重量：".$weight."\n";
        $sql = "SELECT `allow_dif` FROM `weigh_rule` where weight_begin < '$weight' and weight_end >= '$weight'";
        $log .= "===".$sql."===\n";
        $sql = $dbconLocal->getResultArrayBySql($sql);
        //没有找到设置的区间
        if(empty($sql)){
            $log .= "===weigh_rule没有找到符合设置的重量区间===\n";
            writeFile($logPath,$log);
            return "5_".$ebay_id;
        }else{
            $weightStart = $weight - (int)$sql[0]['allow_dif'];
            $weightEnd = $weight + (int)$sql[0]['allow_dif'];
            $str = $weightStart.'|'.$weightEnd.'_'.$ebay_id;
            return $str;
        }
    }

//    $sql="select sku,qty from erp_goods_sale_detail where ebay_id='$ebay_id'";
//    $sql=$dbconLocal->getResultArrayBySql($sql);
//
//    if(count($sql)==1&&$sql[0]['qty']==1){ // 只有一个sku的时候 并且sku 只有一个的时候
//        $sku=$sql[0]['sku'];
//        $weight_sql = "select weight_start,weight_end from goods_out_weight where sku='$sku' and is_del=0 limit 1";
//        $weightInfo = $dbconLocal->getResultArrayBySql($weight_sql);
//        if(count($weightInfo)==1){ // 在强制验重的数据表里面 需要强制 验重
//            $weight_start = $weightInfo[0]['weight_start'];
//            $weight_end = $weightInfo[0]['weight_end'];
//        }else{
//            return "5_".$ebay_id;
//        }
//    }else{
//        return "5_".$ebay_id;
//    }

//    $str = $weight_start.'|'.$weight_end.'_'.$ebay_id;
//    return $str;
}


$type = $_POST['type'];

//称重拦截
$trackNumber = addslashes($_REQUEST['ebayid']);
$sql = "select ebay_id from erp_ebay_order where ebay_tracknumber='$trackNumber' limit 1";
$sql=$dbconLocal->getResultArrayBySql($sql);
$ebay_id = $sql[0]['ebay_id'];
$sql = "select * from order_intercept_record where ebay_id='$ebay_id' and status=0 limit 1";
$sql=$dbconLocal->getResultArrayBySql($sql);
if(count($sql)>0){
    echo -302;// 核对失败:订单已被拦截,请先检查拦截列表
    return ;
}


if ($type == 'newupdateweight') {

    $MAX = 8800;  // 同一种运输方式同一个人最多只能扫100个而不打包  @TODO 以后变动知悉修改此值

    $ebayid = addslashes($_REQUEST['ebayid']);// 这里就是跟踪号了
    $carrier = addslashes($_REQUEST['curr']);
    $orderId = (int) $_REQUEST['orderid'];

    $lastScanTime = strtotime('-6 days');

//    $checkTransNum = <<<TEXT
//select count(id) as counts from api_orderweight where bag_mark='' and scantime >'{$lastScanTime}' and scan_user = '{$_SESSION['truename']}'
//and transport = '{$carrier}'
//TEXT;
//    $orderCounts = $dbconLocal -> getResultArrayBySql($checkTransNum);
//    if ($orderCounts[0]['counts'] >= $MAX) {
//        echo '3';
//        return null;
//    }


    if($orderId == 0){
        $ebayid=Handle_tracknumber($ebayid,$carrier);
        if($ebayid==''){
            echo -700;// 没有跟踪号不允许出库
            return ;
        }
        $sql="select ebay_id from erp_ebay_order where ebay_tracknumber='$ebayid' and ebay_status in(2009) limit 1";
        $sql=$dbconLocal->getResultArrayBySql($sql);
        if(count($sql)==0){
            echo -2;// 没有扫描
            return ;
        }
    }else{
        $sql="select ebay_id from erp_ebay_order where ebay_id='$orderId' and ebay_status=2009 limit 1";
        $sql=$dbconLocal->getResultArrayBySql($sql);
        if(count($sql)==0){
            echo -2;// 没有扫描
            return ;
        }
    }
    $ebay_id = $sql[0]['ebay_id'];
    $tt=time();

    $currentweight = addslashes($_REQUEST['currentweight']);
   // $ss = "update ebay_order set orderweight2 = '$currentweight'  where ebay_id='$ebay_id' limit 1";
    $in="insert into api_orderweight(ebay_id,weight,scantime, scan_user, transport)values('$ebay_id','$currentweight','$tt', '{$_SESSION['truename']}','{$carrier}')";
    $status = '0';
    if ($dbconLocal->execute($in)) {
        $status = '1'; // 同步重量成功!
        /* // 标记已经扫描过了哟!
        $tt=time();
        $up="update api_checksku set estatus=1,scantime='$tt' where ebay_id=$ebay_id limit 1";
        $dbcon->execute($up);*/
    }
    echo $status;

}


if ($type == 'newcheckorder') {

//2016/10/3 新流程出库 检查是否已经出库 扫描都是跟踪号来着
//返回计算重量
    $ebayid = addslashes($_REQUEST['ebayid']);// 这里就是跟踪号了
    $curr = addslashes($_REQUEST['curr']);
    $ebayid=Handle_tracknumber($ebayid,$curr);

    if($ebayid==''){
        echo -700;// 没有跟踪号不允许出库
        return ;
    }

    /**
    *测试人员谭 2017-08-26 14:49:50
    *说明: 无条件写日志
    */
    $log=$_SESSION['truename'].'扫描第一枪条码:'.$_REQUEST['ebayid']."----".date('YmdHis')."\n\n";
    $file=dirname(__FILE__).'/log/firstscan/'.date('YmdH').'.txt';
    writeFile($file,$log);




    $sql="select ebay_id,ebay_status from erp_ebay_order where ebay_tracknumber='$ebayid' limit 1";
    $sql=$dbconLocal->getResultArrayBySql($sql);
    $ebay_id = $sql[0]['ebay_id'];
    if(count($sql)==0){
        //  说明:  顺丰/瑞典小包 的几个 用px 做条码的 垃圾运输方式 又恢复了 朱诗萌 2017/11/18
        $sql="select ebay_id,ebay_status from erp_ebay_order where pxorderid='$ebayid' limit 1";
        $sql=$dbconLocal->getResultArrayBySql($sql);
        $ebay_id = $sql[0]['ebay_id'];
        if(count($sql)==0){
            // 核对失败:跟踪号或pxid不正确,请联系it修改
            echo -304;
            return;
        }
    }

    if($sql[0]['ebay_status'] != 2009){
        //核对失败:订单不是已出库待称重状态
        echo -306;
        return;
    }

    $ebay_id=$sql[0]['ebay_id'];
    $sql = "select * from order_intercept_record where ebay_id=$ebay_id and status = 0";
    $sql=$dbconLocal->getResultArrayBySql($sql);
    if(count($sql)>0){
        echo -302;// 核对失败:订单已被拦截,请先检查拦截列表
        return ;
    }

    $sql="select ebay_id,status from api_orderweight where ebay_id='$ebay_id' limit 1";
    $sql=$dbconLocal->getResultArrayBySql($sql);
    if(count($sql)==1){
        echo -350;// 已经同步过重量了
        return ;
    }

    /**
    *测试人员谭 2017-11-08 21:25:02
    *说明: 必须要验货或者捡货包装
    */
    $sa="select ebay_id,status from api_checksku where ebay_id='$ebay_id' limit 1";
    $sa=$dbconLocal->getResultArrayBySql($sa);

    if(count($sa)==0){
        echo -300;// 必须要验货 或者 捡货包装
        return ;
    }

    if($sa[0]['status']!=2){
        echo -303;//核对失败:订单未回传出库记录,请等待5分钟
        return;
    }


    $ss="select ebay_carrier,ebay_status,pxorderid from erp_ebay_order where ebay_id='$ebay_id' limit 1";
    $ss=$dbconLocal->getResultArrayBySql($ss);

    $ebay_carrier=$ss[0]['ebay_carrier'];
    $ebay_status =$ss[0]['ebay_status']; // 不在等待完成的时候
    $pxorderid   =$ss[0]['pxorderid']; // 有时候需要判断有没交运

    if($ebay_carrier=='EUB'&&!strstr($pxorderid,'交运')){
        echo -301;
        return ;
    }

    if(!in_array($ebay_status,[2009])){
        echo -250; // 订单在出库之后 被截住了
        return ;
    }

    //修改运输方式的匹配 以前是部分，现在是全部
    if($curr=='PMS'){
        if(substr($ebay_carrier,0,3)!=$curr){
            echo -200; //运输方式 选择的有问题
            return ;
        }
    }elseif($ebay_carrier!=$curr){
        echo -200; //运输方式 选择的有问题
        return ;
    }


/*
    if($status==0){
        echo -100; // 验货后处理异常 请尝试使用老方法出库
        return ;
    }

    if($status==-2&&$ebay_status!=2){
        echo -2; // 在验货出库时订单不再等待扫描-被转走!
        return ;
    }

    if($status=='2'){
        echo '2'; // 验货时出库失败 请尝试 使用老的扫描方式出库
        return ;
    }

    if($status==4){
        echo '4'; // 负库存，出库失败 请先校准库存 再出库
        return ;
    }

    if($status==3){
        echo '3';//已经操作过出库了 //请查看日志是 手动出库  还是核对出库
        return ;
    }*/


/*

*/
    //2016/11/2----  第一枪扫描
    $tt=time();
    $in="insert into `api_orderweight_1`(ebay_id,scantime)values('$ebay_id','$tt')";
    $dbconLocal->execute($in);
    //2016/11/2----  第一枪扫描

    if(!$_POST['nosleep']){
        usleep(350000);
    }

    /**
    *测试人员谭 2017-03-03 21:19:03
    *说明:
    */
//    $gweight=0.01; // 随便搞一个重量，反正没什么用
//    $gweight=1000*$gweight;
//    echo $gweight.'g';

   // 计算重量 |  是不是要 强制重量  |  容许的误差是多少
   //$gweight.'g|'.$checkWeight.'|'.$abs;

    $weightstr=GetWeight($ebay_id);
    //$weightstr=CheckWeight($ebay_id);
    echo $weightstr;
}


/**
 * 测试人员成  2017-03-16 15:48
 */
if ($type === 'getCounts') {
    echo getScanCount();
} else if ($type === 'getCurrentUserCarrierCounts') {
    global $dbconLocal;
    $currentUser = $_SESSION['truename'];
    $carrier     = $_REQUEST['carrier'];

    $countsSql = <<<TEXT
SELECT count('id') as counts FROM api_orderweight WHERE bag_mark='' and scan_user='{$currentUser}' and transport='{$carrier}'
TEXT;
    //统计当前用户已称重总重量，不能大于31kg xiao
    if($type === 'getCurrentUserCarrierCounts'){
        $countsSql = <<<TEXT
SELECT count('id') as counts,SUM(`weight`) as total FROM api_orderweight WHERE bag_mark='' and scan_user='{$currentUser}' and transport='{$carrier}'
TEXT;
    }
    $counts = $dbconLocal -> getResultArrayBySql($countsSql);
    if($type === 'getCounts'){
        echo $counts[0]['counts'];
    }else{
        echo $counts[0]['counts']."||".$counts[0]['total'];
    }
}

$dbconLocal->close();
?>



