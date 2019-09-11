<?php
@session_start();
include "include/config.php";
include "include/dbconf.php";
include "include/dbmysqli.php";

error_reporting(0);
$dbconLocal =new DBMysqli($LOCAL);

$transport = $_REQUEST['transport'];
list($transport, $scan_user) = explode('>', $transport);
$packageUser = $_SESSION['truename'];
// 验证 指定运输方式下是否有未打包的订单
$verifySql = <<<TEXT
SELECT * FROM api_orderweight WHERE scan_user = '{$scan_user}' AND transport = '{$transport}' AND bag_mark=''
TEXT;

$isFindItems = $dbconLocal -> getResultArrayBySql($verifySql);

if (!$isFindItems) {
    echo json_encode(['status' => false, 'data' => '未找到运输方式为:<span style="color:#ff7420;">' .$transport.'</span> 需要打包的记录。']);
    return null;
}



$getLastBagMarkSql = <<<TEXT
SELECT mark_code FROM api_bags order by id desc limit 1
TEXT;
$result = $dbconLocal -> getResultArrayBySql($getLastBagMarkSql);

// 生成袋子的条码
$slice = substr($result[0]['mark_code'], 0, 6);
if ($result && strpos($result[0]['mark_code'], '-') !== false && $slice == date('ymd')) {
    $mark = explode('-', $result[0]['mark_code']);
    $count = ++$mark[1];
} else {
    $count = '1';
}
$bagMark = date('ymdH').'-'.$count;


// 检测袋子的重复性，同一个袋子号的不能重复
$checkRepeatSql = <<<TEXT
SELECT id FROM api_bags WHERE mark_code='{$bagMark}' limit 1
TEXT;
$isFind = $dbconLocal -> getResultArrayBySql($checkRepeatSql);
if ($isFind) {
    echo json_encode(['status' => false, 'data' => '相同的袋子已经存在，不能重复创建.']);
    return null;
}



// 保存创建的这个袋子条码值
$createAt = time();
$saveBagSql = <<<TEXT
INSERT INTO api_bags(mark_code, create_at, create_by) VALUE ('{$bagMark}','{$createAt}', '{$scan_user}')
TEXT;
//file_put_contents('aa.log', $saveBagSql."\n", FILE_APPEND);

$saveResult = $dbconLocal -> getResultArrayBySql($saveBagSql);
$lastId = mysqli_insert_id($dbconLocal->link);
//file_put_contents('aa.log', $lastId."\n\n", FILE_APPEND);

if (!$lastId) {
    echo json_encode(['status' => false, 'data' => '生成打包袋保存失败.']);
    return null;
}


// 更新扫描的订单的bag_mark 字段值为刚生成的 $bagMark
$selectScanOrderSql = <<<TEXT
SELECT id, weight FROM api_orderweight WHERE scan_user = '{$scan_user}' AND transport ='{$transport}' AND bag_mark=''
TEXT;
$updateAbleItems = $dbconLocal -> getResultArrayBySql($selectScanOrderSql);

if (!$updateAbleItems) {
    echo json_encode(['status' => false, 'data' => '没有查到需要打包的:'.$transport.' 记录']);
    return null;
}

$sql = "UPDATE api_orderweight SET bag_mark='{$bagMark}' WHERE scan_user='{$scan_user}' AND transport ='{$transport}' AND bag_mark=''";
$result = $dbconLocal -> execute($sql);

//file_put_contents('aa.log', $sql."\n", FILE_APPEND);
//file_put_contents('aa.log', print_r($result, true)."\n\n", FILE_APPEND);

if (!$result) {
    echo json_encode(['status' => false, 'data' => '数据更新失败.请重新操作']);
    return null;
}
$weightTotal = 0;
$counts = 0;
foreach ($updateAbleItems as $item) {
    $weightTotal += $item['weight'];
    $counts++;
}

echo json_encode(['status' => true, 'data' => '<div style="margin: 10px auto;text-align: center;font-size:15px">扫描订单打包成功.<br>共计 : '.$counts.'单<br>计算重量 : '.($weightTotal/1000.00).'Kg</div>', 'bagNumber' => $bagMark]);

