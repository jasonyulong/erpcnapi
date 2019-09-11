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
            #tables{border:1px solid;display: none}
            #tables tr td{line-height: 1.5;border:1px solid #ccc;text-align: center;}
            #tables tr td span{float: left;}
            tr.mingxi td{font-size:11px; padding: 5px;}
        </style>
    <div class="ibox float-e-margins" style="margin:0 auto;height: 100%;padding:20px;width:100%;background:#fff;">
        <table id="tables" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="25%">可打印</td>
                <td width="25%">待打印</td>
                <td width="25%">待扫描(待包装)</td>
                <td width="25%">待称重</td>
            </tr>
        </table>
        <div id="type_count" style="color:#911"></div>
        <div id="noInit" style="color:#911">
            &nbsp;&nbsp;<?php if(strstr($_SESSION['tname'],'测试') || strstr($_SESSION['tname'],'程序'))echo "没初始化：$noInit "; ?>
        </div>
        <form>
            <div class="form-inline">
                <div class="input-group">
                    <div class="input-group-addon">订单号</div>
                    <input type="text" name="content" class="form-control input-sm" style="width:120px" value="<?php echo ($request["content"]); ?>">
                    <select name="field" data-placeholder="" >
                        <?php foreach($fields as $fieldVal=>$fieldName){ ?>
                        <option value="<?php echo ($fieldVal); ?>"  <?php if($fieldVal == $request['field'])echo 'selected' ?>><?php echo ($fieldName); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="input-group">
                    <div class="input-group-addon">SKU</div>
                    <input type="text" name="sku" id="sku" class="form-control input-sm" style="width:150px" onkeydown="changShowSku(event,this)" value="<?php echo ($request["sku"]); ?>">
                </div>

                <div class="input-group">
                    <div class="input-group-addon">wms更新时间</div>
                    <input type="text" class="form-control input-sm" name="w_add_time_start" style="width: 100px;" placeholder="开始日期" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" value="<?php echo ($request["w_add_time_start"]); ?>">
                    <div class="input-group-addon">至</div>
                    <input type="text" class="form-control input-sm" name="w_add_time_end" style="width: 100px;" placeholder="结束日期" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" value="<?php echo ($request["w_add_time_end"]); ?>">
                </div>
                <div class="input-group">
                    <div class="input-group-addon">发货状态</div>
                    <select id="deliver_goods" data-placeholder="订单状态"  onchange="deliverGoods($(this));return false">
                        <option value=""></option>
                        <option value="1" <?php if($deliver_goods == 1): ?>selected<?php endif; ?> >未发货</option>
                        <option value="2"  <?php if($deliver_goods == 2): ?>selected<?php endif; ?>>已发货</option>
                        <option value="3"  <?php if($deliver_goods == 3): ?>selected<?php endif; ?>>回收站</option>
                    </select>

                </div>
                <div class="input-group">
                    <div class="input-group-addon">订单类型</div>
                    <select id="order_type"  data-placeholder="订单类型">
                        <option value="0">未选择</option>
                        <option value="1" <?php if($request['order_type'] == 1): ?>selected<?php endif; ?> >单品单货</option>
                        <option value="2"  <?php if($request['order_type'] == 2): ?>selected<?php endif; ?>>单品多货</option>
                        <option value="3"  <?php if($request['order_type'] == 3): ?>selected<?php endif; ?>>多品多货</option>
                    </select>
                </div>

                <div class="input-group">
                    <div class="input-group-addon">订单平台</div>
                    <select id="platform"  data-placeholder="订单平台" multiple="">
                        <option value="0">未选择</option>
                        <?php
 foreach($platformArr as $platform){ ?>
                        <option value="<?php echo ($platform); ?>" <?php if(in_array($platform,$requestPlatform))echo 'selected="selected"';?> ><?php echo ($platform); ?></option>
                        <?php
 } ?>
                    </select>
                </div>

                <div class="input-group">
                    <div class="input-group-addon">异常操作次数</div>
                    <select id="erp_op_id" data-placeholder="异常操作次数">
                        <option value=""></option>
                        <option value="1"  <?php if($erp_op_id == 1): ?>selected<?php endif; ?>>1次</option>
                        <option value="2"  <?php if($erp_op_id == 2): ?>selected<?php endif; ?>>2次</option>
                        <option value="3"  <?php if($erp_op_id == 3): ?>selected<?php endif; ?>>3次</option>
                        <option value="4"  <?php if($erp_op_id == 4): ?>selected<?php endif; ?>>4次以上</option>
                    </select>
                </div>
                <div class="input-group" id="checkNo" <?php if($deliver_goods != 1): ?>style="display:none"<?php endif; ?> >
                <div class="input-group-addon">订单状态</div>
                <?php $request['ebay_status'] = explode(',',$_GET['ebay_status']); ?>
                <select id="ebay_status" data-placeholder="订单状态" multiple="">
                    <option value=""></option>
                    <?php foreach($topMenus as $id=>$name){ ?>
                    <option value="<?php echo ($id); ?>"  <?php if(in_array($id,$request['ebay_status']))echo 'selected' ?>><?php echo ($name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <input type="hidden" name="sort_name"  value="<?php echo ($request["sort_name"]); ?>">
            <input type="hidden" name="sort_value" value="<?php echo ($request["sort_value"]); ?>">
            <button onclick="searchForm(this)" class="btn btn-primary btn-sm" id="search-btn" type="button">搜索</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button onclick="exports(this);return false;" class="btn btn-primary btn-sm" id="export-btn" type="button" style="background-color: #ccc;border:1px solid #ccc">导出</button>
    </div>

    </form>
    <div class="btn-group" style="margin-top: 10px;">
        <button type="button" class="btn btn-sm btn-info" onclick="printPicklist()">再次打印拣货清单</button>
    </div>
    <div class="btn-group" style="margin-top: 10px;">
        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="badge box_total">0</span> 批量操作<span class="caret"></span>
        </button>
        <ul class="dropdown-menu">

            <!--  这里不再需要了 ，只需要下面的 再次拣货
                      <li>
                                <a href="javascript:void(0);" onclick="editStatus()">修改状态</a>
                                <div class="input-group" id="order_status" style="padding: 3px 20px;display: none;">
                                    <select  data-placeholder="订单状态" onchange="changeStatus($(this));return false;">
                                        <option value="0">未选择</option>
                                        <option value="1723">可打印</option>
                                        <option value="1745">等待打印</option>
                                        <option value="1724">等待扫描</option>
                                    </select>
                                </div>
                            </li>-->
            <li><a href="javascript:void(0);" onclick="AllowCreatePickOrder(0)">转到可打印</a></li>
            <li><a href="javascript:void(0);" onclick="AllowCreatePickOrder(3)">转到拣货打回</a></li>
        </ul>
    </div>
    <div class="btn-group"  style="margin-top: 10px;margin-left: 20px">
            <span class="page-list">每页显示 <span class="btn-group ">
                 <select id="pageSize" name="pageSize" onchange="searchForm(this)">
                     <option value="100" <?php if($request['pageSize'] == 100): ?>selected<?php endif; ?> >100</option>
                     <option value="500"  <?php if($request['pageSize'] == 500): ?>selected<?php endif; ?>>500</option>
                     <option value="1000"  <?php if($request['pageSize'] == 1000): ?>selected<?php endif; ?>>1000</option>
                 </select>
            </span> 条记录</span>
    </div>
    <ul style="margin-left: 20px" class="pagination"><?php echo ($show); ?></ul>
    <table class="table custom-table">
        <thead>
        <th width="5%" style="text-align: left;">
            <input type="checkbox" name="checkAll" class="checkAllBox" onclick="checkAll(this)" /> 全选
        </th>
        <th width="10%" style="text-align: left;">订单号</th>
        <th width="10%" style="text-align: left;">平台</th>
        <th width="18%" style="text-align: left;">运输方式</th>
        <th width="18%" style="text-align: left;">跟踪号</th>
        <th width="18%" style="text-align: left;">客户姓名</th>
        <th width="18%" style="text-align: left;">
            <a href="javascript:;" onclick="sort(this)" data-name="w_update_time" data-value="<?php echo ($request['sort_value'] == 'desc' ? 'asc' : 'desc'); ?>">
                wms更新时间
                <?php if($request['sort_name'] == 'w_update_time'){ ?>
                <?php switch($request["sort_value"]): case "asc": ?><i class="glyphicon glyphicon-arrow-up"></i><?php break;?>
                    <?php case "desc": ?><i class="glyphicon glyphicon-arrow-down"></i><?php break; endswitch;?>
                <?php } ?>
            </a>
        </th>
        <th width="18%" style="text-align: left;">订单类型</th>
        <th width="18%" style="text-align: left;">状态</th>
        </thead>
        <?php foreach($orders as $v){ ?>
        <tr>
            <td style="text-align: left;">
                <input type="checkbox" name="checkItem" value="<?php echo ($v['ebay_id']); ?>" />
            </td>
            <td style="text-align: left;"><?php echo ($v['ebay_id']); ?></td>
            <td style="text-align: left;"><?php echo ($v['ordertype']); ?></td>
            <td style="text-align: left;"><?php echo ($v['ebay_carrier']); ?></td>
            <td style="text-align: left;"><?php echo ($v['ebay_tracknumber']); if(!empty($v['pxorderid'])){ ?><br>(<font color="red">pxId:<?php echo ($v['pxorderid']); ?>)</font><?php } ?></td>
            <td style="text-align: left;"><?php echo ($v['ebay_username']); ?></td>
            <td style="text-align: left;"><?php echo ($v['w_update_time']); ?></td>
            <td style="text-align: left;">
                <?php if($v['type'] == 1): ?>单品单货<?php endif; ?>
                <?php if($v['type'] == 2): ?>单品多货<?php endif; ?>
                <?php if($v['type'] == 3): ?>多品多货<?php endif; ?>
            </td>
            <td style="text-align: left;">
                <?php echo ($topMenus[$v['ebay_status']]); ?><br>
                <a style="margin-left:40px; text-decoration: none;" href="javascript:void(0)" onclick="showAllLog(<?php echo ($v['ebay_id']); ?>)">查看日志</a>
            </td>
            <!--<td style="text-align: left;"><?php echo ($allStatus[$v['status']]); ?></td>-->
        </tr>
        <?php } ?>
    </table>
    <ul class="pagination"><?php echo ($show); ?></ul>
    </div>
    <script src="/newerp/Public/js/qrcode.js"></script>
    <script>

        $(function () {
            $('#pageSize').chosen({search_contains: true, width: '70px', allow_single_deselect: true,disable_search: true});
            $('#ebay_status').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $('#deliver_goods').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $('#order_type').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $('#platform').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $('#erp_op_id').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $('select[name=field]').chosen({search_contains: true, width: '120px', allow_single_deselect: true});
            $(".dropdown-toggle").click(function(){
                var display = $(".dropdown-menu").css("display");
                var count = parseInt($.trim($(".dropdown-toggle .box_total").html()));
                if(count == 0){
                    layer.msg("请选择订单进行操作");
                    return;
                }
                if(display == "none"){
                    $(".dropdown-menu").show();
                }else{
                    $(".dropdown-menu").hide();
                }
            });
            $("input[name='checkItem']").click(function () {
                var bool=$(this).prop("checked");
                var count = parseInt($.trim($(".dropdown-toggle .box_total").html()));
                if(bool){
                    count++;
                }else{
                    count--;
                }
                $(".dropdown-toggle .box_total").html(count);
            });
            //获取订单状态明细
            getOrderStatusList();
            // get1723Count();


            // 避免session 过期
            setInterval(function(){
                var url='<?php echo U("Order/Test/ttt");?>';
                $.post(url,{},function(data){

                });
            },300000)

        });

        //过滤数据
        function deliverGoods(_this){
            var tid = _this.find("option:selected").val();
            if(tid == 1){
                $("#checkNo").show();
            }else{
                $("#checkNo").hide();
            }
        }

        /*
         * 搜索
         */
        function searchForm(obj) {
            var sku = $.trim($('#sku').val());
            var data = $(obj).closest('form').serialize();
            var ebay_status = $('#ebay_status').val();
            var deliver_goods = $('#deliver_goods').find("option:selected").val();
            var order_type = $('#order_type').find("option:selected").val();
            var platform = $('#platform').val();
            var erp_op_id = $('#erp_op_id').find("option:selected").val();
            var pageSize= $('#pageSize').find("option:selected").val();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            var url = '/t.php?s=/Order/Order/index';
            if (url.indexOf('?') > 0) {
                url += '&' + data;
            } else {
                url += '?' + data;
            }

            if(platform==null){
                platform='';
            }

            if(sku){
                var strsss = ['1723','1745','1724','2009'];
                var str = ''+ebay_status+'';
                var array=str.split(",");
                if(array.length>0){
                    if(array.length>1){
                        layer.alert('SKU搜索订单状态必须在可打印，等待打印，等待扫描，待称重中之一');
                        return false;
                    }
                    for(var i=0;i<array.length;i++){
                        if(!strsss.in_array(array[i])){
                            layer.alert('SKU搜索订单状态必须在可打印，等待打印，等待扫描，待称重中之一');
                            return false;
                        }
                    }
                }

            }
            //获取设置值
            if(deliver_goods == 1){
                if(ebay_status == '' || ebay_status == null){
                    url += '&ebay_status=1723,1745,1724,2009';
                }else{
                    url += '&ebay_status='+ebay_status;
                }
            }else if(deliver_goods == 2){
                url += '&ebay_status=2';  //不知道什么状态

            }else if(deliver_goods == 3){
                url += '&ebay_status=1731';  //不知道什么状态
            }

            deliver_goods = deliver_goods == '' ? '' : deliver_goods;
            url +=  "&deliver_goods="+deliver_goods+"&order_type="+order_type+"&pageSize="+pageSize+"&erp_op_id="+erp_op_id;
            url += "&platform="+platform;
            location.href = url;
        }

        Array.prototype.in_array=function(e){
            var r=new RegExp(','+e+',');
            return (r.test(','+this.join(this.S)+','));
        };

        /**
         * 排序
         * @author Simon 2017-11-30
         * @return{[type]}[description]
         */
        function sort(obj){
            var sort_name = $(obj).data('name');
            var sort_value = $(obj).data('value');
            $('input[name=sort_name]').val(sort_name);
            $('input[name=sort_value]').val(sort_value);
            $('#search-btn').click();
        }


        function exports(obj){
            var data = $(obj).closest('form').serialize();
            var ebay_status = $('#ebay_status').val();
            var deliver_goods = $('#deliver_goods').find("option:selected").val();
            var order_type = $('#order_type').find("option:selected").val();
            var platform = $('#platform').val();
            var erp_op_id = $('#erp_op_id').find("option:selected").val();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            var url = '/t.php?s=/Order/Order/exportStatusOrder';
            if (url.indexOf('?') > 0) {
                url += '&' + data;
            } else {
                url += '?' + data;
            }

            if(platform==null){
                platform='';
            }

            //获取设置值
            if(deliver_goods == 1){
                if(ebay_status == '' || ebay_status == null){
                    url += '&ebay_status=1723,1745,1724,2009';
                }else{
                    url += '&ebay_status='+ebay_status;
                }
            }else if(deliver_goods == 2){
                url += '&ebay_status=2';  //不知道什么状态

            }else if(deliver_goods == 3){
                url += '&ebay_status=1731';  //不知道什么状态
            }

            deliver_goods = deliver_goods == '' ? '' : deliver_goods;
            url +=  "&deliver_goods="+deliver_goods+"&order_type="+order_type+"&erp_op_id="+erp_op_id;
            url += "&platform="+platform;
            window.open(url,'_blank');
        }

        /**
         * 全选、反选
         * @param that
         * @author Shawn
         */
        function checkAll(that){
            var bool=$(that).prop("checked");
            $("input[name='checkItem']").prop("checked",bool);
            var checkedItems = $('td input:checked');
            var checkedValue = [];

            $.each(checkedItems, function(key, item) {
                checkedValue.push($(item).val());
            });
            $(".dropdown-toggle .box_total").html(checkedValue.length);
            // var checkedSkuStr = checkedValue.join(',');
        }
        function editStatus() {
            var display = $("#order_status").css("display");
            if(display == "none"){
                $("#order_status").show();
            }else{
                $("#order_status").hide();
            }
        }

        /**
         * 修改订单状态
         * @param obj
         * @returns {boolean}
         */
        function changeStatus(obj) {
            var status = $.trim(obj.val());
            var text = obj.find("option:selected").text();
            var info = "是否确认修改订单状态为：" + text + "!!!";
            var r = confirm(info);
            if (r == true)
            {
                var loadingIndex = layer.load(1, {shade:0.5});
                //找到订单
                var checkedItems = $('td input:checked');
                var checkedValue = [];

                $.each(checkedItems, function(key, item) {
                    checkedValue.push($(item).val());
                });
                var checkedIdStr = checkedValue.join(',');
                $.ajax({
                    url: '<?php echo U("Order/editOrderStatus");?>',
                    type: 'post',
                    data: {ebay_id: checkedIdStr,status:status},
                    dataType: 'json'
                }).done(function (result) {
                    layer.msg(result.msg);
                    // 标记成功后刷新
                    if (result.status == 1) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    }
                }).fail(function () {
                    layer.msg('访问失败.');
                }).always(function () {
                    layer.close(loadingIndex);
                });
                $("#order_status").hide();
            }
            else
            {
                $("#order_status").hide();
                return false;
            }
        }

        /**
         * 让订单可再次拣货
         */

        /**
         *创建人: 测试人员谭 2018-07-17 20:25:09
         *说明: 允许再次生成拣货单子
         */
        function AllowCreatePickOrder(status) {
            //找到订单
            var checkedItems = $('td input:checked');
            var checkedValue = [];

            var i=0;
            $.each(checkedItems, function(key, item) {
                checkedValue.push($(item).val());
                i++;
            });
            var checkedIdStr = checkedValue.join(',');

            var str='';
            if(3==status){
                str+="警告:您讲进行如下操作:\n";
                str+="1.订单强行从拣货单中异常结束!\n";
                str+="2.订单将变成可打印!\n";
                str+="3.订单拣货状态变成 【拣货打回】!\n";
                // str+="您操作的订单是：\n\n"+checkedIdStr+"\n";
                str+=" 一共 "+i+" 单 \n ";
                str+="您确认操作吗?";
            }else{
                str+="警告:您讲进行如下操作:\n";
                str+="1.订单强行从拣货单中异常结束!\n";
                str+="2.订单将变成可打印!\n";
                str+="3.订单拣货状态变成【未生成拣货单】!\n";
                // str+="您操作的订单是：\n\n"+checkedIdStr+"\n";
                str+=" 一共 "+i+" 单 \n ";
                str+="您确认操作吗?";
            }


            if(!confirm(str)){
                return false;
            }
            var url='t.php?s=/Package/PickBack/BattchSetOrder2WaitPick';

            layer.load(1);

            $.post(url,{bill:checkedIdStr,"status":status},function(data){
                layer.closeAll();
                layer.open({
                    type: 1,
                    area: ['770px', '480px'], //宽高
                    content: data
                });
            })

        }


        function showAllLog(ebay_id){
            layer.open({
                type: 2,
                title: '查看日志',
                area: ['900px','500px'],
                shade: 0.8,
                shadeClose: true,
                content: "<?php echo U('Order/Index/showLog/ebay_id/"+ebay_id+"');?>",
            });
        }


        /**
         *测试人员谭 2018-07-20 17:38:38
         *说明: 各种状态 里面的 1723 查一下
         */
        function get1723Count(){
            var url='./t.php?s=/Order/Order/get1723Count';


            $.post(url,{},function(data){
                $("#type_count").html(data);
            })
        }

        //day打印拣货清单
        function printPicklist(){
            var ebay_status = $('#ebay_status').val();
            var order_type = $('#order_type').find("option:selected").val();
            if(order_type != 1 && order_type != 2){
                alert('订单类型必须选择单品单货或单品多货其中之一！');
                return false;
            }

            if(ebay_status != '1745' && ebay_status !=1724){
                alert('订单状态必须为等待打印！');
                return false;
            }
            //找到订单
            var checkedItems = $('td input:checked');
            var checkedValue = [];

            var i=0;
            $.each(checkedItems, function(key, item) {
                checkedValue.push($(item).val());
                i++;
            });
            var checkedIdStr = checkedValue.join(',');

            if(i > 0){
                var url = "<?php echo U('Order/Order/printPicklist/ebay_id/"+checkedIdStr+"/order_type/"+order_type+"');?>";
            }else{
                var url = "<?php echo U('Order/Order/printPicklist/',$_GET);?>";
            }
            window.open(url);
        }

    /**
     * 获取订单明细
     * @author Shawn
     * @returns {boolean}
     */
    function getOrderStatusList() {
        var obj = $("#search-btn");
        var data = $(obj).closest('form').serialize();
        var ebay_status = $('#ebay_status').val();
        var deliver_goods = $('#deliver_goods').find("option:selected").val();
        var order_type = $('#order_type').find("option:selected").val();
        data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
        data     = data.replace(/^&/g, '');

        var url = '/t.php?s=/Order/Order/getOrderStatusList';
        if (url.indexOf('?') > 0) {
            url += '&' + data;
        } else {
            url += '?' + data;
        }
        //获取设置值
        if(deliver_goods == 1 || deliver_goods == ""){
            if(ebay_status == '' || ebay_status == null){
                url += '&ebay_status=1723,1745,1724,2009';
            }else{
                url += '&ebay_status='+ebay_status;
            }
        }else{
            $("#tables").hide();
            return false;
        }
        deliver_goods = deliver_goods == '' ? '' : deliver_goods;
        url +=  "&deliver_goods="+deliver_goods+"&order_type="+order_type;
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function (result) {
            if (result.status == 1) {
                var data = result['data'];
                var html = '<tr ><td width="25%">'+data["1723"]["count"]+'</td>';
                html += '<td width="25%">'+data["1745"]["count"]+'</td>';
                html += '<td width="25%">'+data["1724"]["count"]+'</td>';
                html += '<td width="25%">'+data["2009"]["count"]+'</td></tr>';
                html += '<tr class="mingxi"><td><span>明细：</span><br/>';
                html += '<span>没初始化：'+data["1723"]["noInit"]+'</span><br/>';
                html += '<span>待生成拣货单：'+data["1723"]["noOrder"]+'</span><br/>';
                html += '<span>已生成拣货单：'+data["1723"]["isOrder"]+'</span><br/>';
                html += '<span>拣货打回：'+data["1723"]["backOrder"]+'</span></td>';
                html += '<td><span>明细：</span><br/>';
                html += '<span>单品单货：'+data["1745"][1]+'</span><br/>';
                html += '<span>单品多货：'+data["1745"][2]+'</span><br/>';
                html += '<span>多品多货：'+data["1745"][3]+'</span></td>';
                html += '<td><span>明细：</span><br/>';
                html += '<span>单品单货：'+data["1724"][1]+'</span><br/>';
                html += '<span>单品多货：'+data["1724"][2]+'</span><br/>';
                html += '<span>多品多货：'+data["1724"][3]+'</span></td>';
                html += '<td> </td></tr>';
                $("#tables").append(html);
                $("#tables").show();
            }
        }).fail(function () {
            layer.msg('访问失败.');
        })
    }
    </script>

</body>
</html>