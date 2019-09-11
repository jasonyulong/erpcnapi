<?php
/**
 * Created by PhpStorm.
 * User: CX
 * desc:  已打包的 - 包的列表
 * Date: 2017/3/16
 * Time: 21:24
 */

include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/config.php";

$dbconLocal =new DBMysqli($LOCAL);
//$dbcon      =new DBMysqli($ERPCONF);
$wmsConfig = include("./newerp/Application/Common/Conf/wms_config.php");
$currStoreId = $wmsConfig['CURRENT_STORE_ID'];

/* 异步请求包中订单列表的部分 */
$type = trim($_REQUEST['type']);

if ($type == 'getBagOrders') {  //@TODO : 根据传递的打包编号查询包中的订单详情信息

    $bagCode = $_REQUEST['bagCode'];
    $getOrdersSql = <<<T_EXTENDS
SELECT * FROM api_orderweight where bag_mark = '{$bagCode}'
T_EXTENDS;

    $bagOrders = $dbconLocal -> getResultArrayBySql($getOrdersSql);
    $table = '<table class="list view"><tr> <th>订单编号</th> <th>重量（g）</th> <th>扫描时间</th> <th>扫描人</th> </tr>';

    foreach ($bagOrders as $order) {
        $table .= "<tr>";
        $table .= "<td> {$order['ebay_id']} </td>";
        $table .= "<td> {$order['weight']} </td>";
        $table .= "<td> ".date('Y-m-d H:i',$order['scantime'])." </td>";
        $table .= "<td> {$order['scan_user']} </td>";
        $table .= "</tr>";
    }
    echo $table.'</table>';
    return null;

} elseif ($type == 'setWeight') {  //@TODO : 更新打包的重量

    $bagCode = $_REQUEST['bag'];
    $weight  = $_REQUEST['weight'];

    /* 数据验证 */
    $isFindBagSql = <<<TEXT
SELECT id, create_by, weight FROM api_bags WHERE mark_code = '{$bagCode}'
TEXT;
    $isFind = $dbconLocal -> getResultArrayBySql($isFindBagSql);
    if (count($isFind) == 0) {
        echo json_encode(['status' => false, 'data' => '没有找到指定的包.']);
        return null;
    }

//2018年9月27日10:49:09 894 【章涛】包裹列表允许所有人修改实际重量
//    elseif ($_SESSION['truename'] != $isFind[0]['create_by']) {
//        echo json_encode(['status' => false, 'data' => '此包创建人不是当前操作用户不允许操作更新.']);
//        return null;
//    }

    /* 数据更新 */
    $updateWeightSql = <<<TEXT
UPDATE api_bags SET weight = '{$weight}' WHERE id = {$isFind[0]['id']}
TEXT;
    $updateResult = $dbconLocal -> execute($updateWeightSql);
    if (!$updateResult) {
        echo json_encode(['status' => false, 'data' => '重量更新失败.']);
        return null;
    }

    $addUser = $_SESSION['truename'];
    $logNote = "{$addUser} 修改了包裹重量为 {$weight}kg，修改前是 {$isFind[0]['weight']}kg。";
    $bagsLogsql = <<<TEXT
INSERT INTO bags_log (`bags_id`,`note`,`user`) VALUES ('{$bagCode}','{$logNote}','{$addUser}')
TEXT;
    $bagsLogResult = $dbconLocal -> execute($bagsLogsql);

    echo json_encode(['status' => true, 'data' => '重量数据保存成功!']);
    return ;


} else if ($type == 'markDelivery') {  //@TODO : 将包裹标记为已收包 的状态

    $markCode = $_REQUEST['markCode'];
    $isFindPackageSql = <<<HereDoc
SELECT id, delivery_status FROM api_bags WHERE mark_code = '{$markCode}' limit 1
HereDoc;
    $isFind = $dbconLocal -> getResultArrayBySql($isFindPackageSql);
    // 检测记录是否存在
    if (!$isFind) {
        echo json_encode(['status' => false, 'data' => '未找到指定的打包包裹.']);
        return null;
    }

    // 检测查到的记录的状态是否是未收包
    if ($isFind[0]['delivery_status'] == 1) {
        echo json_encode(['status' => false, 'data' => '指定的打包包裹已经标记为 已收包 状态; 无需重复标记']);
        return null;
    }
    $nowTime = time();
    $userName = $_SESSION['truename'];
    // 正式开始进行标记动作
    $markAsDeliverySql = <<<TEXT
UPDATE api_bags SET `delivery_status`=1,`delivery_time`='{$nowTime}',`delivery_user`='{$userName}' WHERE id = '{$isFind[0]['id']}'
TEXT;
    $markRe = $dbconLocal -> execute($markAsDeliverySql);

    if (!$markRe) {
        echo json_encode(['status' => false, 'data' => '标记收包操作失败.']);
        return null;
    }

    echo json_encode(['status' => true, 'data' => '标记收包成功.']);
    return true;

} elseif ($type == 'batchTransportMark') {  //@TODO : 包裹批量标记为已收包
    $bagMarks = $_REQUEST['bagMark'];
    if (!$bagMarks) {
        echo json_encode(['status' => false, 'data' => '未勾选包裹.']);
        return null;
    }
    $nowTime = time();
    $userName = $_SESSION['truename'];
    $inString = "('".join("','", $bagMarks)."')";
    $updateBagStatusSql = <<<TEXT
UPDATE api_bags SET `delivery_status`=1,`delivery_time`='{$nowTime}',`delivery_user`='{$userName}' WHERE mark_code IN {$inString}
TEXT;
    file_put_contents('aa.log', $updateBagStatusSql, FILE_APPEND);

    $updateResult = $dbconLocal -> execute($updateBagStatusSql);
    if ($updateResult) {
        echo json_encode(['status' => true, 'data' => '收包状态更新成功.']);
    } else {
        echo json_encode(['status' => false, 'data' => '收包状态更新失败,请重新操作.']);
    }

    return null;
}




