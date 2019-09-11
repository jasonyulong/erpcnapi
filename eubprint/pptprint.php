<?php
header("Content-Type:text/html; charset=utf-8;");

include "../include/config.php";
include "eublabel.php";
function p($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}
//订单号
$bill =	trim($_REQUEST['bill'],",");
$sql = "select * from ebay_order where ebay_id in ($bill)";
$result = $dbcon->getResultArrayBySql($sql);

//组合名单数据
$data = array();
foreach ($result as $key => $value) {

    $data[$key]['ebay_id'] 			= 	$value['ebay_id'];
    $data[$key]['ebay_carrier'] 	= 	$value['ebay_carrier'] ;
    $data[$key]['ebay_tracknumber']	=   $value['ebay_tracknumber'];
	$data[$key]['ebay_username']	=	$value['ebay_username'];
	$data[$key]['ebay_street']    	=   $value['ebay_street'];
	$data[$key]['ebay_street1']   	=   $value['ebay_street1'];
	$data[$key]['ebay_city']		=	$value['ebay_city'];
	$data[$key]['ebay_state']     	=   $value['ebay_state'];
	$data[$key]['ebay_countryname']	=   $value['ebay_countryname'];
	$data[$key]['ebay_postcode']  	=   $value['ebay_postcode'];
    $data[$key]['zipcode']        	=   substr($ebay_postcode, 0, 5);
	$data[$key]['ebay_phone']     	=   $value['ebay_phone'] == "" ? $value['ebay_phone1'] : $value['ebay_phone'];
	$data[$key]['print_date']     	=   date('Y-m-d H:i:s');

/*
    if($ebay_carrier != 'EUB' && $ebay_carrier != '线下EUB' && $ebay_carrier != '纯电池EUB'){
        echo '<font style="font-size: 52px" color="red">订单号'.$value['ebay_id'].'运输方式不是EUB</font>';
        exit;
    }
*/

    if($value['ebay_tracknumber'] == ''){
        echo '<font style="font-size: 52px" color="red">订单号'.$value['ebay_id'].'没有跟踪号</font>';
        exit;
    }

    //作用未知
    $data[$key]['skustr'] = '';
	$tmp = OrderResolve($value['ebay_id']);
    foreach($tmp as $k=> $v){
        $data[$key]['skustr'] .= $v[0].'*'.$v[3].','.$k.'('.$v[2].'),';
    }

    //分拣code
    $data[$key]['postnum'] = fenjianCode($value['ebay_postcode']);
    if(false === $data[$key]['postnum']){
        $data[$key]['postnum'] = '<span style="color:#911">'.$value['ebay_id'].'严重错误:找不到邮编分区</span>';
        $data[$key]['skustr'] = $data[$key]['postnum'];
        $data[$key]['ebay_username'] = '';
    }

}
//p($data);die;

?>

<!DOCTYPE html>
<html lang="cn">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
<style>
	* {
		margin: 0;
		padding: 0;
		font-weight: bold;
		font-family: helvetica;
	}
	.box {
		width:100mm;
		height:100mm;
		border:1px dashed black;
		border: none;
		margin: 1px;
		padding:1px;
		font-size:12px;
		overflow:hidden;
	}
	.table {
		border-collapse: collapse;
		width: 100%;
		height: 100%;
		border: 2px solid #000;
	}
	table th,table td{
		border: 0;
	}
</style>

<!-- 循环面单信息 -->
<?php foreach ($data as $key => $value): ?>

