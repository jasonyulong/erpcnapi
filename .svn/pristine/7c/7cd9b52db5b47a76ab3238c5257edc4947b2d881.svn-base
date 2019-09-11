<?php
include "include/config.php";
include "top.php";
$tracknumber		= $_REQUEST['tracknumber'];
$value				= trim($_REQUEST['value']);
$shiptype			= $_REQUEST['shiptype'];
$storeid			= $_REQUEST['storeid'];
$sw		= '';
if ($value != '') {
    $ss = "select * from ebay_order as a join ebay_orderdetail as b on a.ebay_ordersn = b.ebay_ordersn where (a.ebay_id='$value' or a.ebay_tracknumber='$value') ";
    $ss = $dbcon->execute($ss);
    $ss = $dbcon->getResultArray($ss);
    if (count($ss) == '0') {
        $status = " -[<font color='#FF0000'>操作记录:未找到订单</font>]";
        echo "<script>alert('" . '未找到订单' . "')</script>";
        $sw = 1;
    } else {
        $ystatus = $ss[0]['ebay_status'];
        $ebay_carrier = $ss[0]['ebay_carrier'];
        $account = $ss[0]['ebay_account'];
        $osn = $ss[0]['ebay_ordersn'];
        if ($ystatus == '123') {
            $status = " -[<font color='#33CC33'>操作记录订单核对成功</font>]";
            if ($tracknumber != '') {
                $ss = "update ebay_order set ebay_status='2',ebay_tracknumber='$tracknumber',scantime='$mctime' where ebay_id='$value' or ebay_tracknumber='$value' ";
                $type = 0;
                $sql = "select * from ebay_account where ebay_user='$user' and ebay_account='$account'";
                $sql = $dbcon->execute($sql);
                $sql = $dbcon->getResultArray($sql);
                $token = $sql[0]['ebay_token'];
                CompleteSale02($token, $osn, $type);
            } else {
                $ss = "update ebay_order set ebay_status='2',scantime='$mctime' where ebay_id='$value' or ebay_tracknumber='$value' ";
            }
            //	if($ebay_carrier != 'E邮宝' ){
            $sb = "update ebay_order set ebay_markettime='$mctime',ShippedTime='$mctime' where ebay_ordersn='$osn'";
            $dbcon->execute($sb);
            addoutstock($osn);
            //	}
            $dbcon->execute($ss);
        } else {
            $status = " -[<font color='#33CC33'>订单不在已处理，请检查...</font>]";
            echo "<script>alert('" . '订单不在已处理，通知客服人员' . "')</script>";
            $sw = 1;
        }
    }
}
?>
<div id="main">
<div id="content" >
<table style="width:100%"><tr><td><div class='moduleTitle'>
    <h2><?php echo "同步重量(老秤)".$status.' '.$str;?> </h2>
</div>
<div class='listViewBody'>
    <div id='Accountsadvanced_searchSearchForm' style='display:none' class="edit view search advanced"></div>
    <div id='Accountssaved_viewsSearchForm' style='display: none';></div>
