<?php
/*
 * 2016 11 7
 * 清理掉--- 被终止发货的订单～！
 *
 *
 * */
error_reporting(0);
date_default_timezone_set ("Asia/Chongqing");
set_time_limit(200);
if(empty($_SERVER['argv'])){
    echo '---not php-cli----';
    die();
}
include 'include/dbconf.php';
include 'include/dbmysqli.php';
$dbcon=new DBMysqli($LOCAL);
$dbconerp=new DBMysqli($ERPCONF);
include 'include/LocalApi.class.php';


function clearStopOrders(){
    global $dbcon,$dbconerp;
    // 只想用这个类里面的 日志方法
    $LocalApi=new LocalApi();
    $file=dirname(__FILE__).'/log/clearStop/'.date('Ymd').'.txt';
    $tt=time();

    $sql="select ebay_id,addtime from ebay_stopship order by id desc limit 200";
    $sql=$dbconerp->getResultArrayBySql($sql);

    foreach($sql as $list){
        $ebay_id=$list['ebay_id'];
        $addtime=$list['addtime'];

        $ss="select id from ebay_stopship where ebay_id='$ebay_id' and addtime='$addtime' limit 1";
        $ss=$dbcon->getResultArrayBySql($ss);
        if(count($ss)==1){
            echo $ebay_id.',已经处理过了'."\n";
            continue; // 已经处理过了
        }

        echo $ebay_id.',清理数据'."\n";
        $ss="select id from api_orderweight where ebay_id='$ebay_id' limit 1";
        $ss=$dbcon->getResultArrayBySql($ss);
        if(count($ss)==1){
            $log="【严重异常】订单:$ebay_id 已经扫描了重量! 但是被取消发货了!请仓库人员 观察被阻挡包裹是否包含这个订单，如果没有,请联系物流拦截!";
            echo $log."\n";
            $LocalApi->writeFile($file,$log);
            $in="insert into api_error(ebay_id,`note`,`addtime`)values('$ebay_id','$log','$tt')";
            $dbcon->execute($in);
            continue;
        }

        $del="delete from api_checksku where ebay_id='$ebay_id' limit 1";
        $dbcon->execute($del);
        $del="delete from api_orderweight where ebay_id='$ebay_id' limit 1";
        $dbcon->execute($del);


        $in="insert into ebay_stopship(ebay_id,`addtime`)values('$ebay_id','$addtime')";

        if($dbcon->execute($in)){
            $log="订单:$ebay_id 清理完验货记录!";
            $LocalApi->writeFile($file,$log);
        }
    }

}


clearStopOrders();

$dbcon->close();
$dbconerp->close();