<div class="list">
	<!-- 第一面 -->
	<div class="box">
		<table class="table">

			<tr><!-- 土耳其邮政标识 -->
				<td colspan="3"><img src="./ptt.png" alt=""></td>
			</tr>
			
			<tr><!-- 退件地址 -->
				<td style="width: 40px;"><img src="./return.png" alt=""></td>
				<td>
					Return if undeliverabie:<br>
					PO Box 5001 İstanbul - TURKEY<br>
				</td>
				<td style="width: 90px;">
					<div style="border: 2px solid #000; border-right: none;">
						PP<br>
						Turkey<br>
					</div>
				</td>
			</tr>

			<tr style="height: 110px;"><!-- 收件人信息 -->
				<td style="text-align: center;vertical-align: top;">To:</td>
				<td colspan="2" style="vertical-align: top;">
					<div>
						<?php echo $value['ebay_username'] ?><br>
						<?php echo $value['ebay_street'] ?><br>
						<?php echo $value['ebay_postcode'] ?><br>
						<?php echo $value['ebay_phone'] ?><br>
						<?php echo $value['ebay_city'] ?><br>
						<?php echo $value['ebay_state'] ?><br>
						<?php echo $value['ebay_countryname'] ?>
					</div>
				</td>
			</tr>

			<tr><!-- 条码 -->
				<td colspan="3" style="height: 70px;">
					<img src="../barcode128.class.php?data=<?php echo $value['ebay_tracknumber'] ?>" width="100%" height="60px">
					<div style="text-align: center;font-size: 14px;"><?php echo $value['ebay_tracknumber'] ?></div>
				</td>
			</tr>

			<tr><!-- 配货信息，自定义 -->
				<td colspan="3" style="line-height: 50px;">
					<?php echo $value['skustr'] ?>
				</td>
			</tr>

		</table>
	</div>

	<!-- 第二面 -->
	<div class="box">
		<table class="table">

			<tr style="height: 15px;"><!-- CN22标识 -->
				<td colspan="6" style="border-bottom: 2px solid #000;">
					&nbsp;&nbsp;
					CUSTOMS DECLARATION  May be opened officlally
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					CN22
				</td>
			</tr>

			<tr style="height: 20px;"><!-- 土耳其邮政授权标识 -->
				<td colspan="6" style="border-bottom: 2px solid #000;">
					DESIGNATED OPERATOR<br>
					Turkish Post<br>
				</td>
			</tr>
			
			<tr style="height: 10px;"><!-- 勾选gift栏 -->
				<td style="border: 1px solid #000; width: 10%;text-align: center;">√</td>
				<td colspan="2" style="border: 1px solid #000;width: 40%;">Gift</td>
				<td style="border: 1px solid #000;width: 10%;"></td>
				<td colspan="2" style="border: 1px solid #000; border-right: 2px solid #000;width: 40%;">Commercial sample</td>
			</tr>
			<tr style="height: 10px;">
				<td style="border: 1px solid #000; width: 10%;"></td>
				<td colspan="2" style="border: 1px solid #000;width: 40%;">Documents</td>
				<td style="border: 1px solid #000;width: 10%;"></td>
				<td colspan="2" style="border: 1px solid #000; border-right: 2px solid #000;width: 40%;">Other</td>
			</tr>

			<tr style="border-top: 2px solid #000;height: 30px;"><!-- 明细 -->
				<td colspan="4" style="border: 1px solid #000;">QUANTITY AND DETAILED
				DESCRIPTION OF</td>
				<td style="border: 1px solid #000;">Weight(KG)</td>
				<td style="border: 1px solid #000;">Value(USD)</td>
			</tr>
			<tr style="height: 15px;">
				<td colspan="4" style="border: 1px solid #000;"><?php echo $value['skustr'] ?></td>
				<td style="border: 1px solid #000;">0.65</td>
				<td style="border: 1px solid #000;">USD 12</td>
			</tr>
			<tr style="height: 30px;">
				<td colspan="4" style="border: 1px solid #000;">
					If Known,HS Tariff number and
					country of origin of goods
				</td>
				<td style="border: 1px solid #000;">
					Total
					weight(KG)
				</td>
				<td style="border: 1px solid #000;">
					Total
					value(USD)
				</td>
			</tr>
			<tr style="height: 15px;">
				<td colspan="4" style="border: 1px solid #000;">
					ORAIGIN:China
				</td>
				<td style="border: 1px solid #000;width: 20%;">
					0.65
				</td>
				<td style="border: 1px solid #000;width: 20%;">
					USD 12
				</td>
			</tr>

			<tr style="border-top: 2px solid #000; height: 80px;"><!-- 声明内容 -->
				<td colspan="6" style="font-size: 12px;">
					&nbsp;&nbsp;
					The undersigned whose name and address are given on the item certify that the particulars given in the declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.
				</td>
			</tr>

			<tr style="border-top: 2px solid #000;height: 15px;"><!-- 电子签名 -->
				<td colspan="3">
					signature <img src="./sign.png" alt="">
				</td>
				<td colspan="3" style="text-align: center;"><!-- 面单打印日期 -->
					DATE <?php echo $value['print_date'] ?>
				</td>
			</tr>

			<tr style="border-top: 2px solid #000;"><!-- 自定义内容 -->
				<td colspan="6">
					note信息<br>
				</td>
			</tr>

		</table>
	</div>
</div>
<?php endforeach; ?>

</body>
</html>