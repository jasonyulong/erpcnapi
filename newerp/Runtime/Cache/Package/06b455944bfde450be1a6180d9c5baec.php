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
        table tr th,td {text-align: center !important;}
        .btn {border-radius: 2px;}
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
                
                    <?php if(can('view_pick_carrier_group')): ?><li>
                            <a href="<?php echo U('Package/CarrierGroup/groupList');?>">
                                <i class="fa fa fa-cubes"></i>
                                <span class="nav-label">运输方式分组</span>
                                <!--<span class="fa arrow"></span>-->
                            </a>
                        </li><?php endif; ?>
                
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
        <a class="m-r-xs"> 运输方式分组 &gt; 运输方式分组列表</a>
    </li>

                    </ul>
                </nav>
            </div>
        </div>
        <!-- 头部导航-结束 -->

        
    <div class="container-fluid">
        <!-- 查询行 -->
        <div class="row">

        </div>

        <!-- 分页行 -->
        <div class="row">
            <?php if(can('add_pick_carrier_group')): ?><button class="btn btn-info btn-sm" id="createNewGroup"> 新建运输方式分组 </button><?php endif; ?>
            <div class="pageInfo">
                <ul class="pagination" style="margin: 5px"><?php echo ($pageString); ?></ul>
            </div>
        </div>

        <!-- 主体内容行 -->
        <div class="row">
            <table class="table table-responsive table-hover table-condensed">
                <tr>
                    <th>编号</th>
                    <th>分组名称</th>
                    <th>创建人</th>
                    <th>创建时间</th>
                    <th>备注信息</th>
                    <th>操作</th>
                </tr>

                <?php if(is_array($carrierGroups)): foreach($carrierGroups as $key=>$groupItem): ?><tr>
                        <td><?php echo ($groupItem["id"]); ?></td>
                        <td><?php echo ($groupItem["group_name"]); ?></td>
                        <td><?php echo ($groupItem["create_by"]); ?></td>
                        <td><?php echo ($groupItem["create_at"]); ?></td>
                        <td style="width: 250px;word-break: break-all;"><?php echo ($groupItem["memo"]); ?></td>
                        <td>
                            <?php if(can('update_pick_carrier_group')): ?><button class="btn btn-success btn-sm btn-modifyItem" data-id="<?php echo ($groupItem["id"]); ?>"> 修改 </button><?php endif; ?>

                            <?php if(can('delete_pick_carrier_group')): ?><button class="btn btn-danger btn-sm btn-deleteItem" data-id="<?php echo ($groupItem["id"]); ?>" data-item="<?php echo ($groupItem["group_name"]); ?>"> 删除 </button><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; ?>


            </table>
        </div>
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
    s_url = "/t.php?s=/Package/CarrierGroup/groupList.html";
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

    <script type="application/javascript">
        (function(window, $, layer, undefined) {

            /**
             * 新建运输方式
             */
            $('#createNewGroup').on('click', function() {
                var url = '<?php echo U("CarrierGroup/showCreateGroup");?>';
                var createLoadIndex = layer.open({
                    type : 2,
                    area : ['40%', '65%'],
                    title: '创建新运输方式分组',
                    shift: 2,
                    maxmin: true,
                    scrollbar : false,
                    content : url,
                    cancel : function(index, layerOb) {
                        localStorage.removeItem('createLayerIndex');
                    }
                });

                localStorage.setItem('createLayerIndex', createLoadIndex);
            });


            /**
             * 修改运输方式分组
             */
            $('.btn-modifyItem').on('click', function() {
                var groupId = $(this).attr('data-id');

                layer.open({
                    content : "<?php echo U('CarrierGroup/showUpdateGroup');?>&groupId=" + groupId,
                    type    : 2,
                    title   : '修改运输方式分组',
                    area    : ['50%', '70%'],
                    maxmin  : true,
                    shift   : 2
                });
            });


            /**
             * 删除运输方式分组
             */
            $('.btn-deleteItem').on('click', function() {
                var groupName = $(this).attr('data-item');
                var groupId   = $(this).attr('data-id');

                layer.confirm(
                        '您确认要删除运输方式分组<span class="alert alert-danger" style="padding: 5px 20px">' + groupName + '</span> ？',
                        {
                            title : '删除运输方式分组确认',
                            btn   : ['确认删除', '放弃删除'],
                            yes   : function(index, layerO) {
                                var loadIndex = layer.load(1, {shade: 0.5});
                                $.ajax({
                                    url      :  '<?php echo U("CarrierGroup/deleteGroup");?>',
                                    type     :  'post',
                                    data     :  {groupId:groupId},
                                    dataType :  'json'
                                }).done(function(result) {
                                    layer.msg(result.data);
                                    if (result.status) {
                                        setTimeout(function() {window.location.reload();}, 1000);
                                    }
                                }).fail(function() {
                                    layer.msg('访问出错.');
                                }).always(function() {
                                    layer.close(loadIndex);
                                });
                            },
                            btn2  : function(index, layerO) {
                                layer.close(index);
                            }
                        }
                );
            });

        })(window, $, layer);
    </script>



    <!-- <?php require_once ROOT_PATH.'/bottom.php' ?> -->