<?php
@session_start();
include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/functions.php";

error_reporting(0);
$dbconLocal =new DBMysqli($LOCAL);
//$dbcon      =new DBMysqli($ERPCONF);

//bagMark={$bagMark}&count={$counts}&weight={$weightTotal}&transport={$transport}
$bagMark = $_REQUEST['bagMark'];

//$count   = $_REQUEST['counts'];
//$weight  = $_REQUEST['weight'];
//$transport = $_REQUEST['transport'];
$getBagInfoSql = <<<TEXT
SELECT weight,create_by FROM api_bags WHERE mark_code = '{$bagMark}' limit 1
TEXT;
$resultSet = $dbconLocal -> getResultArrayBySql($getBagInfoSql);

$weight = $resultSet[0]['weight'];
$createBy = $resultSet[0]['create_by'];

$ss="select sum(weight) as cc,count(id) as counts, transport,scan_user from  api_orderweight where bag_mark='$bagMark' limit 1";
//echo $ss;

$ss=$dbconLocal->getResultArrayBySql($ss);
$weight=number_format($ss[0]['cc']/1000,2);
$count = $ss[0]['counts'];
$transport = $ss[0]['transport'];
//要求显示最后一个称重人
$user_sql = "select scan_user from  api_orderweight where bag_mark='$bagMark' ORDER BY id DESC limit 1";
$user_data = $dbconLocal->getResultArrayBySql($user_sql);
$scan_user = $user_data[0]['scan_user'];

$config = include "newerp/Application/Common/Conf/wms_config.php";
$store = $config['CURRENT_STORE_ID'];
$storeSql = "select store_sn from ebay_store where id='$store' limit 1";
$storeSql = $dbconLocal->getResultArrayBySql($storeSql);
$store_mb = $storeSql[0]['store_sn'];

$sqlcarrier = "select a.sorting_code,a.CompanyName as companyname,a.is_show_name,b.sup_code from ebay_carrier AS a JOIN ebay_carrier_company as b on a.CompanyName=b.id where name='$transport' order by a.status desc limit 1";
$sqlcarrier = $dbconLocal->getResultArrayBySql($sqlcarrier);
$sorting_code = $sqlcarrier[0]['sorting_code'];
$sup_code = $sqlcarrier[0]['sup_code'];
$is_show_name = $sqlcarrier[0]['is_show_name'];
list($barcode,$_) = str2barcode($bagMark);
$daihao = explode('-',$bagMark)[1];

