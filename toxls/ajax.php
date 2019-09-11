<?php
include "../include/config.php";

//======================================================================function start

function ModItemTitleByItemID($itemid,$newtitle){
    global $userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb,$dbcon;
    $sql="select ebay_account from ebay_list where ItemID='$itemid' limit 1";
    $sql=$dbcon->getResultArrayBySql($sql);
    $ebay_account=$sql[0]['ebay_account'];

    if(empty($ebay_account)){
        return -1;
    }
    $token				= geteBayaccountToken($ebay_account);
    $verb = 'ReviseItem';
    $xmlRequest		= '<?xml version="1.0" encoding="utf-8"?>
<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
   <RequesterCredentials>
      <eBayAuthToken>'.$token.'</eBayAuthToken>
   </RequesterCredentials>
   <ErrorLanguage>en_US</ErrorLanguage>
   <WarningLevel>High</WarningLevel>
  <Item>
  <ItemID>'.$itemid.'</ItemID>
  <Title>'.$newtitle.'</Title>
  </Item>
</ReviseItemRequest>';
    $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
    $responseXml = $session->sendHttpRequest($xmlRequest);
    if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
    $data	= XML_unserialize($responseXml);
    //print_r($data);
    if($data['ReviseItemResponse']['Ack']!='Success'){
        $emsg=$data['ReviseItemResponse']['Errors'];
        if(isset($emsg[0])){
            $emsg=$emsg[0]['LongMessage'].'<br>'.$emsg[1]['LongMessage'];
        }else{
            $emsg=$data['ReviseItemResponse']['Errors']['LongMessage'];
        }
        return $emsg;
    }else{

        $u="update ebay_list set `Title`='$newtitle' where ItemID='$itemid' limit 1";
        $dbcon->execute($u);
        return 2;
    }
}

function ModItemLocationByItemID($itemid,$postCode,$newLocation){
    global $userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb,$dbcon;
    $sql="select ebay_account from ebay_list where ItemID='$itemid' limit 1";
    $sql=$dbcon->getResultArrayBySql($sql);
    $ebay_account=$sql[0]['ebay_account'];

    if(empty($ebay_account)){
        return -1;
    }
    $token				= geteBayaccountToken($ebay_account);
    $verb = 'ReviseItem';
    $xmlRequest		= '<?xml version="1.0" encoding="utf-8"?>
<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
   <RequesterCredentials>
      <eBayAuthToken>'.$token.'</eBayAuthToken>
   </RequesterCredentials>
   <ErrorLanguage>en_US</ErrorLanguage>
   <WarningLevel>High</WarningLevel>
  <Item>
  <ItemID>'.$itemid.'</ItemID>
  <Location>'.$newLocation.'</Location>
  <PostalCode>'.$postCode.'</PostalCode>
  </Item>
</ReviseItemRequest>'; //echo $xmlRequest;
    $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
    $responseXml = $session->sendHttpRequest($xmlRequest);
    if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
    $data	= XML_unserialize($responseXml);
    //print_r($data);
    if($data['ReviseItemResponse']['Ack']!='Success'){
        return $data['ReviseItemResponse']['Errors']['LongMessage'];
    }else{
        //print_r($data);
        $u="update ebay_list set `Location`='$newLocation' where ItemID='$itemid' limit 1";
        $dbcon->execute($u);
        $file = dirname(dirname(__FILE__)).'/log/ModLocation/'.date('Ymd').".txt";
        $log=$_SESSION['truename']." 修改了".$itemid.' Location 为:'.$newLocation.'-----'.date('Y-m-d H:i:s')."\r\n\r\n";
        writeFile($file,$log);
        return 2;
    }
}
function writeFile($file, $str) {
    $index = strripos($file, '/');
    if (!file_exists($file) && strripos($file, '/') !== false) {
        $fileDir = substr($file, 0, $index);
        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
        }
    }
    file_put_contents($file, "\xEF\xBB\xBF" . $str, FILE_APPEND);
}

//======================================================================function end
$ajaxType=trim($_POST['doaction']);

if($ajaxType=='ajaxCheckPackage'){
	$pid=mysql_real_escape_string(trim($_POST['packageID']));
	$warehouse=mysql_real_escape_string(trim($_POST['warehouse']));
	$sql="select id from package where packageID='$pid' AND  `status`=2 and get_warehouse='$warehouse' limit 1";
	$sql=$dbcon->getResultArrayBySql($sql);
	//sleep(10);
	if(count($sql)==0){
		echo -1;
	}else{
		echo 2;
	}
	$dbcon->close();
	die();
}

