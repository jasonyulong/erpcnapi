<?php

include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/config.php";

$dbconLocal =new DBMysqli($LOCAL);



/* 条件搜索部分的处理 */
$where  = '';
$bag    = trim($_REQUEST['bag_number']);
$ebayId = trim($_REQUEST['ebay_id']);
$start  = trim($_REQUEST['startTime']);
$end    = trim($_REQUEST['endTime']);
$createBy = trim($_REQUEST['create_by']);
$transport= trim($_REQUEST['transport']);

$deliveryValue = trim($_REQUEST['delivery_status']);
$deliveryStatus = ($deliveryValue == 0 || $deliveryValue == '') ? 0 : $deliveryValue;



$start = $start ? $start.' 00:00:00' : date('Y-m-d H:i:s', strtotime('-7 days'));
$end   = $end   ? $end.' 23:59:59' : date('Y-m-d H:i:s');

if ($bag) {
    $where = "a.mark_code = '{$bag}'";
}
if ($ebayId) {
    $where .= ($where ? ' and ' : '')."b.ebay_id = {$ebayId}";
}

if ($createBy) {
    $where .= ($where ? ' and ' : '') . "a.create_by='{$createBy}'";
}

if ($transport) {
    $where .= ($where ? ' and ' : '') . "b.transport='{$transport}'";
}

if ($deliveryStatus == 0) {
    $where .= ($where ? ' and ' : '') . "a.delivery_status=0";
} elseif ($deliveryStatus == 1) {
    $where .= ($where ? ' and ' : '') . "a.delivery_status=1";
}

$start = strtotime($start);
$end   = strtotime($end);
if ($where) {
    $where= "where {$where} and a.create_at between {$start} and {$end}";
} else {
    $where = "where a.create_at between {$start} and {$end}";
}
$start = date('Y-m-d', $start);
$end   = date('Y-m-d', $end);

$getPackageListSql = <<<TEXT
SELECT a.mark_code, a.weight, a.create_at, a.create_by, b.transport , count(b.id) as counts, a.delivery_status,
sum(b.weight) as calc_weight FROM api_bags as a
inner join api_orderweight as b on a.mark_code = b.bag_mark {$where}
group by a.id order by b.transport asc
TEXT;
$resultSet = $dbconLocal -> getResultArrayBySql($getPackageListSql);


?>
<style type="text/css">
    body {
        width: 98%;
        margin:10px auto;
        background-color: #ccc;
    }
    div.c_list ul{padding: 0;margin: 0}
    div.c_list ul li{list-style: none;cursor:pointer;text-indent:5px;margin:0;}
    div.c_list ul li:hover{background:#79a7d8;}
    .tipspan b{color:#990000}
    .selecthigh li{background:#eee;cursor:pointer;}
    .selecthigh li:hover{background:#bbb;}
    #ModboxContent img{margin:60px auto;display:block;}
    table th,td {
        padding:5px 10px;
        border-right: 1px solid #aaa;
        border-bottom: 1px solid #aaa;
    }
    label {
        font-weight: bold;
    }
    .print_table {
        margin: 10px auto;
        border-top: 1px solid #aaa;
        border-left: 1px solid #aaa;
        /*padding:10px 5px;*/
    }

    .selectBtn {
        padding: 3px 10px;
        border-radius: 2px;
        border: none;
        margin-left:20px;
        font-size: 14px;
    }

    .selectBtn:hover {
        color: #000;
        box-shadow: 0 0 2px #999;
    }

    @media print {
        .no-print {
            display: none;
        }
    }

</style>

<meta charset="UTF-8">

<div class="print_box" style="background-color: white;padding-top: 10px;">

    <div class="no-print">
        <button class="selectBtn" id="print_btn" style="background-color: #24c037"> 打印 </button>
    </div>

    <table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view print_table'>
        <tr height='20'>
            <th style="text-align: center">打包编号</th>
            <th style="text-align: center">实际重量(KG)</th>
            <th style="text-align: center">计算重量(KG)</th>
            <!--        <th style="text-align: center" class="no-print">单数</th>-->
            <th style="text-align: center">创建时间</th>
            <th style="text-align: center">创建人</th>
            <th style="text-align: center">运输方式</th>
        </tr>

        <?php foreach ($resultSet as $item) { ?>
            <tr class='oddListRowS1'>
                <td scope='row' align='left' valign="top"
                    style="text-align: center"> <?php echo $item['mark_code']; ?> </td>
                <td scope='row' align='left' valign="top" style="text-align: center">
                    <a href="javascript:void(0)" class="write_weight" data-bag="<?php echo $item['mark_code']; ?>">
                        <?php
                        if ($item['weight']) echo $item['weight'];
                        else echo '0.00';
                        ?>
                    </a>
                </td>

                <td style="text-align: center"> <?php echo $item['calc_weight'] / 1000; ?> </td>

                <!--            <td scope='row' align='left' valign="top" style="text-align: center" class="no-print"> -->
                <?php //echo $item['counts'];?><!-- </td>-->
                <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo date('Y-m-d H:i', $item['create_at']); ?> </td>
                <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo $item['create_by']; ?> </td>
                <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo $item['transport']; ?> </td>
            </tr>
        <?php } ?>
    </table>

    <div style="margin-top: 20px;margin-left: 30px; background-color: white">
        <span style="font-weight: bold;font-size: 18px">发运人：_________________</span><br>
        <span style="font-weight: bold;font-size: 18px;line-height: 2.2em"> 发运时间: _________________ </span>
    </div>
</div>

<script type="application/javascript">
    document.getElementById('print_btn').addEventListener('click', function(){
        window.print();
    });
</script>







