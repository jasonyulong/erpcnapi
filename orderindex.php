<?php
/*
** 11月27日 
	1.添加包货地址打印格式
	2.修改总价格计算
**** 修改人  胡耀龙
*/
include "include/config.php";
include "top.php";
// hank 2018/1/12 15:12 加个页码
if(isset($_GET['pagesize'])){$pagesize=trim($_GET['pagesize']);}
//========================================
function getStoreIndexID(){
    global $dbcon;
    $sql="select id,store_name,store_name_en,store_sn from ebay_store limit 200";
    $rs=$dbcon->getResultArrayBySql($sql);
    $arr=array();
    foreach($rs as $v){
        $arr[$v['id']]=array('store_name'=>$v['store_name'],'store_name_en'=>$v['store_name_en'],'store_sn'=>$v['store_sn']);
    }
    return $arr;

}
include 'include/orderindexfile.php'

?>

<style type="text/css">
td em.hacks{color:#f00;font-style:normal;}
.STYLE1 {font-size: xx-small}
em.location,b.location{color:#a00;font-style:normal;font-size:10px!important;}
#filesid,#printid{width:100px!important;}
#orderoperation,#orderoperationShip{width:130px!important;}
/*.Modboxtitle{*/
    /*display: block;*/
    /*padding:8px 10px;*/
    /*background-color: #00aa00;*/
    /*margin:5px auto;*/
    /*text-align: center;*/
/*}*/
.dataTable{
    width:100%;
    display: block;
    margin:10px auto;
    margin-left:10px;
}
.dataTable td{
    padding:10px;

}

.__close{
    float:right;
    background-color: #747474;
    padding:0 2px;
    cursor: pointer;
}

#bodyMask{background:url("themes/Sugar5/images/maskbgs.png")!important;}
#ModboxContent img{margin:60px auto;display:block;}
#ModboxContent{overflow-y:scroll;}
.Modboxtitlebox .btn_close {background:url(themes/Sugar5/images/icon.gif) 0 3102px  !important;cursor: pointer;display: block;float: right;height: 15px;margin-top: 6px;width: 15px;border:1px solid #fafafa}
.Modboxtitlebox {background:#0e580c;color: #fff;font-size: 13px;height: 30px;line-height: 30px;padding: 0 8px;}


</style>

 <script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
 <script src="/newerp/Public/js/qrcode.js"></script>


<div id="main">

    <div id="content" >

        <table style="width:100%"><tr><td><div class='moduleTitle'>

<h2><?php echo $navAction.$status;?>&nbsp;&nbsp;&nbsp;&nbsp;</h2>
</div>
<div class='listViewBody'>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td >操作：
	  <input name="showAllcount" type="button" value="显示数量" onclick="showAllStatusCounts()" />
      <select name="filesid" id="filesid" onchange="exportstofiles()" >
              <option value="" id="filesidselect">导出到XLS</option>
               <!--<option value="12">热敏EUB-xls</option>-->
                   <option value="1">WT-燕文俄罗斯格式</option>
                    <option value="01">WT-夏普香港平挂</option>
                    <option value="001">WT-夏普小包交接</option>
                 <!--  <option value="10">发货清单-2-xls</option>
				   <option value="100">发货清单-3-xls</option>
                   <option value="105">发货清单-4-xls</option>
				   <option value="1051">发货清单-5-xls</option>
				   <option value="01251">发货清单-6-xls</option>
				    <option value="102">发货信息-4PX-xls</option>
					
				   <option value="101">一页24个地址pdf导出</option>
                   <option value="11">发货地址-xls</option>
                   <option value="5">拣货单_01-xls</option>
                    <option value="104">拣货单_02-xls</option>
                     <option value="107">拣货单_03_无库存-xls</option>
					  <option value="01252">拣货单_04-xls</option>
                   <option value="2">递四方-xls</option>
                   <option value="103">出口易_01-xls</option>
				   <option value="92101">出口易_UK-xls</option>
				   <option value="92102">出口易_M2C版-xls</option>
				   <option value="92103">出口易_专线-xls</option>
                   <option value="3">三态导出-xls</option>
                   <option value="4">eBay-csv</option>
				   <option value="0131">eBay-csv(修)</option>
				    <option value="44">eBay-csv（THG）</option>
				   <option value="116">速达xls</option>
                   <option value="6">常规列表导出xls</option>
                   <option value="7">本地发货记录导出xls</option>
                   <option value="8">美国发货记录导出xls</option>
                   <option value="9">欧洲发货记录导出xls</option>
				   <option value="15">地址+拣货清单</option>
                   <option value="106">地址+图片+拣货清单</option>
                   <option value="113">地址+图片+拣货清单2</option>
				   <option value="1131">地址+图片+拣货清单3</option>
				   <option value="108">客户预留单格式</option>
				   <option value="110">导出需要格式</option>
				   <option value="111">fedex导出</option>
				   
                   <option value="112">导出无库存商品</option>
				   <option value="c1">仓库1</option>
				   <option value="c2">仓库2</option>
				   <option value="c3">仓库3</option>
                   <option value="c4">发货清单_UK</option>-->
                    <!--<option value="999">SalesHistory_01</option>-->
                    <!--<option value="20">SalesHistory_02</option>
					 <option value="112601">数据导出格式</option>
					 <option value="112701">包货地址打印格式</option>
					 <option value="0108">小狼导出格式</option>
                     
                     <option value="0109">A_订单导出格式</option>
                     <option value="0110">B_订单导出格式</option>
                     <option value="0111">速卖通格式</option>
					 <option value="0117">亚马逊格式导出</option>
					 <option value="0122">西班牙邮政</option>
					 <option value="130226">西班牙邮政挂号小包</option>
					 <option value="1302261">西班牙邮政2(送货上门)</option>
					 <option value="1302263">西班牙邮政1(邮政取货)</option>
					 <option value="1302264">西班牙24H货代CSV</option>
					 <option value="1302265">西班牙48-72H货代CSV</option>
					 <option value="1302262">香港MAX600</option>
                     <option value="1302263">HK_PaypalDetails</option>
                     <option value="1302266">夏普_发货单导出</option>
					 <option value="1329">EU Letter_2 - doc</option>
                     <option value="130407">order UK-xls</option>
					 <option value="1304071">order HK-xls</option>
                     <option value="1304072">KU_system-xls</option>-->
					 <option value="130413">WT-快递单格式导出</option>
                      <!--<option value="1304133">夏普格式导出</option>-->
                      <option value="1304233">缺货明细--导出</option>
                      <option value="ku">酷鸟系统导出</option>
                      <option value="fourdays">4天未发货订单</option>
                      <option value="ordertoexcel">导出订单</option>
                  <?php 	if(in_array("ordershipfee",$cpower)){?>
                      <option value="getordercost">海外仓运费</option>
                  <?php } ?>
          </select>
          <select name="printid" id="printid" onchange="printtofiles()">
                   <option value="" id="printidselect">打印订单</option>
                       <option value="FedExInvoice" >FedEx-Invoice</option>
                       <option value="picksku" >--打印捡货单--</option>
                       <option value="mixprint" >--混合打印--</option>
                       <option value="183" >WT-标签打印50*100-new</option>
                       	<option value="181" >WT-夏普格式02-标签（挂号）</option>
			            <option value="1811" >WT-夏普格式02-标签（平邮）</option>
                        <option value="1812" >WT-通用标签-地址设定</option>
                        <option value="16" >Packing Slip打印</option>
                        <option value="248" >Invoice-打印</option>
                        <option value="201" >Invoice-法仓-打印</option>
                        <option value="18" >夏普格式01</option>
                       <option value="15" >A4包货单01</option>
                       <option value="14" >A4包货单02</option>
                       <option value="151" >A4包货单03</option>
                       <option value="141" >A4包货单04</option>
                       <option value="13" >标签打印40*60</option>
                       <option value="1" >标签打印50*100</option>
                       <option value="1833" >标签打印50*100-new2</option>
                       <option value="12" >A4快递打印</option>
                       <option value="5" >标签打印-100*100</option>
                       <option value="2" >国际EUB-A4打印</option>
                       <option value="3" >国际EUB-热敏打印</option>
                        <option value="17" >国际EUB-AU热敏打印</option>
                       <option value="6" >国际EUB发货清单-A4打印</option>
                       <option value="305" >地址+物品+图片每页1个-A4打印</option>
                       <option value="4" >地址+条码+物品每页5个-A4打印</option>
                       <option value="241">地址+物品+图片5个-A4打印</option>
                       <option value="40">地址+条码+物品5个-A4打印(修)</option>
                       <option value="41">地址+条码+物品5个-A4打印(修2)</option>
                       <option value="182">地址+条码+物品5个-A4打印(修3)</option>
                       <option value="7" >地址10-A4打印</option>
                       <option value="11" >地址10+条码+sku-A4打印</option>
                       <option value="42" >地址10+条码+sku-A4打印(修改)</option>
                       <option value="9" >地址8+条码+sku-A4打印</option>
                       <option value="10" >拣货清单+条码-A4打印</option>
                       <option value="245" >拣货清单+条码-A4打印2</option>
                       <option value="20">a4/8</option>
                       <option value="19">label 100X100</option>
                       <option value="21">label 80X60</option>
                       <option value="22">小包 80X60</option>
                       <option value="222">小包 100X100</option>
                       <option value="23">挂号 80X60</option>
                       <option value="244">小包 70X60</option>
                        <option value="24">燕文a4</option>
                       <option value="25">燕文a5</option>
                       <option value="26">燕文a6</option>
                       <option value="27">本地a6打印</option>
                       <option value="246" >THGlable102mm*70mm打印</option>
                       <option value="247">地址打印</option>
                        <option value="121001">三态A4地址打印</option>
                        <option value="0116">亚马逊_A4打印</option>
                        <option value="0314">label 100*100(2)</option>
                        <option value="03141">label 80</option>
                        <option value="13331">TNT</option>
                        <option value="13332">EMS</option>
                        <option value="130413">系统sample</option>
                        <option value="130414">TNT-Invoice</option>
                        <option value="printAmzOrder">亚马逊发票</option>
              </select>
            <?php
              if(!empty($_SESSION['viewstore'])){
                  $WarehouserArr=array();
                  $p=preg_match_all('/(\d{3})/',$_SESSION['viewstore'],$WarehouserArr);
                  //print_r($WarehouserArr[1]);
                  $WarehouserArr=$WarehouserArr[1];
              }else{
                  $WarehouserArr='all';
              }
            ?>
          <select name="orderoperation" id="orderoperation"  onchange="orderoperation(this)" >
            <option value="" id="orderoperationselect">--订单操作--</option>
            <option value="0">标记打印</option>
            <option value="1">取消打印标记</option>
            <option value="2">手动合并</option>
            <option value="3">自动合并</option>
            <option value="4">添加新订单</option>
            <option value="7">Excel跟踪号批量导入</option>

              <?php if (in_array('cancel_order', $cpower)) {?>
                  <option value="quick">快速终止发货</option>
              <?php } ?>
            <!--
            <option value="6">Amazon订单上传</option>
            <option value="5">速卖通订单上传</option>
            <option value="8">eBay CSV导入</option>
			<option value="36">自定义模板 CSV导入</option>
			<option value="37">测试1 CSV导入</option>-->
            <option value="9">批量修改</option>
            <?php if(in_array("cancel_market",$cpower)){ ?><option value="9de">去除标记</option><?php } ?>
            <option value="9re">重新分配待处理</option>
            <option value="fail_analysis">运输方式分派失败分析</option>


            <option value="">--------</option>
          </select>
        <select name="orderoperationShip" id="orderoperationShip"  onchange="orderoperation(this)" >
            <option value="" id="orderoperationselectShip">物流渠道操作</option>
            <?php
            //------万邑通------
            if($WarehouserArr=='all'||in_array('203',$WarehouserArr)) {
                ?>
                <option value="">------万邑通------</option>
                <option value="40">提交订单并确认</option>
                <option value="41">作废订单</option>
                <option value="42">跟踪号+标发出</option>
                <option value="43">总运费+出库单+转到已发货</option>
            <?php
            }
            ?>
            <option value="">------万邑通小包------</option>
            <option value="wyttj">提交订单并交运</option>
            <option value="wytjy">交运订单</option>
            <option value="wytdel">作废订单</option>
            <option value="wytprint">万邑通打印</option>

            <option value="">------Wish邮-----</option>
            <option value="wishtj">提交订单并交运</option>
            <option value="wishprint">erp打印100*100</option>

            <option value="">------云途物流-----</option>
            <option value="fzuporder">提交订单并交运</option>
            <option value="fzprint">erp打印100*100</option>

<!--            <option value="">------中欧专线-----</option>-->
<!--            <option value="rduporder">提交订单并交运</option>-->
<!--            <option value="rdprint">erp打印100*100</option>-->
            <?php

            //------酷系统------
            if($WarehouserArr=='all'||in_array('197',$WarehouserArr)) {
                ?>
                <option value="">------酷系统------</option>
                <option value="cool_1">提交订单</option>
                <option value="cool_2">跟踪号+运费+标发出</option>
                <option value="cool_3">出库单+转到已发货</option>
            <?php
            }
            ?>

            <?php
            //------4px英国仓------
            if($WarehouserArr=='all'||in_array('204',$WarehouserArr)) {
                ?>
                <option value="">------英国仓4px------</option>
                <option value="uklh_1">提交订单</option>
                <option value="uklh_2">跟踪号+运费+标发出</option>
                <option value="uklh_3">出库单+转到已发货</option>
                <option value="uklh_4">偏远地区提交专用</option>
            <?php
            }
            ?>
            <option value="">------------</option>
            <!--            <option value="10">EUB-申请跟踪号并交运</option>
                          <option value="11">EUB-交运订单</option>
                        <option value="12">EUB-热敏打印</option>
                        <option value="13">EUB-A4打印</option>
                        <option value="14">EUB-取消跟踪号</option>
                        <option value="15">EUB-重新发货</option>-->
            <option value="">---EUB新操作---</option>
            <option value="10_v4">EUB-申请跟踪号并交运</option>
            <option value="11_v4">EUB-交运订单</option>
            <option value="12_v4">EUB-热敏打印</option>
            <option value="13_v4">EUB-A4打印</option>
            <option value="14_v4">EUB-取消跟踪号</option>
            <option value="15_v4">EUB-重新发货</option>
            <option value="16_v4">EUB-自制面单</option>
            <option value="">----------</option>
            <option value="16">线下EUB-申请跟踪号</option>
            <option value="17">线下EUB-取消跟踪号</option>
            <option value="18">线下EUB-标签打印</option>
<!--            <option value="new16">外围EUB-申请跟踪号</option>
            <option value="new17">外围EUB-取消跟踪号</option>
            <option value="new18">外围EUB-标签打印</option>-->
<!--            <option value="">---贝邮宝----</option>-->
<!--            <option value="byb">贝邮宝上传</option>-->
<!--            <option value="bybtn">贝邮宝打印</option>-->
            <!--            <option value="19">HK-ecship-申请跟踪号</option>
                        <option value="20">HK-ecship-Common label</option>
                        <option value="21">HK-ecship-4-inch</option>
                        <option value="22">HK-ecship-A4*3</option>
                        <option value="23">HK-ecship-A4*2</option>
                        -->

            <option value="">------4PX API------</option>
            <option value="35">4PX-本地上传和确认V2.0</option>
            <option value="36">4PX-api打印</option>

<!--
            <option value="">------4PX 海外仓API------</option>
            <option value="25">4PX订单宝-上传并确认</option>
            <option value="26">4PX订单宝-同步订单状态</option>
            <option value="27">4PX订单宝-查看处理费</option>-->


<!--            <option value="">------出口易API------</option>-->
<!--            <option value="28">上传并确认</option>-->
<!--            <option value="29">获取跟踪号</option>-->


<!--            <option value="">------出口易M2C------</option>-->
<!--            <option value="30">上传并确认</option>-->
<!--            <option value="31">获取跟踪号</option>-->


<!--            <option value="">------出口易专线------</option>-->
<!--            <option value="32">上传并确认</option>-->
<!--            <option value="33">获取跟踪号</option>-->


<!--            <option value="">------三态------</option>-->
<!--            <option value="34">上传并确认</option>-->
<!--            <option value="34print">热敏打印10*10</option>-->

            <option value="">------东莞小包------</option>
            <option value="38">上传并确认</option>
            <option value="38print">热敏打印10*10</option>
            <option value="38del">删除运单</option>

            <option value="">-----文慧小包挂号操作---</option>
            <!--原哈尔滨挂号-->
            <option value="45">上传文慧小包</option>
            <option value="46">api打印100*100</option>
            <option value="46P">erp打印100*100</option>
            <option value="46A">取消订单</option>


            <option value="">-----燕文小包挂号操作---</option>
            <option value="47xj">新疆小包分配跟踪号</option>
            <option value="47">上传分配跟踪号</option>
            <option value="48">打印10*10</option>
            <option value="49">打印a4</option>

<!--            <option value="">----夏普小包操作(俄邮通)---</option>-->
<!--            <option value="xp1">上传订单并申请单号</option>-->
<!--            <option value="xp2">夏普系统删除订单</option>-->
<!--            <option value="xp3">打印10*10</option>-->

            <option value="">---俄速通----</option>
            <option value="est1">俄速通申请跟踪号</option>
            <option value="est">俄速通10*10打印</option>

            <option value="">---捷买送----</option>
            <option value="jms">上传订单并申请跟踪号</option>
            <option value="jms1">10*10打印</option>

            <option value="">---欧速通----</option>
            <option value="ost">上传订单并申请跟踪号</option>
            <option value="ost1">10*10打印</option>

            <option value="">----速卖通线上发货---</option>
            <option value="xsfh">1.上传订单发货</option>
            <option value="xstrack">2.获取跟踪号和pdf</option>
            <option value="xsprint">3.批量下载打印</option>
            <option value="xsfhresend">4.重新上传</option>
            <option value="xstoyuntu">5.上传到云途</option>
            <!--<option value="znjh">---智能捡货----</option>-->
            <option value="">----经济小包或欧洲小包---</option>
            <option value="eurupgh">1.上传订单发货</option>
            <option value="eurdown">2.批量下载打印</option>
<!--            <option value="">----俄罗斯小包---</option>-->
<!--            <option value="rsgh">1.上传订单发货(挂号)</option>-->
<!--            <option value="rspy">1.上传订单发货(平邮)</option>-->
<!--            <option value="rsprintgh">2.批量下载打印(挂号)</option>-->
<!--            <option value="rsprintpy">2.批量下载打印(平邮)</option>-->
            <option value="">---法国专线---</option>
            <option value="fgzx">上传订单</option>
            <option value="fgzxdy">打印标签</option>



            <option value="">---阿里物流---</option>
            <option value="cdeub_submit">纯电池EUB提交</option>
            <option value="cdeub_print">纯电池EUB打印</option>
<!--            <option value="cdeub_mark_deliver">纯电池EUB标记发货</option>-->

            <option value="">---UBI智能包裹---</option>
            <option value="ubixb">1.UBI上传订单</option>
            <option value="ubixbpdf">2.UBI批量下载打印</option>
            <!--
            <option value="">----东莞邮政---</option>
            <option value="postgh">1.上传订单发货(挂号)</option>
            <option value="postpy">1.上传订单发货(平邮)</option>
            <option value="postprintgh">2.批量下载打印(挂号)</option>
            <option value="postprintpy">2.批量下载打印(平邮)</option>
            <option value="uploadtno">上传跟踪号（EXCEL）</option>
            -->
        </select>
        <select name="pagesize" id="pagesize">
            <option <?php echo isset($_GET['pagesize']) && $_GET['pagesize'] == 100 ?'selected="selected"':'';?> value="100">每页100条</option>
            <option <?php echo isset($_GET['pagesize']) && $_GET['pagesize'] == 300 ?'selected="selected"':'';?>  value="300">每页300条</option>
            <option <?php echo isset($_GET['pagesize']) && $_GET['pagesize'] == 500 ?'selected="selected"':'';?>  value="500">每页500条</option>
        </select>
          <div style="border-bottom:#CCCCCC solid 1px">&nbsp;</div>
          搜索：
          <input name="keys" title="<?php echo $keys ?>" type="text" id="keys" onkeydown="changShowSku(event,this)" value="<?php echo $keys ?>" />
          <select name="searchtype" id="searchtype" style="width:90px">
            <option value="1" <?php if($searchtype == '1') echo 'selected="selected"' ?>>客户ID</option>
            <option value="3" <?php if($searchtype == '3') echo 'selected="selected"' ?>>客户邮箱</option>
            <option value="8" <?php if($searchtype == '8') echo 'selected="selected"' ?>>SKU</option>
            <option value="6" <?php if($searchtype == '6') echo 'selected="selected"' ?>>Item Number</option>
            <option value="7" <?php if($searchtype == '7') echo 'selected="selected"' ?>>物品标题</option>
            <option value="9" <?php if($searchtype == '9') echo 'selected="selected"' ?>>订单编号</option>
            <option value="11" <?php if($searchtype == '11') echo 'selected="selected"' ?>>Amazon订单ID</option>

            <option value="0" <?php if($searchtype == '0') echo 'selected="selected"' ?>>Record No.</option>
            <option value="2" <?php if($searchtype == '2') echo 'selected="selected"' ?>>客户姓名</option>
            <option value="4" <?php if($searchtype == '4') echo 'selected="selected"' ?>>Paypal交易ID</option>
            <option value="5" <?php if($searchtype == '5') echo 'selected="selected"' ?>>跟踪号</option>
            <option value="10" <?php if($searchtype == '10') echo 'selected="selected"' ?>>运送地址</option>

            <option value="12" <?php if($searchtype == '12') echo 'selected="selected"' ?>>显示相地址的订单</option>
            <option value="13" <?php if($searchtype == '13') echo 'selected="selected"' ?>>未通过审核订单</option>
            <option value="14" <?php if($searchtype == '14') echo 'selected="selected"' ?>>Item location</option>
          </select>
         <select name="" id="input_warehouse">
             <option value="">-选择仓库-</option>
             <?php

             $sql	 = "SELECT store_name,id,store_name_en FROM ebay_store  WHERE ".str_replace('a.ebay_warehouse','id',str_replace('and','',$ebaystoreview));
             if(empty($ebaystoreview)){
                 $sql	 = "SELECT store_name,id,store_name_en FROM ebay_store limit 200";
             }
             $sql	 = $dbcon->execute($sql);
             $sql	 = $dbcon->getResultArray($sql);
             for($i=0;$i<count($sql);$i++){
                 $select_store	= $sql[$i]['store_name'];
                 $select_store_en	= $sql[$i]['store_name_en'];
                 $select_store_id	= $sql[$i]['id'];
                 ?>
                 <option value="<?php echo $select_store_id;?>" <?php if($requestWarehouse == $select_store_id) echo "selected=selected" ?>><?php echo $select_store;?></option>
             <?php } ?>
         </select>
          <select name="acc" id="acc" style="width:90px"><!--changeaccount()-->
            <option value="">eBay帐号</option>
            <?php

					$sql	 = "select ebay_account from ebay_account as a where a.ebay_user='$user' and ($ebayacc) order by ebay_account desc ";
					$sql	 = $dbcon->execute($sql);
					$sql	 = $dbcon->getResultArray($sql);
					for($i=0;$i<count($sql);$i++){
					 	$acc	= $sql[$i]['ebay_account'];
					 ?>
            <option value="<?php echo $acc;?>" <?php if($account == $acc) echo "selected=selected" ?>><?php echo $acc;?></option>
            <?php } ?>
          </select>
          <select name="isnote" id="isnote" style="width:90px">
            <option value="">留言</option>
            <option value="1" <?php if($note == '1') echo 'selected="selected"'; ?> >有note</option>
            <option value="0" <?php if($note == '0') echo 'selected="selected"'; ?>>无note</option>
          </select>
          <select name="ebay_site" id="ebay_site" style="width:90px" >
            <option value="">站点</option>
            <option value="US" <?php if($ebay_site == 'US' ) echo 'selected="selected"';?> >US</option>
            <option value="UK" <?php if($ebay_site == 'UK' ) echo 'selected="selected"';?> >UK</option>
            <option value="Germany" <?php if($ebay_site == 'Germany' ) echo 'selected="selected"';?> >Germany</option>
            <option value="France" <?php if($ebay_site == 'France' ) echo 'selected="selected"';?> >France</option>
            <option value="eBayMotors" <?php if($ebay_site == 'eBayMotors' ) echo 'selected="selected"';?> >eBayMotors</option>
            <option value="Canada" <?php if($ebay_site == 'Canada' ) echo 'selected="selected"';?> >Canada</option>
            <option value="Australia" <?php if($ebay_site == 'Australia' ) echo 'selected="selected"';?> >Australia</option>
          </select>
          <select name="Shipping" id="Shipping" style="width:90px" >
            <option value="">运送方式</option>
            <option value="88" <?php if($shipping == '88') echo 'selected="selected"';?>>未设置</option>
              <?php
              $sql = "select distinct name  from ebay_carrier a where status=1 and ebay_user='$user' $ebaystoreview order by `name`";
              echo $sql;
              $sql = $dbcon->execute($sql);
              $sql = $dbcon->getResultArray($sql);
              for ($i = 0; $i < count($sql); $i++) {
                  $name = $sql[$i]['name'];
                  ?>
            <option value="<?php echo $name;?>"  <?php if($shipping == $name) echo 'selected="selected"';?> ><?php echo $name;?></option>
            <?php } ?>
          </select>
          <select name="isprint" id="isprint" style="width:90px" >
            <option value="">打印状态</option>
             <option value="0">未打印</option>
            <option value="1">已打印</option>
          </select>
          <select name="erp_op_id" id="erp_op_id" style="width:90px" >
            <option value="">审核状态</option>
            <option value="1">未通过审核</option>
          </select>
          <select name="ebay_ordertype" id="ebay_ordertype" style="width:90px" >
            <option value="-1" >订单类型</option>
              <?php
              $tql = "select typename from ebay_ordertype where ebay_user = '$user'";
              $tql = $dbcon->execute($tql);
              $tql = $dbcon->getResultArray($tql);
              for ($i = 0; $i < count($tql); $i++) {
                  $typename1 = $tql[$i]['typename'];
                  ?>
                  <option value="<?php echo $typename1;?>"  <?php if($ebay_ordertype == $typename1) echo "selected=selected" ?>><?php echo $typename1;?></option>
                  <?php
              }
              ?>
          </select>
          <select name="status" id="status" style="width:90px" >
            <option value="" <?php if($status0 == "") echo "selected=selected" ?>>订单状态</option>
            <option value="100" <?php  if($status0 == "100")  echo "selected=selected" ?>>所有订单</option>
            <option value="0" <?php  if($status0 == "0")  echo "selected=selected" ?>>未付款订单</option>
            <option value="1" <?php  if($status0 == "1")  echo "selected=selected" ?>>待处理订单</option>
              <?php
              $ss = "select id,name from ebay_topmenu order by ordernumber";
              $ss = $dbcon->getResultArrayBySql($ss);
              $SaveTopmenu = array();
              for ($i = 0; $i < count($ss); $i++) {
                  $ssid = $ss[$i]['id'];
                  $ssname = $ss[$i]['name'];
                  $SaveTopmenu[$ssid] = $ssname;
                  ?>
            <option value="<?php echo $ssid; ?>" <?php  if($status0 == $ssid)  echo "selected=selected" ?>><?php echo $ssname; ?></option>
            <?php } ?>
            <option value="2" <?php  if($status0 == '2')  echo "selected=selected" ?>>已经发货</option>
          </select><!--
          <select name="hunhe" id="hunhe" >
            <option value="">Please select</option>
            <option value="0" <?php #if($hunhe == '0') echo 'selected="selected"';?>>两件或两件以上的订单</option>
            <option value="1" <?php #if($hunhe == '1') echo 'selected="selected"';?>>一件物品的订单</option>
            <option value="2" <?php #if($hunhe == '2') echo 'selected="selected"';?>>一件物品的多数量订单</option>
            <option value="3" <?php #if($hunhe == '3') echo 'selected="selected"';?>>四件或四件以下</option>
            <option value="4" <?php #if($hunhe == '4') echo 'selected="selected"';?>>五件到8件之间</option>
            <option value="5" <?php #if($hunhe == '5') echo 'selected="selected"';?>>9件到9件以上</option>
          </select>-->
          国家:
    <input name="country" style="width:90px;" id="country" value="<?php echo $country;?>" type="text" />

    <br />
价格：
<input name="fprice" id="fprice" type="text" style="width:30px"  value="<?php echo $fprice;?>" />
~
<input name="eprice" id="eprice" type="text"  style="width:30px"   value="<?php echo $eprice;?>" />

重量
&nbsp;&nbsp;&nbsp;
<input name="fweight" id="fweight" type="text"  style="width:30px"   value="<?php echo $fweight;?>" />
to
<input name="eweight" id="eweight" type="text"   style="width:30px"   value="<?php echo $eweight;?>" />
付款时间:
<input name="start" id="start" type="text" onclick="WdatePicker()" style="width:70px"  value="<?php echo $start;?>" />
~
<input name="end" id="end" type="text" onclick="WdatePicker()" style="width:70px"  value="<?php echo $end;?>" />



扫描时间:
<input name="scanstart" id="scanstart" type="text" onclick="WdatePicker()"  style="width:70px"   value="<?php echo $scanstart;?>" />
~
<input name="scanend" id="scanend" type="text" onclick="WdatePicker()" style="width:70px"  value="<?php echo $scanend;?>" />
    <select name="stop_time" id="stop_time">
        <option value="">驻留时间</option>
        <option value="1" <?php if($stoptime==1) echo 'selected="selected"'; ?>>2天</option>
        <option value="2" <?php if($stoptime==2) echo 'selected="selected"'; ?>>3天</option>
        <option value="3" <?php if($stoptime==3) echo 'selected="selected"'; ?>>4天</option>
        <option value="4" <?php if($stoptime==4) echo 'selected="selected"'; ?>>5天</option>
        <option value="5" <?php if($stoptime==5) echo 'selected="selected"'; ?>>6天&以上</option>
    </select>
    <select name="stockstatus" id="stockstatus"   style="width:90px" >
        <option value="" id="printidselect2">出库状态</option>
        <option value="0"  <?php if($stockstatus == '0') echo 'selected="selected"' ?>>未出库</option>
        <option value="1"  <?php if($stockstatus == '1') echo 'selected="selected"' ?>>已出库</option>
        <option value="2"  <?php if($stockstatus == '2') echo 'selected="selected"' ?>>已打印</option>
        <option value="3"  <?php if($stockstatus == '3') echo 'selected="selected"' ?>>未打印</option>
    </select>
<input type="button" value="搜索" onclick="searchorder()" /></td>
</tr>
</table>




<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>

	<tr class='pagination'>
		<td colspan='16'><div id="rows"></div> <div id="rows2"></div>		  </td>
	</tr><tr height='20'>

					<th nowrap="nowrap" scope='col'><div style='white-space: nowrap;'width='100%' align='left'><input name="ordersn2" type="checkbox" id="ordersn2" value="<?php echo $ordersn;?>" onClick="check_all('ordersn','ordersn')" /></div></th>
					<th nowrap="nowrap" scope='col'>
                        <a href="orderindex.php?module=orders&amp;ostatus=<?php echo $ostatus;?>&amp;action=<?php echo $_REQUEST['action'];?>&amp;sort=onumber&amp;sortstatus=<?php echo $sortstatus;?>&amp;account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>">
                            <font color="#0033FF">编号</font>
                        </a>
                    </th>
                    <th nowrap="nowrap" scope='col'>操作</th>
					<th nowrap="nowrap" scope='col'><span style="white-space: nowrap;"><a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=recordnumber&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">Record No.</font></a></span>

                    <?php if($sort == 'recordnumber') echo $sortsimg; ?>                    </th>

					<th width="5%" nowrap="nowrap" scope='col'>


                <a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_account&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">帐号</font></a><span style="white-space: nowrap;">

					  <?php if($sort == 'ebay_account') echo $sortsimg; ?>

					</span></span></th>



					<th width="10%" nowrap="nowrap" scope='col'>

				<div style='white-space: nowrap;'width='100%' align='left'><a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_userid&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">Buyer Email/ID</font></a>

				  <?php if($sort == 'ebay_userid') echo $sortsimg; ?>
				</div>			</th>



					<th width="5%" nowrap="nowrap" scope='col'>
                        <span class="left_bt2">
                            <a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=sku&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>">
                                <font color="#0033FF">SKU</font>
                            </a>
                            <span style="white-space: nowrap;">

					  <?php if($sort == 'sku') echo $sortsimg; ?>

					</span></span></th>

                    <th scope='col' nowrap="nowrap">Qty</th>
                    <th scope='col' nowrap="nowrap">出库/打印</th>
        <th scope='col' nowrap="nowrap">

        <a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_countryname&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">


                国家</font></a><span style="white-space: nowrap;">

					  <?php if($sort == 'ebay_countryname') echo $sortsimg; ?>

					</span></span>        </th>
<th scope='col' nowrap="nowrap">跟踪号</th>
        <th scope='col' nowrap="nowrap">运输</th>
        <th scope='col' nowrap="nowrap"><a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_total&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">总价</font></a><?php if($sort == 'ebay_total') echo $sortsimg; ?>&nbsp;</th>

					<th scope='col' nowrap="nowrap">

					<a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_createdtime&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">Sale Date</font></a><?php if($sort == 'ebay_createdtime') echo $sortsimg; ?>					</th>
					<th scope='col' nowrap="nowrap">

				<div style='white-space: nowrap;'width='100%' align='left'><a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ebay_paidtime&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">Paid	Date</font></a><?php if($sort == 'ebay_paidtime') echo $sortsimg; ?></div>			</th>
                <td scope='col' nowrap="nowrap" style="width: 31px;">驻留</th>


		            <th scope='col' nowrap="nowrap"><a href="orderindex.php?module=orders&ostatus=<?php echo $ostatus;?>&action=<?php echo $_REQUEST['action'];?>&sort=ShippedTime&sortstatus=<?php echo $sortstatus;?>&account=<?php echo $account;?>&country=<?php echo $country;?>&Shipping=<?php echo $shipping; ?>&start=<?php echo $start;?>&end=<?php echo $end;?>&status=<?php echo $status;?>&isnote=<?php echo $note;?>&ebay_ordertype=<?php echo $ebay_ordertype;?>&keys=<?php echo $keys;?>&searchtype=<?php echo $searchtype;?>"><font color="#0033FF">Shipped Date</font></a><?php if($sort == 'ShippedTime') echo $sortsimg; ?></th>

        </tr>

			  <?php
				$allprice = 0;
//$sql	.= " and ($ebayacc) $ebaystoreview ";


              $ebay_orderFilld=' a.ebay_id,a.ebay_ordersn,a.ebay_paidtime,a.RefundAmount,a.ebay_createdtime,a.ebay_countryname,a.isprint,a.ebay_account,a.ebay_warehouse,a.pxorderid,a.erp_op_id,a.ebay_shipfee,a.ebay_carrier,a.recordnumber,a.ebay_tracknumber,a.ebay_markettime,a.ShippedTime,a.ebay_status,a.totalprofit,a.ebay_note,a.ebay_noteb,a.ebay_orderqk,a.ordershipfee,a.orderweight,a.packingtype,a.orderweight2,a.ebay_userid,a.ebay_username,a.scantime,a.ebay_usermail,a.ebay_total,a.ebay_currency,a.ebay_account,a.ebay_warehouse,a.ebay_createdtime,a.ebay_ptid,a.ebay_combine ';

              if($ostatus ==0){// 默认未付款
                  $sql		= "select $ebay_orderFilld from erp_ebay_order as a where  ebay_status='0'  $ebaystoreview  and ebay_combine!='1'";

              }elseif($ostatus ==100){// 所有订单
                  $sql		= "select $ebay_orderFilld from erp_ebay_order as a where ($ebayacc) $ebaystoreview and ebay_combine!='1' ";

              }else{//指定状态
                  $sql		= "select $ebay_orderFilld from erp_ebay_order as a where ($ebayacc) and ebay_status=$ostatus $ebaystoreview and ebay_combine!='1'";
              }


              if ($sort == 'sku' || ($searchtype == 8&&!empty($keys)) || ($searchtype == 6&&!empty($keys)) || ($searchtype == 7&&!empty($keys)) || ($searchtype == 14&&!empty($keys)) || $ebay_site != '' || $stockstatus == '0' || $stockstatus == 1) {

                  if ($ostatus == 100) {
                      $sql = "select $ebay_orderFilld,b.ebay_id as ddd from erp_ebay_order as a join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn where ($ebayacc) $ebaystoreview and ebay_combine!='1' ";
                  } else {
                      $sql = "select $ebay_orderFilld,b.ebay_id as ddd from erp_ebay_order as a join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn where ($ebayacc)  and a.ebay_status='$ostatus' $ebaystoreview  and ebay_combine!='1' ";
                  }
              }


              if($start !='' && $end != ''){

                  $st00	= strtotime($start." 00:00:00");
                  $st11	= strtotime($end." 23:59:59");
                  $sql	.= " and (a.ebay_paidtime>=$st00 and a.ebay_paidtime<=$st11)";
              }


              if($scanstart !='' && $scanend != ''){

                  $st00	= strtotime($scanstart." 00:00:00");
                  $st11	= strtotime($scanend." 23:59:59");
                  $sql	.= " and (a.scantime>=$st00 and a.scantime<=$st11)";
              }



				if($searchtype == '0' && $keys != ''){$sql	.= " and a.recordnumber		 = '$keys'";}
				if($searchtype == '1' && $keys != ''){$sql	.= " and a.ebay_userid		 = '$keys'";}
				if($searchtype == '2' && $keys != ''){$sql	.= " and a.ebay_username	 = '$keys'";}
				if($searchtype == '3' && $keys != ''){$sql	.= " and a.ebay_usermail	 = '$keys'";}
				if($searchtype == '4' && $keys != ''){$sql	.= " and a.ebay_ptid		 = '$keys'";}
				if($searchtype == '5' && $keys != ''){$sql	.= " and a.ebay_tracknumber	 =  '$keys'";}
				if($searchtype == '6' && $keys != ''){$sql	.= " and b.ebay_itemid		 = '$keys'";}
				if($searchtype == '7' && $keys != ''){$sql	.= " and b.ebay_itemtitle	 like '$keys%'";}
				if($searchtype == '8' && $keys != ''){

					$tmp_sql = "select goods_sn from ebay_productscombine where combinestr like '%#$keys#%' and ebay_user='$user'";
					$tmp_sql	=$dbcon->execute($tmp_sql);
					$tmp_sql	=$dbcon->getResultArray($tmp_sql);
					$tmp_str = '';
					foreach($tmp_sql as $k=>$v){
						$tmp_str .= " or b.sku = '".$v['goods_sn']."'";
					}
					if(strlen($tmp_str)>0){
						$tmp_str = " and (b.sku ='$keys' ".$tmp_str.") ";
					}

					if(!empty($tmp_str)){
						$sql	.= $tmp_str;

					}else{
						$sql	.= " and b.sku='$keys'";
					}
				}
				if($searchtype == '9' && $keys != ''){
                    //2016-01-16
                    $keys=str_replace('，',',',$keys);
                    $keys=preg_replace('/[^0-9,]/','',$keys);
                    $sql	.= " and a.ebay_id in ($keys)";
                }
                if($searchtype=='9'&&$fromdbl=='1'){
                    if($_POST['orderids']){
                        $_SESSION['orderids']=trim(preg_replace('/[^0-9,]/','',$_POST['orderids']),',');
                    }
                    $orderids=$_SESSION['orderids'];
                    if($orderids!=''){
                        $sql	.= " and a.ebay_id	in($orderids)";
                    }
                }
				if($searchtype == '11' && $keys != ''){$sql	.= " and a.ebay_ordersn	 	 = '$keys'";}

				if($stockstatus != '' && $stockstatus == '0' ) {$sql	.= " and b.istrue		 = '0' ";}
				if($stockstatus != '' && $stockstatus == '1' ) {$sql	.= " and b.istrue		 = '1' ";}

				if($stockstatus != '' && $stockstatus == '2' ) {$sql	.= " and a.isprint		 = '1' ";}
				if($stockstatus != '' && $stockstatus == '3' ) {$sql	.= " and a.isprint		 = '0' ";}

				if($isprint == '1' ) {$sql	.= " and a.isprint		 = '1' ";}
				if($isprint == '0' ) {$sql	.= " and a.isprint		 = '0' ";}
				if($erp_op_id !='' ) {$sql	.= " and a.erp_op_id	 = '1' ";}


				if($searchtype == '13'){$sql	.= " and a.erp_op_id = '1'";}
				if($searchtype == '14'){$sql	.= " and b.goods_location= '$keys'";}

				if($searchtype == '10' && $keys != ''){$sql	.= " and (a.ebay_username like '$keys%' or a.ebay_street like '%$keys%' or a.ebay_street1 like '%$keys%' or a.ebay_city like '%$keys%' or a.ebay_state like '%$keys%' or a.ebay_countryname like '%$keys%' or a.ebay_postcode like '%$keys%' or a.ebay_phone like '%$keys%') ";}

				if($ebay_ordertype !='' && $ebay_ordertype !='-1'  ) $sql.=" and a.ebay_ordertype='$ebay_ordertype'";

				if($country !=''){
					if($country == 'United States'){
				 	    $sql.=" and (a.ebay_countryname='United States' or a.ebay_countryname='US' or a.ebay_countryname='USA' or a.ebay_countryname='APO/FPO' OR a.ebay_countryname='Puerto Rico' )";
				 	}else{
					    $sql.=" and a.ebay_countryname='$country'";
					}
				 }


				if( $note == '1') 	$sql.=" and a.ebay_note	!=''";
				if( $note == '0') 	$sql.=" and a.ebay_note	=''";
				if( $ebay_site != '') 	$sql.=" and b.ebay_site	='$ebay_site'";

              if ($shipping != '') {
                  if ($shipping == '88') {
                      $sql .= " and (a.ebay_carrier='' or a.ebay_carrier is null )";
                  } else {
                      $sql .= " and a.ebay_carrier='$shipping'";
                  }
              }
				//$sql	.= " and ($ebayacc) $ebaystoreview ";


				if($account !="") $sql.= " and a.ebay_account='$account'";

				if(!empty($requestWarehouse)){ $sql.= " and a.ebay_warehouse='$requestWarehouse'";}



				if($fprice!='')
				{
					$sql.=" and a.ebay_total>=$fprice";
				}
				if($eprice!=''){
					$sql.=" and a.ebay_total<=$eprice";
				}
				if($fweight!='')
				{
					$sql.=" and a.orderweight>=$fweight";
				}
				if($eweight!=''){
					$sql.=" and a.orderweight<=$eweight";
				}

                if(!empty($stoptime)){
                    $timenow=time();

                    switch($stoptime){
                        case 1:$m=$timenow-86400;$n=$timenow-172800;$sql.=" and $m-a.ebay_paidtime>0 and $n-a.ebay_paidtime<=0";break;
                        case 2:$m=$timenow-172800;$n=$timenow-259200;$sql.=" and $m-a.ebay_paidtime>0 and $n-a.ebay_paidtime<=0";break;
                        case 3:$m=$timenow-259200;$n=$timenow-345600;$sql.=" and $m-a.ebay_paidtime>0 and $n-a.ebay_paidtime<=0";break;
                        case 4:$m=$timenow-345600;$n=$timenow-432000;$sql.=" and $m-a.ebay_paidtime>0 and $n-a.ebay_paidtime<=0";break;
                        case 5:$m=$timenow-432000;$sql.=" and $m-a.ebay_paidtime>0";break;
                        default:;
                    }

                }

                //由评价直接定位到订单 2014/3/25 谭联星 START
                if($tid!=''){
                    $sql .= ' and b.ebay_tid='."'$tid'";
                }
                //由评价直接定位到订单 2014/3/25 谭联星 END
				if($sku	!= "" ){
					$sql .= " group by a.ebay_id order by ebay_id desc ";

				}else{
					//$sql .= ' group by a.ebay_id '.$sortstr;

					$sql .= ' group by a.ebay_id '.$sortstr;

				}

				if($searchtype == '12'){


						$sql	= "SELECT DISTINCT ebay_userid, COUNT( * ) AS cc, ebay_id,ebay_account FROM erp_ebay_order WHERE  ebay_userid !='' and ebay_combine !=  '1' and ebay_status='$ostatus' GROUP BY ebay_account, ebay_userid HAVING cc >=2";
						$sql		= $dbcon->execute($sql);
						$sql		= $dbcon->getResultArray($sql);
						$tjstr		= '';


						for($i=0;$i<count($sql);$i++){
								$ebay_id 		= $sql[$i]['ebay_id'];
								$ebay_userid 		= $sql[$i]['ebay_userid'];
								$ebay_account 		= $sql[$i]['ebay_account'];
								$vv			= "select ebay_id from erp_ebay_order where ebay_id !='$ebay_id' and ebay_status='$ostatus' and ebay_account='$ebay_account' and ebay_combine !='1' and ebay_userid='$ebay_userid' ";
								$vv			= $dbcon->execute($vv);
								$vv			= $dbcon->getResultArray($vv);
								$tjstr		.=" ebay_id ='$ebay_id' or ";
								for($v=0;$v<count($vv);$v++){

										$ebay_id 		= $vv[$v]['ebay_id'];
										$tjstr		.=" ebay_id ='$ebay_id' or ";
								}



						}


						$tjstr = substr($tjstr,0,strlen($tjstr)-3);
						$sql		= "select $ebay_orderFilld from erp_ebay_order as a where ($ebayacc) and ebay_status=$ostatus  $ebaystoreview and ebay_combine!='1' and ($tjstr)";
                        $sql .= ' group by a.ebay_id ';
				}

                //a.*,b.ebay_id as ddd
                if(strstr($sql,'select '.$ebay_orderFilld.',b.ebay_id as ddd from')!==false){
                    $resultField=" $ebay_orderFilld,b.ebay_id as ddd ";
                }else{
                    $resultField=" $ebay_orderFilld ";
                }
              $SearchStr="select".$resultField."from";
              $sql=str_replace($SearchStr,"select count(a.ebay_id) as cc from",$sql);
//              echo $sql;
              if($_REQUEST['debug']==1&&$_SESSION['truename']=='测试人员谭'){echo $sql;}

              $total = $dbcon->getResultArrayBySql($sql);
              $total = count($total);//[0]['cc'];

              $totalpages = $total;

              $pageindex = (isset($_GET['pageindex'])) ? $_GET['pageindex'] : 1;
              $limit     = " limit " . ($pageindex - 1) * $pagesize . ",$pagesize";

              $page = new page(array('total' => $total, 'perpage' => $pagesize));

//              $resultField .= 'a.ebay_combine';

              $sql=str_replace(" count(a.ebay_id) as cc ",$resultField,$sql);

              if($_REQUEST['debug1']==1&&$_SESSION['truename']=='测试人员谭'){echo $sql;}
				$sql = $sql.$limit;
//              echo $sql.'<br>';
//              exit;
 				$sqla		= $dbcon->execute($sql);
				$sql		= $dbcon->getResultArray($sqla);

				/* 释放mysql 系统资源 */
				$dbcon->free_result($sqla);


				$dpage		= 0;

				for($i=0;$i<count($sql);$i++){
					$totalprofit		= $sql[$i]['totalprofit'];
					$noteb				= $sql[$i]['ebay_noteb']?$sql[$i]['ebay_noteb']:"";
					$ebayid				= $sql[$i]['ebay_id'];
                    $ebay_orderqk       = $sql[$i]['ebay_orderqk'];
					$ordersn			= $sql[$i]['ebay_ordersn'];
					$ordershipfee		= $sql[$i]['ordershipfee'];
					$orderweight		= $sql[$i]['orderweight'];
					$orderweight2		= $sql[$i]['orderweight2'];
					$userid				= $sql[$i]['ebay_userid'];
					$username			= $sql[$i]['ebay_username'];
					$scantime			= $sql[$i]['scantime'];
					$email				= $sql[$i]['ebay_usermail'];
					$total				= $sql[$i]['ebay_total'];
					$currency			= $sql[$i]['ebay_currency'];
					$account			= $sql[$i]['ebay_account'];
					$Orderhouse		    = $sql[$i]['ebay_warehouse'];
                    $combine            = $sql[$i]['ebay_combine'];

					$sss = "select appkey,refresh_token,ebay_expirtime,AWS_ACCESS_KEY_ID,wish_token,cdtoken,pm_token from ebay_account where ebay_account='$account' and ebay_user='$user' limit 1";
					$sss = $dbcon->execute($sss);
					$sss	= $dbcon->getResultArray($sss);
					$refresh_token		= $sss[0]['refresh_token'];
                    $wish_token         = $sss[0]['wish_token'];
                    $cdtoken         = $sss[0]['cdtoken'];
                    $pm_token         = $sss[0]['pm_token'];
                    $ebay_expirtime     = $sss[0]['ebay_expirtime'];
                    $ali_appkey         = $sss[0]['appkey'];
                    $AWS_ACCESS_KEY_ID  = $sss[0]['AWS_ACCESS_KEY_ID'];


					$sss = "select rates from ebay_currency where currency='$currency' and user='$user' limit 1";
					$sss = $dbcon->execute($sss);
					$sss	= $dbcon->getResultArray($sss);
					$rates  = $sss[0]['rates']?$sss[0]['rates']:1;
					$truetotal = $total*$rates;
					$allprice			+= $truetotal;

                      $paidtime     = $sql[$i]['ebay_paidtime'];
                      $RefundAmount = $sql[$i]['RefundAmount'];
                      $ebay_ordersn = $sql[$i]['ebay_ordersn'];
                      $dpage++;
                      $ebay_createdtime = $sql[$i]['ebay_createdtime'];
                      $country          = $sql[$i]['ebay_countryname'];
                      $isprint          = $sql[$i]['isprint'];
                      $account          = $sql[$i]['ebay_account'];
                      $ebay_warehouse   = $sql[$i]['ebay_warehouse'];
                      $pxorderid        = $sql[$i]['pxorderid'];
                      $erp_op_id        = $sql[$i]['erp_op_id'];
                      $shipfee          = $sql[$i]['ebay_shipfee'];
                      $ebaynote         = $sql[$i]['ebay_note'];
                      $ebay_carrier     = $sql[$i]['ebay_carrier'];
                      $recordnumber     = $sql[$i]['recordnumber'];
                      $ebay_tracknumber = $sql[$i]['ebay_tracknumber'];
                      $ShippedTime      = $sql[$i]['ebay_markettime'];
                      $markeShippedTime = $sql[$i]['ShippedTime'];
                      $ebay_status      = $sql[$i]['ebay_status'];
//					$moneyback= $sql[$i]['moneyback'];
//					$moneyback_total= $sql[$i]['moneyback_total'];

					$ebay_createdtime	= $sql[$i]['ebay_createdtime'];
					//$formatshipprice	= $currency.$shipfee;
					$formattotalprice	= $currency.$total;

					/*  对应物品明细表  2012-05-13号 */
					$st	= "select id as ebay_id,ebay_itemid,ebay_tid,ebay_itemtitle,goods_location,ebay_itemurl,sku,ebay_itemprice,ebay_amount,istrue,notes,recordnumber,attribute,ebay_shiptype,smturl,feedbacktype from erp_ebay_order_detail where ebay_ordersn='$ordersn'";
                    $st	= $dbcon->getResultArrayBySql($st);


					$itemid		    = $st[0]['ebay_itemid'];
					$ebay_ptid		=  $sql[$i]['ebay_ptid'];
                    $stop_time=floor((time()-$paidtime)/3600);
                    $colors   ='';
                    switch($stop_time){
                        case $stop_time>=24&&$stop_time<48:$colors='#096';break;
                        case $stop_time>=48&&$stop_time<72:$colors='#039';break;
                        case $stop_time>=72&&$stop_time<96:$colors='#ee5';break;
                        case $stop_time>=96&&$stop_time<120:$colors='#f90';break;
                        case $stop_time>=120:$colors='#f66';break;
                        default:$colors='';
                    }
			  ?>
         		<tr height='20' class='oddListRowS1'>

                    <td scope='row' align='left' valign="top"><input name="ordersn" type="checkbox" id="ordersn"
                                                                     value="<?php echo $ebayid; ?>"
                                                                     onchange="displayselect()">
                        <?php
                        if ($ebaynote != "") echo "<img src='notes.gif'  width=\"20\" height=\"20\"/>";
                        ?>
                    </td>
						<td scope='row' align='left' valign="top" ><?php echo '<b class="jg_b" style="color:'.$colors.';background:'.$colors.'">&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;'.$ebayid;?><?php echo $combine?'<a href="javascript:void(0)" onclick=show_combine_details("'.$combine.'")><br>合并单</a>':'';?>  </td>
						<td scope='row' align='left' valign="top" >
                            <a href="ordermodifive.php?ordersn=<?php echo $ordersn;?>&module=orders&ostatus=1&action=ModifiveOrder"  target="_blank">编辑</a>&nbsp;
                            <?php if(in_array('orders_copy',$cpower)){  ?>
                              &nbsp;<a href="#" onclick="copyorders('<?php echo $ordersn;?>')" >复制</a>
                            <?php }?>
                            &nbsp;<a href="expressprint.php?id=<?php echo $ebayid;?>" target="_blank">快递</a>                                                      </td>
		          <td scope='row' align='left' valign="top" ><?php echo $recordnumber;

				  if($erp_op_id == 1) echo '未审核订单';
				  ?>
                  </td>
						    <td scope='row' align='left' valign="top" >

							<?php echo $account; ?>                            </td>
						    <td scope='row' align='left' valign="top" ><?php echo $userid;?><br>
                            <?php
							echo $username.'<br>';
							echo $ordersn;
							?>                             </td>
						    <td scope='row' align='left' valign="top" ><?php echo $storeArr[$Orderhouse]['store_name'];?></td>
						    <td scope='row' align='left' valign="top" >&nbsp;</td>
						    <td scope='row' align='left' valign="top" >&nbsp;</td>
			                <td scope='row' align='left' valign="top" ><?php echo $country;?></td>
                             <td scope='row' align='left' valign="top" ><?php
							if($ebay_tracknumber != '') echo '<br><font color=red>'.$ebay_tracknumber.'</font>';
							?></td>
						    <td scope='row' align='left' valign="top" ><?php echo $ebay_carrier;?></td>
						    <td scope='row' align='left' valign="top" ><?php echo $formattotalprice;?>&nbsp;</td>
						    <td scope='row' align='left' valign="top" >
                            <?php
							if($ebay_createdtime != '0' && $ebay_createdtime != ''){
							echo date('Y-m-d',$ebay_createdtime);
							}
							 ?>
                            &nbsp;</td>
						    <td scope='row' align='left' valign="top" ><?php
							if($paidtime != 0){
							  echo date('M-d',$paidtime);
                            }

							?></td>



		                    <td scope='row' align='left' valign="top" >
                                <?php //@@@
                                    echo $stop_time.' h';
                                ?>
		                    </td>
		                    <td scope='row' align='left' valign="top" ><?php
							if($markeShippedTime != 0){
							echo 'Marked:'.date('M-d',$markeShippedTime).'<br>';
							}

							if($scantime > 0) echo  '扫描:'.date('M-d H:i',$scantime);

							 ?>&nbsp;</td>
              </tr>



             <?php

							$total	= 0;
							$productcost		= 0;
							$productweight		= 0;

							for($t=0;$t<count($st);$t++){
								$pid			= $st[$t]['ebay_id'];
								$qname			= $st[$t]['ebay_itemtitle'];
								$qitemid		= $st[$t]['ebay_itemid'];
								$sku			= $st[$t]['sku'];
								$imagepic		= $st[$t]['ebay_itemurl'];

								if($imagepic == '' ){
										$ddsql		= "select goods_pic from ebay_goods where  goods_sn ='$sku' limit 1";
										$ddsql 		= $dbcon->execute($ddsql);
										$ddsql		= $dbcon->getResultArray($ddsql);
										$imagepic	= 'images/'.$ddsql[0]['goods_pic'];
								}


                                 $ebay_amount    = $st[$t]['ebay_amount'];
                                 $qname          = $st[$t]['ebay_itemtitle'];
                                 $recordnumber   = $st[$t]['recordnumber'];
                                 $ebay_itemprice = $st[$t]['ebay_itemprice'];
                                 $istrue         = $st[$t]['istrue'];
                                 $notes          = $st[$t]['notes'];
                                 $attribute      = $st[$t]['attribute'];
                                 $ebay_shiptype  = $st[$t]['ebay_shiptype'];
                                 $location	     = $st[$t]['goods_location'];
                                 $ebay_tid     = $st[$t]['ebay_tid'];
                                 $feedbacktype = $st[$t]['feedbacktype'];
                                 if($feedbacktype!=''){
                                     $feedbacktype=$feedbacktypeArr[$feedbacktype];
                                 }

			  ?>

         		<tr height='20' class='oddListRowS1'>

         		  <td>&nbsp;</td>
         		  <td scope='row' align='left' valign="top" >&nbsp;
                      <em class="location">
                          <?php
                          if(!empty($location)){
                              echo $location;
                          }elseif(empty($location)&&empty($ali_appkey)&&empty($AWS_ACCESS_KEY_ID)&&empty($wish_token)){//ebay 没有location
                             echo '<a href="./toxls/getLocationFromOrder.php?pid='.$pid.'" target="_blank">获取Location</a>';
                          }

                          ?>
                      </em>
                  </td>
         		  <td align='left' valign="top" scope='row' ><?php
			// echo"	  交昜号：".$ebay_ptid ."  Paypal:".$PayPalEmailAddress;

				  if($t == 0){
				  if($ebay_status == '0'){
					echo '未付款订单';
				  }else if($ebay_status == '1'){
					echo '待处理订单';
				  }else if($ebay_status == '2'){
					echo '已经发货';
				  }else{

					 echo $SaveTopmenu[$ebay_status];

                     if($SaveTopmenu[$ebay_status]=='酷系统订单'){
                        if($ebay_orderqk==1){echo '<br><font color=red>已导出</font>';}
                     }

				  }

				  if($pxorderid != '') echo '<br>4px ID:<font color=red>'.$pxorderid.'<br>';

				  }


                  if(strlen($qitemid) >  10 ){
                      $url	= "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=$qitemid";
                  }elseif($AWS_ACCESS_KEY_ID!=''){
                      $url	= "http://www.amazon.com/gp/product/$qitemid";
                  }
                  if($refresh_token != '' ) $url   = $st[$t]['smturl'];
                  if($wish_token!='')$url='https://www.wish.com/c/'.$qitemid;
                  if($cdtoken!='')$url='http://www.cdiscount.com/search/10/'.$qitemid.'.html';
                  if($pm_token!=''){$url='http://www.priceminister.com/';}

				  ?></td>
         		  <td scope='row' align='left' valign="top" ><?php if(count($st) >1) echo  $recordnumber; ?></td>

         		  <td scope='row' align='left' valign="top" ><a href="<?php echo $url;?>" target="_blank">

				 <img src="<?php echo $imagepic; ?>" border="0" width="50" height="50" />


         		  <td scope='row' align='left' valign="top" ></td>

         		  <td scope='row' align='left' valign="top" ><span class="STYLE1">
         		    <input name="sku<?php echo $pid;?>" style="width:90px!important;" type="text" id="sku<?php echo $pid;?>" value="<?php echo $sku;?>" />
       		      <a href="#" onclick="savesku('<?php echo $pid;?>')">Save</a><span id="mstatus<?php echo $pid;?>" name="mstatus<?php echo $pid;?>"></span>&nbsp;</td>

         		  <td scope='row' align='left' valign="top" ><strong><?php echo $ebay_amount;?></strong></td>
         		  <td scope='row' align='left' valign="top" ><?php

							if($istrue == '1'){

								echo "√";
							}else{
								echo "×";
							}
							echo '/';
							echo $isprint?'√':'×';
						?></td>
         		  <td align='left' valign="top" scope='row' >
                      属性:<?php echo $attribute.' '; ?><br>
                      <?php if(!empty($feedbacktype)){
                          echo '评价:';
                      }?>
                  </td>
         		  <td colspan="3" align='left' valign="top" scope='row' >
                  </td>

					<td colspan="4" align='left' valign="top" scope='row' ><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="1" >
                      <?php

					$rr			= "select goods_sncombine from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
					$rra			= $dbcon->execute($rr);
					$rr 	 	= $dbcon->getResultArray($rra);

					/* 释放mysql 系统资源 */
					$dbcon->free_result($rra);

					if(count($rr) > 0){
					$goods_sncombine	= $rr[0]['goods_sncombine'];
					$goods_sncombine    = explode(',',$goods_sncombine);
					for($e=0;$e<count($goods_sncombine);$e++){
						$pline			= explode('*',$goods_sncombine[$e]);

						$goods_sn		= $pline[0];

						$goddscount     = $pline[1] * $ebay_amount;



						$gg			= "SELECT goods_sn,goods_name,isuse FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
						$gga			= $dbcon->execute($gg);
						$gg 	 		= $dbcon->getResultArray($gga);

						/* 释放mysql 系统资源 */
						$dbcon->free_result($gga);
						if($gg=='')
						{
							$goods_sn="无该商品";
						}
                       else{
						$goods_sn		= $gg[0]['goods_sn'];

						$goods_name		= $gg[0]['goods_name'];

						$isuse		= $gg[0]['isuse'];

                        }
						?>
                      <tr>
                        <td  ><a href="javascript:viewProduct('<?php echo $goods_sn;?>')"><?php echo $goods_sn;?></a>&nbsp;</td>
                        <td  ><?php echo $goods_name;?>&nbsp;</td>
                        <td  ><?php echo $goddscount;?>&nbsp;/0</td>
                        <td  ><?php


					if($isuse == '0') $isuse	= '在线';
					if($isuse == '1') $isuse	= '下线';
					if($isuse == '2') $isuse	= '零库存';
					if($isuse == '3') $isuse	= '清仓';

					    echo $isuse;


					  ?>
                          &nbsp;</td>
                      </tr>
                      <?php
					}

					}else{
						$gg				= "SELECT goods_sn,goods_name,isuse FROM ebay_goods where goods_sn='$sku' and ebay_user='$user'";
						$gga			= $dbcon->execute($gg);
						$gg 	 		= $dbcon->getResultArray($gga);

						$dbcon->free_result($gga);

					if($gg=='')
						{
							$goods_sn="无该商品";
						}
                       else{
						$goods_sn		= $gg[0]['goods_sn'];
						$goods_name		= $gg[0]['goods_name'];
						$isuse		= $gg[0]['isuse'];

                        }
					 ?>
                      <tr>
                        <td><a href="javascript:viewProduct('<?php echo $goods_sn;?>')"><?php echo $goods_sn;?></a>&nbsp;</td>
                        <td><?php echo $goods_name;?>&nbsp;</td>
                        <td><?php echo $ebay_amount;?>&nbsp;/0 </td>
                        <td><?php


					echo $isuse;
					  ?>
                          &nbsp;</td>
                      </tr>
                      <?php
					 }
					  ?>
                    </table></td>
              <?php
			  $ebaynote					= str_replace('<![CDATA[','',$ebaynote);
			  $ebaynote					= str_replace(']]>','',$ebaynote);
			  ?>
         		<tr height='20' class='oddListRowS1'>

         		  <td scope='row' align='left' valign="top" >&nbsp;</td>
         		  <td scope='row' align='left' valign="top" >&nbsp;</td>
         		  <td scope='row' align='left' valign="top" >&nbsp;</td>
         		  <td scope='row' align='left' valign="top" >&nbsp;</td>

         		  <td scope='row' align='left' valign="top" >&nbsp;</td>

         		  <td colspan="10" align='left' valign="top" scope='row' >
                  <?php if($ebaynote != '') echo 'eBay notes:'.$ebaynote.'<br>'; ?>
                  <?php if($noteb != '') echo 'My note:'.$noteb.'<br>'; ?>                  </td>

         		  <td scope='row' align='left' valign="top" >&nbsp;</td>
   		      </tr>
               <?php  } } ?>

		<tr class='pagination'>

		<td colspan='16'>

			<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
				<tr>
					<td nowrap="nowrap" class='paginationActionButtons'>
                    本页订单条数为：<?php echo $dpage;?> 总金额是：<?php echo $allprice;?>
                    <div align="center"><?php  echo '<br><center>'.$page->show(2)."</center>";//输出分页 ?>
                </div></td>
					</tr>
			</table>
        </td>
	</tr></table>
    <div class="clear"></div>

<?php
include "include/orderindexjs.php";
include "bottom.php";
?>