/* 条件搜索部分的处理 */
$where  = '';
$bag    = trim($_REQUEST['bag_number']);
$ebayId = trim($_REQUEST['ebay_id']);
$start  = trim($_REQUEST['start']);
$end    = trim($_REQUEST['end']);
$delivery_start  = trim($_REQUEST['delivery_start']);
$delivery_end    = trim($_REQUEST['delivery_end']);
$createBy = trim($_REQUEST['create_by']);
$deliveryBy = trim($_REQUEST['delivery_user']);
$transport= $_REQUEST['transport'];
$deliveryValue = trim($_REQUEST['delivery_status']);
$deliveryStatus = ($deliveryValue == 0 || $deliveryValue == '') ? 0 : $deliveryValue;
//物流公司搜索 by Shawn 2018-08-24
$company_name = $_REQUEST['company_name'];


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

if ($deliveryBy) {
    $where .= ($where ? ' and ' : '') . "a.delivery_user='{$deliveryBy}'";
}

if ($delivery_start && $delivery_end) {
    $delivery_starts = strtotime($delivery_start.' 00:00:00');
    $delivery_ends   = strtotime($delivery_end.' 23:59:59');
    $where .= ($where ? ' and ' : '') . "a.delivery_time between {$delivery_starts} and {$delivery_ends}";
}

if (!empty($transport)) {
    //如果只有一个运输方式，不需要进行in查询
    if(count($transport) == 1){
        $transportStr = trim($transport[0]);
        $where .= ($where ? ' and ' : '') . "b.transport='{$transportStr}'";
    }else{
        for($i=0;$i<count($transport);$i++){
            $transportSql .= "'".trim($transport[$i])."'".",";
            $transportStr .= trim($transport[$i]).",";
        }
        $transportSql = trim($transportSql,',');
        $transportStr = trim($transportStr,',');
        $where .= ($where ? ' and ' : '') . "b.transport in ({$transportSql})";
    }
}
/**
 * 物流公司搜索
 * @author Shawn
 * @date 2018-08-24
 */
