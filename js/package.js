/**
 * Created by Administrator on 2014/11/11.
 */

function showAndHideMorebox(){
    var bit=document.getElementById('moreFilter');
    var display=bit.style.display;
    if(display=='none'||display==''){
        bit.style.display="block";return;
    }
    bit.style.display="none";
}

function showAndHideMorebox01(){
    var bit=document.getElementById('moreController');
    var display=bit.style.display;
    if(display=='none'||display==''){
        bit.style.display="block";return;
    }
    bit.style.display="none";
}


function check_all(that){
    var er=$(that).prop("checked");
    $("input.package_cbox").prop("checked",er);
}

function getVal(id){
    return $.trim($("#"+id).val());
}

function getCshu(val,str){
    val= $.trim(val);
    return val==""?'':'&'+str+'='+val;
}


function searchorder(action){
    var keys=getVal("keys");
    var searchtype=getVal("searchtype");
    var warehouse=getVal("warehouse");
    var start=getVal("start");
    var end=getVal("end");
    var createuser=getVal("createuser");
    var perPage=getVal("perPage");
    var url=location.href;
    var file=url.match(/\/([a-z_]+\.php)/i);
    if(file.length<1){
        return false;
    }
    ///alert();
    var url=file[1]+"?module=package&action="+action+getCshu(keys,'keys')+getCshu(searchtype,'searchtype')+getCshu(warehouse,'warehouse')+getCshu(createuser,'createuser')+getCshu(end,'end')+getCshu(start,'start')+getCshu(perPage,'perPage');
    location.href=url;
}

function orderbyUrgent(){
    var url=location.href;
    url=url.replace("&sorturgent=1","");
    url=url+"&sorturgent=1";
    location.href=url;
}


function getBaseUrl(){
    var url=location.href;
    var file=url.match(/\/([a-z_]+\.php)/i);
    if(file.length<1){
        return '';
    }
    return file[1];
}

function ChangeThePackStatus(ID,str,PID){
    var url=getBaseUrl();
    var s=null;
    if(str=='submit'){
        s=2;
        if(!confirm("confirm to submit the package?")){
            return false;
        }
    }else if(str=='void'){
        if(!confirm("confirm Set package as void ?")){
            return false;
        }
        s=5;
    }else{
        if(!confirm("confirm Set package as Drafe ?")){
            return false;
        }
        s=1;
    }
    funsTool.showModbox("修改包裹状态",300,420,function(){
        $.post(
            url,
            {"ID":ID,"PID":PID,"s":s,"doaction":"ajaxSetPackStatus"},
            function(data){
                funsTool.insertModBox(data,true);
            }
        )
    });

}

function changeUrgent(id){
    id= $.trim(id);
    if(id==''){
        funsTool.showTips(false,'ID Error',1600);return;
    }
    var url=getBaseUrl();
    $.post(
        url,
        {"PID":id,"doaction":"ajaxSetUrgent"},
        function(data){
            data= $.trim(data);
            if(data=='-2'){
                funsTool.showTips(false,'Change Failed:ID',1600);return;
            }
            if(data=='-1'){
                funsTool.showTips(false,'Change Failed:DB',1600);return;
            }
            //alert(data);//SUCCESS959SUCCESS1
            if(data.indexOf("SUCCESS")>=0){
                data=data.split("SUCCESS");

                var id=data[1];
                var classNum=data[2];
                $("#hongqi"+id).removeClass("hongqibg1");
                $("#hongqi"+id).removeClass("hongqibg2");
                $("#hongqi"+id).removeClass("hongqibg3");
                $("#hongqi"+id).removeClass("hongqibg4");
                $("#hongqi"+id).removeClass("hongqibg5");
                $("#hongqi"+id).addClass("hongqibg"+classNum);
            }
        }
    )

}

function prints(){
    var str='';
    $("input.package_cbox").each(function(){
        if($(this).prop("checked")){
            str+=$(this).val()+','
        }
    });

    str=str.replace(/,$/,'');
    if(str==''){
        alert('Please select the order');
        return ;
    }
    var url="toxls/package_print.php?PID="+str;
    window.open(url);
}

//批量提交
function ChangeThePackStatusMore(){
    var str='';
    $("input.package_cbox").each(function(){
        if($(this).prop("checked")){
            str+=$(this).val()+','
        }
    });

    str=str.replace(/,$/,'');
    if(str==''){
        alert('Please select the order');
        return ;
    }
    var url=getBaseUrl();

    funsTool.showModbox("批量提交",300,420,function(){
        $.post(
            url,
            {"str":str,"doaction":"ajaxChangeThePackStatusMore"},
            function(data){
                funsTool.insertModBox(data);
            }
        )
    })

}
