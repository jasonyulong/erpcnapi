function showhideOther(id) {
    var o = $ ("#" + id);
    if (o.css ("display") == 'none') {
        o.show ();
    } else {
        o.hide ();
    }
}
function print_skus() {// 打印sku 那些事情
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }
    //
    window.open ("toxls/print_newproduct.php?bill=" + bill, "_blank");

}

function updatejindu() {
    if (confirm ("只有必填项在系统配置修改后才需要更新进度，确认更新?")) {
        var url = "addgoodsindex.php";
        $.post (
            url,
            {action: 'ajaxUpdatejindu'},
            function (data) {
                var data = $.trim (data);
                if (data == - 2) {
                    funsTool.showTips (false, '更新失败!', 1500);
                }

                if (data == 2) {
                    funsTool.showTips (true, '更新成功!请刷新页面', 1500);
                }
            }
        );
    }
}

//创意删除-----
function delcy() {
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }
    if (confirm ('确认删除记录吗,此操作不可恢复')) {
        bill = bill.substring (1);
        var url = 'productadd_audit_cy.php?module=goods&action=ajaxdelcy&auditted=5&type=delcy&bill=' + bill;
        //var url = 'productadd_audit_cy.php?pid='+bill+'&action=ajaxtokf';
        $.get (url, {}, function (result) {
            if (result == 2) {

                funsTool.showTips (true, "删除成功!");
            } else {
                funsTool.showTips (false, "删除失败!");
            }
        }, 'html')

    }
}
//转到开发中
function changeTokf(bool, id) {
    if (! confirm ('确定要转入开发中？')) return;

    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }

    if (bool) {//批量转到
        if (bill == "") {
            alert ("请选择产品");
            return
        }
    } else {
        bill = id;
    }
    //ajax 提交
    var url = 'productadd_audit_cy.php?pid=' + bill + '&action=ajaxtokf';
    $.get (url, {}, function (result) {
        if (result == 2) {
            funsTool.showTips (true, "转入成功!");
        } else {
            funsTool.showTips (false, "转入失败!");
        }
    }, 'html')
}

function changeToCtu() {
    if (! confirm ('确定要转图片处理？')) return;
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }

    if (bill == "") {
        alert ("请选择产品");
        return
    }
    funsTool.showModbox ('批量转到图片处理', 200, 300, function () {
        $.post (
            'addgoodsindex.php',
            {'doaction': 'ajaxtoUpimg', bill: bill},
            function (data) {
                funsTool.insertModBox (data);
            }
        );
    })
}

//通过审核\
function passaudits(that) {
    var obj = $ (that).parents (".auditboxdata");
    var bill = obj.find (".sku input").val ();
    var iscopyright = obj.find ("input.yes").prop ("checked") ? 1 : 0;
    var setcopyright_note = encodeURIComponent (obj.find (".setcopyright_note").val ());
    //console.log(bill,iscopyright,setcopyright_note);
    passaudit (bill, iscopyright, setcopyright_note);
    obj.remove ();
}

function notpassAudit(that) {
    var obj = $ (that).parents (".auditboxdata");
    var bill = obj.find (".sku input").val ();
    var iscopyright = obj.find ("input.yes").prop ("checked") ? 1 : 0;
    var setcopyright_note = encodeURIComponent (obj.find (".setcopyright_note").val ());
    //return console.log(bill,iscopyright,setcopyright_note);
    notPassaudit (bill, iscopyright, setcopyright_note);
    obj.remove ();
}

function notPassaudit(bill, iscopyright, setcopyright_note) {
    var url = 'productadd_audit.php?audit=notapproved';
    $.post (
        url,
        {pid: bill, iscopyright: iscopyright, copyright_note: setcopyright_note},
        function (msg) {
            alert (msg.replace (/\|/g, '\n'));
        }, 'html');
}

