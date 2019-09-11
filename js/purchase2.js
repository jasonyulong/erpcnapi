Array.prototype.searchVal=function(val){
    var arr=this;
    for(var i=0;i<arr.length;i++){
        if(arr[i]==val){
            return i;
        }
    }
    return -1;
};

function toRefresh(){
    var url=location.href;
    url=url.replace(/&round\=.*/,'')+"&round="+new Date().getTime();
    location.href=url;
}

function saveCheck(checkCode){
    /*
    * @checkCode=1 采购计划单中添加
    * @checkeCode=1 预定中订单添加 不用 验证付款方式 验收员 供应商单号
    *@checkeCode=2  预定中订单转入库单 基本上全部要验证 其他状态一样
    *@
    * */
    if(isNaN(checkCode)){
        alert('验证代码有误');
        return false;
    }

    var io_partner					= $.trim($("#io_partner").val());
    var io_ordersn					= $.trim($("#io_ordersn").val());
    var note						= $.trim($("#note").val());
    var io_purchaseuser				= $.trim($("#io_purchaseuser").val());
    var deliverytime				= $.trim($("#deliverytime").val());
    var io_paymentmethod			= $.trim($("#io_paymentmethod").val());
    var in_warehouse				= $.trim($("#in_warehouse").val());
    var io_shipfee					= $.trim($("#io_shipfee").val());
    var qc_user					    = $.trim($("#qc_user").val());
    var sourceorder					= $.trim($("#sourceorder").val());
    var orderType					= $.trim($("#orderType").val());

    /*
    * @ 后面数组的意思是 那一种验证模式下不需要验证这个值 比如1 验证模式下 不需要验证 付款方式
    * @ 以后有这样的验证模式直接加在后面即可
    * */
    var rule={
        'io_partner':[io_partner,'供应商不能为空',[]],
        'io_ordersn':[io_ordersn,'采购单号有误请刷新',[]],
        'in_warehouse':[in_warehouse,'入库仓库不能为空',[]],
        'io_purchaseuser':[io_purchaseuser,'采购人员不能为空',[]],
        'qc_user':[qc_user,'验收人员不能为空',[1]],
        'io_paymentmethod':[io_paymentmethod,'付款方式不能为空',[1]],
        'sourceorder':[sourceorder,'供应商单号不能为空',[1]]
    };

    for(var i in rule){
        if(rule[i][2].searchVal(checkCode)==-1){
            if(''== $.trim(rule[i][0])){
                alert(rule[i][1]);return false;
            }
        }

    }

    return {io_partner:io_partner,io_ordersn:io_ordersn,note:note,io_purchaseuser:io_purchaseuser,deliverytime:deliverytime,io_paymentmethod:io_paymentmethod,in_warehouse:in_warehouse,io_shipfee:io_shipfee,qc_user:qc_user,sourceorder:sourceorder,orderType:orderType};
}

function skuCheck(){
    var oldsku='';
    var newsku='';
    var over=false;
    $("input[name='skulist'][type='checkbox']").each(function(i){
        var id=$(this).attr("id");
        var sku=$(this).val();
        if(id!='new'){
            var cost=$("#goods_cost"+id).val();
            var count=$("#goods_count2"+id).val();
            if(isNaN(cost)||cost<=0){
                alert(sku+"采购价有误");
                over=true;
                return false;
            }
            if(isNaN(count)||count<=0||/\./.test(count)){
                alert(sku+"数量有误");
                over=true;
                return false;
            }
            oldsku+=id+'@'+cost+'@'+count+'###';
        }
    });
    if(over){return;}
    $(".goods_cost_new").each(function(){
        var cost=$(this).val();//alert(cost);
        var sku=$(this).attr("data");
        if(isNaN(cost)||cost<=0){
            alert(sku+"采购价有误!");
            over=true;
            return false;
        }
        newsku+=sku+'@'+cost+'@'+sku+'count###';
    });
    if(over){return false;}
    $(".goods_count_new").each(function(){
        var count=$(this).val();
        var sku=$(this).attr("data");
        if(isNaN(count)||count<=0||/\./.test(count)){
            alert(sku+"数量有误");
            over=true;
            return false;
        }
        var reg=new RegExp('@'+sku+'count');
        newsku=newsku.replace(reg,'@'+count);
    });
    if(over){return false;}
    //console.log(oldsku);
    //console.log(newsku);
    return {'oldsku':oldsku,newsku:newsku};
}

