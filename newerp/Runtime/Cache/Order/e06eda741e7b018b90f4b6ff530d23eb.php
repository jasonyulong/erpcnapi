<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css" rel="stylesheet">
    <link href="/newerp/Public/layer/skin/layer.css" rel="stylesheet">
</head>
<style>
    #body{padding: 20px}
</style>
<body id="body">
<div style="color: #FF6600">拣货单子</div>
<table class="table table-bordered">
    <tr class="success">
        <td width="155">拣货单号</td>
        <td width="75">拣货员</td>
        <td width="75">拣货时间</td>
        <td width="75">是否包装</td>
        <td width="75">是否移除</td>
        <td width="75">包装员</td>
        <td width="75">包装时间</td>
    </tr>

    <?php
 foreach($PickOrderDetail as $List){ $addtime=date('Y-m-d H:i:s',$PickOrderTime[$List['ordersn']]); $baleTime='--'; if($List['scan_time']){ $baleTime=date('Y-m-d H:i:s',$List['scan_time']); } ?>
        <tr>
            <td width="155"><?php echo $List['ordersn'];?></td>
            <td width="75"><?php echo $List['picker'];?></td>
            <td width="75"><?php echo $addtime;?></td>
            <td width="75">
                <?php if($List['is_baled']){ echo '已包装'; }else{ echo '未包装'; } ?>
            </td>
            <td width="75">
                <?php if($List['is_delete']){ echo '异常移除'; }else{ echo '无异常'; } ?>
            </td>
            <td width="75"><?php echo $List['scan_user'];?></td>
            <td width="75"><?php echo $baleTime;?></td>
        </tr>
    <?php
 } ?>
</table>



<div style="color: #FF6600">操作记录</div>
<table class="table table-bordered">
    <tr class="success">
        <td width="155">记录时间</td>
        <td width="75">记录用户</td>
        <td>记录内容</td>
    </tr>
    <?php if(is_array($operationLog)): $i = 0; $__LIST__ = $operationLog;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="active">
            <td width="155"><?php echo ($vo["operationtime"]); ?></td>
            <td width="75"><?php echo ($vo["operationuser"]); ?></td>
            <td><?php echo ($vo["notes"]); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>

</body>
<script src="/newerp/Public/js/jquery.min.js-v=2.1.4"></script>
<script src="/newerp/Public/layer/layer.js"></script>
<script src="/newerp/Public/js/bootstrap.min.js-v=3.3.5"></script>
</html>