<?php
//程序功能: 检查 等待出库的订单，等待同步重量的订单  是不是已经完成，但是现在的 缓存里面还是等待处理，这需要
//2016/10/14
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


// 已经出库了的, 但是出库的结果没有反馈给 erpcnapi 的
function searchOrderOutstock(){
    global $dbcon,$dbconerp;
    $tt=strtotime('-15 minutes');
    $sql="select id,ebay_id,status from api_checksku where `addtime`<$tt and status=1 order by id asc limit 3000";
    echo $sql."\n";
    $sql=$dbcon->getResultArrayBySql($sql);
    foreach($sql as $list){
        $ebayid=$list['ebay_id'];
        $id=$list['id'];
        $ss="select ebay_status from ebay_order where ebay_id='$ebayid' limit 1";
        $rs=$dbconerp->getResultArrayBySql($ss);
        $ebay_status=$rs[0]['ebay_status'];
        if($ebay_status==2||$ebay_status==2009){ // 已经出库了的
            // 这里其实有一个问题，如果这个订单 在回收站怎么办!
            // 要标记已经处理，不能让程序像个傻逼似的一个劲地 往服务器发
            $up="update api_checksku set `status`='2' where id='$id' limit 1";
            $dbcon->execute($up);
        }
    }
}

// 已经同步成功了重量，但是客户端没有收到 反馈，这里根据主从同步 反查
function searchOrderweight(){
    global $dbcon,$dbconerp;
    $tt=strtotime('-15 minutes');
    //status =1  等待传 重量 status=2 已经传了重量
    $sql="select ebay_id,id from api_orderweight where  scantime<$tt and status=1 order by id asc limit 3000";
    echo $sql."\n";
    $sql=$dbcon->getResultArrayBySql($sql);
    foreach($sql as $list){
        $ebayid=$list['ebay_id'];
        $id=$list['id'];
        //estatus =1 已经同步了重量  ==0 等待同步  注意这个表是 erp 里面的
        $ss="select estatus from api_checksku where ebay_id='$ebayid' limit 1";
        $rs=$dbconerp->getResultArrayBySql($ss);
        if($rs[0]['estatus']==1){
            //重量已经同步成功了
            $up="update api_orderweight set status=2 where id=$id and status=1 limit 1";
            $dbcon->execute($up);
        }

    }
}

searchOrderOutstock();
searchOrderweight();


$dbcon->close();
$dbconerp->close();