
/*
* 刊登要素在哪里-------- SSSS
* */
function addNewKeyword(){
    //alert("ss");
    var html='<li class="keyword_li" data_id=""><input type="text" value="" onblur="sendModWord(this,1)"><b class="del_target" onclick="deleteWord(this)">×</b></li>';
    $(".listkeyword .keyword_ul").append($(html));
    $(".listkeyword .keyword_ul").find("li.empty").remove();
}

function addNewlistTitle(){
    var TypeArr=['E','A','W'];
    var option='';
    for(var i=0;i<TypeArr.length;i++){
        option+="<option value='"+(i+1)+"'> "+TypeArr[i]+" </option>";
    }
    var price="<input type='text' class='t_price' style='width:55px!important;padding-left:2px;' placeholder='输价格' value=''/>";
    var selectd="<select class='profselect' onchange='ModthisTitleType(this)'>"+option+"</select>"+price+"<input style='margin:0 5px;' onclick='sendModTitle(this,1)' type='button' value='保存'/>";//onblur="sendModTitle(this,1)"
    var html='<div class="title_line" data_id="" type="1" title="E"><input class="input_title" type="text" value="" onkeyup="calcThisTitle(this)">'+selectd+'<b class="del_target" onclick="deleteTitle(this)">×</b></div>';
    $(".listtitle .title_box").append($(html));
    $(".listtitle .title_box").find("div.empty").remove();
}

function addNewSalePoint(){
    var html='<div class="title_line" data_id="">'
        +'<textarea  class="input_title" rows="2" cols="145" onkeyup="calcthisSalePointlength(this)"></textarea>'
        +"<input style='margin:-10px 5px 0 5px;' onclick='sendModSalepoint(this,1)' type='button' value='保存'/>"
        +'<b class="del_target" onclick="deleteSalePoint(this)">×</b></div>';
    $(".listpoint .title_box").append($(html));
    $(".listpoint .title_box").find("div.empty").remove();
}

function deleteSalePoint(that){
    that=$(that);
    var id=that.parent("div").attr("data_id");
    if(id==''){
        that.parent("div").remove();
        delete that;
        return false;
    }
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    var title=that.prevAll("textarea.input_title").val();
    if(!confirm("确定删除产品卖点:"+title+"?")){
        return false;
    }

    var url=getSendUrl();
    $.post(
        url+'&id='+id+'&user='+user,
        {"id":id,"sku":sku,"doaction":"ajaxdelSalePoint"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'产品卖点操作失败',1600);return;
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
            if(data=='2'){
                that.parent("div").remove();
                delete that;
                funsTool.showTips(true,'产品卖点操作成功',1600);
            }
        }
    )

}

function sendModSalepoint(that,type){
    that=$(that);
    var url=getSendUrl();
    var value=$.trim(that.prev().val());
    var id=that.parent("div").attr("data_id");
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");

    if(value==''){
        funsTool.showTips(false,'产品卖点为空!');
        return false;
    }

    if(value.length>500){// 阿里 120
        funsTool.showTips(false,'产品卖点最多500字符');
        return false;
    }

    $.post(
        url+'&id='+id+'&user='+user,
        {"id":id,"value":value,"sku":sku,"type":type,"doaction":"ajaxmodsalepoint"},
        function(data){
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }

            if(data=='-2'){
                funsTool.showTips(false,'产品卖点-操作失败',1600);return;
            }
            if(data=='-3'){
                funsTool.showTips(false,'最多只能添加10个卖点',1600);return;
            }
            if(data=='-4'){
                funsTool.showTips(false,'产品卖点已存在',1600);return;
            }
            if(data>0){
                that.parent("div").attr("data_id",data);
                that.parent("div.title_line").html(value);
                delete that;
                funsTool.showTips(true,'产品卖点操作成功',1600);
            }
        }
    )
}


function calcthisSalePointlength(that){
    var len=$(that).val().length;//console.log(len);
    $("#calcthisSalePointlength").html(len);
}

function calcThisTitle(that){
    var len=$(that).val().length;//console.log(len);
    $("#calcthisTilelength").html(len);
}

function  ModthisTitleType(that){
    var val=$(that).val();
    var TypeArr=['E','A','W'];
    $(that).parent('.title_line').attr('type',val).attr('title',TypeArr[val-1]);
    //var isadd=$(that).parent('.title_line').attr('data_id')!==''?'0':'1';
    //sendModTitle($(that).prev(),isadd);
}