if($ajaxType=='sendPackageWeight'){
	$pid=mysql_real_escape_string(trim($_POST['packageID']));
	$weight=mysql_real_escape_string(trim($_POST['weight']));
	$sqlsku="SELECT a.id,a.from_warehouse,a.get_warehouse,b.price,b.sku,b.qty FROM package a JOIN package_detail b ON a.packageID=b.packageID WHERE a.packageID='$pid' and a.`status`=2";
	//echo $sql;die();
	$sqlsku=$dbcon->getResultArrayBySql($sqlsku);
	$id=$sqlsku[0]['id'];
	$warehouse=$sqlsku[0]['from_warehouse'];//备货仓
	$inwarehouse=$sqlsku[0]['get_warehouse'];//海外仓
	//检测仓库
	foreach($sqlsku as $v){
		$sku=$v['sku'];
		$qty=$v['qty'];
		$sql="select id,gross_weight,abroad_freight,goods_count as cc from ebay_onhandle_".$warehouse." where goods_sn='$sku' limit 1";
		$sql=$dbcon->getResultArrayBySql($sql);
		if(count($sql)==0){
			echo "-1@出库仓库中不存在SKU:".$sku;
			die();
		}
        $cc=$sql[0]['cc'];
        if($cc<$qty){
            echo "-1@出库仓中".$sku."数量不足,出库".$qty.'个,在库'.$cc.'个';
            die();
        }

        $sql="select id,gross_weight,abroad_freight,packingmaterial from ebay_onhandle_".$inwarehouse." where goods_sn='$sku' limit 1";
        $sql=$dbcon->getResultArrayBySql($sql);
        if(count($sql)==0){
            echo "-1@入库仓库中不存在SKU:".$sku;
            die();
        }

        if($sql[0]['gross_weight']==0&&$inwarehouse!=197){
        	echo "-1@SKU:".$sku.'入库仓库未设置毛重';
        	die();
        }  
        if($sql[0]['abroad_freight']==0){
        	//echo "-1@SKU:".$sku.'入库仓库未设置头程运费';
        	//die();
        }
        if($sql[0]['packingmaterial']==0){
            echo "-1@SKU:".$sku.'入库仓库未设置包材';
            die();
        }
	}

	//检验这个包裹是否已经出库
	$isout="select id from ebay_iostore where and `type`='1' sourceorder='$pid' limit 1";
	$isout=$dbcon->getResultArrayBySql($isout);
	if(count($isout)!=0){
		echo "-1@该包裹已经扫描过了!";
		die();
	}
    $ss="select id, ebay_storename from ebay_storetype where ebay_storename like '海外仓包裹出库' limit 1";
    $ss=$dbcon->getResultArrayBySql($ss);
    $io_typeid=$ss[0]['id'];

	$io_ordersn = "IO-".$pid;
	$scantime=time();
	$addstore = "insert into ebay_iostore(ebay_account,io_audittime,io_ordersn,io_addtime,io_warehouse,io_type,io_status,io_note,ebay_user,`type`,operationuser,io_user,sourceorder,audituser) ";
	$addstore.= "values('','$scantime','$io_ordersn','$scantime','$warehouse','$io_typeid','1','海外仓包裹出库:{$pid}','$user','1','$user','$user','$pid','$user')";
	$addstore=$dbcon->execute($addstore);
	if(!$io_ordersn){
		echo "-1@出库单生成失败!";
		die();
	}
	$sql = "insert into ebay_iostoredetail(status,io_ordersn,goods_name,goods_sn,goods_cost,goods_unit,goods_count,goods_id,transactioncurrncy) values";
	foreach($sqlsku as $v){
		$sku=$v['sku'];
		$qty=$v['qty'];
		$price=$v['price'];
		$outsql="update ebay_onhandle_".$warehouse." set  goods_count=goods_count-".$qty." where goods_sn='$sku' limit 1"; //echo $outsql;die();
		$sql.="('B','$io_ordersn','','$sku','$price','个','$qty','',''),";
		$outsql=$dbcon->execute($outsql);
	}
	$sql=trim($sql,',');
	$rs=$dbcon->execute($sql);
	$lastSql="update package set scantime='$scantime',`status`=3,weight='$weight' where id='$id' limit 1";
	$rs=$dbcon->execute($lastSql);
	if($rs){
		echo '2@';
	}
	$dbcon->close();
	die();
}