$is_empty = false;
if (!empty($company_name)) {
    //如果只有一个公司，不需要进行in查询
    if(count($company_name) == 1){
        $companyId = trim($company_name[0]);
        $getCompanyCarrierSql = <<<TEXT
SELECT name from ebay_carrier where companyname = {$companyId}
TEXT;
    }else{
        for($i=0;$i<count($company_name);$i++){
            $companySql .= "'".trim($company_name[$i])."'".",";
        }
        $companySql = trim($companySql,',');
        $getCompanyCarrierSql = <<<TEXT
SELECT name from ebay_carrier where companyname in ({$companySql})
TEXT;
    }
    //找到物流公司的物流渠道
    $companyCarrier = $dbconLocal -> getResultArrayBySql($getCompanyCarrierSql);
    //要判断下是否查询了物流方式与公司下的物流是否一致
    if(count($companyCarrier) == 1){
        $carrierNameStr = trim($companyCarrier[0]['name']);
        if(empty($transport)){
            $where .= ($where ? ' and ' : '') . "b.transport='{$carrierNameStr}'";
        }else{
            //如果物流渠道和物流公司搜索冲动就不查了
            if(!in_array($carrierNameStr,$transport)){
                $is_empty = true;
            }
        }
    }else{
        for($i=0;$i<count($companyCarrier);$i++){
            $carrierStr = trim($companyCarrier[$i]['name']);
            $carrierNameStr .= $carrierStr.",";
            $carrierNameSql .= "'".$carrierStr."'".",";
            if(!empty($transport)){
                if(!in_array($carrierStr,$transport)){
                    $is_empty = true;
                }
            }
        }
        $carrierNameStr = trim($carrierNameStr,',');
        if(empty($transport)){
            $carrierNameSql = trim($carrierNameSql,',');
            $where .= ($where ? ' and ' : '') . "b.transport in ({$carrierNameSql})";
        }
    }

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
SELECT a.mark_code, a.weight, a.create_at, a.create_by,a.delivery_time,a.delivery_user, b.transport , count(b.id) as counts, a.delivery_status,
sum(b.weight) as calc_weight FROM api_bags as a
inner join api_orderweight as b on a.mark_code = b.bag_mark {$where}
group by a.id order by a.id desc
TEXT;

if(!$is_empty){
    $resultSet = $dbconLocal -> getResultArrayBySql($getPackageListSql);
}else{
    $resultSet = [];
}

$getBagUsersSql = <<<T_EXTENDS
SELECT create_by from api_bags group by create_by
T_EXTENDS;
// 获取包裹创建者列表用于 select
$users = $dbconLocal -> getResultArrayBySql($getBagUsersSql);

$getDeliveryUsersSql = <<<T_EXTENDS
SELECT delivery_user from api_bags where delivery_user<>'' group by delivery_user
T_EXTENDS;
// 获取包裹收包者列表用于 select
$deliverUsers = $dbconLocal -> getResultArrayBySql($getDeliveryUsersSql);

//搜索运输方式需要用到分拣代码，直接等值查询出分拣代码字段
$getTransportsSql = <<<TEXT
SELECT
	api_orderweight.transport,
	ebay_carrier.sorting_code
FROM
	api_orderweight,ebay_carrier
WHERE
api_orderweight.transport = ebay_carrier.name
AND
	api_orderweight.bag_mark <> ''
AND api_orderweight.bag_mark IS NOT NULL
AND api_orderweight.transport <> ''
GROUP BY
	api_orderweight.transport,ebay_carrier.sorting_code;
TEXT;

/**
*测试人员谭 2018-03-17 14:56:36
*说明: 这里卡成了狗子
*/
//搜索运输方式需要用到分拣代码，直接等值查询出分拣代码字段
$getTransportsSql = <<<TEXT
SELECT DISTINCT NAME AS transport,sorting_code FROM ebay_carrier WHERE `ebay_warehouse` = '{$currStoreId}' AND `status` = '1';
TEXT;

$transports = $dbconLocal -> getResultArrayBySql($getTransportsSql);

/**
 * 获取所有开启的物流公司
 * @author Shawn
 * @date 2018-08-24
 */
$getCompanySql = <<<TEXT
SELECT
	b.id,
	b.sup_abbr,
	b.sup_code
FROM
	ebay_carrier a
INNER JOIN ebay_carrier_company AS b ON a.companyname = b.id
WHERE
	a.status = 1
AND a.ebay_warehouse = '{$currStoreId}'
GROUP BY
	b.sup_name
ORDER BY
	b.sup_code + 0 ASC,b.id asc
TEXT;

$companyData = $dbconLocal -> getResultArrayBySql($getCompanySql);


include "top.php";
$countWeight = 0.00;
$countOrder = 0;
$totalRealWeight = '0.00';
$totalCalWeight  = '0.00';

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
    .weightForm {
        padding:2px 5px;
        border:1px solid #ccc;
        font-family: 微软雅黑;
        font-size:15px;
    }
    .submitWeightSet {
        border-radius: 2px;
        border:none;
        background-color: #0Bc0FF;
        padding:3px 5px;
    }
    .form {
        display: block;
        margin:20px auto;
        text-align: center;
    }

    .listBtn {
        border:none;
        border-radius: 2px
    }

    .selectBtn {
        padding: 3px 10px;
        border-radius: 2px;
        border: none;
    }

    .selectBtn:hover {
        color: #000;
        box-shadow: 0 0 2px #999;
    }

    label {
        font-weight: bold;
    }
    .btn-addon {
        border:none;
        border-left: 1px solid #717171;

        margin-left: -4px;
        padding-bottom: 3px;
        padding-right: 6px;
        padding-left: 3px;

        border-bottom-right-radius: 2px;
        border-top-right-radius: 2px;
    }

    .print-able {
        display: none;
    }

    @media print {
        #Accountsbasic_searchSearchForm,.pagination, .no-print{
            display: none;
        }
        .print-able {
            display: block;
        }
    }