function  addNewCompetitor(){
    var o=$(".competitor .addcompetitor");
    o.show();
    o.find("input.item_mod").val("");
    o.find("select.pltform").val("");
}

function subaddcompetitor(that){
    that=$(that);
    var vvv= $.trim(that.val());
    if(vvv!='添加竞争者item'){
        return false;
    }
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    var item=$(".addcompetitor input.item_mod").val();
    var pltform=$(".addcompetitor select.pltform").val();
    if(sku==''){
        funsTool.showTips(false,'SKU输入有误!');
        return false;
    }
    if(pltform==''){
        funsTool.showTips(false,'请选择平台!');
        return false;
    }
    if(pltform==3){
        if(item==''){
            item='000';
        }
    }
    //alert(item);
    if(!/^[\w]+$/.test(item)||item==''){
        funsTool.showTips(false,'item输入有误!');
        return false;
    }
    that.val("正在添加...");
    $.post(
        'list/listCompetitor.php?itemid='+item+'&sku='+sku+'&user='+user,
        {"itemid":item,"sku":sku,pltform:pltform,"doaction":"ajaxaddComlist"},
        function(data){
            that.val("添加竞争者item");
            $ ("#show_msg_addlist").html(data);
        }
    );
}
function deleteTitle(that){
    that=$(that);
    var title=that.prevAll("input.input_title").val();
    var id=that.parent("div").attr("data_id");
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(id==''){
        that.parent("div").remove();
        delete that;
        return false;
    }
    if(!confirm("确定标题:"+title+"?")){
        return false;
    }
    var url=getSendUrl('title');
    $.post(
        url+'word='+encodeURIComponent(title)+'&id='+id+'&user='+user,
        {"id":id,"sku":sku,"doaction":"ajaxdellisttitle"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'标题删除-操作失败',1600);return;
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
            if(data=='2'){
                that.parent("div").remove();
                delete that;
                funsTool.showTips(true,'标题删除-操作成功',1600);
            }
        }
    )
}

function deleteWord(that){
    that=$(that);
    var word=that.prev ("input").val();
    var id=that.parent("li").attr("data_id");
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(id==''){
        that.parent("li").remove();
        delete that;
        return false;
    }
    if(!confirm("确定删掉SKU"+sku+" 关键字:"+word+"?")){
        return false;
    }
    var url=getSendUrl('word');
    //console.log(url);
    $.post(
        url+'word='+encodeURIComponent(word)+'&id='+id+'&user='+user,
        {"id":id,"sku":sku,"doaction":"ajaxdellistword"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'关键词删除-操作失败',1600);return;
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
            if(data=='2'){
                that.parent("li").remove();
                delete that;
                funsTool.showTips(true,'关键词删除-操作成功',1600);
            }
        }
    )
}

function deleteList(that,id){
    that=$(that);
    var item=$("#list_tr_"+id).children('td').eq(0).html();
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(!confirm("确定删掉SKU"+sku+" 竞争对手item:"+item+"?")){
        return false;
    }
    $.post(
        'list/listCompetitor.php?type=del&item='+encodeURIComponent(item)+'&id='+id+'&user='+user,
        {"id":id,"sku":sku,"doaction":"ajaxdellistitem"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'竞争对手-操作失败',1600);return;
            }
            if(data>0){
                funsTool.showTips(true,'竞争对手-操作成功',1600);
            }
        }
    )
}

function sendModWord(that,isAdd){
    that=$(that);
    var word= $.trim(that.val());
    var id=that.parent("li").attr("data_id");
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(word==''){//格式无误
        funsTool.showTips(false,'关键字-格式有误!');
        return false;
    }
    var url=getSendUrl('word');
    //console.log(url);
    $.post(
        //'list/listkeyword.php?word='+encodeURIComponent(word)+'&id='+id+'&user='+user,
        url+'word='+encodeURIComponent(word)+'&id='+id+'&user='+user,
        {"id":id,"word":word,"isAdd":isAdd,"sku":sku,"doaction":"ajaxmodfilistword"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'关键字-操作失败',1600);return;
            }
            if(data=='-3'){
                funsTool.showTips(false,'最多只能添加10个关键字',1600);return;
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
            if(data>0){
                //恢复去掉input
                that.parent("li").attr("data_id",data);
                that.parent("li").html(word);
                delete that;
                funsTool.showTips(true,'关键字-操作成功。',1600);
            }
        }
    )
}