$ab = getCountryAb($transport);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> 打印打包单 </title>
</head>
<style type="text/css">
    *{
        margin:0;
        padding:0;
    }
    .container {
        position: relative;
        width:10cm;
        height:10cm;
        background-color: #fff;
        border-radius: 5px;
        border:1px dashed #555;
    }

    html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, font, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,p {  margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: top;background: transparent;list-style:none;  }
    body{padding:0;margin:0;font-family:helvetica;}
    table{border-top:1px #000 solid;border-left:1px #000 solid}.mleft{margin-left:5mm;}
    table td{border-right:1px #000 solid;border-bottom:1px #000 solid;padding:2px;}
    #next {border-style:none;padding:2px;}
    #next td{border-style:none;padding:2px;}
    #mytr td{border-top:1px #000 solid;border-left:1px #000 solid}
    td{padding-top:10px}
    .view{height:100mm;width:100mm;}
    .noright{border-right:none}
    .nobottom{border-bottom:none}
    .noleft{border-left:none}
    .notop{border-top:none}
    .f12{font-size: 12px;}
    h2,h3{padding:0;margin:0;text-align:center}
    h3{font-size:10pt;}
    h2{font-size:9pt;}
    .font5{font-size:5pt;}
    .font6{font-size:6pt;}
    .font7{font-size:7pt;}
    .font8{font-size:8pt;}
    .font9{font-size:9pt;}
    .font10{font-size:10pt;}
    .left{float:left;text-align:center;}
    .height1{height:10mm;}
    .height2{height:6mm;}
    .height3{height:5mm;}
    .ffa{font-family:helvetica;}
    .blod{font-weight:bold;}
    .ffa2{font-family:stsongstdlight;}
    .ttt{height:1mm}
    .barcode{height:100%;}
    .barcodeImg{margin-left:10px;}
    .pcenter{text-align:center;font-size:12pt;}
    p.peihuo{width:80mm;white-space:normal;}
    .bigf{font-size:15mm;margin:3px;border:2px solid #000;height:15mm;width:15mm;}
    .pborder{margin:3px;border:2px solid #000;text-align:center;}
    .trancNoDiv{border-top:3px solid #000;border-bottom:3px solid #000;padding-top:3mm;}
    .bnum{font-size:30pt;margin-left:140px;margin-top:-80px;height:15mm;width:15mm;}
    .bdadd{font-size:9pt ;line-height:10pt;}
    .sku{white-space:normal;font-size:8pt;word-break:break-all;}
    .last{font-size:7pt;line-height:10px}
    .line{border-bottom:1px solid black;width:120px;}
    @font-face {
        font-family: 'IDAutomationC128S';
        src: url('font/IDAutomationC128S.eot'); /* IE9 Compat Modes */
        src: url('font/IDAutomationC128S.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
        url('font/IDAutomationC128S.woff') format('woff'), /* Modern Browsers */
        url('font/IDAutomationC128S.ttf')  format('truetype'), /* Safari, Android, iOS */
        url('font/IDAutomationC128S.svg#svgFontName') format('svg'); /* Legacy iOS */
    }

    .barCodeText {
        font-family: IDAutomationC128S;
        font-size:30px;
        text-align: center;
        padding-top:20px;
    }
    .transport {
        margin-top:5px;
    }
    .transport_name{
        /*font-weight: bold;*/
        font-size:20px;
        display: inline-block;
        /*text-align: center;*/
    }
    .footer {
        margin-top:25px;
        font-size:20px;
        font-weight:bold;
    }

    .pattern{
        font-size: 16px;
        margin-left: 30px;
        width: 50%;
        margin-top:10px;
    }
    .pattern_tow{
        font-size: 16px;
        margin-left: 30px;
        margin-top:10px;
        display: inline-block;
        width: 40%;
    }
</style>

<div style="width:100mm;min-height:100mm;max-height:100mm;border:1px dashed black;padding:1mm;font-size:12px; margin:1px;overflow:hidden;">

    <div style="font-family: '微软雅黑';font-size:26px;text-indent:30px;margin-top:10px;text-align: center;">
        <b>公司:SWTX</b>
    </div>

    <div class="transport" style="margin-left: 30px;text-align: center;">
        <?php if($ab != ''): ?>
        <div class="transport_name" style="width: 95%;font-size: 31px;">
            <font style="font-size: 16px">运输方式:</font>
            <?php echo $sup_code.'-'.$sorting_code;?>
            <span style="font-size: 20px;"><?php echo $ab != '' ? '' . $ab . '' : '';?></span>
        </div>
        <?php else: ?>
        <div class="transport_name" style="width: 95%;font-size: 32px;">
            <font style="font-size: 16px">运输方式:</font>
            <?php echo $sup_code.'-'.$sorting_code;?>
        </div>
        <?php endif; ?>
        <?php if($is_show_name == 1){ ?>
        <div style="font-size: 14px;">
            <?php echo $transport;?>
        </div>
        <?php } ?>
    </div>
    <div class="pattern">
        订单数: <?php echo $count;?> 单
    </div>
    <div class="pattern">
        包裹分区:
    </div>
    <div class="pattern_tow" style="width: 40%">
        称重人: <?php echo $scan_user;?>
    </div>
    <div class="pattern_tow" style="width: 40%">
        称重重量: <?php echo $weight;?> KG
    </div>
    <div class="pattern_tow">
        袋号: <?php echo $store_mb.$daihao;?>
    </div>
    <div class="pattern_tow">
        实际重量:
    </div>
    <div class="pattern" style="width: 60%">
        打印时间:  <?php date_default_timezone_set('PRC'); echo date('Y/m/d H:i:s'); ?>
    </div>
    <div class="barcode">
        <div class="barCodeText">
            <?php echo htmlspecialchars($barcode);?>
        </div>
        <div style="font-size: 20px;font-weight: bold;text-align: center">
            <?php echo $bagMark;?>
        </div>
    </div>
</div>