</style>
<script type="application/javascript" src="My97DatePicker/WdatePicker.js"></script>


<div id='Accountsbasic_searchSearchForm' style='' class="edit view search basic">
    <form action="" method="post" id="condition_select_form">
        <table width="97%" cellspacing="0" cellpadding="0" border="0" style="margin: 10px auto;">
            <tr>
                <td style="font-size: 15px;font-weight: bold;"> 搜索查询: </td>
            </tr>
            <tr>
                <td>
                    <label>包裹编号：</label>
                    <input type="text" name="bag_number" value="<?php echo $bag;?>" placeholder="打包编号搜索"/>

                    &nbsp;&nbsp;&nbsp;
                    <label>订单编号：</label>
                    <input type="text" name="ebay_id" value="<?php echo $ebayId;?>" placeholder="订单编号搜索"/>

                    &nbsp;&nbsp;&nbsp;
                    <label>创建人：</label>
                    <select name="create_by" style="padding: 2px 5px; border-radius: 1px">
                        <option value=""> - 选择创建人 - </option>
                        <?php foreach ($users as $user) {?>
                            <?php if ($createBy === $user['create_by']) {?>
                                <option value="<?php echo $user['create_by'];?>" selected> <?php echo $user['create_by'];?> </option>
                            <?php } else {?>
                                <option value="<?php echo $user['create_by'];?>"> <?php echo $user['create_by'];?> </option>
                            <?php }?>
                        <?php }?>
                    </select>

                    &nbsp;&nbsp;&nbsp;
                    <label>运输方式:</label>
                    <select name="transport[]" id="transport_id" multiple="multiple">
                        <option value=""> -- 选择运输方式 </option>
                        <?php foreach($transports as $item) { $item['sorting_code'] = empty($item['sorting_code']) ? '' :'('.$item['sorting_code'].')'; ?>
                            <?php if ($transport == $item['transport']) { ?>
                                <option value="<?php echo $item['transport'];?>" selected> <?php echo $item['transport'].$item['sorting_code'];?> </option>
                            <?php } else { ?>
                                <option value="<?php echo $item['transport'];?>"> <?php echo $item['transport'].$item['sorting_code'];?> </option>
                            <?php }?>
                        <?php }?>
                    </select>
                    <label>物流公司</label>
                    <select name="company_name[]" id="company_name" multiple="multiple">
                        <option value="">-- 选择物流公司 --</option>
                        <?php foreach($companyData as $cvalue) { $cvalue['sup_code'] = empty($cvalue['sup_code']) ? '' :'('.$cvalue['sup_code'].')'; ?>
                            <?php if (in_array($cvalue['id'],$company_name)) { ?>
                                <option value="<?php echo $cvalue['id'];?>" selected> <?php echo $cvalue['sup_abbr'].$cvalue['sup_code'];?> </option>
                            <?php } else { ?>
                                <option value="<?php echo $cvalue['id'];?>"> <?php echo $cvalue['sup_abbr'].$cvalue['sup_code'];?> </option>
                            <?php }?>
                        <?php }?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label>状态:</label>
                    <select name="delivery_status" style="padding: 3px 5px;">
                        <option value="0" <?php echo $deliveryValue == 0 ? 'selected' : ''; ?>> 未收包 </option>
                        <option value="1" <?php echo $deliveryValue == 1 ? 'selected' : ''; ?>> 已收包 </option>
                        <option value="2" <?php echo $deliveryValue == 2 ? 'selected' : ''; ?>>  所有 </option>
                    </select>

                    &nbsp;&nbsp;&nbsp;
                    <label>添加时间:</label>
                    <input name="start" type="text" id="start" style="width:80px !important" onClick="WdatePicker()" value="<?php echo $start; ?>"/>
                    ~
                    <input name="end" type="text" style="width:80px !important" id="end" onClick="WdatePicker()" value="<?php echo $end; ?>"/>

                    &nbsp;&nbsp;&nbsp;
                    <label>收包时间:</label>
                    <input name="delivery_start" type="text" id="delivery_start" style="width:80px !important" onClick="WdatePicker()" value="<?php echo $delivery_start; ?>"/>
                    ~
                    <input name="delivery_end" type="text" style="width:80px !important" id="delivery_end" onClick="WdatePicker()" value="<?php echo $delivery_end; ?>"/>

                    &nbsp;&nbsp;&nbsp;
                    <label>收包操作员：</label>
                    <select name="delivery_user" style="padding: 2px 5px; border-radius: 1px">
                        <option value=""> - 选择操作人 - </option>
                        <?php foreach ($deliverUsers as $deliveruser) {?>
                            <?php if ($deliveryBy === $deliveruser['delivery_user']) {?>
                                <option value="<?php echo $deliveruser['delivery_user'];?>" selected> <?php echo $deliveruser['delivery_user'];?> </option>
                            <?php } else {?>
                                <option value="<?php echo $deliveruser['delivery_user'];?>"> <?php echo $deliveruser['delivery_user'];?> </option>
                            <?php }?>
                        <?php }?>
                    </select>
                    <button type="submit" class="selectBtn" style="background-color: #13d241"> 查询 </button>
                    <button type="button" class="selectBtn" style="background-color: #22d2c3" onclick="exportExcel()">
                        装袋导出<span style="color: #911;">(所有)</span>
                    </button>
                    <button type="button" class="selectBtn" style="background-color: #999fff" onclick="toExcel()">
                        装袋导出<span style="color: #911;">(选中项)</span>
                    </button>
                    <?php if($deliveryValue == 0):?>
                    <button type="button" class="selectBtn batch-transport-mark" style="background-color: #88e1a1">
                        批量收包选中
                    </button>
                    <?php endif;?>
                    <button type="button" class="selectBtn print_page_btn" style="background-color: #3ea8ff"> 打印列表 </button>
                </td>
            </tr>

        </table>
    </form>