function sendModTitle(that,isAdd){
    that=$(that);
    var title= $.trim(that.prevAll("input.input_title").val());
    var price= $.trim(that.prevAll("input.t_price").val());

    var id=that.parent("div").attr("data_id");
    var type=that.parent("div").attr("type");
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(isNaN(parseFloat(price))){
        alert("价格必须是数字!");
        return false;
    }

    if(title==''){//格式无误
        funsTool.showTips(false,'标题-格式有误!');
        return false;
    }
    if(title.length>120&&type==2){// 阿里 120
        funsTool.showTips(false,'标题-速卖通最多120字符');
        return false;
    }


    if(title.length>80&&type==1){// ebay 80
        funsTool.showTips(false,'标题-ebay最多80字符');
        return false;
    }
    var url=getSendUrl('title');
    //console.log(url);
    $.post(
        url+'title='+encodeURIComponent(title)+'&id='+id+'&user='+user,
        {"id":id,"title":title,"isAdd":isAdd,"sku":sku,"type":type,price:price,"doaction":"ajaxmodfilisttitle"},
        function(data){
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }

            if(data=='-2'){
                funsTool.showTips(false,'标题-操作失败',1600);return;
            }
            if(data=='-3'){
                funsTool.showTips(false,'最多只能添加8个标题',1600);return;
            }
            if(data=='-4'){
                funsTool.showTips(false,'该标题已存在',1600);return;
            }
            if(data=='-5'){
                funsTool.showTips(false,'该标题相似度太高',1600);return;
            }
            if(data>0){
                that.parent("div").attr("data_id",data);
                that.parent("div.title_line").html(title);
                delete that;
                funsTool.showTips(true,'标题-操作成功请刷新',1600);
            }
            if(data==='0'){
                that.parent("div.title_line").html(title);
                delete that;
                funsTool.showTips(true,'标题-修改成功请刷新',1600);
            }
        }
    )
}

function getSendUrl(type){
    var file=$("#urlAction").val();
    var defaulturl='';
    if(type=='word'){
        defaulturl='list/listkeyword.php?';
    }else if(type=='cate'){
         defaulturl='addgoodsindex.php?';
    }else if(type=='description'){
        defaulturl='addgoodsindex.php?';
    }else if(type=='title'){
        defaulturl='list/listtitle.php?';
    }
    if(file=='kandengys.php'){
        return defaulturl;
    }

    var arr=file.split('.');
    var filename=arr[0];
    var ftype=filename.substr(filename.length-2,2);

    defaulturl="updatekdinfo.php?language="+ftype+"&type="+type+"&";
    return defaulturl;
}
//设定打开窗口并居中
function openwindow(url,name,iWidth,iHeight)
{
    var url; //转向网页的地址;
    var name; //网页名称，可为空;
    var iWidth; //弹出窗口的宽度;
    var iHeight; //弹出窗口的高度;
    var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
    var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
    window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars=yes,resizeable=no,location=no,status=no');
}
function showListInfo(sku,type) {
    // 2016 -03 -18
    var url="kandengys.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showListInfoFr(sku,path){
    var url=path+"kandengysfr.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showListInfoRu(sku,path){
    var url=path+"kandengysru.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showListInfoIt(sku,path){
    var url=path+"kandengysit.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showListInfoEs(sku,path){
    var url=path+"kandengyses.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showListInfoDe(sku,path){
    var url=path+"kandengysde.php?sku="+sku;
    openwindow(url,'',950,605);
    return ;
}

function showhidedescription(){
    //alert('sss');
    $(".descriptionDiv_content").toggle();
}

