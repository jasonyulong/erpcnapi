<?php
//2016 年 1-08 谭联星 分割文件 orderindex.php 的 request
//=============================================
if(!in_array("orders",$cpower)){
    die('无权限，请在系统管理，用户管理中设置');
}
$scanstart = $_REQUEST['scanstart'];
$scanend   = $_REQUEST['scanend'];
$erp_op_id = $_REQUEST['erp_op_id'];
$isprint     = $_REQUEST['isprint'];
$stockstatus = $_REQUEST['stockstatus'];
#$hunhe				= $_REQUEST['hunhe'];
$ebay_ordertype		= $_REQUEST['ebay_ordertype'];
$ostatus			= $_REQUEST['ostatus']?$_REQUEST['ostatus']:"0";
$status0			= $_REQUEST['status'];// 指定订单状态
$fprice             = $_REQUEST['fprice'];
$eprice             =$_REQUEST['eprice'];
$fweight             =$_REQUEST['fweight'];
$eweight             =$_REQUEST['eweight'];
$country			= $_REQUEST['country'];
$keys				= $_REQUEST['keys']?trim($_REQUEST['keys']):"";
$shipping			= $_REQUEST['Shipping'];
$sku				= $_REQUEST['sku'];
$note				= $_REQUEST['isnote'];

$account			= $_REQUEST['account'];
$searchtype			= $_REQUEST['searchtype'];
$start				= $_REQUEST['start'];
$end				= $_REQUEST['end'];
$tid                = $_REQUEST['tid'];
$fromdbl            = $_GET['fromdbl'];
//ostatus=1
if(!in_array($searchtype,array('9','11','5','1','0'))||$keys==''){//搜索结果必然很大的情况下
    $start				= empty($start)?date('Y-m-d',strtotime("-30 days")):$start;
    $end				= empty($end)?date('Y-m-d'):$end;
}else{
    $start				= '';
    $end				= '';
}

//待处理不能有付款时间 未付款不能有付款时间
if($status0=='1'||in_array($ostatus,array(0,1,1728))||($fromdbl=='1')){
    $start				= '';
    $end				= '';
}

if($tid!=''){
    $start				= '';
    $end				= '';
}

$type				= $_REQUEST['type'];
$ebay_site			= $_REQUEST['ebay_site'];
$sort				= $_REQUEST['sort']?$_REQUEST['sort']:'';
$screen             = $_REQUEST['screen'];
$sortstatus			= $_REQUEST['sortstatus']?$_REQUEST['sortstatus']:0;
$sortdefault		= 0;
$navAction          = $_REQUEST['action'];
$requestWarehouse          = $_REQUEST['input_warehouse'];

// 避免没有 key 值的 全局扫描  2015-01-02 谭星
if($status0==100){
    $searchField=$keys.$scanstart.$scanend.$start.$end.$fprice.$eprice.$fweight.$eweight.$country.$shipping.$account.$type.$fromdbl;
    #echo '@@'.$searchField.'@@@';
    if(!empty($searchField)){
        $ostatus = $status0;
    }else{
        $ostatus = 1;
        $navAction='待处理';
    }
}elseif($status0 != ''){
    $ostatus = $status0;
}
// 大量订单搜索，不润需
if((!in_array($searchtype,array('9','11','5','1','0'))||$keys=='')&&in_array($ostatus,array(100,2))){
    if((strtotime($end)/86400-strtotime($start)/86400)>182){
        $logsearch=$_SESSION['truename'].'++++'.$_SERVER["QUERY_STRING"]."++++".date("YmdHis")."\r\n";
        $start				= date('Y-m-d',strtotime("-10 days"));
        echo "<div style='color:#911;font-size:13px;margin:4px 0 0 4px;'>条件不精确,禁止搜索半年以上的订单!</div>";
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/log/logsearch.txt",$logsearch,FILE_APPEND);
    }
}

// end

$stoptime           = $_REQUEST['stoptime'];

$feedbacktypeArr=array('Positive'=>'<img width="20" height="20" src="images/iconPos_16x16.gif">','Negative'=>'<img width="20" height="20" src="images/iconNeg_16x16.gif">','Neutral'=>'<img width="20" height="20" src="images/iconNeu_16x16.gif">');

if($sort == 'recordnumber'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.recordnumber desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.recordnumber asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'ebay_userid'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_userid desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_userid asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'sku'){
    if($sortstatus  == '0'){
        $sortstr	= " order by b.sku desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by b.sku asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'ebay_shipfee'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_shipfee desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_shipfee asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'ebay_total'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_total desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_total asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}
if($sort == 'ebay_createdtime'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_createdtime desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_createdtime asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}



if($sort == 'ebay_paidtime'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_paidtime desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_paidtime asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'onumber'){

    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_id desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_id asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'ShippedTime'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ShippedTime desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ShippedTime asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}

if($sort == 'ebay_account'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_account desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_account asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}
if($sort == 'ebay_countryname'){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_countryname desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_countryname asc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
}
if($sort == ''  ){
    if($sortstatus  == '0'){
        $sortstr	= " order by a.ebay_paidtime desc";
        $sortstatus = 1;
        $sortsimg	= "<img src='images/descend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }else{
        $sortstr	= " order by a.ebay_paidtime desc";
        $sortstatus	= 0;
        $sortsimg	= "<img src='images/ascend_10x5.gif'   width=\"10\" height=\"5\"/>";
    }
    $sort = 'ebay_paidtime';
}


$storeArr=getStoreIndexID();