</div>



<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view' style="margin: 10px auto;margin-bottom: 10px">
    <tr class='pagination'>
        <?php foreach ($resultSet as $item) {?>
            <?php $totalRealWeight += $item['weight']; $totalCalWeight += $item['calc_weight']/1000; }?>
        <td>共计: <?php echo count($resultSet);?>&nbsp;条</td>
        <td>实际重量汇总: <?php echo $totalRealWeight;?>kg</td>
        <td>计算重量汇总: <?php echo $totalCalWeight;?>kg</td>
        <td>误差(<span style="color: red;">实际重量汇总-计算重量汇总</span>): <?php echo $totalRealWeight-$totalCalWeight;?>kg</td>
    </tr>
    <tr height='20'>

        <th style="text-align: center" class="no-print">
            操作&nbsp;&nbsp; / &nbsp;&nbsp;
            <label>收包全选/反选 : <input type="checkbox" class="checkAll"/></label>
        </th>

        <th style="text-align: center">包裹编号</th>
        <th style="text-align: center">实际重量(KG)</th>
        <th style="text-align: center">计算重量(KG)</th>
        <th style="text-align: center" class="no-print">单数</th>
        <th style="text-align: center">创建时间</th>
        <th style="text-align: center">创建人</th>
        <th style="text-align: center">收包时间</th>
        <th style="text-align: center">收包操作员</th>
        <th style="text-align: center">运输方式</th>
    </tr>

    <?php foreach ($resultSet as $item) {?>
    <tr class='oddListRowS1'>

        <td scope='row' align='left' valign="top" style="text-align: center" class="no-print">
            <button style="background-color: #3ea8ff;" data-bag="<?php echo $item['mark_code'];?>" class="listBtn showOrderListInBag"> 包含订单 </button>
            <button onclick="rePrint('<?php echo $item['mark_code'];?>','<?php echo $item['weight'];?>', '<?php echo $item['counts'];?>','<?php echo $item['transport'];?>'
                )" class="listBtn" style="background-color: #ff9245;"> 打 印 </button>

            <?php if ($item['delivery_status'] == 0) {?>
                <button style="background-color: #22d2c3" class="listBtn" onclick="deliveryTransport('<?php echo $item['mark_code'];?>')">待收包</button>
                <label style="background-color: #22d2c3" class="btn-addon"><input type="checkbox" name="checkedItem" value="<?php echo $item['mark_code'];?>"/></label>
            <?php } else {?>
                <button style="background-color: #d0d0d0; cursor: not-allowed" class="listBtn" disabled> 已收包 </button>
                <label style="background-color: #22d2c3" class="btn-addon">
                    <input type="checkbox" name="checkedItem" value="<?php echo $item['mark_code'];?>"/>
                </label>
            <?php }?>
        </td>

        <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo $item['mark_code'];?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center">
            <a href="javascript:void(0)" class="write_weight" data-bag="<?php echo $item['mark_code'];?>">
                <?php
                if ($item['weight']) echo $item['weight'];
                else echo '0.00';
                ?>
            </a>
        </td>

        <td style="text-align: center"> <?php echo $item['calc_weight']/1000;?> </td>

        <td scope='row' align='left' valign="top" style="text-align: center" class="no-print"> <?php echo $item['counts'];?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo date('Y-m-d H:i',$item['create_at']);?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo $item['create_by'];?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center">  <?php if($item['delivery_time']){ echo date('Y-m-d H:i',$item['delivery_time']);}?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center">  <?php echo $item['delivery_user'];?> </td>
        <td scope='row' align='left' valign="top" style="text-align: center"> <?php echo $item['transport'];?> </td>
    </tr>
        <?php $countOrder += $item['counts']; $countWeight += $item['weight']; ?>
    <?php }?>

    <tr class="no-print">
        <td style="text-align: center"> 总计：<?php echo count($resultSet);?> 条 </td>
        <td style="text-align: center"> 重量合计：<?php echo $countWeight; ?> Kg </td>
        <td style="text-align: center"> 单数合计：<?php echo $countOrder;?> 单 </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
    </tr>
