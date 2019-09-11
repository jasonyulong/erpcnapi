<?php
include "../include/config.php";
include "../include/dbconf.php";
include "../include/dbmysqli.php";

echo "<meta charset='utf-8'>";

$cpower = explode(',', $_SESSION['power']);
if (!in_array('cancel_order', $cpower)) {
    echo "<h1 style='color: red'> 您没有执行还该操作的权限. </h1>";
    exit(0);
}


$statusArr = [
    '2'=>'订单有客户留言（客服处理）',
    '13'=>'需要客服联系客户（客服处理）',
    '6'=>'订单地址异常（客服处理）',
    '7'=>'其他必要信息异常（客服处理）',
    '9'=>'疑似黑名单（客服处理）',
    '3'=>'订单分配仓库失败（业务处理）',
    '4'=>'订单有sku找不到（业务处理）',
    '5'=>'订单分配运输方式失败（业务处理）',
    '8'=>'订单产品金额异常（业务处理）',
    '10'=>'偏远地区（业务处理）',
    '11'=>'订单规则匹配失败（业务主管）',
    '12'=>'特殊的产品体积重量信息（业务处理）',
    '14'=>'国家二字码不识别（物流组长）'
];

$dbconLocal =new DBMysqli($LOCAL);

$bill    = $_GET['bill'];
$billArr = explode(',', $bill);

$checkResult = [];
$stopAbleOrder = '';
foreach ($billArr as $bv) {
    if (empty($bv)) {continue;}
    $checkWeightedSql = "SELECT id FROM ebay_weight WHERE ebay_id = '{$bv}'";
    $isFind = $dbconLocal -> getResultArrayBySql($checkWeightedSql);
    if ($isFind) {
        $checkResult[$bv] = ['status' => false, 'data' => '订单已经称重, 不能阻止发货了'];
    } else {
        $checkResult[$bv] = ['status' => true, 'data' => '未称重,可以阻止发货.'];
        $stopAbleOrder .= ','.$bv;
    }
}
$stopAbleOrder = trim($stopAbleOrder, ' ,');

//$doaction = $_POST['doaction'];
//$cpower   = explode(",", $_SESSION['power']);
//if (!in_array("cancel_order", $cpower)) {
//    echo '<meta charset="utf-8"/>';
//    echo '<div style="color:#911">您没有终止订单的权限!请联系管理员 在权限设置->订单权限->终止发货</div>';
//    die();
//}


//2016 /10/06 强化版 终止发货! 包含倒回库存! 倒回虚拟库存
//if($doaction == 'changestatus'){
//  }
?>
<meta charset="utf-8"/>
<style>
  #container{border: 1px solid black;width:70%;border-radius: 5px}
  #control,#showmsg{margin:50px 0 50px 80px;line-height: 30px;}
  label,select{font-size: 18px;font-weight: bold;}
    .table {
        border-top:2px solid #000000;
        border-bottom:1px solid #000000;
        text-align: center;
        width:90%;
        border-radius: 2px;
    }
    .table th {
        font-size:16px;
    }
    .table td {
        border-top: 2px solid #C0C0C0;
        padding: 5px;
        font-size:14px;
    }
    select.select {
        font-size:14px;
        width:30%;
        padding : 5px 10px;
    }
    select option {
        padding: 5px;
    }
    div.row {
        margin:10px auto;
    }
    #submit {
        border-radius: 2px;
        padding:5px 25px;
    }
    #container {
        margin:20px auto;
        padding-right: 20px;
    }
</style>
<link rel="stylesheet" type="text/css" href="../cache/themes/Sugar5/css/style.css" />
<div id="container">
<div id="control">
  <label>订单初步检测结果</label>

    <table class="table">
        <tr>
            <th>订单编号</th>
            <th>检测结果：</th>
        </tr>
        <?php foreach ($checkResult as $key => $value) :?>
        <tr>
            <td> <?php echo $key;?> </td>
            <td> <?php echo $value['status'] ?
                    "<span style='color: green'> {$value['data']} </span>" :
                    "<span style='color: orangered'> {$value['data']} </span>";?> </td>
        </tr>
        <?php endforeach;?>
    </table>

    <input type="hidden" name="stopAbleOrder" id="stopAbleOrder" value="<?php echo $stopAbleOrder;?>"/>

    <div class="row">
        <label>您要转入 : &nbsp;&nbsp;&nbsp;</label>
        <select name="orderstatus" id="orderstatus" class="select">
            <option value="">请选择...</option>
            <option value="1731">回收站</option>
            <option value="1728">有问题订单</option>
        </select>


        &nbsp;&nbsp;&nbsp;
        <label> 订单状态: </label>
        <select name="status" id="statusTo" class="select">
            <option value=""> 请选择订单状态转入: </option>
            <?php foreach ($statusArr as $key => $value) :?>
                <?php echo "<option value='{$key}'> {$value} </option>";?>
            <?php endforeach;?>
        </select>
    </div>
    <div class="row">
        <label for="noteb">订单阻止备注:</label><br/>
        <textarea name="noteb" id="noteb" cols="60" rows="4" style="border: 1px solid #aaa;border-radius: 3px"></textarea>
    </div>
    <div style="text-align: center">
        <button id="submit" style="background-color: #99FF66;">确定提交</button>
    </div>
</div>
<div id="showmsg"></div>
</div>
<script src="../js/jquery.js"></script>
<script>

    $('#orderstatus').bind('change', function() {
        if ($(this).val() == '1731') {
            $('#statusTo').attr('disabled', true);
        } else {
            $('#statusTo').attr('disabled', false);
        }
    });


  $('#submit').click(function(){
      var stopAble = $('#stopAbleOrder').val();
      if (!stopAble) {
          alert('为检测到可阻止订单.');return false;
      }

      var ordertype = $('#orderstatus').val();
      if(ordertype == ''){
          alert('请选择订单转入类型！');return false;
      }

      var statusTo = $.trim($('#statusTo').val());
      if (!statusTo && ordertype != '1731') {
          alert('非转入回收站操作 , 订单状态必选！'); return false;
      }

      var ordertypename=$("option[value='"+ordertype+"']").html();
      var note = $('#noteb').val();

      if(note.length<5){
          alert('备注不能少于五个字！');return false;
      }

      if(confirm("确定将当前所有订单都转入"+ordertypename+"？")) {
//          location.href = './doStopOrders.php?orderIds='+stopAble+'&orderTo='+ordertype+'&note='+note;
          $.post(
              'doStopOrders.php',
              {orderIds:stopAble,orderTo:ordertype,type:ordertype,note:note, statusTo:statusTo},
              function(data){
                  alert(data);
                  $('#showmsg').html(data);
                  $('#submit').attr('disabled', 'disabled').css('cursor', 'no-drop');
              }
          );
      }
  });
</script>