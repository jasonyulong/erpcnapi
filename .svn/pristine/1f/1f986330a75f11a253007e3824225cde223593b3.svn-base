<?php
/**
 * Created by PhpStorm.
 * User: 测试人员成
 * Date: 2017/3/16 15:49
 * Time: 15:47
 */
include "include/config.php";
include "include/dbconf.php";
include "include/dbmysqli.php";
$dbconLocal =new DBMysqli($LOCAL);
error_reporting(0);

@session_start();
$type = $_REQUEST['type'];
$scan_user = $_REQUEST['scan_user'];
$scanedOrders = [];

if ($type === 'selectScanResult') {
    $transport = $_REQUEST['transport'];
    $selectScanSql = <<<TEXT
SELECT ebay_id, weight, scantime, scan_user, transport from api_orderweight where scan_user = '{$scan_user}'
and transport='{$transport}' AND bag_mark =''
TEXT;
    $scanedOrders = $dbconLocal -> getResultArrayBySql($selectScanSql);
}


/* 显示当前页面的方法 */
function getAllTransport() {
    global $dbconLocal;

//    $timeArea = strtotime('-3 days');  //
    $getTransportSql = <<<TEXT
select transport, count(id) as counts, scan_user from api_orderweight where bag_mark=''
group by scan_user, transport
TEXT;
//    $getTransportSql = <<<TEXT
//select transport, count(id) as counts from api_orderweight where  scan_user = '{$_SESSION['truename']}' and
//bag_mark='' group by transport
//TEXT;

    $transports = $dbconLocal -> getResultArrayBySql($getTransportSql);
    return $transports;
//    $formatTransport = [];
//    foreach ($transports as $val) {
//        $formatTransport[$val['counts']] = $val['transport'];
//    }
//    return  $formatTransport;
}

$formatTransport = getAllTransport();

include "top.php";
?>