function passaudit(bill) {
    if (confirm ('确定要审核通过吗？')) {
        var url = 'productadd_audit.php?audit=approved';
        $.post (
            url,
            {pid: bill},
            function (msg) {
                alert (msg.replace (/\|/g, '\n'));
            }, 'html');

    }
}
//批量通过审核
function batchApproved(that) {

    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }

    passaudit (bill);

}

function batchCopyRight(that, pid, mleft, sn) {
    var bill = pid || "";
    var sku = sn || '批量sku';
    if (sku === '批量sku') {
        var checkboxs = document.getElementsByName ("ordersn");
        for (var i = 0; i < checkboxs.length; i ++) {
            if (checkboxs[i].checked == true) {
                bill = bill + "," + checkboxs[i].value;
            }
        }
        if (bill == "") {
            alert ("请选择产品");
            return false;
        }
    }
    showauditbox (that, bill, sku, mleft);
}

function saveCopyright(that) {
    var obj = $ (that).parents (".auditboxdata");
    var bill = obj.find (".sku input").val ();
    var iscopyright = obj.find ("input.yes").prop ("checked") ? 1 : (obj.find ("input.no").prop ("checked") ? 2 : 0);
    var setcopyright_note = encodeURIComponent (obj.find (".setcopyright_note").val ());
    if (iscopyright === 0) {
        alert ("请选择是否存在版权问题！");
        return;
    }
    //console.log(bill,iscopyright,setcopyright_note);
    // passaudit(bill,iscopyright,setcopyright_note);
    var url = 'productadd_audit.php?audit=saveCopyright';
    $.post (
        url,
        {pid: bill, iscopyright: iscopyright, copyright_note: setcopyright_note},
        function (msg) {
            if (msg == 2) {
                funsTool.showTips (true, "版权审核成功!", 1500);
            } else {
                funsTool.showTips (false, "版权审核失败!", 1500);
            }
        }, 'html');

    obj.remove ();
}
//恢复到创意
function backtocy(gid) {
    if (confirm ('确认恢复记录吗')) {
        if ($.trim (gid) == "") {
            alert ("id意外丢失!");
            return
        }
        location.href = 'addgoodsindex.php?module=goods&action=新创意&auditted=5&type=backtocg&goods_id=' + gid;
    }
}
//恢复到草稿
function backtofirst(gid) {
    if (confirm ('确认恢复记录吗')) {
        if ($.trim (gid) == "") {
            alert ("id意外丢失!");
            return
        }
        location.href = 'addgoodsindex.php?module=goods&action=开发中&auditted=0&type=backtocg&goods_id=' + gid;
    }
}
//复制
function copyproducts() {

    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    var j = 0;
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = checkboxs[i].value;
            j ++;
        }
    }
    if (j > 1) {
        alert ("一次只能复制一个产品！");
        return false;
    }
    if (bill == "") {

        alert ("请选择一个产品");
        return false;
    }
    var url = "productadd_audit.php?pid=" + bill + "&module=goods&auditted=0&doaction=copy&action=货品资料复制";
    location.href = url;
}

function check_all(obj, cName) {
    var checkboxs = document.getElementsByName (cName);
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == false) {

            checkboxs[i].checked = true;
        } else {

            checkboxs[i].checked = false;
        }

    }
}

function auditlist(goods_id) {
    if (confirm ('确定要提交到审批列表吗？')) {
        var url = 'addgoodsindex.php?pid=' + goods_id + '&audit=intolist';
        $.get (url, {}, function (result) {
            alert (result);
        }, 'html')
    }
}

function batchauditlist() {

    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {

        alert ("请选择订单号");
        return false;
    }

    if (confirm ('确定要提交到审批列表吗？')) {
        var url = 'addgoodsindex.php?pid=' + bill + '&audit=intolist';
        $.get (url, {}, function (msg) {
            alert (msg.replace (/\|/g, '\n'));
        }, 'html')
    }


}

function deleteallsystem() {
//转移到回收站
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }

    if (confirm ('确认删除记录吗')) {
        bill = bill.substring (1);
        location.href = 'addgoodsindex.php?module=goods&action=开发中&auditted=0&type=delsystem&ordersn=' + bill;

    }

}