function saveSmtCateid(id){
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    var url=getSendUrl('cate');
    //console.log(url);
    $.post (
        url+"id="+id+"&user="+user,
        {sku: sku, doaction: "saveSmtCateid",id:id},
        function (data) {
            data= $.trim(data);
            if(data=='-2'){
                funsTool.showTips(false,'速卖通分类-操作失败：DB',1600);
            }
            if(data=='-3'){
                funsTool.showTips(false,'速卖通分类-参数错误!',1600);
            }
            if(data=='2'){
                funsTool.showTips(true,'速卖通分类-操作成功',1600);
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
        }
    )
}

function updatedccate(that){
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    var val= $.trim($(that).val());
    if(val==''){
        funsTool.showTips(false,'DC id不能为空',1600);
        return false;
    }
    var url=getSendUrl('cate');
    $.post (
        url+"dcid="+val+"&user="+user,
        {"sku": sku, "doaction": "savedcCateid","id":val},
        function (data) {
            data= $.trim(data);
            if(data=='-2'){
                funsTool.showTips(false,'DC id操作失败：DB',1600);
            }
            if(data=='-3'){
                funsTool.showTips(false,'DC id参数错误!',1600);
            }
            if(data=='2'){
                funsTool.showTips(true,'DC id操作成功请刷新',1600);
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
        }
    )
}

function stopDefault(e) {
    if(e && e.preventDefault){   //如果提供了事件对象，则这是一个非IE浏览器
        e.preventDefault();//阻止默认浏览器动作(W3C)
    } else {
        //IE中阻止函数器默认动作的方式
        window.event.returnValue = false;
    }
    return false;
}

function insertlistDiv(Arr,keyword){
    var Jq=$("#searchCateList");
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
        var id=Arr[i]['id'];
        var fullCate=Arr[i]['fullCate'];

        var reg=new RegExp(keyword,"ig");
        fullCate=fullCate.replace(reg,'<b>'+keyword+'</b>');
        str+='<li val="'+id+'"'+classstr+'>'+fullCate+'</li>';
        j++;
    }
    var o=Jq;
    var ul= o.children("ul");
    o.find('ul.c_list1').html($(str));
    if(str!=''){
        o.show();
        $("#searchCateBox").unbind("keydown").bind('keydown',function(event){ //上下按键功能
            var keyCode=getKeyCodes(event);
            //console.log(keyCode);
            //var ul=$("#searchCateList>ul");
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
                var idinfo=ul.children("li.li_hover");
                var id=idinfo.attr('val');
                ul.children("li").unbind().remove();
                Jq.hide();
                $("#smtid").val(id);saveSmtCateid(id);
            }
            //
            //alert(keyCode);
        });

        ul.children("li").bind('mouseenter',function(){ // mouseover 主要是实现 上下按键功能的
            ul.children("li").removeClass('li_hover');
            $(this).addClass('li_hover');
        });

        ul.children("li").bind('click',function(){
            //Jq.val($(this).attr('val')).unbind('keydown');
            var idinfo=ul.children("li.li_hover");
            var id=idinfo.attr('val');
            ul.children("li").unbind().remove();
            Jq.hide();
            $("#smtid").val(id);saveSmtCateid(id);
        });
    }
}

function realizeJson(str){
    if(JSON.parse){
        return JSON.parse(str);
    }
    return eval('('+str+")");
}

function getKeyCodes(event){
    var keyCode;
    var isie = (document.all) ? true : false;
    if(isie){keyCode = window.event.keyCode; }else {keyCode = event.which;}
    return keyCode;
}

function modfidescription(){
    if(undefined==SKU_description){
        alert("发生严重错误!编辑器初始化失败!");
    }
    var description=SKU_description.getSource();
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(description.indexOf('<script')>0||description.indexOf('<iframe')>0){
        funsTool.showTips(false,'含有不容许的字符',1600);return;
        return false;
    }
    var url=getSendUrl('description');
    $.post(
        url+'type=saveDiscription&sku='+sku+'&user='+user,
        {"sku":sku,description:description,"doaction":"ajaxsaveDiscription"},
        function(data){
            if(data=='-2'){
                funsTool.showTips(false,'描述-操作失败：DB',1600);return;
            }
            if(data=='-3'){
                funsTool.showTips(false,'描述-操作失败:参数有误',1600);return;
            }
            if(data=='-4'){
                funsTool.showTips(false,'描述操作失败:异常字符字符',1600);return;
            }
            if(data=='2'){
                funsTool.showTips(true,'描述-保存成功!',1600);return;
            }
            if(data=='-100'){
                funsTool.showTips(false,'无权操作',1600);return;
            }
        }
    )

}

