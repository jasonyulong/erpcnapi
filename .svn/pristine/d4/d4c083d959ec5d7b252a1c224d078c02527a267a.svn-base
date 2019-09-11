/*
 * 获取类别
 * */
var __COM="M";
 function ajaxGetCate(sku){//填写sku之后自动获取品名
    var url="productcombineadd.php?sku="+sku+"&do=ajaxgetcate";
    $.get(url,{},function(data){
            switch(data){
                case "":;break;
                case -1:;break;
                case -2:;break;
                default :setCate(data);
            }
        },'html'
    );
}
//*************获取组合采购user
function ajaxGetCguser(sku){
    var url="productcombineadd.php?sku="+sku+"&do=ajaxgetcguser";
    $.get(url,{},function(data){
            switch(data){
                case "":;break;
                case -1:;break;
                case -2:;break;
                default :setCguser(data);
            }
        },'html'
    );
}

function setCguser(val){
    val= $.trim(val);
    $("#cguser").val(val);
}

function setCate(val){
    val= $.trim(val);
    if(isNaN(val)){return;}
    $("#pid").val(val);
}

$("#goods_sncombine").bind("blur",function(){
    if("1"!=isadd){//是否是添加
        return ;
    }

    var val=$.trim($("#goods_sncombine").val());
    $("#sptips").html("");
    if(!checkComSKUFormat(val)){
        $("#sptips").html("*组合产品格式不正确*").show();
        return;
    }
    var arr=new Array();
    val=val.split("*")[0];
    if(val.length>0){//alert(val);
        ajaxGetCate(val);
        ajaxCheckComName(val);
        ajaxGetCguser(val);
    }
});
/*****
 *验证唯一性
 *******/
function ajaxCheckComName(val){
    var val= $.trim(val);
    var newCode= $.trim(val)+__COM;
    var url="productcombineadd.php?sku="+newCode+"&do=checkName";
    $.get(url,{},function(data){
            if(data=='-4'){
                alert("网络错误!");
                return;
            }
            if(data=='-5'){
                alert("组合过多");
                return;
            }
            if(data){
               setComName(data);
            }
        },'html'
    );
}
//set组合名
function setComName(val){
     $("#goods_sn").val(val);
}

//提交检查重复的组合

function submits(){
    $("#sptips").hide();
    var sku=$.trim($("#goods_sn").val());
    var goods_sncombine=$.trim($("#goods_sncombine").val());
    var notes=$.trim($("#notes").val());
    var pid=$.trim($("#pid").val());
    var salesuser=$.trim($("#salesuser").val());
    var rule={
        'sku':[sku,'产品编号'],
        'goods_sncombine':[goods_sncombine,'组合产品因子'],
        'notes':[notes,'组合名'],
        'pid':[pid,'组合分类'],
        'salesuser':[salesuser,'销售人员']
    };
    if(!checkComSKUFormat(goods_sncombine)){
        alert("*组合产品格式不正确*");
        return;
    }
    for(var i in rule){
        if(''== $.trim(rule[i][0])){
            alert(rule[i][1]+"不能为空");return;
        }
    }

    //======== 重点测试组合格式

    if("1"!=isadd){//是否是添加
        submitmycom();
        return ;
    }
    var comname= $.trim($("#goods_sncombine").val());
    var url="productcombineadd.php?comname="+comname+"&do=checkComName";
    $.get(url,{},function(data){
          if(data.length>2){
              /*///alert("重复的sku是"+data);*/
              $("#sptips").html("*该组合已存在，组合sku是"+data+"，请确认一下*").show();
              return;
          }else{
              submitmycom();
          }
        },'html'
    );
}

function submitmycom(){
    $("#ads").submit();
}

function checkComSKUFormat(str){
    if($.trim(str)==''){
        return false;
    }
    if(/\s/.test($.trim(str))){
        alert("有空格!");
        return false;
    }
    var arr=str.split(',');
    var len=arr.length;
    for(var i=0;i<len;i++){
        var sku_count=arr[i];
        var skuArr=sku_count.split('*');
        //console.log(isNaN(skuArr[1]));
        if(skuArr.length!=2||isNaN(skuArr[1])||''==skuArr[1]){
            return false;
        }

    }
    return true;
}