function deletesure() {
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }

    if (confirm ('该删除是永久性的,确认删除吗?')) {
        bill = bill.substring (1);
        location.href = 'addgoodsindex.php?module=goods&action=回收站&auditted=4&type=delsure&ordersn=' + bill;
    }

}


function getCshu(val, str) {
    val = $.trim (val);
    return val == "" ? '' : '&' + str + '=' + val;
}

function showauditbox(that, id, sku, width) {
    var that = that || 'body';
    var box = $ ("#auditboxdata .auditboxdata").clone ();
    box.find (".skutext").html (sku);
    box.find (".sku input").val (id);
    funsTool.showModbox ('审核SKU版权', 250, 350, function () {
        funsTool.insertModBox (box);
    });

}

function showdescribe(id, sku) {
    funsTool.showBodyMask ();
    funsTool.showModbox ('查看【<b>' + sku + '</b>】的产品描述', 460, 580, function () {
        $.post (
            "addgoodsindex.php",
            {id: id, doaction: "showdescribe"},
            function (data) {
                funsTool.insertModBox (data);
            }
        )
    });
}

function getnewurl(lx) {
    lx ++;
    var url = location.search;
    location.href = "" + url + "&sort=sku&skutype=" + lx;
}

function getdays(lxs) {
    lxs ++;
    var url = location.search;
    location.href = "" + url + "&sort=days&daytype=" + lxs;
}
function openUpdateimgs(type) {
    if (type == 1) {
        var url = "toxls/dragUploadimg.php";
    } else {
        var url = "toxls/new_product_ctu.php";
    }
    window.open (url, '_blank');
}

/*		function updatect(){
    var bill	= "";
    var checkboxs = document.getElementsByName("ordersn");
    for(var i=0;i<checkboxs.length;i++){
    if(checkboxs[i].checked == true){
    bill = bill + ","+checkboxs[i].value;
    }
    }
    if(bill == ""){
    alert("请选择产品");
    return false;
    }

    bill=bill.substring(1);
    var url="toxls/new_product_cyimgs.php?bill="+bill;
    window.open(url,'_blank');
    }*/

function printcode(goods_id) {
    window.open ("newgoodsprint.php?goods_id=" + goods_id);
}


function view_speed(id) {
    $ (".finish_info[id!='finish_info" + id + "']").slideUp ('fast');
    $ ("#finish_info" + id).slideToggle ('fast');
}
function initWarehouse() {
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }

    bill = bill.substring (1);
    if (! confirm ('确认初始化选中SKU的仓库信息吗?')) {
        return false;
    }
    funsTool.showBodyMask ();
    funsTool.showModbox ("批量初始化仓库", 280, 450, function () {
        $.post (
            "addgoodsindex.php",
            {"doaction": "ajaxinitWarehouse", "bill": bill},
            function (msg) {
                funsTool.insertModBox (msg);
            }
        )
    });
}

function bitchModWarehouse() {//
    var bill = "";
    var checkboxs = document.getElementsByName ("ordersn");
    for (var i = 0; i < checkboxs.length; i ++) {
        if (checkboxs[i].checked == true) {
            bill = bill + "," + checkboxs[i].value;
        }
    }
    if (bill == "") {
        alert ("请选择产品");
        return false;
    }

    funsTool.showModbox ('批量修改仓库信息', 380, 500, function () {
        var html = createHtmlforModWarehouse (bill, '', '');
        funsTool.insertModBox (html);
    })


}

function ManagementKeyword() {
    window.open ('list/listkeyword.php');
}
function ManagementTitle() {
    window.open ('list/listtitle.php');
}
function ManagementCompetitor() {
    window.open ('list/listCompetitor.php');
}
function initWarehouseXls(){
	window.open('initWarehouse.php');
}