function updatejinduOne(that){
    var sku=$("#modbox_main_sku").val();
    var user=$("#modbox_main_sku").attr("datamd5");
    if(!confirm("确定更新sku进度么?")){
        return false;
    }
    $.post (
        'addgoodsindex.php?user='+user,
        {'doaction': 'ajaxUpdatejinduOne', sku: sku},
        function (data) {
            data = $.trim(data);
            if(data=='2'){
                alert("更新成功");
            }else{
                alert("更新失败");
            }
        }
    );
}

// 2016-04-20 这里是验证是否刊登要素完成
function checkComplete(sku){
    var url=getSendUrl();
    funsTool.showModbox("sku: "+sku+" 刊登要素是否完成",350,680,function(){});
    $.post(
        url,
        {'doaction':'checkComplete',sku:sku},
        function(data){
            funsTool.insertModBox(data,true);
        }
    )
}

(function(){
    var ajaxTool={
        searchs:function(that,event){
            var event=window.event||event;
            var keyCode=getKeyCodes(event);
            var keyCodeArr=[37,38,39,40,15,17,13];
            if($.inArray(keyCode,keyCodeArr)!==-1){return;}

            $(that).unbind();
            var keyword= $.trim($(that).val());


            if(keyword==''){$("#searchCateList").hide();return;}
            if(keyword.length<4){
                return;
            }
            if(undefined==window.cache){
                window.cache={};
            }
            if(undefined!==window.cache[keyword]){ // 命中缓存
                insertlistDiv(window.cache[keyword],keyword);return;
            }
            var url=getSendUrl('cate',that);
            $.post(
                url,
                {'w':keyword,'doaction':'ajaxSearchSku'},
                function(data){
                    data=realizeJson($.trim(data));
                    window.cache[keyword]=data;
                    insertlistDiv(data,keyword);
                }
            );

        }

    };
    window.ajaxTool=ajaxTool;
})();


$(function(){
    //修改 关键字
    $("li.keyword_li").live("click",function(){
        var val=$(this).html();
        if(val.search(/<input\s/)==-1){
            var html='<input type="text" onBlur="sendModWord(this,0)" value="'+val+'"/><b class="del_target" onclick="deleteWord(this)">×</b>';
            $(this).html($(html));
           // $(this).find("input").focus();
        }
    });

    //修改标题
    $(".listtitle div.title_line").live("click",function(event){
        var val=$(this).html();
        var event=event||window.event;
        //val=val.replace(/\\/,"");
        //val=val.replace(/"/g,"\\\"");
        if(event.target.tagName.toLocaleLowerCase()!='div'){return false;}
        var type=$(this).attr("type");
        var p=$(this).attr("price");
        p=undefined==p?'':p;
        var TypeArr=['E','A','W'];
        if(val.search(/<input\s/)==-1){
            var option='';
            for(var i=0;i<TypeArr.length;i++){
                var se=i==(type-1)?' selected="selected" ':'';
                option+="<option value='"+(i+1)+"'"+se+"> "+TypeArr[i]+" </option>";
            }
            var price="<input type='text' class='t_price' style='width:55px!important;padding-left:2px;' placeholder='输价格' value='"+p+"'/>";
            var selectd="<select class='profselect' onchange='ModthisTitleType(this)'>"+option+"</select>"+price+"<input style='margin:0 5px;' onclick='sendModTitle(this,0)' type='button' value='保存'/>";
            var html='<input type="text" class="input_title" onkeyup="calcThisTitle(this)" value=""/>'+selectd+'<b class="del_target" onclick="deleteTitle(this)">×</b>';
            $(this).html($(html));//onBlur="sendModTitle(this,0)"
            $(this).find("input[type='text'].input_title").val(val);
        }
    });

    //修改标题
    $(".listpoint div.title_line").live("click",function(event){
        var val=$(this).html();
        var event=event||window.event;
        if(event.target.tagName.toLocaleLowerCase()!='div'){return false;}
        if(val.search(/<textarea\s/)==-1){
            var html='<textarea class="input_title" rows="2" cols="145" onkeyup="calcthisSalePointlength(this)">'+val+'</textarea>'
                +"<input style='margin:-10px 5px 0 5px;' onclick='sendModSalepoint(this,1)' type='button' value='保存'/>"
                +'<b class="del_target" onclick="deleteSalePoint(this)">×</b>';
            $(this).html($(html));
        }
    });

});