if($ajaxType=='ajaxFixdListTitle'){
    $itemid=$_POST['itemid'];
    $newtitle=$_POST['newTitle'];
    $newtitle=str_replace('\\','',$newtitle);
    $rs=ModItemTitleByItemID($itemid,$newtitle);
    if($rs==-1){
        echo 'get token error';
    }else{
        echo $rs;
    }

}

if($ajaxType=='ajaxFixLocation'){
    $itemid=explode(',',$_POST['itemid']);
    $newtitle=$_POST['newlocation'];
    $postCode=trim($_POST['postCode']);
    $newtitle=str_replace('\\','',trim($newtitle));
    foreach($itemid as $item){
        if($item==''){ continue;}
        $itemsql="select ItemID from ebay_list where id='$item' limit 1";
        $itemsql=$dbcon->getResultArrayBySql($itemsql);
        $item=$itemsql[0]['ItemID'];
        $rs=ModItemLocationByItemID($item,$postCode,$newtitle);
        if($rs==-1){
            echo '<div style="color:#933">'.$item.':get token error</div>';
        }elseif($rs==2){
            echo '<div style="color:#393">'.$item.':修改成功!!</div>';
        }else{
            echo '<div style="color:#933">'.$rs.'</div>';
        }
    }

}

if($ajaxType=='ajaxgetstatuscount'){
    //sleep(10);
    $ssid=(int)trim($_POST['status']);
    $vieworderstatus=$_SESSION['vieworderstatus'];
    $vieworderstatusArr=explode(',',$vieworderstatus);
    if(!empty($vieworderstatus)){
        $vieworderstatusStr=" and id in($vieworderstatus) ";
    }else{
        $vieworderstatusStr='';
    }
     $sql  = "select count(ebay_id) as cc from erp_ebay_order as a where  a.ebay_status='$ssid' and a.ebay_combine!='1'";
     //echo $sql;
     $sql = $dbcon->getResultArrayBySql($sql);
     echo $sql[0]['cc'];
}


/**
 * @param $re
 */
function deal_combine_order(&$re)
{
    $ordersn = [];
    $targets = [];
    $result = [];
    foreach($re['data'] as $key=>$val){
        if( in_array( $val['ebay_ordersn'] , $ordersn ) ){
            $targets[$val['ebay_ordersn']][] = $val;
        }else{
            array_push( $ordersn , $val['ebay_ordersn'] );
            $targets[$val['ebay_ordersn']] = [$val];
        }
    }

    $flg = 0;
    foreach($targets as $k=>$v){
        $result[$flg] = $v[0];
        $result[$flg]['sku'] = [];
        $result[$flg]['recordnumber'] = [];
        $result[$flg]['ebay_amount'] = [];
        $result[$flg]['ebay_itemid'] = [];
        foreach ($v as $ke => $va) {
            $result[$flg]['sku'][] = $va['sku'];
            $result[$flg]['recordnumber'][] = $va['recordnumber'];
            $result[$flg]['ebay_amount'][] = $va['ebay_amount'];
            $result[$flg]['ebay_itemid'][] = $va['ebay_itemid'];
        }
        $flg++;
    }

    $re['data'] = $result;
}




/*
 * 查看合并订单的详情
 * */
if($ajaxType=='ajaxViewCombine'){

    $combines = explode('##' , trim($_POST['combines'] , '##'));
    $data = ['status'=>false , 'data'=>[]];

    foreach($combines as $value){
        $sql = "select d.recordnumber , d.ebay_itemid , d.sku , d.ebay_amount , a.ebay_id , a.ebay_ordersn , a.ebay_orderid , a.ebay_addtime , a.ebay_paidtime , ebay_username from ebay_order a
inner join ebay_orderdetail d on d.ebay_ordersn=a.ebay_ordersn
 where a.ebay_id='{$value}'";

        $result = $dbcon->getResultArrayBySql($sql);
        foreach($result as $k=>$v){
            $result[$k]['ebay_addtime'] = date('Y-m-d H:i:s' , $result[$k]['ebay_addtime']);
            $result[$k]['ebay_paidtime'] = date('Y-m-d H:i:s' , $result[$k]['ebay_paidtime']);
            $data['data'][] = $result[$k];
        }
    }
    if($data['data']){
        $data['status'] = true;
    }

    deal_combine_order($data);
    echo json_encode($data);
}




$dbcon->close();