function auditCheck(){
    var str='';
    var over=false;
    $("input[name='skulist'][type='checkbox']").each(function(){
        var id=$(this).attr('id');
        var sku=$(this).val();
        var count=parseInt(getVal("goods_count2"+id));// 进货数量
        var a_count=parseInt(getVal("goods_count"+id));// 通过数量
        var yiruku =parseInt(getVal("yiruku"+id));// 已入库
        var yichang=parseInt(getVal("stockqty"+id));// 异常数量
        var house  = $("#in_warehouse").val();
        if(yiruku+a_count+yichang>count){
            alert('sku:'+sku+"检测数量加入库数据大于进货数量");
            over=true;
            return false;
        }
        str+=id+'@'+a_count+'@'+count+'@'+yiruku+'@'+house+'@'+yichang+'###';
    });
    if(over){
        return false;
    }else{
        return str;
    }
}


function skuSpliteCheck(){//验证+ 区数据
    var str='';
    var over=false;
    $("input[name='skulist'][type='checkbox']").each(function(){
        var id=$(this).attr('id');
        var sku=$(this).val();
        if(id!=='new'&&$(this).prop("checked")){
            var ruku=getVal("rk"+id);
            //console.log(ruku);
            if(/^[1-9](\d?)+$/.test(ruku)){
                str+=id+'@'+ruku+'###';
            }else{
                alert('sku:'+sku+"数量填写有误!");
                over=true;
                return false;
            }
        }
    });
    if(over){
        return false;
    }else{
        return str;
    }
}

function saveOrder(checkcode){
    var baseinfo=saveCheck(checkcode);
    if(false===baseinfo){
        return false;
    }
    var skuInfo=skuCheck();
    if(false===skuInfo){
        return false;
    }
    baseinfo['oldsku']=skuInfo['oldsku'];
    baseinfo['newsku']=skuInfo['newsku'];
    baseinfo['isadd']=getVal('isAdd');
    baseinfo['doaction']='ajaxModpurchase';
    funsTool.showBodyMask();
    funsTool.showModbox('保存采购单',280,340,function(){
        $.post(
            'purchaseorderadd99.php',
            baseinfo,
            function(data){
                funsTool.insertModBox(data);
            }
        )
    })

}

function auditOrder(io_ordersn){
    var str=auditCheck();
    if(false===str){return;}
    //LOG(str);return;
    if(str==''){
        if(!confirm("采购单所有的sku都没有良品?确认审核通过?")){
            return false;
        }
    }
    funsTool.showBodyMask();
    funsTool.showModbox('质检审核'+io_ordersn,260,380,function(){
        $.post(
            'purchaseorderadd99.php',
            {iostore:io_ordersn,doaction:'ajaxAuditPurchase',sku:str},
            function(data){
                funsTool.insertModBox('<div style="margin: 10px;">'+data+'</div>');
            }
        )
    })
}

function addIoStoreDetail(){
    var sku=getVal("add_goods_sn");
    var count=getVal("add_goods_count");
    var name=getVal("add_goods_name");
    var cost=getVal("add_goods_cost");
    if(sku==''){ alert("sku不能为空!");return;}
    if(count==''){ alert("sku数量不能为空!");return;}
    if(isNaN(count)||count<0||/\./.test(count)){ alert("sku数量必须是正整数!");return;}
    var old=$("input[name='skulist'][value='"+sku+"']");
    if(undefined==old.val()){
        ///不存在就添加吧
        var html="<tr><td><input type='checkbox' id='new' value='"+sku+"' name='skulist'/></td><td>"+sku+"</td><td></td>";
        html+="<td>"+name+"</td>";
        html+="<td>0</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>"+cost+"</td><td>---</td><td>---</td>";
        html+="<td><textarea data='"+sku+"' rows='1' cols='5' class='goods_cost_new'>"+cost+"</textarea></td>";
        html+="<td><textarea data='"+sku+"' rows='1' cols='5' class='goods_count_new'>"+count+"</textarea></td>";
        html+="<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>";
        $("#addtrskus").before($(html));
        $("#add_goods_sn").val("");
        $("#add_goods_count").val("");
    }else{
        alert("您已经添加了"+sku+"请不要重复添加!");
    }

}

