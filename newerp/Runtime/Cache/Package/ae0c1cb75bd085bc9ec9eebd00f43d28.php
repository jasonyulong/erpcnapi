<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>

<link href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css" rel="stylesheet">
<link href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.css" rel="stylesheet">

<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-xs-12" style="margin-top: 10px">
            传送带层
            <h1><?php echo ($_GET['id']); ?></h1>
        </div>

        <div class="col-xs-12" style="margin-top: 10px">
            <label for="carriers">包含运输方式:</label>
            <select id="carriers" multiple class="chosen-select-no-results form-control account chzn-select" data-placeholder="选择分组包含的运输方式" style="width:100%;">
                <?php if(is_array($carriers)): foreach($carriers as $key=>$carrier): if(in_array($carrier, $beltCarrier)): ?><option value="<?php echo ($carrier); ?>" selected><?php echo ($carrier); ?></option>
                        <?php else: ?>
                        <option value="<?php echo ($carrier); ?>"><?php echo ($carrier); ?></option><?php endif; endforeach; endif; ?>
            </select>
        </div>


        <div class="col-xs-12" style="margin-top: 10px;text-align: center">
            <button class="btn btn-sm btn-success" id="update_carrier_group" onclick="SaveCarrier(<?php echo ($id); ?>)"> 保存 </button>
        </div>

    </div>
</div>


</body>

<script src="/newerp/Public/js/jquery.min.js-v=2.1.4"></script>
<script src="/newerp/Public/js/bootstrap.min.js-v=3.3.5"></script>
<script src="/newerp/Public/layer/layer.js"></script>
<script type="text/javascript" src="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.jquery.js"></script>

<script type="application/javascript">
    $('#carriers').chosen();


    // 保存物流方式
    function SaveCarrier(id){
        var carriers=$("#carriers").val();

        carriers=carriers.toString();

        if(''==carriers){
            alert("您起码要选择点什么把?");
            return false;
        }

        var url="/t.php?s=/Package/Belt/saveSetting";

        layer.load(1);

        $.post(url,{carrier:carriers,id:id},function(data){
            layer.closeAll();
            layer.msg(data);

        });
    }

</script>

</html>