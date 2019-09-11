<?php
@session_start();
error_reporting(0);
include "include/dbconf.php";
include "include/dbmysqli.php";
unset($dbcon);
$dbconLocal =new DBMysqli($LOCAL);
$dbcon      =new DBMysqli($ERPCONF);
$user	    = $_SESSION['user'];

if($user == ""){
    echo "<script>location.href='login.php'</script>";die();
}

date_default_timezone_set ("Asia/Chongqing");

$type = $_REQUEST['type'];

if ($type == 'get_scancount') {
    $start = trim($_REQUEST['start']);
    $end = trim($_REQUEST['end']);
    if($start==''){
        $start=date('Y-m-d');
    }

    if($end==''){
        $end=date('Y-m-d');
    }
    $sstart=strtotime($start.' 00:00:00');
    $send=strtotime($end.' 23:59:59');

    $sql="select count(a.ebay_id) as cc from api_orderweight_1 a left join api_orderweight b using(ebay_id) ";
    $sql.="where a.scantime>=$sstart and a.scantime<=$send and b.ebay_id is null";
    $rs=$dbconLocal->getResultArrayBySql($sql);
    $badcount=$rs[0]['cc'];

    $sql="select count(a.ebay_id) as cc from api_orderweight_1 a";
    $sql.=" where a.scantime>=$sstart and a.scantime<=$send ";
    $rs=$dbconLocal->getResultArrayBySql($sql);
    $count=$rs[0]['cc'];

    echo $badcount.'/'.$count;
}

$dbcon->close();
?>