</form>
<?php if($sw == '1'){ ?>
<embed   src= "r.wav"   loop=false  autostart=true   name=bgss   width="0"   height=0>
<?php } ?>
<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>
    <tr class='pagination'>
        <td width="65%">
            <table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
                <tr>
                    <td nowrap="nowrap" class='paginationActionButtons'>
                        <table width="100%" border="0" align="center">
                            <tr>
                                <td width="53%"><DIV style="font-size:36px">1.EUB跟踪号:
                                        <input name="order" type="text" id="order" onkeyup="checkByTrackNo(event)" style="width:180px; height:50px; font-size:24px" />
                                        <select id="curr">
                                            <?php
                                            $sql = "select distinct name  from ebay_carrier a where ebay_warehouse=196 and status=1 order by `name`";
                                            $sql = $dbcon->execute($sql);
                                            $sql = $dbcon->getResultArray($sql);
                                            for ($i = 0; $i < count($sql); $i++) {
                                                $name = $sql[$i]['name'];
                                                ?>
                                                <option value="<?php echo $name; ?>"  <?php
                                                if ($shipping == $name) echo 'selected="selected"'; ?> ><?php echo $name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <select id="openSound">
                                            <option value="2">开启声音提示</option>
                                            <option value="1">关闭声音提示</option>
                                        </select>
                                        <input type="button" data="" value="清除上次失败" onclick="clearPrevError()"/>
                                    </DIV></td>
                                <td width="47%" colspan="3" rowspan="3" valign="top">包装人员：
                                    <select name="packagingstaff" id="packagingstaff">
                                        <?php

                                        $ss		= "select * from ebay_user where username ='$truename'  ";
                                        $ss		= $dbcon->execute($ss);
                                        $ss		= $dbcon->getResultArray($ss);
                                        for($i=0;$i<count($ss); $i++){
                                            $usernames	= $ss[$i]['username'];
                                            ?>
                                            <option value="<?php echo $usernames;?>" <?php if($cguser == $usernames) echo 'selected="selected"'; ?>><?php echo $usernames;?></option>
                                        <?php

                                        }

                                        ?>
                                    </select>
                                    <br />
                                    <div id="mstatus2"></div>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>同步重量
                                                <input name="currentweight" type="text" id="confirmOrder" style="width:120px; height:50px; font-size:46px"  onkeydown="getnewweight(event)"  />
                                                <!--<input type="button" value="手动确认重量" onclick="updateweightByBtn()"  />-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span style="font-size:36px">产品计算重量：</span>   <div id="mstatus3" style="font-size:36px">    </div>   </td>
                                        </tr>
                                    </table>
                                    <br /></td>
                            </tr>

                            <tr>
                                <td>
                                    <div id="mstatus" style="font-size:36px"></div>

                                    <DIV style="font-size:36px">2.同步重量</DIV>                                              </td>
                            </tr>
                            <tr>
                                <td>
                                    <Applet id="app" code="a.class" height=387 width=400>
                                        <PARAM NAME=ARCHIVE VALUE="comm.jar">
                                        <param name=myName value="kaka">
                                        <param name=mySex value="mail">
                                        <param name=myNum value=200630170>
                                        <param name=myAge value=22>
                                    </Applet>

                                    &nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="4"><select name="account3" size="10" multiple="multiple" id="account3">
                                        <?php
                                        $sql = "select * from ebay_account as a  where a.ebay_user='$user'   ";
                                        $sql = $dbcon->execute($sql);
                                        $sql = $dbcon->getResultArray($sql);
                                        for ($i = 0; $i < count($sql); $i++) {
                                            $account = $sql[$i]['ebay_account'];
                                            ?>
                                            <option value="<?php echo $account; ?>"><?php echo $account;?></option>
                                        <?php } ?>
                                    </select>
                                    扫描开始时间:
                                    <?php
                                    $start1 = date('Y-m-d ') . ' 00:00:00';
                                    $start2 = date('Y-m-d ') . ' 23:59:59';
                                    ?>
                                    <input name="start" id="start" type="text" onclick="WdatePicker()"
                                        value="<?php echo $start1; ?>"/>
                                    扫描结束时间:
                                    <input name="end" id="end" type="text" onclick="WdatePicker()"
                                        value="<?php echo $start2; ?>"/>
                                    <input type="button" value="导出到xls" onclick="xlsbaobiao()"/></td>
                            </tr>
                            <tr>
                                <td colspan="4">速卖通批量发货格式化导出:<br />
                                    eBay帐号：
                                    <select name="account" id="account">
                                        <?php

                                        $sql	 = "select * from ebay_account as a where a.ebay_user='$user' and ($ebayacc) order by a.ebay_account desc ";
                                        $sql	 = $dbcon->execute($sql);
                                        $sql	 = $dbcon->getResultArray($sql);
                                        for($i=0;$i<count($sql);$i++){

                                            $account	= $sql[$i]['ebay_account'];
                                            ?>
                                            <option value="<?php echo $account;?>"><?php echo $account;?></option>
                                        <?php } ?>
                                    </select>
                                    扫描开始时间:


                                    <?php

                                    $start1						= date('Y-m-d ').' 00:00:00';
                                    $start2						= date('Y-m-d ').' 23:59:59';




                                    ?>
                                    <input name="start1" id="start1" type="text" onclick="WdatePicker()" value="<?php echo $start1;?>" />
                                    扫描结束时间:
                                    <input name="end1" id="end1" type="text" onclick="WdatePicker()" value="<?php echo $start2;?>" />
                                    <input type="button" value="导出到xls" onclick="xlsbaobiao2()" />
                                    <br />
                                    <br />
                                    B2B销售报表数据导出：<br />
                                    eBay帐号：
                                    <select name="account2" id="account2">
                                        <?php

                                        $sql	 = "select * from ebay_account as a where a.ebay_user='$user' and ($ebayacc) order by a.ebay_account desc ";
                                        $sql	 = $dbcon->execute($sql);
                                        $sql	 = $dbcon->getResultArray($sql);
                                        for($i=0;$i<count($sql);$i++){

                                            $account	= $sql[$i]['ebay_account'];
                                            ?>
                                            <option value="<?php echo $account;?>"><?php echo $account;?></option>
                                        <?php } ?>
                                    </select>
                                    扫描开始时间:
                                    <?php

                                    $start1						= date('Y-m-d ').' 00:00:00';
                                    $start2						= date('Y-m-d ').' 23:59:59';




                                    ?>
                                    <input name="start2" id="start2" type="text" onclick="WdatePicker()" value="<?php echo $start1;?>" />
                                    扫描结束时间:
                                    <input name="end2" id="end2" type="text" onclick="WdatePicker()" value="<?php echo $start2;?>" />
                                    <input type="button" value="导出到xls" onclick="xlsbaobiao3()" /><br /></td>
                            </tr>
                            <tr>
                                <td colspan="4">&nbsp;</td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>		</td>
    </tr>


    <tr class='pagination'>
        <td>
            <table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
                <tr>
                    <td nowrap="nowrap" class='paginationActionButtons'></td>
                </tr>
            </table>		</td>
    </tr></table>
<audio  id="successSound" autoplay="autoplay">
    <source src='' type='audio/mp3'>
</audio>
<audio id="failSound" autoplay="autoplay">
    <source src='' type='audio/mp3'>
</audio>
<div class="clear"></div>
<?php
include "bottom.php";
?>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript">
function $1(id){
    return document.getElementById(id);
}
function checkByTrackNo(event){
    var order	= document.getElementById('order').value;
    //var keyCode = event.keyCode;
    if(order=="")return;
    var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
    var keyCode;
    if(isie){
        keyCode = window.event.keyCode;//IE使用windows.event事件
    }else {
        keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e.which给出一个索引值给Mo内核（注释1）
    }
    //console.log(keyCode);
    if (keyCode != 13) { return false;}
    var savethisorder=$("#order").attr("data");
    if(savethisorder!=''&&undefined!=savethisorder){
        showMsg("上一个订单:"+savethisorder+" 未扫描完成!",false);
        return ;
    }
    var ebayid	= order;
    showMsg();
    var curr	=  document.getElementById('curr').value;
    curr	=  encodeURIComponent(curr);
    var url		=  "getajax4.php?newcheckorder=1";
    var param	=  "type=newcheckorder&ebayid="+ebayid+"&curr="+curr;

    $.ajax({
        type:"POST",
        async: false,
        url:url,
        data:param,
        success:function(text){
            text= $.trim(text);
            if(undefined==text){
                showMsg("网络错误",false);
                return;
            }
            if(text.indexOf('g')>-1){
                showMsg("订单验证通过",true);
                $("#order").attr("data",ebayid);
                $("#confirmOrder").select().focus();//currentweight
                document.getElementById('mstatus3').innerHTML = "<font color='#FF0000'>" + text + "</font>";
                return;
            }

            if(text==-300){
                showMsg("核对失败:订单没有扫描,请先扫描订单,然后等待5分钟后再同步重量!",false);
                play(false);
                return;
            }

            if(text==-350){
                showMsg("核对失败:订单已经核对过重量!",false);
                play(false);
                return;
            }

            if(text==-250){
                showMsg("核对失败:订单在扣库存之后,被转走,请查看订单备注或日志!",false);
                play(false);
                return;
            }

            if(text==-200){
                showMsg("核对失败:可能不是选中的运输方式!",false);
                play(false);
                return;
            }

            if(text==-2){
                showMsg("核对失败:在验货出库时订单不在等待扫描,被转走!",false);
                play(false);
                return;
            }


            if(text=='2'){
                showMsg("核对失败:验货时出库失败,请尝试使用老的扫描方式出库!",false);
                play(false);
                return;
            }


            if(text=='4'){
                showMsg("核对失败:负库存，出库失败请先校准库存,再试使用老的扫描方式出库!",false);
                play(false);
                return;
            }

            if(text=='3'){
                showMsg("核对失败:已经操作过出库了!请查看日志!",false);
                play(false);
                return ;
            }

        }
    });

}


function showMsg(str,bool){
    var bool=bool||false;
    var str=str||'';
    var color="#f33";
    if(bool){
        color="#393";
    }
    if(str==''){
        html="<img src=cx.gif />";
        document.getElementById("mstatus").innerHTML=html;
        return;
    }
    var html="<p style='color:"+color+"'>"+str+"</p>";
    document.getElementById("mstatus").innerHTML=html;
}

function clearPrevError(){
    $("#order").attr("data","");
}

//电子秤
function getWeight(){
    var currentweight = window.document.app.currentweight;
    //1750175
    if(/(\d{2,4})0{1,2}\1/.test(currentweight)){ // 小包出库重量最多是 2000g 所以只考虑4位
        var arr=currentweight.toString().match(/(\d{2,4})0{1,2}\1/);
        currentweight = arr[1];
    }
    return currentweight;
}

//获取新的重量
function getnewweight(event) {//扫描第二枪
    var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
    var keyCode;
    if(isie){
        keyCode = window.event.keyCode;//IE使用windows.event事件
    }else {
        keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e.which给出一个索引值给Mo内核（注释1）
    }

    if (keyCode != 13) {return;}


    var currentweight=getWeight();

    if(isNaN(currentweight)||currentweight<=0){
        showMsg("重量"+currentweight+"重量读取失败!请检查电子秤",false);
        return;
    }

    if(currentweight>30000){
        showMsg("重量"+currentweight+"不合理!请检查电子秤",false);
        play(false);
        return false;
    }

    var confirmOrder=$("#confirmOrder");
    confirmOrder.val("");

    // 开始发送重量
    var ebayid	= document.getElementById('order').value;

    if(ebayid == ''){
        alert('请先扫描订单号');
        showMsg("请先扫描跟踪号",false);
        return false;
    }
    showMsg("正在发送重量! "+currentweight+"g",true);
    confirmOrder.blur();
    sendWeight(ebayid,currentweight)
}

//发送重量并且出库
function sendWeight(ebayid,currentweight){
    var url		= "getajax4.php?newupdateweight=1";
    var packagingstaff	= document.getElementById('packagingstaff').value;
    var param	= "type=newupdateweight&ebayid="+ebayid+"&packagingstaff="+packagingstaff+"&currentweight="+currentweight;
    $.ajax({
        type:"POST",
        // async: false,
        url:url,
        data:param,
        success:function(text){
            text = $.trim(text);
            if(text=='-2'){
                showMsg("同步重量失败：没有核对SKU!!",false); play(false); return ;
            }
            if(text=='2'){
                showMsg("同步重量失败：验货时出库失败,需要使用老方式出库!",false); play(false); return ;
            }

            if(text=='0'){
                showMsg("同步重量失败：请重试!!",false); play(false); return ;
            }

            if(text=='1'){
                showMsg("重量同步成功!",true); play(true);
                document.getElementById('order').focus();
                document.getElementById('order').value	= '';
                clearPrevError();
                return;
            }

            showMsg("同步重量失败：原因未知!",false); play(false);
        }

    });
}



// 关于声音的事情
function play(bool){
    if(document.getElementById("openSound").value==1){
        return;
    }
    if(!bool){
        document.getElementById("failSound").src='music/sound/fail.mp3';
    }else{
        document.getElementById("successSound").src='music/sound/suss.mp3';
    }
}


//这是报表
function xlsbaobiao(){
    var start		= document.getElementById('start').value;
    var end			= document.getElementById('end').value;
    var account		= '';
    var len			= document.getElementById('account3').options.length;
    for(var i = 0; i < len; i++){
        if( document.getElementById('account3').options[i].selected){
            var e =  document.getElementById('account3').options[i];

            account	+= e.value+'#';

        }
    }
    var url			= 'xlsbaobiao.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);
    window.open(url,"_blank");
}

function xlsbaobiao2(){
    var start		= document.getElementById('start1').value;
    var end			= document.getElementById('end1').value;
    var account			= document.getElementById('account').value;
    var url			= 'xlsbaobiao2.php?start='+start+"&end="+end+"&account="+account;
    window.open(url,"_blank");
}

function xlsbaobiao3(){
    var start		= document.getElementById('start2').value;
    var end			= document.getElementById('end2').value;
    var account			= document.getElementById('account').value;
    var url			= 'xlsbaobiao3.php?start='+start+"&end="+end+"&account="+account;
    window.open(url,"_blank");
}


</script>
