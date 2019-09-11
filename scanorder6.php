<?php
include "include/config.php";
include "top.php";
$tracknumber		= $_REQUEST['tracknumber'];
$value				= trim($_REQUEST['value']);
$shiptype			= $_REQUEST['shiptype'];
$storeid			= $_REQUEST['storeid'];
$sw		= '';


?>

<style type="text/css">
    .box {
        /*border:1px solid #ccc;*/
        padding-left: 10px;
        border-radius: 2px;
    }
    .relative_box {
        width:400px;
        top:0;
        border:1px solid #ccc;
        border-radius: 2px;
        border-bottom: none;
        background-color: rgba(200,200,200, 0.7);
        box-shadow: 0 0 1px #bbb;
        padding-bottom: 15px;
    }

    .header {
        background-color: rgba(190,190,190, 0.9);
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
        padding: 5px 10px;
    }
    .content {
        padding:5px 10px;
    }

    .countTable {
        width:100%;
        background-color: rgba(200,200,200, 0.1);
    }

    .countTable th{
        text-align: center;
    }

    .scanUser,.currentCounts{
        color: green;
        font-size: 14px;
        font-weight: bold;
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
<link href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.css" rel="stylesheet">
<div id="main" xmlns="http://www.w3.org/1999/html">
<div id="content" >
<table style="width:100%"><tr><td><div class='moduleTitle'>
    <h2><?php echo "订单扫描【仅同步重量】".$status.' '.$str;?> </h2>
                <div >
                     &nbsp;&nbsp;&nbsp;
                    <a href="inBagList.php" class="inBagLink" target="_blank"> 装袋列表 </a>

                    <a href="javascript:getUsersData()" class="inBagLink"
                       style="background-color: #37d5ff;padding:5px 10px;margin-left: 10px">更新扫描数据
                    </a>
                </div>
</div>
<div class='listViewBody'>
    <div id='Accountsadvanced_searchSearchForm' style='display:none' class="edit view search advanced"></div>
    <div id='Accountssaved_viewsSearchForm' style='display: none';></div>
<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>
    <tr class='pagination'>
        <td width="65%">
            <table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
                <tr>
                    <td nowrap="nowrap" class='paginationActionButtons'>
                        <table style="max-width:100%" border="0" align="center">
                            <tr>
                                <td width="100%" colspan="3"><DIV style="font-size:36px">1.通用跟踪号:
                                        <input name="order" type="text" id="order" onkeyup="checkByTrackNo(event)" style="width:230px; height:50px; font-size:24px" />
                                        <select name="curr" id="curr" onchange="changeCarrier(this)">
                                            <?php
                                            $sql = "select distinct name  from ebay_carrier a where ebay_warehouse=196 order by status asc,`name`";
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


                                        <span style="font-size: 16px;color: #000;">包装人员：</span>
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


                                    </DIV></td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <div id="mstatus" style="font-size:36px"></div>
                                </td>
                            </tr>
                            <tr>
                               <!-- <td style="width: 40%;">
                                    <div style="font-size:36px"> 确认跟踪号：&nbsp;
                                        <input type="text" id="confirmOrder" checkWeight="0" WeightAbs="0" calcWeight="0"
                                               style="width:330px; height:50px; font-size:46px"  onkeyup="getnewweight(event)"  />
                                    </div>
                                </td>-->

                                <td rowspan="2">
                                    <br />
                                    <div id="mstatus2"></div>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-left: 20px">
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span style="font-size:36px">产品计算重量：</span>
                                                <div id="mstatus3" style="font-size:36px;color:#F11"></div></td>
                                        </tr>
                                    </table>
                                    <br />
                                </td>


                                <td rowspan="2">
                                    <div class="box">
                                        <div class="relative_box">
                                            <div class="header">
                                                运输方式： <span class="transport_manner"></span> &nbsp;&nbsp;
                                                扫描员：<span class="scanUser"> <?php echo $_SESSION['truename']; ?> </span> &nbsp;&nbsp;
                                                计数 ： <span class="currentCounts"> 0 </span>
                                            </div>
                                            <div class="content" style="overflow-y: scroll;">

                                            </div>
                                        </div>
                                    </div>
                                </td>



                            </tr>

                            <tr>
                                <td>
<!--                                    <div id="mstatus" style="font-size:36px"></div>-->
                                    <div style="font-size:36px">2.同步重量：&nbsp;
                                    <input name="currentweight" type="text" id="currentweight" style="width:330px; height:50px; font-size:46px" />
                                    <span id="debug_prev_weight" style="color:#911;font-size:12px;"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input id="weightbyhand" type="text"><input onclick="submitweight()" type="button" value="手动同步"></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                   </td>
                            </tr>
                            <tr>
                                <td colspan="4">&nbsp;</td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>		</td>
    </tr>
    <input type="text" style="width:40px;" id="startWeight">
    <input type="text" style="width:40px;" id="endWeight">

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
<script type="text/javascript" src="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.jquery.min.js"></script>


<script language="javascript">
    //第一次称重
    var firstCurrentWeight = 0;
    $(function(){
        $('select[name=curr]').chosen({search_contains: true, width: '200px', allow_single_deselect: true});
    })
function $1(id){
    return document.getElementById(id);
}

function submitweight(){
    var weight=$("#weightbyhand").val();
    var ebayid	= document.getElementById('order').value;
    if(ebayid==''){
        alert("必须输入跟踪号");return;
    }
    if(isNaN(weight)||weight<0||weight==''){
        alert("重量必须是合理的数字");return;
    }
    sendWeight(ebayid,weight);
}

function checkByTrackNo(event){
    //清空隐藏域--重量范围
    $('#startWeight').val('');
    $('#endWeight').val('');

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
    if(curr.indexOf('PMS')==0){
        curr='PMS'
    }
    // hank 20180312 万欧物流的加拿大、澳大利亚 、意大利专线需要截取跟踪号，截取办法：E后面取10位数字
    curr = curr.trim();
    if(curr == '意大利专线-万欧'){
        ebayid = ebayid.substring(0,11);
    }
    curr	=  encodeURIComponent(curr);
    var url		=  "getajax4.php?newcheckorder=1";
    var param	=  "type=newcheckorder&ebayid="+ebayid+"&curr="+curr;
    $("#order").blur();
    $.ajax({
        type:"POST",
       // async: false,
        url:url,
        data:param,
        success:function(text){
            text= $.trim(text);
            if(undefined==text){
                showMsg("网络错误",false);
                return;
            }

            /**
             * 测试人员杨 2018-01-12
             * 说明:限制的最大重量与最小重量
             */
            if(text.indexOf('|')>0){
                showMsg("订单验证通过",true);
                $("#order").attr("data",ebayid);
                var weightInfo = text.split ('|');
                var startWeight = parseInt(weightInfo[0]);
                var endWeight = parseInt(weightInfo[1]);
                $('#startWeight').val(startWeight);
                $('#endWeight').val(endWeight);
                $("#confirmOrder").select().focus();
                //验证称重
                checkCurrentWeight();
                return;
            }

            if(text == 5){
                showMsg("订单验证通过",true);
                $("#order").attr("data",ebayid);
                $("#confirmOrder").select().focus();
                //验证称重
                checkCurrentWeight();
                return;
            }
/*            if(text.indexOf('g')>-1){
                showMsg("订单验证通过",true);
                $("#order").attr("data",ebayid);
                $("#confirmOrder").select().focus();
                document.getElementById('mstatus3').innerHTML = "<font color='#FF0000'>" + text + "</font>";
                return;
            }*/
            /**
            *测试人员谭 2017-03-03 21:27:01
            *说明: 限重的玩法
             * $gweight.'g|'.$checkWeight.'|'.$abs;

            if(text.indexOf('g|')>0){
                showMsg("订单验证通过",true);
                $("#order").attr("data",ebayid);
                var confirmOrderJq=$("#confirmOrder");
                confirmOrderJq.select().focus();
                var weightInfo = text.split ('g|');
                var weight = parseInt (weightInfo[0]);
                var bijiaoInfo = weightInfo[1].split ('|');
                var checkWeight = bijiaoInfo[0];
                var WeightAbs = bijiaoInfo[1];
                confirmOrderJq.attr("checkWeight",checkWeight);
                confirmOrderJq.attr("WeightAbs",WeightAbs);
                confirmOrderJq.attr("calcWeight",weight);
                document.getElementById('mstatus3').innerHTML = "<font color='#FF0000'>" + weight + "g </font>";
                return;
            }*/

            if(text==-300){
                showMsg("核对失败:订单没有扫描,请先扫描订单,然后等待5分钟后再同步重量!",false);
                play(false);
                return;
            }

            if(text== -302){
                showMsg("核对失败:订单已被拦截,请先检查拦截列表",false);
                play(false);
                return;
            }

            if(text== -303){
                showMsg("核对失败:订单未回传出库记录,请等待5分钟",false);
                play(false);
                return;
            }

            if(text== -304){
                showMsg("核对失败:跟踪号或pxid不正确,请联系it修改",false);
                play(false);
                return;
            }

            if(text== -306){
                showMsg("核对失败:订单不是已出库待称重状态",false);
                play(false);
                return;
            }

            if(text==-301){
                showMsg("核对失败:EUB可能没有交运!请联系主管处理",false);
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

            if(text==-700){
                showMsg("核对失败:订单没有跟踪号不可以称重!",false);
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
    var currentweightObj=document.getElementById("currentweight");
    var currentweight = currentweightObj.value;
    //currentweightObj.value='';
    currentweight=currentweight.replace(/[^\d]/g,'');
    return currentweight;
}

function setWeightByService(){
    var lastConnected=new Date().getTime();
    var url		= "http://127.0.0.1:38383/weight?"+lastConnected;
    $.get(url,{},function(data){
        console.log(data);
    },'text');

}


//获取新的重量
function getnewweight() {//扫描第二枪
    /*
    var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
    var keyCode;
    if(isie){
        keyCode = window.event.keyCode;//IE使用windows.event事件
    }else {
        keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e.which给出一个索引值给Mo内核（注释1）
    }

    if (keyCode != 13) {return;}*/


    var currentweight=getWeight();

    if(isNaN(currentweight)||currentweight<=0){
        showMsg("重量"+currentweight+"重量读取失败!请检查电子秤",false);
        ///document.getElementById('currentweight').focus();
        return;
    }

    if(currentweight>3000){
        showMsg("重量"+currentweight+"不合理!请检查电子秤",false);
        play(false);
        return false;
    }

    //测试人员杨 2018-03-12 重量范围检查
    var startWeight = $('#startWeight').val();
    var endWeight = $('#endWeight').val();
    startWeight=parseInt(startWeight);
    endWeight=parseInt(endWeight);

    if(!isNaN(startWeight) && !isNaN(endWeight)){
        if(currentweight<startWeight || currentweight>endWeight){
            showMsg("重量限制范围为:"+startWeight+"--"+endWeight+"g!",false);
            return false;
        }
    }

    /*var confirmOrder=$("#confirmOrder");

    var checkWeight=parseInt(confirmOrder.attr("checkWeight"));

    if(checkWeight){ // 需要强制验证
        var WeightAbs=parseInt(confirmOrder.attr("WeightAbs")); // 误差 范围
        var calcWeight=parseInt(confirmOrder.attr("calcWeight")); // 计算重量
        var chayi=Math.abs(calcWeight-currentweight);
        if(chayi>WeightAbs){
            showMsg("称重 和 计算重量 差异过大,允许差异:"+WeightAbs+"!",false);
            return false;
        }
    }

    confirmOrder.val("");*/

    // 开始发送重量
    var ebayid	= document.getElementById('order').value;

    if(ebayid == ''){
        alert('请先扫描订单号');
        showMsg("请先扫描跟踪号",false);
        return false;
    }
    showMsg("正在发送重量! "+currentweight+"g",true);
    document.getElementById('debug_prev_weight').innerHTML='上一个:'+currentweight+'g';
    // confirmOrder.blur();
    sendWeight(ebayid,currentweight)
}

//发送重量并且出库
function sendWeight(ebayid,currentweight){
    var url		= "getajax4.php?newupdateweight=1";
    var packagingstaff	= document.getElementById('packagingstaff').value;
    var curr	=  document.getElementById('curr').value;
    if(curr.indexOf('PMS')==0){
        curr='PMS'
    }
    if (!$.trim(curr)) {
        alert('运输方式未获取到.');
        return ;
    }
    // hank 20180312 万欧物流的加拿大、澳大利亚 、意大利专线需要截取跟踪号，截取办法：E后面取10位数字
    if(curr == '意大利专线-万欧'){
        ebayid = ebayid.substring(0,11);
    }
    var param	= "type=newupdateweight&ebayid="+ebayid+"&packagingstaff="+packagingstaff+"&currentweight="+currentweight+'&curr='+curr;

//    alert(curr);
//    alert(param);

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
                showMsg("同步重量失败：验货时出库失败!",false); play(false); return ;
            }

            if(text=='0'){
                showMsg("同步重量失败：可能是重单!!",false); play(false); return ;
            }

            if(text=='1'){
                showMsg("重量同步成功!",true); play(true);
                document.getElementById('order').focus();
                document.getElementById('order').value	= '';
                clearPrevError();

                // 添加扫描统计 -- 2017-03-16 15:26
                updateScanCount();

                return;
            }

            if (text == '3') {
                showMsg("同种运输方式单人最大扫描数量已达到最大值,需要先将已扫描的去装袋.", false);
                play(false);
                return null;
            }

            if(text==-700){
                showMsg("核对失败:订单没有跟踪号不可以称重!",false);
                play(false);
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
        document.getElementById("successSound").src='music/sound/159.mp3';
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


function changeCarrier(that)
{
//    alert('Hello world');
    $('.transport_manner').css({color:'green', fontSize:'14px',fontWeight:'bold'}).html($(that).val());
    getCurrentUserCurrentCarrierCounts();
}
/**
 * 每次扫描时的统计更新
 */
function updateScanCount() {
    var obj = $('.currentCounts');
    var preCounts = obj.html();
    obj.html(parseInt(preCounts) + 1);
}

/**
 * 查询当前选中的运输方式下的扫描统计数据
 */
function getCurrentUserCurrentCarrierCounts(){
    var url = 'getajax4.php';
    var carrier = $.trim($('#curr').val());
    if (!carrier) {
        alert('运输方式获取获取失败.');
        return null;
    }
    $.ajax({
        url : url,
        type : 'post',
        data : {type : 'getCurrentUserCarrierCounts', carrier : carrier},
        dataType : 'text'
    }).done(function(re) {
        $('.currentCounts').html(re);
    }).fail(function() {
        alert('当前扫员扫描统计访问失败.');
    });
}


/**
 * 获取所有的人的统计数据
 */
function getUsersData() {
    /* 获取用户扫描统计数据 */
    var url = 'getajax4.php';
    $.ajax({
        url: url,
        type: 'post',
        data: {type: 'getCounts'},
        dataType: 'text'
    }).done(function (re) {
        $('.content').html(re);
    }).fail(function () {
        alert('访问出错.');
    }).done(function () {
        getCurrentUserCurrentCarrierCounts();
    });
}

/**
 * @author xiao
 * @date 2018-03-26
 * 第一次扫描成功，记录当前重量，然后0.3秒获取新重量进行对比，相差10%可以接受
 *
 *
 */
 function checkCurrentWeight(){
     //设置第一次称重
    firstCurrentWeight = getWeight();
    setTimeout(function(){
        var checkInterVal = setInterval(
            function () {
                var currentweight=getWeight();
                var begin = firstCurrentWeight - (firstCurrentWeight*0.1);
                var end = firstCurrentWeight + (firstCurrentWeight*0.1);
                if(currentweight>end || currentweight < begin){
                    //重新设置第一次称重值
                    firstCurrentWeight = currentweight;
                }else{
                    //称重在取值范围，清除定时器
                    clearInterval(checkInterVal);
                    getnewweight();
                }
            },300);
    },300)
 }


/* 页面假造完成后立即执行的操作 */
$(function(){
    $("#order").focus();

    // 获取初始运输方式
    $('.transport_manner').html($('#curr').val()).css({color:'green', fontSize:'14px',fontWeight:'bold'});
    getUsersData();
    //TODO: 不用再自动的去获取 改用手动的去更新 -- 成祥
//    setInterval(function () {
//        getUsersData();
//    }, 30000);
});



</script>

<script src="js/cw.js"></script>
<script>
    CW.listen(function(text){
        if(text.indexOf('+')>-1&&text.indexOf('g')>1){
            document.getElementById("currentweight").value=text;
        }
    });
    CW.listenState(function(state){
        if(state!=1){
            document.getElementById("currentweight").value="WAIT...";
        }
    });
</script>