</table>

<div class="print-able" style="margin-top: 20px;margin-left: 30px;">
    <span style="font-weight: bold;font-size: 18px">发运人：_________________</span><br>
    <span style="font-weight: bold;font-size: 18px;line-height: 2.2em"> 发运时间: _________________ </span>
</div>


<script type="application/javascript" src="js/jquery.js"></script>
<script type="application/javascript" src="js/mytips.js"></script>
<link rel="stylesheet" href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.min.css">
<link rel="stylesheet" href="/newerp/Public/layer/skin/layer.css">
<script type="application/javascript" src="/newerp/Public/js/plugins/chosen/chosen.jquery.js"></script>
<script type="application/javascript" src="/newerp/Public/layer/layer.js"></script>

<script type="application/javascript">
    // 多选 select 数据初始化
    function chose_mult_set_ini(select, values) {
        var arr = values.split(',');
        var length = arr.length;
        var value = '';
        for (i = 0; i < length; i++) {
            value = arr[i];
            $(select + " option[value='" + value + "']").attr('selected', 'selected');
        }
        // $(select).trigger("liszt:updated");
    }
    $(function(){
        <?php if(!empty($transport)){ ?>
            var transport = '<?php echo $transportStr; ?>';
            chose_mult_set_ini('#transport_id',transport);
        <?php } ?>
        $("#transport_id").chosen({"height":"26px","width":"14%" });
        $('#company_name').chosen ({search_contains: true, width: '200px', allow_single_deselect: true});

    });

    /**
     * 展示出包中的订单列表
     */
    $('.showOrderListInBag').click(function() {
        var bagCode = $(this).attr('data-bag');
        funsTool.showModbox('包：'+bagCode+' 中订单列表', 400, 600, function() {});
        var defer = $.ajax({
            url : 'inBagList.php',
            type: 'post',
            data: {type : 'getBagOrders', bagCode : bagCode},
            dataType : 'text'
        });

        defer.done(function(re) {
            funsTool.insertModBox(re, 15);
        });
    });


    /**
     * 收包全选的点击事件
     */
    $('.checkAll').click(function() {
        $('input[name=checkedItem]').click();
    });


    /**
     * 批量标记收包的按钮动作
     */
    $('.batch-transport-mark').click (function() {
        var checkedItems = $('input[name=checkedItem]:checked');
        var bagCodes = [];

        $.each(checkedItems, function(key, val) {
            bagCodes[key] = $(val).val();
        });

        if (bagCodes.length == 0) {
            alert('请先勾选要提交的记录.');
            return null;
        }

        var data = {};
        data.type = 'batchTransportMark';
        data.bagMark = bagCodes;

        var defer = $.ajax({
            url  : 'inBagList.php',
            type : 'post',
            data : data,
            dataType : 'json'
        });

        defer.done(function(re) {
            if (re.status) {
                alert(re.data);
                setTimeout(function () {
                    history.go(0);
                }, 1200);
            } else {
                alert(re.data);
            }
        });

        defer.fail(function() {
            alert('网络访问失败.');
        });

    });




    /**
     * 填写包重量的点击事件
     */
    $('.write_weight').click(function(event) {
        var bag = $(this).attr('data-bag');
        funsTool.showModbox('包 ：'+bag+' 重量更新',150, 350, function() {});

        var form = '<div class="form">\
        <input type="text" name="weight" class="weightForm" placeholder="请填写称重重量（kg）"/> \
        <button class="submitWeightSet" onclick="saveBagWeight('+"'"+bag+"'"+')"> 保存重量 </button>\
            </div>';

        funsTool.insertModBox(form, 30);
    });

