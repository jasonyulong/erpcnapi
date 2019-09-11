<?php
include "include/config.php";
include "top.php";
include "include/dbconf.php";
include "include/dbmysqli.php";
unset($dbcon);
$dbconLocal =new DBMysqli($LOCAL);


//======================================================
$start = trim($_REQUEST['start']);
$end = trim($_REQUEST['end']);
if($start==''){
    $start=date('Y-m-d');
}

if($end==''){
    $end=date('Y-m-d');
}
$sstart=strtotime($start.' 00:00:00');
$send=strtotime($end.' 23:59:59');

?>
<style>
    #yichang b{color:#a11;}
</style>
<div id="main">
    <div id="content" >
        <table style="width:100%"><tr><td><div class='moduleTitle'>
<h2><?php echo "订单扫描".$status;?> </h2>
</div>

<div class='listViewBody'>
<div id='Accountsadvanced_searchSearchForm' style='display:none' class="edit view search advanced"></div>
<div id='Accountssaved_viewsSearchForm' style='display: none';></div>
</form>
<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>
    <tr>
        <td>
            验证时间：
            <input type="text" id="start" value="<?php echo $start;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            <input type="text" id="end" value="<?php echo $end;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
            <input type="button" value="查找" onclick="searchorder()"/>
        </td>
    </tr>
</table>
<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>

    <?php

    $sql="select a.ebay_id from api_orderweight_1 a left join api_orderweight b using(ebay_id) ";
    $sql.="where a.scantime>=$sstart and a.scantime<=$send and b.ebay_id is null";

    if($send-$sstart>86400*31){
        echo '<div style="color:#911">所选时间太长!系统拒绝操作</div>';$sql='';
    }

    $sql=$dbconLocal->getResultArrayBySql($sql);

    foreach($sql as $list){
        $ebay_id=$list['ebay_id'];
        $ss="select ebay_tracknumber from erp_ebay_order where ebay_id='$ebay_id' limit 1";
        $ss=$dbconLocal->getResultArrayBySql($ss);
        $ebay_tracknumber=$ss[0]['ebay_tracknumber'];
    ?>
	<tr class='pagination'>
		<td width="65%"><?php echo $ebay_id;?></td>
		<td width="65%"><?php echo $ebay_tracknumber;?></td>
	</tr>
		
<?php }?>
              
		<tr class='pagination'>
		<td>
			<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
				<tr>
					<td nowrap="nowrap" class='paginationActionButtons'></td>
					</tr>
			</table>		</td>
	</tr></table>


    <div class="clear"></div>
<?php

include "bottom.php";


?>

<script language="javascript" src="My97DatePicker/WdatePicker.js"></script>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript">
    function searchorder(){
        var start=$("#start").val();
        var end=$("#end").val();
        var url="beweight.php?start="+start+"&end="+end;
        location.href=url;
    }
</script>