function getKeyCodes(event){
    var keyCode;
    var isie = (document.all) ? true : false;
    if(isie){keyCode = window.event.keyCode; }else {keyCode = event.which;}
    return keyCode;
}

function insertlistDiv(Arr,keyword){
    var Jq=$("#searchSkuList");
    if(Arr===false||Arr.length==0){Jq.hide();return false;}
    var str="";
    //var i= 0,len=Arr.length;

    var j=0;var classstr='';
    for(var i in Arr){
        if(0===j){
            classstr=' class="li_hover" '
        }else{
            classstr=''
        }
        var goods_sn=Arr[i]['goods_sn'];///console.log(goods_sn);
        if(undefined==goods_sn){continue;}
        var name=Arr[i]['goods_name'];
        var cost=Arr[i]['goods_cost'];
        var goods_sn_b=goods_sn.replace(keyword,'<b>'+keyword+'</b>');
        str+='<li cost="'+cost+'" names="'+name+'" val="'+goods_sn+'"'+classstr+'>'+goods_sn_b+' '+name+'</li>';
        j++;
    }
    var o=Jq;
    var ul= o.children("ul");
    o.find('ul.c_list').html($(str));
    if(str!=''){
        o.show();
        $("#add_goods_sn").unbind("keydown").bind('keydown',function(event){//上下按键功能
            var keyCode=getKeyCodes(event);
            //console.log(keyCode);
            //var ul=$("#searchSkuList>ul");
            if(keyCode==38){
                var isFrist=ul.children("li.li_hover").prev('li').attr('val');
                if(isFrist==''){
                    return false;
                }else{
                    ul.children("li.li_hover").prev('li').addClass('li_hover');
                    ul.children("li.li_hover").next('li').removeClass('li_hover');
                }
            }
            if(keyCode==40){
                var islast=ul.children("li.li_hover").next('li').attr('val');
                if(islast==''){
                    return false;
                }else{
                    ul.children("li.li_hover").next('li').addClass('li_hover');
                    ul.children("li.li_hover").prev('li').removeClass('li_hover');
                }
            }
            if(keyCode==13){
                stopDefault(event);
                var skuinfo=ul.children("li.li_hover");
                var sku=skuinfo.attr('val');
                var skuCost=skuinfo.attr('cost');
                var skuName=skuinfo.attr('names');
                $("#add_goods_sn").val(sku);
                $("#add_goods_cost").val(skuCost);
                $("#add_goods_name").val(skuName);
                $("#add_goods_count").focus();
                ul.children("li").unbind().remove();
                Jq.hide();
            }
            //
            //alert(keyCode);
        });

        ul.children("li").bind('mouseenter',function(){// mouseover 主要是实现 上下按键功能的
            ul.children("li").removeClass('li_hover');
            $(this).addClass('li_hover');
        });

        ul.children("li").bind('click',function(){
            //Jq.val($(this).attr('val')).unbind('keydown');
            var skuinfo=$(this);
            var sku=skuinfo.attr('val');
            var skuCost=skuinfo.attr('cost');
            var skuName=skuinfo.attr('names');
            $("#add_goods_sn").val(sku);
            $("#add_goods_cost").val(skuCost);
            $("#add_goods_name").val(skuName);
            $("#add_goods_count").focus();
            ul.children("li").unbind().remove();
            Jq.hide();
        });
    }
}

function realizeJson(str){
    if(JSON.parse){
        return JSON.parse(str);
    }
    return eval('('+str+")");
}

function getVal(id){
    return $.trim($("#"+id).val());
}

function stopDefault(e) {
    if(e && e.preventDefault) {//如果提供了事件对象，则这是一个非IE浏览器
        e.preventDefault();//阻止默认浏览器动作(W3C)
    } else {
        //IE中阻止函数器默认动作的方式
        window.event.returnValue = false;
    }
    return false;
}