<style type="text/css">

    #keys,#warehouse,#kfuser,#cguser,{width:110px!important;}
    div.c_list{width:200px;margin-top:1px;background:#fff;border:1px #444 solid;overflow-x:hidden;overflow-y:scroll;height:auto;padding:5px 0;max-height:200px; position:absolute;}
    div.c_list ul{padding: 0;margin: 0}
    div.c_list ul li{list-style: none;cursor:pointer;text-indent:5px;margin:0;}
    div.c_list ul li:hover{background:#79a7d8;}
    .tipspan{font-size: 12px;font-family: '微软雅黑'}
    .tipspan b{color:#990000}
    .s_high{color:#b00;font-weight: bold;}
    .s_mid{color:#C68433;font-weight: bold;}
    .s_mids{font-weight: bold;}
    .s_high,.s_mid,.s_mids {border-left:none;border-top:none;border-right:none}
    span.red{color:#911;}
    .selecthigh,.selecthigh li{width:32px;padding:0;margin:0; list-style:none;}
    .selecthigh li{background:#eee;cursor:pointer;}
    .selecthigh li:hover{background:#bbb;}
    ul.selecthigh{position:absolute;margin-left:3px;display:none;}
    #bodyMask{background:url("themes/Sugar5/images/maskbgs.png")!important;}
    #ModboxContent img{margin:60px auto;display:block;}
    #ModboxContent{overflow-y:scroll;}
    .Modboxtitlebox .btn_close {background:url(themes/Sugar5/images/icon.gif) 0 3102px  !important;cursor: pointer;display: block;float: right;height: 15px;margin-top: 6px;width: 15px;border:1px solid #fafafa}
    .Modboxtitlebox {background:#0e580c;color: #fff;font-size: 13px;height: 30px;line-height: 30px;padding: 0 8px;}
    .shuoming{font-size:13px;color: #911;padding: 10px;}


    .container {
        position: relative;
        margin:10px auto;
        width: 90%;
    }
    .transport_select {
        padding:3px 5px;
        border-radius: 1px;
        border:1px solid #777;
    }
    .submit_query {
        padding:3px 5px;
        border-radius: 2px;
        background-color: #33CCFF;
    }
    .listItems {
        margin-top:15px;
        width:90%;
        background-color: rgba(200,200,200, 0.1);
    }
    .listItems th, td {
        text-align: center;
        font-size:14px;
    }
    td{
        padding:5px 10px;
    }
    .bagCode {
        border:1px solid green;
        border-radius: 2px;
        padding:3px 5px;
    }

    .inBagLink {
        text-decoration: none;
        font-size:14px;
        /*font-weight: bold;*/
        cursor: pointer;
        background-color: #00FFCC;
        border-radius: 2px;
        padding:3px 10px;
        box-shadow: 0 0 2px #ccc;
    }
</style>



<div class="container">

    <div class="header">
        
    </div>

    <div class="selectBox" style="border-bottom: 1px solid #999;padding-bottom: 10px;">
        <form action="inBagPage.php?type=selectScanResult" method="post" style="display: inline-block">

            <select name="transport" class="transport_select" onchange="changeTransportHandler(this)">
                <option value=""> -- 请选择一种运输方式 -- </option>
                <?php foreach ($formatTransport as $key => $item) {?>
                    <?php if ($transport === $item['transport'] && $scan_user == $item['scan_user'] ) {
                        $str = 'selected';
                    } else {
                        $str='';
                    }?>
                    <option <?php echo $str;?> value="<?php echo $item['transport'].'>'.$item['scan_user'];?>"> <?php echo $item['transport']."({$item['scan_user']} : {$item['counts']})";?> </option>
                <?php } ?>
            </select>
<!--            <button type="submit" class="submit_query"> 提交查询 </button>-->
            <button type="button" id="inBagConfirm" class="submit_query" onclick="confirmInBag()"
                    style="background-color: #66CC00;display: inline-block;margin-left: 20px"> 确认装袋 </button>
            <a class="inBagLink" href="inBagList.php" target="_blank"> 装袋列表 </a>
        </form>

    </div>

    <div class="content">
        <?php
        if ($scanedOrders) {
            ?>
            <table class="listItems" cellpadding="0" cellspacing="0">
                <tr>
                    <th>订单编号</th>
                    <th>重量(g)</th>
                    <th>扫描时间</th>
                    <th>扫描员</th>
                    <th>运输方式</th>
                </tr>


                <?php foreach($scanedOrders as $item){?>
                    <tr>
                        <td><?php echo $item['ebay_id']; ?></td>
                        <td><?php echo $item['weight'];?></td>
                        <td><?php echo date('Y-m-d H:i', $item['scantime']);?></td>
                        <td><?php echo $item['scan_user'];?></td>
                        <td><?php echo $item['transport'];?></td>
                    </tr>
                <?php }?>

            </table>
            <?php
        }
        ?>
    </div>
</div>

<script type="application/javascript" src="js/jquery.js"></script>
<script type="application/javascript" src="js/mytips.js"></script>
<script type="text/javascript">
    function confirmInBag(){
        var lists = $('.listItems').length;
        if (lists == 0) {
            alert('请先查询出列表.');
            return null;
        }

        var transport = $('.transport_select').val();

        // 执行相关更新和添加之后展示但因页面的东西
//        location.href = "createPrintPage.php?transport="+transport;
//        window.open("inBagAction.php?transport="+transport, '_blank');

        funsTool.showModbox('装袋确认.', 200, 300, function() {});
        funsTool.insertModBox('<div style="text-align: center;margin: 15px auto;font-size: 15px; font-weight: bold; color: #ff8f1d;">是否确认去装袋？</div>\
        <div style="margin: 10px auto; text-align: center">\
        <button style="border: none; border-radius: 2px; background-color: #148dff;padding: 5px 10px;" onclick="doInBagAction('+"'"+transport+"'"+')"> 确认装袋 </button> </div>\
        ');

    }

    /**
     * 执行装袋的动作
     * @param transport
     */
    function doInBagAction(transport) {
        funsTool.deleteModBox();
        funsTool.showModbox('装袋确认.', 200, 300, function() {});
        var defer = $.ajax({
            url : 'inBagAction.php',
            type: 'post',
            data: {transport:transport},
            dataType : 'json'
        });

        defer.done(function(re) {
            if (re.status) {
                funsTool.insertModBox('<div style="color:green;font-size: 15px;font-weight:bold;margin: 20px auto;">'+re.data+'</div>\
                <div style="margin: 10px auto; text-align: center">\
                <button style="border: none; border-radius: 2px; background-color: #148dff;padding: 5px 10px;" onclick="toPrintPage('+"'"+re.bagNumber+"'"+')"> 去打印 </button> </div>\
                ');
            } else {
                funsTool.insertModBox('<div style="color:#ff6d52;font-size: 15px;font-weight:bold;margin: 20px auto;">'+re.data+'</div>');
            }
        });

        defer.fail(function() {
            alert('网络请求失败.');
        });
    }


    function toPrintPage(bagNumber) {
        window.open('inBagList.php?bag_number='+bagNumber, '_blank');
    }


    function changeTransportHandler(that) {
        var valueString = $(that).val();
        var splitResult = valueString.split('>');
        location.href = "inBagPage.php?scan_user="+splitResult[1]+'&transport='+splitResult[0]+'&type=selectScanResult';
    }


</script>
















