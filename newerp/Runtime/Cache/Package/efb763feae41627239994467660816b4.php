<?php if (!defined('THINK_PATH')) exit();?>
    <?php include ROOT_PATH."/include/config.php"; ?>
    <?php require_once ROOT_PATH.'/top.php' ?>

<link href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css" rel="stylesheet">
<link href="/newerp/Public/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/newerp/Public/css/plugins/iCheck/custom.css" rel="stylesheet">
<!-- morris -->

<!-- fullcalendar -->
<link href="/newerp/Public/css/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
<link href="/newerp/Public/css/plugins/fullcalendar/fullcalendar.print.css" rel="stylesheet">

<link href="/newerp/Public/css/animate.css" rel="stylesheet">
<link href="/newerp/Public/css/style.css" rel="stylesheet">

<!-- Data Tables -->
<!--<link href="/newerp/Public/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">-->
<link href="/newerp/Public/css/custom.css" rel="stylesheet">


    <style type="text/css">

        .btn {
            border-radius: 2px;
            margin-left: 20px;
        }
        .colors{font-size:14px;color:#0066cc}
        .form-group select,label{
            margin-left: 10px;
            font-size: 18px;
            color: #777;
            font-weight: unset;
            line-height: 2;
            display: inline-block;
            vertical-align: middle;
        }
        .table-responsive table {
            border-collapse: collapse;
            border: 1px #e3e3e3 solid;
        }

        .table-responsive th, td {
            border: 1px solid #e3e3e3;
            background: white;
        }

        .table-responsive th {
            background: #199fff;
            color: white;
        } /*模拟对角线*/
        .out {
            border-top: 3em #199fff solid; /*上边框宽度等于表格第一行行高*/
            border-left: 160px #ff8838 solid; /*左边框宽度等于表格第一行第一格宽度*/
            position: relative; /*让里面的两个子容器绝对定位*/
            color:white;
            font-size: 16px;
        }

        .out b {
            font-style: normal;
            display: block;
            position: absolute;
            top: -3.3em;
            left: -70px;
            width: 160px;
        }

        .out em {
            font-style: normal;
            display: block;
            position: absolute;
            top: -30px;
            left: -156px;
            width: 160px;
        }
        .date_show{
            white-space: pre;
            width: 100px;
            text-align: center;
        }
        .table-responsive table tbody tr{
            text-align: center;
        }
        .show_count{
            color: green;
            font-size: 18px;
            font-weight: bold;
        }
        .total{
            color: #337ab7;
            font-size: 18px;
            font-weight: bold;
        }
        .table-responsive{
            overflow: auto;
        }
    </style>


<style>
    .headerList{padding:0!important;border-bottom:none!important;}
    .nav>li>a.J_menuItem{padding: 6px 20px 6px 25px;}
    .nav>li>a.J_menuItem:hover{background:#E0E0E0!important;}
    .form-control{padding:2px 0 2px 2px;height: 28px;}
</style>

<div id="wrapper">
    <!-- 侧边菜单-开始 -->
    <nav class="navbar-default navbar-static-side " role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                
                    <!--<?php if(can('view_pick_carrier_group')): ?>-->
                        <!--<li>-->
                            <!--<a href="<?php echo U('Package/CarrierGroup/groupList');?>">-->
                                <!--<i class="fa fa fa-cubes"></i>-->
                                <!--<span class="nav-label">运输方式分组</span>-->
                                <!--&lt;!&ndash;<span class="fa arrow"></span>&ndash;&gt;-->
                            <!--</a>-->
                        <!--</li>-->
                    <!--<?php endif; ?>-->

                    <?php if(true): ?><!--   <li>
                            <a href="<?php echo U('Package/UserPackageFee/showStatistic');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">订单包装费用统计</span>
                            </a>
                        </li>-->
                        <!--<li>
                            <a href="<?php echo U('Package/UserPackageFee/resetPackageFee');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">重置订单包装费用</span>
                            </a>
                        </li>-->
                     <!--   <li>
                            <a href="<?php echo U('Package/UserPackageFee/pickerStatistic');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">捡货统计</span>
                            </a>
                        </li>-->

                       <!-- <li>
                            <a href="<?php echo U('Package/UserPackageFee/pickerCount');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">捡货统计2</span>
                            </a>
                        </li>-->

                        <li>
                            <a href="<?php echo U('Package/TwoPickCount/index');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">二次分拣统计</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo U('Package/Statistics/showPackerStatistics');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">包装统计<span class="label label-primary">新</span></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo U('Package/Statistics/showPickerStatistics');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">拣货统计<span class="label label-primary">新</span></span>
                            </a>
                        </li>

                        <li>
                            <a href="/scanorder_baoiao.php">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">订单扫描统计</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo U('Package/Statistics/showPackSubsidyStatistics');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">包装补贴统计</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo U('Package/Statistics/showPickingAbnormalityStatistics');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">拣货异常sku统计</span>
                            </a>
                        </li>
                        <?php if(can('orderOutStoreStatistics')): ?><li>
                                <a href="<?php echo U('Package/Statistics/orderOutStoreStatistics');?>">
                                    <i class="fa fa fa-cubes"></i>
                                    <span class="nav-label">订单出库报表</span>
                                </a>
                            </li><?php endif; ?>
                        <?php if(can('skuOutStoreStatistics')): ?><li>
                                <a href="<?php echo U('Package/Statistics/skuOutStoreStatistics');?>">
                                    <i class="fa fa fa-cubes"></i>
                                    <span class="nav-label">sku出库报表</span>
                                </a>
                            </li><?php endif; ?>
                        <?php if(can('orderBaseAnalysisStatistics')): ?><li>
                                <a href="<?php echo U('Package/Statistics/orderBaseAnalysis');?>">
                                    <i class="fa fa fa-cubes"></i>
                                    <span class="nav-label">订单基数分析报表</span>
                                </a>
                            </li><?php endif; ?>
                        <?php if(can('weightFailureStatistics')): ?><li>
                                <a href="<?php echo U('Package/Statistics/weightFailureIndex');?>">
                                    <i class="fa fa fa-cubes"></i>
                                    <span class="nav-label">称重失败统计表</span>
                                </a>
                            </li><?php endif; ?>
                        <?php if(can('abnormalOrderStatistics')): ?><li>
                                <a href="<?php echo U('Package/Statistics/abnormalOrderIndex');?>">
                                    <i class="fa fa fa-cubes"></i>
                                    <span class="nav-label">异常订单报表</span>
                                </a>
                            </li><?php endif; endif; ?>
                
            </ul>
        </div>
    </nav>
    <!-- 侧边菜单-结束 -->

    <!-- 头部导航-开始 -->
    <div id="page-wrapper" style="background: #fff;">
        <div class="row">
            <div class="col-sm-12" style="padding: 0 !important;">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom:0;min-height:30px;">
                    <ul class="nav navbar-top-links navbar-left" style="margin:0px;">
                        
    <li>
        <a class="m-r-xs text-muted welcome-message">&nbsp;&nbsp;<span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;当前位置：</a>
    </li>
    <li>
        <a class="m-r-xs"> 包装统计（新）</a>
    </li>

                    </ul>
                </nav>
            </div>
        </div>
        <!-- 头部导航-结束 -->

        
    <!--头部条件搜索框-->
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="javaScript:void(0);">时间</a>
            </div>
            <form class="navbar-form navbar-left" action="/t.php?s=/Package/Statistics/showPackerStatistics" method="post" role="search" onSubmit="return checkDate();">
                <div class="form-group">
                    <input type="text" class="form-control"  value="<?php echo ($rawCondition['timeArea_start']); ?>" name="timeArea_start" placeholder="开始时间点" onclick="laydate({format:'YYYY-MM-DD'})" id="timeArea_start"/>
                    <b style="margin: 0 10px 0 10px;">——</b>
                    <input type="text" class="form-control" value="<?php echo ($rawCondition['timeArea_end']); ?>" name="timeArea_end" onclick="laydate({format:'YYYY-MM-DD'})" placeholder="结束时间点" id="timeArea_end"/>
                    <label for="packType">订单类型</label>
                    <select name="packType" id="packType">
                        <?php if(is_array($typeInfo)): foreach($typeInfo as $key=>$type): ?><option value="<?php echo ($key); ?>" <?php if($packType == $key): ?>selected<?php endif; ?>><?php echo ($type); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-info">搜索</button>
                <button type="button" class="btn btn-warning" onclick="exportExcel()">导出</button>
            </form>
        </div>
    </nav>
    <div class="table-responsive" id="contentArr">
        <table class="table">
            <thead>
            <tr>
                <th class="date_show" style="vertical-align: middle;width: 50px;">序号</th>
                <th  width="160px;">
                    <div class="out">
                        <b>日期</b> <em>包装员</em>
                    </div>
                </th>
                <?php if(is_array($dateData)): $i = 0; $__LIST__ = $dateData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$day): $mod = ($i % 2 );++$i;?><th class="date_show"  style="vertical-align: middle;"><?php echo ($day); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                <th class="date_show" style="vertical-align: middle;">合计</th>
            </thead>
            <tbody>
            <?php $number=1; ?>
            <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
                    <td><?php echo $number++; ?></td>
                    <td><?php echo ($key); ?>（<?php echo ($vo["packerId"]); ?>）</td>
                    <?php $count=0; foreach($dateData as $today) { ?>
                    <td>
                        <?php
 if(in_array($today,array_keys($vo['arr']))){ $num = $vo['arr'][$today]; $count = $count + $num; echo "<span class='show_count'>".$num."</span>"; }else{ echo 0; } ?>
                    </td>
                    <?php }?>
                    <td class="total"><?php echo ($count); ?></td>
                </tr><?php endforeach; endif; ?>
            <tr>
                <td></td>
                <td>合计</td>
                <?php $counts=0; foreach($dateData as $value) { ?>
                <td>
                    <?php
 if(in_array($value,array_keys($countsArr['arr']))){ $num = $countsArr['arr'][$value]; $counts = $counts + $num; echo "<span class='show_count'>".$num."</span>"; }else{ echo '0'; } ?>
                </td>
                <?php }?>
                <td class="total"><?php echo ($counts); ?></td>
            </tr>
            </tbody>
        </table>
    </div>






    </div>
</div>

<script src="/newerp/Public/js/jquery.min.js-v=2.1.4"></script>
<script src="/newerp/Public/js/jquery-ui-1.10.4.min.js"></script>
<script src="/newerp/Public/js/bootstrap.min.js-v=3.3.5"></script>

<script src="/newerp/Public/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/newerp/Public/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="/newerp/Public/js/inspinia.js"></script>
<script src="/newerp/Public/js/plugins/pace/pace.min.js"></script>


<!-- iCheck -->
<!--<script src="/newerp/Public/js/plugins/iCheck/icheck.min.js"></script>-->

<!-- Jvectormap -->
<!--<script src="/newerp/Public/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/newerp/Public/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>-->

<!-- Flot -->
<!--<script src="/newerp/Public/js/plugins/flot/jquery.flot.js"></script>
<script src="/newerp/Public/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="/newerp/Public/js/plugins/flot/jquery.flot.resize.js"></script>-->

<!-- laydate -->
<!--<script src="/newerp/Public/js/plugins/layer/laydate/laydate.js"></script>-->

<!-- validate -->
<!--<script src="/newerp/Public/js/plugins/validate/jquery.validate.min.js"></script>
<script src="/newerp/Public/js/plugins/validate/messages_zh.min.js"></script>-->

<!-- morris -->
<!--<script src="/newerp/Public/js/plugins/morris/morris.js"></script>
<script src="/newerp/Public/js/plugins/morris/raphael-2.1.0.min.js"></script>-->

<!-- morris -->
<!--<script src="/newerp/Public/js/plugins/tableexport/Blob.js"></script>
<script src="/newerp/Public/js/plugins/tableexport/FileSaver.js"></script>
<script src="/newerp/Public/js/plugins/tableexport/tableExport.js"></script>-->

<!-- Data Tables -->
<script src="/newerp/Public/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="/newerp/Public/js/plugins/dataTables/dataTables.bootstrap.js"></script>

<!-- fullcalendar -->
<!--<script src="/newerp/Public/js/jquery-ui.custom.min.js"></script>
<script src="/newerp/Public/js/plugins/fullcalendar/fullcalendar.min.js"></script>-->

<!--layer-->
<script src="/newerp/Public/layer/layer.js"></script>

<!--消息任务-->
<!--<script src="/newerp/Public/js/tipsbar.js"></script>-->


<!-- 时间选择插件 laydate -->
<!--<script>
    laydate({
        elem: "#hello",
        event: "focus"
    });
</script>-->
<!-- 菜单 -->
<script>
    var s_url = window.location.pathname;
    s_url = "/t.php?s=/Package/Statistics/showPackerStatistics";
    var now_url = '';
    for(var i = 0; i < $("#side-menu li").length; i++) {
        now_url = $("#side-menu li a").eq(i).attr("href");
        if(now_url == s_url) {
            $("#side-menu a").eq(i).parent().addClass("active");
            $("#side-menu a").eq(i).parent().parent().parent().addClass("active");
            $("#side-menu a").eq(i).parent().parent().addClass("in");
        } else {
            $("#side-menu a").eq(i).parent().removeClass("active");
        }
    }
</script>


<!--模态框html-->


<!--额外js-->

    <script src="/newerp/Public/js/plugins/layer/laydate/laydate.js"></script>

    <script type="application/javascript">
        $(function(){
            var s = $('#contentArr').offset().top;
            var h= window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
            var hig = h - s - 45;
            $("#contentArr").height(hig)
        });
        /**
         * 导出功能
         * @author Shawn
         * @date 2018-08-03
         */
        function exportExcel(){
            var url='t.php?s=/Package/Statistics/exportPackerStatistics'+"&timeArea_start="+$("#timeArea_start").val()+'&timeArea_end='+$("#timeArea_end").val();
            window.open(url,'_blank')
        }
        /**
         * 判断时间是否超过一个月
         * @author Shawn
         * @date 2018-04-06
         */
        function checkDate() {
            var beginTime = $.trim($('input[name="timeArea_start"]').val());
            var endTime   = $.trim($('input[name="timeArea_end"]').val());
            var time1 = new Date(beginTime).getTime();
            var time2 = new Date(endTime).getTime();
            if(beginTime == ''){
                alert("开始时间不能为空");
                return false;
            }
            if(endTime == ''){
                alert("结束时间不能为空");
                return false;
            }
            if(time1 > time2){
                alert("开始时间不能大于结束时间");
                return false;
            }
            //判断时间跨度是否大于1个月
            var arr1 = beginTime.split('-');
            var arr2 = endTime.split('-');
            arr1[1] = parseInt(arr1[1]);
            arr1[2] = parseInt(arr1[2]);
            arr2[1] = parseInt(arr2[1]);
            arr2[2] = parseInt(arr2[2]);
            var flag = true;
            if(arr1[0] == arr2[0]){ //同年
                if(arr2[1]-arr1[1] > 1){ //月间隔超过1个月
                    flag = false;
                }else if(arr2[1]-arr1[1] == 1){ //月相隔1个月，比较日
                    if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
                        flag = false;
                    }
                }
            }else{ //不同年
                if(arr2[0] - arr1[0] > 1){
                    flag = false;
                }else if(arr2[0] - arr1[0] == 1){
                    if(arr1[1] < 12){ //开始年的月份小于12时，不需要跨年
                        flag = false;
                    }else if(arr1[1]+1-arr2[1] < 12){ //月相隔大于1个月
                        flag = false;
                    }else if(arr1[1]+1-arr2[1] == 12){ //月相隔1个月，比较日
                        if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
                            flag = false;
                        }
                    }
                }
            }
            if(!flag){
                alert("查询区间不能超过一个月");
            }
            return flag;
        }
    </script>



    <!-- <?php require_once ROOT_PATH.'/bottom.php' ?> -->