function local_add(){
    var sku=getVal("goods_sn");
    var qty=getVal('goods_count');
    var name=getVal('goods_name');
    var cost=getVal('goods_cost');
    if(sku==''||qty==''){
        funsTool.showTips(false,'SKU and Quantity is required',1600);
        return false;
    }
    var repeat=false;
    $("input.addskutemp").each(function(){
        var val=$(this).val();
        val=val.replace(/\*\d+/,'');
        if(val==sku){
            repeat=true;return;
        }
    });
    if(repeat){
        funsTool.showTips(false,'Repeat to add',1600);
        return;
    }

    var str='<tr>';
    str+="<td><input name='addskutemp[]' class='addskutemp' type='checkbox' value='"+sku+"*"+qty+"'/></td><td>"+sku+"</td><td></td><td>"+name+"</td><td></td><td>"+cost+"</td><td>"+qty+"</td><td colspan='2'></td><td><a href='javascript:' onclick=\"DelSKU('"+sku+"',"+"'','"+qty+"')\">Delete</</td>";
    str+='</tr>';
    $("#table_td_addsku").after($(str));
    $("#goods_sn").val("").focus();
    $("#goods_count").val("");
    $("#goods_name").val("");
    $("#goods_cost").val("");

}

function savePk(){
    var str='';
    $("input.addskutemp").each(function(){
        var val=$(this).val();
        str+=val+',';
    });
    var f_w=getVal("from_warehouse");
    if(f_w==''){
        funsTool.showTips(false,'Warehouse is required',1600);
        return;
    }
    var in_w=getVal("in_warehouse");
    if(in_w==''){
        funsTool.showTips(false,'Destination is required',1600);
        return;
    }
    var len=$("input.addskutemp").length;
    if(len==0){
        funsTool.showTips(false,'SKU is required',1600);
        return;
    }
    var fee=getVal("fee");
    var tax=getVal("tax");
    var cntax=getVal("cntax");
    var fee_currency=getVal("fee_currency");
    var tax_currency=getVal("tax_currency");
    var cntax_currency=getVal("cntax_currency");
    if(isNaN(fee)){
        funsTool.showTips(false,'Fee must be numeric',1600);
        return;
    }
    if(isNaN(tax)){
        funsTool.showTips(false,'Tax must be numeric',1600);
        return;
    }
    if(isNaN(cntax)){
        funsTool.showTips(false,'cnTax must be numeric',1600);
        return;
    }
    if(fee!=''&&fee_currency==''){
        funsTool.showTips(false,'Please fill in fee currency',1600);
        return;
    }
    if(tax!=''&&tax_currency==''){
        funsTool.showTips(false,'Please fill in tax_currency',1600);
        return;
    }
    if(cntax!=''&&cntax_currency==''){
        funsTool.showTips(false,'Please fill in cmtax_currency',1600);
        return;
    }


    $("#skuarr").val(str);
    $("#ccs").submit();
}

function isNewSKUSave(){// 是否有新的sku 添加了
    var addsku=$(".goods_cost_new").eq(0).attr("data");
    if(addsku!=''&&undefined!=addsku){
        console.log(addsku);
        if(!confirm("新增的sku没有保存,继续操作将不会保存它们!\n不保存,继续继续操作点击确认!\n否则点击取消")){
            return false;
        }
    }
    return true;
}

function deliostoredetail(iostoresn,id){//要考虑 删除前检查是否为保存
    var sku=getVal(id);
    if(!confirm("您确定删除"+iostoresn+"中的SKU:"+sku)){
        return ;
    }
    if(false===isNewSKUSave()){

    }
    //删除数据
    funsTool.showBodyMask();
    funsTool.showModbox('删除SKU:'+sku,230,360,function(){
        $.post(
            'purchaseorderadd99.php',
            {'doaction':'ajaxDeleteIostoreDetail','iostoresn':iostoresn,id:id},
            function(data){
                funsTool.insertModBox(data);
            }
        )
    })


}


