<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/17
 * Time: 11:19
 */
 include "../include/config.php";
 include "eublabel.php";
 $bill	=	trim($_REQUEST['bill'],",");
$sql="select ebay_carrier,ebay_warehouse,ebay_id from ebay_order where ebay_id in ($bill)";
$sql=$dbcon->getResultArrayBySql($sql);
$orders=array();
foreach($sql as $v){
    $carrier=$v['ebay_carrier'];
    $ebay_id=$v['ebay_id'];
    $ebay_warehouse=$v['ebay_warehouse'];
    //if($carrier==''){continue;}
    $local=GetOrderMainSKULocal($ebay_id,$ebay_warehouse);
    $orders[$v['ebay_id']]=$local;
}

natsort($orders);//debug($orders);
$arraysss=array();
foreach($orders as $key=>$v){
    $arraysss[]=$key;
}
$orders=$arraysss;
//debug($orders);
foreach($orders as $lnums=> $item){
    $error   =   '';
    $sql     =   "select * from ebay_order where ebay_id=$item limit 1";
    $sql     =   $dbcon->getResultArrayBySql($sql);
    $ss      =   OrderResolve($item);
    $ebay_id        =   $sql[0]['ebay_id'];
    $ebay_carrier   =   $sql[0]['ebay_carrier'] ;

    // 不是EUB系列
    if(!strstr($ebay_carrier,'EUB')){
        echo '<font style="font-size: 52px" color="red">订单号'.$ebay_id.'运输方式不是EUB</font>';
        exit;
    }


    $ebay_tracknumber=  $sql[0]['ebay_tracknumber'];
    if($ebay_tracknumber==''){
        echo '<font style="font-size: 52px" color="red">订单号'.$ebay_id.'没有跟踪号</font>';
        exit;
    }
	$ebay_username	=	$sql[0]['ebay_username'];
	$ebay_street    =   $sql[0]['ebay_street'];
	$ebay_street1   =   $sql[0]['ebay_street1'];
	$ebay_city		=	$sql[0]['ebay_city'];
	$ebay_state     =   $sql[0]['ebay_state'];
	$ebay_countryname=  $sql[0]['ebay_countryname'];
	$ebay_postcode  =   $sql[0]['ebay_postcode'];
    $zipcode        =   substr($ebay_postcode,0,5);

    $postnum        =   '';
    /*    $num            =   substr($ebay_postcode,0,2);
    if(0<$num&&$num<=29){
        $postnum = 1;
    }
     if(29<$num&&$num<=79){
         $postnum = 3;
    }
     if(79<$num&&$num<=93){
         $postnum = 4;
     }
     if(93<$num&&$num<=99){
         $postnum = 2;
     }*/


	$ebay_phone     =   $sql[0]['ebay_phone']==""?$sql[0]['ebay_phone1']:$sql[0]['ebay_phone'];

	$ss             =   OrderResolve($ebay_id);
    $skustr         =   '';
    foreach($ss as $key=> $vv){
        $skustr.=  $vv[0].'*'.$vv[3].','.$key.'('.$vv[2].'),';
    }
    $postnum=fenjianCode($ebay_postcode);
    if(false===$postnum){
        $postnum='<span style="color:#911">'.$ebay_id.'严重错误:找不到邮编分区</span>';
        $skustr=$postnum;
        $ebay_username='';
    }

    $ss="select address from ebay_carrier where name='$ebay_carrier' and status=1 limit 1";

    $ss=$dbcon->getResultArrayBySql($ss);
    $backAddress=trim($ss[0]['address']);

    if($backAddress==''){
        echo '<font style="font-size: 52px" color="red">订单号'.$ebay_id.',运输方式 '.$ebay_carrier.' 没有回邮地址，请在物流管理设置!</font>';
        exit;
    }


    showTemplate($ebay_username,$ebay_street,$ebay_street1,$ebay_city,$ebay_state,$ebay_countryname,$ebay_postcode,$zipcode,$ebay_phone,$ebay_tracknumber,$skustr,$postnum,$ebay_id,$backAddress);
 }

