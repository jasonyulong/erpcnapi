<?php if (!defined('THINK_PATH')) exit(); include "include/config.php"; include "top.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css">
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/custom.css">
    <link href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.css" rel="stylesheet">
    <script src="/newerp/Public/js/jquery.js"></script>
    <script src="/newerp/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.jquery.min.js"></script>
    <script src="/newerp/Public/js/plugins/layer/laydate/laydate.js"></script>
    <style>
        th[type=sort] {
            cursor: pointer;
        }

        .text-primary {
            color: #1ab394;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .ibox-content {
            padding: 0
        }

        .search-bar > div > .searchTitle, .search-bar > div > div > .searchTitle {
            line-height: 30px;
        }

        .custom-table th {
            font-weight: normal;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-table td {
            border-bottom: 1px solid #f0f0f0;
        }

        /*.chosen-container .chosen-single {*/
            /*height: 30px;*/
            /*line-height: 30px;*/
            /*border: 1px solid #ccc;*/
        /*}*/

        .input-group {
            margin: 5px 5px;
        }

        .form-control {
            border: 1px solid #ccc;
            padding: 0px 12px;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }
        .chosen-container .chosen-single {
            height: 30px;
            line-height: 30px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>



    <style>
        #scanRecord {
            background: #DDDDDD;
        }
        #scanRecord div td{width: 20%}
        #scanRecord div td.loc{width: 30%}
        a{  cursor:pointer;color: #0b578f;  }
    </style>
    <div class="form-inline">
        <div class="input-group" style="margin-left: 25px;">
            <p style="font-size: 18px;">拣货异常sku扫描</p>
        </div>
        <div class="input-group" style="margin-left: 30%">
            <p style="font-size: 18px;color: red">今日已成功：<span id="skucount" style="display: none"><?php echo ($toDayCount['skucount'] ?: 0); ?></span><span id="qtycount"><?php echo ($toDayCount['qtycount'] ?: 0); ?></span>&nbsp;个</p>
        </div>
    </div>
    <div class="ibox float-e-margins" style="margin:0 auto;height: 100%;padding:20px;width:100%;background:#fff;">

        <div class="form-inline" style="float: left;width: 25%;height: 20%" >
            <div class="input-group">
                <div class="input-group-addon">SKU</div>
                <input type="text" id="sku" class="form-control input-sm" name="sku" style="width: 250px;height: 40px" onKeyPress="check(event)" placeholder="请扫描sku">
            </div>
        </div>
        <div id="pickername" style="float: left;width: 40%;height:11%;font-size: 40px;margin-left: 30px;color: red;font-weight: bold">

        </div>
        <div id="scanRecord" style="float: left;width: 40%;height:60%;font-size: 18px;margin-left: 30px;margin-top:2%;overflow:auto">

            <div style="padding:20px;">
                <table>
                    <?php if(is_array($datalist)): foreach($datalist as $key=>$v): ?><tr id="sku_<?php echo ($v["sku"]); ?>_<?php echo ($v["picker"]); ?>">
                            <td><?php echo ($v["sku"]); ?>*<?php echo ($v["qty"]); ?></td>
                            <td class="loc">库位号：<?php echo ($v["g_location"]); ?></td>
                            <td class="jhr">拣货人：<?php echo ($v["picker"]); ?></td>
                            <td><a onclick="printbq('<?php echo ($v["sku"]); ?>')" >打印标签</a></td>
                        </tr><?php endforeach; endif; ?>
                </table>
            </div>
        </div>
    </div>

    <audio id='jspeech_iframe'  src='' ></audio>

    <script>
        $(function () {
            $("#sku").select().focus();
        });
        /**
         * 扫描
         * @param event
         * @returns {boolean}
         */
        function check(event) {

            var sku = $.trim($("#sku").val());
            var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
            var keyCode;
            if (isie) {
                keyCode = window.event.keyCode;//IE使用windows.event事件
            } else {
                keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e
            }
            if (keyCode !==13) {
                return false;
            }
            //扫描二维码替换输入框值 Shawn 2018-09-04
            if(sku.indexOf("$") >= 0){
                var skuStr = sku.split("$");
                sku = skuStr[1];
                document.getElementById('sku').value = sku;
            }
            $("#sku").select().focus();
            var count = $("#qtycount").html();
            count = parseInt(count)+1;
            var skucount = parseInt($("#skucount").html())+1;
            if (keyCode == 13) {
                $.ajax({
                    type : "post",
                    url :  "t.php?s=/Order/PickingAbnormalitySku/saveAbnormalitySku",
                    data : {sku:sku},
                    success : function(data) {
                        if(data.status){
                            $("#sku_"+data.sku+'_'+data.picker).remove();
                            $("#qtycount").html(count);
                            if(data.newsku == 1){
                                $("#skucount").html(skucount);
                            }
                            var shtml = '<tr id="sku_'+data.sku+'_'+data.picker+'"><td>'+data.sku+'*'+data.qty+'</td><td class="loc">库位号：'+data.location+'</td><td class="jhr">拣货人：'+data.picker+'</td><td><a onclick="printbq(\''+data.sku+'\')">打印标签</a></td></tr>'
                            $("#scanRecord").find("div").find("table").prepend(shtml);

                            $("#pickername").html(data.picker);

                            var src='capi/number/'+data.picker+'.mp3';
                            if(!fileExists(src)){
                                src = 'http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=6&text=' + data.picker;
                            }
                            $("#jspeech_iframe").attr("src",src);
                            soundHandle();
                        }else{
                            layer.msg(data.msg, {icon: 2});
                        }
                    }
                });
            }
        }

        function printbq(sku){
            window.open('http://erp.spocoo.com/printskulabel001.php?sku='+sku+'&storeid=196&store_name_en=1_NO')
        }
        function fileExists(url) {
            var isExists;
            $.ajax({
                url: url,
                async: false,
                type: 'HEAD',
                error: function () {
                    isExists = 0;
                },
                success: function () {
                    isExists = 1;

                }
            });
            if (isExists == 1) {
                return true;
            } else {
                return false;
            }
        }

        function soundHandle(){
            var video	=	document.getElementById("jspeech_iframe");
            video.play();
        }

    </script>

</body>
</html>