//分割转到入库单
function spliteRuku(){

    if(false===isNewSKUSave()){
        return false;
    }
    var save=saveCheck(2);
    if(save===false){
        return false;
    }
    //
    var splitData=skuSpliteCheck();
    //LOG(splitData);
    if(splitData===false){
        return false;
    }
    if(splitData===''){
        alert("请勾选到货的sku!");
        return false;
    }
    save['splitData']=splitData;
    save['doaction']='ajaxSpliteSku';
    funsTool.showBodyMask();
    funsTool.showModbox('预定转入库',250,360,function(){
        $.post(
            'purchaseorderadd99.php',
            save,
            function(data){
                funsTool.insertModBox(data);
            }
        )
    })
}

function changeStatus(io_ordersn,status,title){
    //转过去
    funsTool.showBodyMask();
    funsTool.showModbox(title,200,300,function(){
        $.post(
            'purchaseorderadd99.php',
            {'io_ordersn':io_ordersn,'doaction':'ajaxChangeType',thisType:status},
            function(data){
                funsTool.insertModBox(data);
            }
        )
    })
}
function checkAll(that){
    var bool=$(that).prop("checked");
    $("input[name='skulist'][type='checkbox']").prop("checked",bool);
}

function warehouseAgain(io_ordersn){// 异常 继续入库
    var str=auditCheck();
    if(false===str){return;}
    //LOG(str);
    if(str==''){
        alert("继续入库至少填写一个sku的通过数量!");return;
    }
    funsTool.showBodyMask();
    funsTool.showModbox('质检审核'+io_ordersn,260,380,function(){
        $.post(
            'purchaseorderadd99.php',
            {iostore:io_ordersn,doaction:'warehouseAgain',sku:str},
            function(data){
                funsTool.insertModBox('<div style="margin: 10px;">'+data+'</div>');
            }
        )
    })
}

function LostCost(io_ordersn){// 报损并完成  必须要写明备注
    if(!confirm("转到完成不会修改入库数量直接转到有异常完成")){
        return false;
    }

    var note=getVal("note");
    if(''==note){
        alert("直接转到完成必须要填写原因!");return false;
    }

    //转过去
    funsTool.showBodyMask();
    funsTool.showModbox('',200,300,function(){
        $.post(
            'purchaseorderadd99.php',
            {'io_ordersn':io_ordersn,'doaction':'ajaxChangeTo101',note:note},
            function(data){
                funsTool.insertModBox('<div style="margin: 10px;">'+data+'</div>');
            }
        )
    })
}
//驳回
function turnDown(io_ordersn){
    var url				= "toxls/FinanceCheck_notaudit.php?io_ordersn="+io_ordersn;
    openwindow(url,'',1000,600);
}

//显示日志的
function showLog(io_ordersn){
    funsTool.showModbox('显示采购单: '+io_ordersn+" 的日志!",360,580,function(){
        $.post(
            'purchaseorderadd99.php',
            {iostore:io_ordersn,doaction:'showMyLogs'},
            function(data){
                //funsTool.insertModBox('<div style="margin: 10px;">'+data+'</div>');
            }
        )
    })
}

function LOG(){
    console.log.apply(null,arguments);
}
function openwindow(url,name,iWidth,iHeight) {
    var url; //转向网页的地址;
    var name; //网页名称，可为空;
    var iWidth; //弹出窗口的宽度;
    var iHeight; //弹出窗口的高度;
    var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
    var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
    window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=yes,menubar=yes,scrollbars=yes,resizeable=yes,location=no,status=no');
}
(function(){
    var ajaxTool={
        searchs:function(that,event){
            var event=window.event||event;
            var keyCode=getKeyCodes(event);
            var keyCodeArr=[37,38,39,40,15,17,13];
            if($.inArray(keyCode,keyCodeArr)!==-1){return;}

            $(that).unbind();
            var keyword= $.trim($(that).val()).toLocaleUpperCase();
            $(that).val(keyword);/*强制性不准输入空格*/
            if(keyword==''){$("#searchSkuList").hide();return;}
            if(undefined==window.cache){
                window.cache={};
            }
            if(undefined!==window.cache[keyword]){// 命中缓存
                insertlistDiv(window.cache[keyword],keyword);return;
            }
            $.post(
                'addpackage.php',
                {'w':keyword,'doaction':'ajaxSearchSku'},
                function(data){
                    data=realizeJson(data);
                    window.cache[keyword]=data;
                    insertlistDiv(data,keyword);
                }
            );

        }

    };
    window.ajaxTool=ajaxTool;
})();