function createHtmlforModWarehouse(bill, saleuser, baocai) {
    var html = "<div style='margin:10px;'><input type='hidden' class='bill' value='" + bill + "'/>";
    html += "<p><span>上限:</span><input type='text' name='goods_sx' class='goods_sx' value=''/></p>";
    html += "<p><span>下限:</span><input type='text' name='goods_xx' class='goods_xx' value=''/></p>";
    html += "<p><span>库存报警天数:</span><input type='text' name='goods_days' class='goods_days' value=''/></p>";
    html += "<p><span>备货天数:</span><input type='text' name='purchasedays' class='purchasedays' value=''/></p>";
    html += "<p><span>头程运费(￥):</span><input type='text' name='abroad_freight' class='abroad_freight' value=''/></p>";
    html += "<p><span>毛重(g):</span><input type='text' name='gross_weight' class='gross_weight' value=''/></p>";
    html += "<p><span>包材:</span>" + baocai + "</p>";
    html += "<p><span>销售人员</span>:" + saleuser + "</p>";
    html += "<p><input type='button' value='提交' onclick='submitModWarehouse(this)'/></p>";
    html += "</div>";
    return html;

}

function Handle() {
    var val = $ ("#handle").val ();
    switch (val) {
        case '1':
            deleteallsystem ();
            break; //删除
        case '2':
            deletesure ();
            break;//彻底删除
        case '3':
            copyproducts ();
            break;//复制
        case '4':
            location.href = 'productadd_audit.php?module=goods&auditted=0&type=add&action=新品添加';
            break;//添加货品(开发中)
        case '5':
            break;
            //location.href = 'productaddxls_audit.php?module=goods&auditted=0&action=货品资料批量更新';
            //break;//批量导入货品
        case '6':
            initWarehouse ();
            break;//批量初始化库存
        case '7':
            batchauditlist ();
            break;//批量提交到审批
        case '8':
            batchCopyRight ('', '', '131px');
            break;//批量审核版权
        case '9':
            batchApproved ('');
            break;//批量审核通过
        case '10':
            updatejindu ();
            break;//更新进度
        case '11':
            location.href = 'productadd_audit_cy.php?module=goods&auditted=5&type=add&action=新品添加';
            break;//添加货品(新创意)
        case '12':
            changeTokf (true, '');
            break;//批量转到开发中
        case '13':
            changeToCtu ();
            break;//批量转到等待传图
        case '14':
            openUpdateimgs (1);
            break;//批量传产品图v3.0
        case '15':
            openUpdateimgs (2);
            break;//批量传草图v3.0
        case '16':
            changeTokf (true, '');
            break;//批量传草图v2.0
        case '17':
            bitchModWarehouse ();
            break;//批量修改仓库信息
        case '18':
            ManagementKeyword ();
            break;//管理sku关键字
        case '19':
            ManagementTitle ();
            break;//管理sku title信息
        case '20':
            ManagementCompetitor ();
            break;//管理sku 竞争者信息
		case '21':
            initWarehouseXls ();
            break;//excel 初始化
        case '22':
            location.href = 'plgetskus.php?module=goods&action=批量生成SKU';
            break;
        case '333':
            location.href = 'plimportprices.php?module=goods&action=批量导入询价信息';
            break;
        case '':
            break;
        default :
            funsTool.showTips (false, '您的选择有误!', 1500);
            break;
    }

}
//显示具体进度
/*        $("span.view_speed").live('click',function(){
    $(this).next('div.finish_info').slideToggle('fast');
    });*/

document.onkeydown = function (event) {
    e = event ? event : (window.event ? window.event : null);
    if (e.keyCode == 13) {
        searchorder ();
    }
};
$ (document).ready (function () {
    $ ('.fanhui').click (function () {
        var goods_id = $ (this).attr ('lang');
        //alert("#fhnote"+goods_id);
        $ ("#fhnote" + goods_id).toggle ('slow');
    });


    //
    $ (".auditboxdata b").live ('click', function () {
        $ (this).parent (".auditboxdata").remove ();
    });

    $ ("body").click (function (e) {
        //alert($(e.target).html());
        if ($ (e.target).attr ('class') != 'jindu_bar') {
            $ (".finish_info").slideUp ('fast');
        }
    });

});

