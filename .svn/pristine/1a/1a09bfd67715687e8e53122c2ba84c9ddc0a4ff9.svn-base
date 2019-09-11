<?php
error_reporting(0);
echo '<meta charset=utf-8>';
include "include/dbconf.php";
include "include/dbmysqli.php";
unset($dbcon);
$dbconLocal =new DBMysqli($LOCAL);
$dbcon      =new DBMysqli($ERPCONF);

$ebay_ids= include 'test.php';


$arrs=[];
foreach($ebay_ids as $List){
    $sql="SELECT is_delete,ordersn FROM `pick_order_detail`  WHERE `ebay_id` =$List limit 1 ";
    $sql=$dbconLocal->getResultArrayBySql($sql);

    $ss="select ebay_status from ebay_order where ebay_id='$List' limit 1";
    $ss=$dbcon->getResultArrayBySql($ss);
    $arrs[$ss[0]['ebay_status']]=1;//'<br>';

    if(count($sql)==0){

        //echo $List.',没有捡货单<br>';
    }else{
        if($sql[0]['is_delete']==1){
          //  echo $List.',捡货单中被删除<br>';
        }else{
            //echo $List.',正常捡货单'.$sql[0]['ordersn'].'<br>';
        }
    }

}

print_r($arrs);