//
    $('.print_page_btn').click(function() {
//        window.print();    // TODO : 要求按照运输方式排序，直接在当前页面通过JS打印无法实现，废弃，修改为使用新的页面打印的情况

        var formObj= $('#condition_select_form');

        var bag_number = formObj.find('input[name="bag_number"]').val();
        var ebay_id    = formObj.find('input[name="ebay_id"]').val();
        var create_by  = formObj.find('select[name="create_by"]').val();
        var transport  = formObj.find('select[name="transport"]').val();
        var delivery_status = formObj.find('select[name="delivery_status"]').val();
        var startTime  = formObj.find('input[name="start"]').val();
        var endTime    = formObj.find('input[name="end"]').val();
//        console.log([bag_number, ebay_id,create_by, transport, delivery_status, startTime, endTime]);

        var params = 'bag_number='+bag_number+'&ebay_id='+ebay_id+'&create_by='+create_by+'&transport='+transport
            +'&delivery_status='+delivery_status+'&startTime='+startTime+'&endTime='+endTime;

        window.open('inBagPrintList.php?'+params, '_blank');

    });



    function saveBagWeight(bagCode) {
        var weightValue = $.trim($('.weightForm').val());

        if (!weightValue) {
            alert( bagCode+'重量值未填写.');
            return null;
        }

        var url = 'inBagList.php';

        var defer = $.ajax({
            url : url,
            type: 'post',
            data: {weight:weightValue, bag:bagCode, type : 'setWeight'},
            dataType : 'json'
        });

        defer.done(function(re) {
            if (re.status) {
                funsTool.deleteModBox();
                setTimeout(function() {
                    history.go(0);
                }, 500);
            } else {
                alert(re.data);
            }
        }).fail(function() {
            alert('访问出错.');
        });
    }


    /**
     * 点击重新打印按钮
     */
    function rePrint(markCode, weight, counts, transport) {
        window.open("inBagPrint.php?bagMark="+markCode+'&counts=' + counts+'&weight='+weight+'&transport='+transport, '_blank');
    }


    /**
     * 点击导出按钮的事件处理函数
     */
    function exportExcel() {
        var conditions = {};

        conditions.bagCode = $('input[name=bag_number]').val();
        conditions.createBy = $('select[name=create_by]').val();
        conditions.transport = $('select[name=transport]').val();
        conditions.addTimeStart = $('input[name=start]').val();
        conditions.addTimeEnd = $('input[name=end]').val();
        conditions.deliveryStatus = $('select[name=delivery_status]').val();

        var getQueryString =  "bagCode=" + conditions.bagCode + "&createBy=" + conditions.createBy + "&transport=" + conditions.transport
        + "&addTimeStart=" + conditions.addTimeStart + "&addTimeEnd=" + conditions.addTimeEnd + "&deliveryStatus=" + conditions.deliveryStatus;

        var href = "inBagExport.php?" + getQueryString;
        window.open(href, '_blank');
    }

    /**
     * ran 2017-10-21 添加选择导出
     */
    function toExcel(){
        var checkedItems = $('input[name=checkedItem]:checked');
        var bagCodes = [];

        $.each(checkedItems, function(key, val) {
            bagCodes[key] = $(val).val();
        });

        if (bagCodes.length == 0) {
            alert('请先勾选要导出的记录.');
            return null;
        }

        var data = {};
        data.transport = $('select[name=transport]').val();
        data.type = 'toExcel';
        data.bagMark = bagCodes;

        var getQueryString =  "bagMark=" + data.bagMark + "&transport=" + data.transport + "&type=" + data.type;
        var href = "inBagExport.php?" + getQueryString;
        window.open(href, '_blank');
    }


    /**
     * 交付物流
     * @param markCode {string} : 打包的包裹编号
     */
    function deliveryTransport(markCode) {
        var confirmRe = confirm('确认标记为已收包 ？');
        if (!confirmRe) {
            return false;
        }

        var defer = $.ajax({
            url : 'inBagList.php',
            type: 'post',
            data: {markCode:markCode, type: 'markDelivery'},
            dataType: 'json'
        });

        defer.done(function(re) {
            if (re.status) {
                alert(re.data);
                history.go(0);
            } else {
                alert(re.data);
            }
        });

        defer.fail(function() {
            alert('网络访问失败.');
        });

    }


</script>








