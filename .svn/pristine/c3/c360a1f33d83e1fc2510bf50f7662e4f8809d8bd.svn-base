<?php

include "./include/dbmysqli.php";
include "./include/dbconf.php";


$dbcon = new DBMysqli($ERPCONF);

$sql = <<<HereDoc
select b.code, b.name, d.username from sys_organization as a
inner join sys_organization as b on a.id = b.parent_id
inner join sys_organization_user as c on c.organize_id = b.id
inner join ebay_user as d on d.id = c.user_id
where a.name = '扫描验货员' and c.status = 1
HereDoc;

$result = $dbcon -> getResultArrayBySql($sql);
//echo "<pre>" . print_r($result, true) . "</pre>";

echo "<table border='1' cellpadding='5' cellspacing='0'>";

echo "<tr>
<th>用户名</th>
<th>岗位名称</th>
<th>编号</th>
<th>条码</th>
</tr>";

foreach ($result as $key => $val) {
    echo '<tr>';
    echo '<td>'.$val['username'].'</td>';
    echo '<td>'.$val['name'].'</td>';
    echo '<td>'.$val['code'].'</td>';
    echo '<td><img src="barcodeLib.php?data='.$val['code'].'" alt="' . $val['code'] . '" width="119" height="45"/></td>';
    echo '</tr>';
}
echo "</table>";




