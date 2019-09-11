<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2017/4/15
 * Time: 18:01
 */
include "../include/dbconf.php";
include "../include/dbmysqli.php";

$dbconLocal =new DBMysqli($LOCAL);

session_start();
error_reporting(0);
echo "<meta charset='utf-8'>";
$cpower = explode(',', $_SESSION['power']);
if (!in_array('cancel_order', $cpower)) {
    echo "<h1 style='color: red'> 您没有执行还该操作的权限. </h1>";
    exit(0);
}

//$apiHost = 'http://127.0.0.1:1024';
$apiHost = 'http://hkerp.wisstone.com';
$apiUri  = '/API/quickStopDelivery.php';

$orderIds = trim($_POST['orderIds'], ', ');
$orderTo  = trim($_POST['orderTo']);
$statusTo = trim($_POST['statusTo']);
$orderNote= trim($_POST['note']);
$user     = trim($_SESSION['truename']);

$orderIdArr = explode(',', $orderIds);
$stopAbleOrder = '';
foreach ($orderIdArr as $key => $val) {
    $checkWeightedSql = "SELECT id FROM ebay_weight WHERE ebay_id = '{$val}'";
    $isFind = $dbconLocal -> getResultArrayBySql($checkWeightedSql);
    if ($isFind) {
        echo "<h1>订单:{$val} 已经称重, 不能阻止发货了, 请重新勾选要终止发货的订单.</h1>";
        return null;
    }
}

if (!$orderIds) {
    echo '<h1>未检测到提交订单数据.</h1>';
    return null;
} elseif (!$orderTo) {
    echo "<h1>未检测到订单转入的状态.</h1>";
    return null;
} elseif (strlen($orderNote) < 5) {
    echo '<h1>订单备注消息长度不足最低长度 : 5字符.</h1>';
    return null;
} elseif ($orderTo != '1731' && !$statusTo) {
    echo '<h1>订单状态 未选择.</h1>';
    return null;
}

$fields = [
    'order_num_str' => $orderIds,
    'reason'        => $orderNote,
    'to_status'     => $orderTo,
    'status'        => $statusTo,
    'user'          => $user,
];

try {
    $requestHandler = curl_init($apiHost . $apiUri);
    $options = [
        CURLOPT_TIMEOUT => 120,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($fields),
    ];
    curl_setopt_array($requestHandler, $options);
    $results = curl_exec($requestHandler);
    curl_close($requestHandler);
} catch (Exception $e) {
    echo '<pre style="color: red;font-weight: bold;font-size: 18px;">[ 有异常情况产生 ] : '.$e -> getMessage().'</pre>';
    return null;
}
$resultArr = json_decode($results, true);

if (!$resultArr['status']) {
    echo "<h1 style='color: red;'>{$resultArr['data']}</h1>";
    return null;
}

foreach ($resultArr['data'] as $key => $data) {
    echo <<<HereDoc
<div style='border-bottom:2px solid #999;margin: 10px auto;width: 100%;border-radius:2px'>
    <div style='font-size: 16px;font-family: 微软雅黑;font-weight:bold;'> 订单编号 ：{$data['order']} </div>
    <div style='font-family: 微软雅黑;font-size: 15px;padding:10px 5px;background-color:#CCFFCC;border-radius:5px'> {$data['data']} </div>
</div>
HereDoc;
}