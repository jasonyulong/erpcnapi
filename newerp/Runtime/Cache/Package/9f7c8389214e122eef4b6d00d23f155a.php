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
        .table-striped th,td{
            text-align: center;
        }
    </style>
    <button type="button" class="btn btn-primary" id="addData" data-url="<?php echo U('Package/WeighSystem/addWeightingPage');?>">添加</button>
    <input type="hidden" id="delUrl" data-url="<?php echo U('Package/WeighSystem/delWeighting');?>" />
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>优先级</th>
            <th>关键字</th>
            <th>加权重量(g)</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td><?php echo ($vo['id']); ?></td>
                <td>
                    <span class="glyphicon glyphicon-arrow-up btn btn-xs btn-primary" style="cursor: pointer;" aria-hidden="true" onclick="altRuleSort(<?php echo ($vo['id']); ?>, 'prev')">上移</span>
                    &nbsp;
                    <span class="glyphicon glyphicon-arrow-down btn btn-xs btn-success" style="cursor: pointer;" aria-hidden="true" onclick="altRuleSort(<?php echo ($vo['id']); ?>, 'next')">下移</span>
                    &nbsp;
                    <span class="btn btn-xs btn-info" style="cursor: pointer;" aria-hidden="true" onclick="moveRule(<?php echo ($vo['id']); ?>, <?php echo ($vo['sort']); ?>)">移到指定ID前</span>
                </td>
                <td><?php echo ($vo['keyword']); ?></td>
                <td><?php echo ($vo['weighting']); ?></td>
                <td>
                    <button type="button" class="btn btn-info" id="editData" onclick="editData('<?php echo ($vo["id"]); ?>')">修改</button>
                    <button type="button" class="btn btn-danger" id="delData" onclick="delData('<?php echo ($vo["id"]); ?>')">删除</button>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php if(empty($data)): ?><tr><td colspan="4">暂无数据</td></tr><?php endif; ?>
        </tbody>
    </table>
    <script>
        /**
         * 监听添加功能
         * @author Shawn
         * @date 2018-12-25
         */
        $("#addData").unbind("click").click(function () {
            var url = $(this).attr("data-url");
            layer.open({
                type:2,
                title:'添加数据',
                shift:'1',
                closeBtn:1,
                scrollbar:false,
                area:['500px','400px'],
                content:url
            })
        });
        /**
         * 监听修改功能
         * @author Shawn
         * @date 2018-12-25
         */
        function editData(id) {
            var url = $("#addData").attr("data-url");
            layer.open({
                type:2,
                title:'添加数据',
                shift:'1',
                closeBtn:1,
                scrollbar:false,
                area:['500px','400px'],
                content:url+'&id='+id
            })
        }
        /**
         * 监听删除功能
         * @author Shawn
         * @date 2018-12-25
         */
        
        function delData(id) {
            var url = $("#delUrl").attr("data-url");
            layer.confirm('确认是否删除', {icon: 3, title:'提示'}, function(index){
                layer.close(index);
                $.ajax({
                    url:url,
                    type:'post',
                    dataType:'json',
                    data:{id:id},
                    success:function(res){
                        if(res.status == 1){
                            layer.msg(res.msg,{icon:1});
                            setTimeout(function(){
                                location.reload();
                            }, 1500);
                        }else{
                            layer.msg(res.msg,{icon:2});
                        }

                    }
                }).fail(function() {
                    layer.msg('访问出错',{icon:2});
                });
            });
        }

        //修改优先级
        function altRuleSort(id, type){
            if(isNaN(id) || type == ''){
                layer.msg('提交的数据不正确！请检查！', {icon : 2});
                return false;
            }
            $.ajax({
                url : 't.php?s=/Package/WeighSystem/altRuleSort',
                type : 'post',
                data : {
                    id : id,
                    type : type,
                },
                dataType:'json',
                success : function(res){
                    if(res.status == 1){
                        layer.msg(res.msg, {icon : 1});
                        setTimeout(function(){
                            location.reload(true);
                        }, 1500);
                    }else{
                        layer.msg(res.msg, {icon : 2});
                    }
                }
            });
        }


        /**
         * 移动排序
         * @param id
         * @param sort
         */
        function moveRule(id, sort){
            layer.prompt(
                { title: 'ID: ' + id +' 移到指定ID前（输入ID）', formType: 3 },
                function(text, index){
                    var target_id = $.trim(text);   //目标id
                    if(isNaN(target_id) || target_id == '' || target_id == 0){
                        layer.msg('ID必须为大于0的数字！', {icon : 2});
                        return false;
                    }
                    if(target_id == id){
                        layer.msg('目标ID不能是自己！', {icon : 2});
                        return false;
                    }
                    layer.close(index);
                    var loading = layer.load();
                    $.ajax({
                        url : 't.php?s=/Package/WeighSystem/moveRule',
                        type : 'post',
                        data : {
                            id : id,
                            sort : sort,
                            target_id : target_id,  //目标id
                        },
                        dataType:'json',
                        success : function(res){
                            layer.close(loading);
                            if(res.status == 1){
                                layer.msg(res.msg, {icon : 1});
                                setTimeout(function(){
                                    location.reload(true);
                                }, 1500);
                            }else{
                                layer.msg(res.msg, {icon : 2});
                            }
                        }
                    });
                }
            );
        }
    </script>

</body>
</html>