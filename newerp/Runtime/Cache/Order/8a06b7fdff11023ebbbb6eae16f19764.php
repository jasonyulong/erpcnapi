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
        .detilspan p span{ float:left;width: 17%}
        .detilspan .skuArr span{ float:left;width: 10%}
        .detilspan p span{ float:left;width: 17%}
    </style>
    <div class="form-inline">
    <div class="input-group" style="margin-left: 25px;">
        <div class="input-group-addon">扫描订单或跟踪号</div>
        <input type="text" name="ebay_id" id="ebay_id" class="form-control input-sm" style="width:200px" value="<?php echo ($scanNumber); ?>" onKeyPress="check(event)">
    </div>
    <div class="input-group" style="margin-left: 25%">
        <p style="font-size: 18px;color: #008000">今日已成功：<?php echo ($toDayCount); ?>个</p>
        <span style="font-size: 30px;color: red"><?php if(msg): echo ($msg); endif; ?></span>
    </div>
    </div>
    <div class="ibox float-e-margins" style="margin:0 auto;height: 100%;padding:20px;width:100%;background:#fff;">
        <form>
            <div class="form-inline">
                <div class="input-group">
                    <div class="input-group-addon">异常扫描时间</div>
                    <input type="text" class="form-control input-sm" name="ebay_addtime_start" style="width: 100px;" placeholder="开始日期" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" value="<?php echo ($request["ebay_addtime_start"]); ?>">
                    <div class="input-group-addon">至</div>
                    <input type="text" class="form-control input-sm" name="ebay_addtime_end" style="width: 100px;" placeholder="结束日期" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" value="<?php echo ($request["ebay_addtime_end"]); ?>">
                </div>
            <button onclick="searchForm(this)" class="btn btn-primary btn-sm" id="search-btn" type="button">搜索</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button onclick="exports(this,1);" class="btn btn-primary btn-sm" type="button" style="background-color: #31B0D5;border:1px solid #ccc">异常订单导出</button>
            <button onclick="exports(this,2);" class="btn btn-primary btn-sm" type="button" style="background-color: #31B0D5;border:1px solid #ccc">重量修改导出</button>
            </div>
        </form>
        <ul class="pagination"><?php echo ($show); ?></ul>
        <table class="table custom-table">
            <thead>
            <!--<th width="4%" style="text-align: left;">-->
                <!--&lt;!&ndash;<input type="checkbox" name="checkAll" class="checkAllBox" onclick="checkAll(this)" /> 全选&ndash;&gt;-->
            <!--</th>-->
            <th width="12%" style="text-align: center;">状态/平台</th>
            <th width="50%" style="text-align: center;">订单详情</th>
            <th width="18%" style="text-align: left;">异常扫描时间</th>
            </thead>
            <?php if(is_array($data)): foreach($data as $key=>$a): ?><tr class="detilspan">
                    <!--<td style="text-align: left;">-->
                        <!--&lt;!&ndash;<input type="checkbox" name="checkItem"  value="<?php echo ($a['ebay_id']); ?>" />&ndash;&gt;-->
                    <!--</td>-->
                    <td style="text-align: center;">
                        <div class="btn btn-success btn-sm"><?php echo ($topMenus[$a['ebay_status']]); ?></div>
                        <br>
                        <h4><?php echo ($a["platform"]); ?></h4>
                        <p>销售员:<?php if($a['market']): echo ($a["market"]); endif; ?></p>
                    </td>
                    <td style="text-align: left; ">
                        <p>
                            <span onclick="blankUrl('<?php echo ($a["ebay_id"]); ?>')">订单:<a><?php echo ($a["ebay_id"]); ?></a></span><span style="width: 200px">跟踪号:<?php echo ($a["ebay_tracknumber"]); ?></span><span style="width: 200px">物流:<?php echo ($a["ebay_carrier"]); ?></span><span>&nbsp;总重量:<?php echo array_sum(array_column($a['skuArr'],'calcWeight'));?>&nbsp;kg</span>
                        </p>
                        <br>
                        <div class="skuArr" style="margin-top: 10px">
                            <?php if(is_array($a["skuArr"])): foreach($a["skuArr"] as $sk=>$sku): ?><div>
                                <span><?php if($sk < 1): echo ($store[$a['ebay_warehouse']]); else: ?>&nbsp;<?php endif; ?></span>
                                <span style="width: 100px"><?php echo ($sku["qty"]); ?>*<?php echo ($sku["sku"]); ?></span>
                                <span style="color: #003bb3;width: 8%;cursor:pointer;" data-weight="<?php echo ($sku["weight"]); ?>" onclick="updateWeight('<?php echo ($a["ebay_id"]); ?>','<?php echo ($sku["sku"]); ?>','<?php echo ($a["abnormal"]["abnormal"]); ?>','<?php echo ($a["abnormal"]["abnormal_id"]); ?>',this)"><?php echo ($sku["weight"]); ?>kg</span>
                                <span style="width: 5%;color: #0000cc;cursor:pointer;" onclick="lookLogs('<?php echo ($sku["sku"]); ?>')">日志</span>
                                <span style="width: 430px;margin-left: 1%"><?php echo ($sku["goods_name"]); ?></span>
                                </div>
                                <br style="margin-top: 8px"><?php endforeach; endif; ?>
                        </div>
                        <div class="form-inline" style="margin-top: 7px">
                            <div class="input-group">
                                <div class="input-group-addon">选择异常原因</div>
                                <select id="traceStatus_<?php echo ($a["ebay_id"]); ?>" class="traceStatus">
                                    <option value=""></option>
                                    <?php if(is_array($traceStatus)): foreach($traceStatus as $k=>$statusName): ?><option value="<?php echo ($k); ?>" <?php if($k == $a['abnormal']['abnormal']): ?>selected<?php endif; ?> ><?php echo ($statusName); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                            <button onclick="saveTrace('<?php echo ($a["ebay_id"]); ?>')" class="btn btn-warning btn-sm"  type="button">保存</button>
                        </div>
                    </td>
                    <td style="text-align: left;" id="abnormal_<?php echo ($a["ebay_id"]); ?>">
                        <?php if(($a["abnormal"]["addtime"]) != ""): echo ($a["abnormal"]["addtime"]); endif; ?>
                    </td>
                </tr><?php endforeach; endif; ?>
        </table>
    </div>
    <script>
        $(function () {
            $("#ebay_id").select().focus();
            $('.traceStatus').chosen({search_contains: true, width: '180px', allow_single_deselect: true});
        });

        function blankUrl(ebay_id){
            var url = "http://erp.spocoo.com/t.php?s=/Orders/Index/index&search_field=ebay_id&search_value="+ebay_id;
            window.open(url);
        }
        /**
         * 扫描
         * @param event
         * @returns {boolean}
         */
        function check(event) {

            var ebay_id = $.trim($("#ebay_id").val());
            var isie = (document.all) ? true : false;//判断是IE内核还是Mozilla
            var keyCode;
            if (isie) {
                keyCode = window.event.keyCode;//IE使用windows.event事件
            } else {
                keyCode = event.which;//3个按键函数有一个默认的隐藏变量，这里用e来传递。e
            }
            var data = $('form').closest('form').serialize();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            if (keyCode == 13) {
                var da = '<?php echo ($data["0"]["ebay_id"]); ?>';
                var status = '<?php echo ($data["0"]["abnormal"]["abnormal"]); ?>';
                if(0){
                    layer.confirm("上一个扫描的还未处理是否继续？", {
                        btn: ["确定","取消"] //按钮
                    }, function(index){
                        var url = "<?php echo U('Trace/index');?>&scanNumber=" + ebay_id + "&" +data;
                        location.href = url;
                    }, function(){
                        var index=parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    });
                }else{
                    var url = "<?php echo U('Trace/index');?>&scanNumber=" + ebay_id + "&" +data;
                    location.href = url;
                }
                return false;
            }
        }

        function lookLogs(sku){
            layer.open({
                type: 2,
                title: 'SKU修改重量日志',
                area: ['900px','500px'],
                shade: 0.8,
                shadeClose: true,
                content: "<?php echo U('Order/Trace/updateWeight/sku/"+sku+"');?>"
            });
        }
        function updateWeight(ebay_id,sku,status,abnormal_id,that){

            $('.sku_weight').remove();
            var time = $('#abnormal_'+ebay_id).html();
            if(time == '' || status != '1'){
                layer.alert('当前异常状态不允许修改重量！');
                return false;
            }
            var weight  = $(that).attr('data-weight');
            sku = "'"+sku+"'";var nebay_id = "'"+ebay_id+"'";abnormal_id = "'"+abnormal_id+"'";weight = "'"+weight+"'";

            var html = '<div class="form-inline sku_weight" style="margin-left: 18%;width: 40%;">'
                    +'<div class="input-group">'
                    +'<input type="text" class="form-control input-sm" id="sku_weight_'+ebay_id+'" style="width: 100px;" placeholder="请填写重量">'
                    +'<div class="input-group-addon">g</div></div>'
                    +'<button onclick="saveWeight('+sku+','+nebay_id+','+abnormal_id+','+weight+')" class="btn btn-primary btn-sm" id="search-btn" type="button">保存修改</button>'
                    +'<button style="margin-left: 3px" class="btn btn-danger btn-sm" onclick="quxiao()" type="button">取消</button></div>';
            $(that).parent().append(html);
            $("#sku_weight_"+ebay_id).select().focus();
        }

        function quxiao(){
            $('.sku_weight').remove();
            $("#ebay_id").select().focus();
        }

        function saveWeight(sku,ebay_id,abnormal_id){

            var weight = $.trim($('#sku_weight_'+ebay_id).val());
            sku = $.trim(sku);
            ebay_id = $.trim(ebay_id);
            abnormal_id = $.trim(abnormal_id);

            if(weight == ''){
                layer.alert('请填写重量！');
                return false;
            }

            if(!(/(^[1-9]\d*$)/.test(weight))){
                layer.alert('请输入正整数！');
                return false;
            }

            $.ajax({
                type : "post",
                url :  "t.php?s=/Order/Trace/updateWeight",
                data : {ebay_id:ebay_id,sku:sku,weight:weight,abnormal_id:abnormal_id},
                success : function(data) {
                    if(data.status){
                        layer.msg(data.msg, {
                            icon: 1,
                            time: 1000
                        }, function(){
                            window.location.reload();
                        });
                    }else{
                        layer.alert(data.msg);
                    }
                }
            });
        }

        function saveTrace(ebay_id){

            var traceStatus = $('#traceStatus_'+ebay_id).find("option:selected").val();

            if(traceStatus == ''){
                layer.alert('请选择异常原因！');
                return false;
            }
            $.ajax({
                type : "post",
                url :  "t.php?s=/Order/Trace/saveAbnormal",
                data : {ebay_id:ebay_id,traceStatus:traceStatus},
                success : function(data) {
                    if(data.status){
                        layer.msg(data.msg, {
                            icon: 1,
                            time: 1000
                        }, function(){
                            window.location.reload();
                        });
                    }else{
                        layer.alert(data.msg);
                    }
                }
            });
        }

        function searchForm(obj){

            var data = $(obj).closest('form').serialize();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            var url = '/t.php?s=/Order/Trace/index';
            if (url.indexOf('?') > 0) {
                url += '&' + data;
            } else {
                url += '?' + data;
            }
            location.href = url;
        }


        function exports(obj,type){
            var data = $(obj).closest('form').serialize();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            var url = '/t.php?s=/Order/Trace/exports';
            if (url.indexOf('?') > 0) {
                url += '&' + data;
            } else {
                url += '?' + data;
            }
            url += '&type='+type;
            window.open(url);
        }
    </script>

</body>
</html>