/**
 * Created with JetBrains PhpStorm.
 * User: 谭联星
 * Date: 14-3-26
 * Time: 上午9:58
 * To change this template use File | Settings | File Templates.
 */
funsTool=(function(){
    //关于快速选择
    var fuck=function(o){
        var vals=$.trim(o.value);
        if(vals==""){
            return;
        }
        //谁懂大写字母的悲哀
        var caseVals=vals.toUpperCase();
        //执行查找
        var father=$(o).next("select");
        father.css("position","absolute");
        var choose=$(o).next("select").find("option");
        var len=choose.length;
        var j=0;
        //console.log(caseVals);
        for(var i=0;i<len;i++){
            var html=choose.eq(i).html();
            if(html.indexOf(vals)!=-1||html.indexOf(caseVals)!=-1){
                j++;
                if(j==1){
                    choose.eq(i).attr("selected","selected");//alert(html);
                    //father.val(choose.eq(i).val());
                    //console.log(choose.eq(i).val());
                }
                choose.eq(i).show();

            }else{
                choose.eq(i).hide();
            }

        }
        if(j==0){
            //father.val("");
            choose.eq(0).show();
        }
        //console.log(j);
        $(father)[0].size=j<=20?j:20;
        choose.css("z-index","9999");
        $(father).change(function(){
            $(this)[0].size=1;
            $(this).css("position","static");
            $(o).val("");
        })
        choose.click(function(){
            $(this).show();$(father)[0].size=1;
            $(father).css("position","static");
        })
        $(o).click(function(){
            if($.trim($(this).val()).length==""){
                choose.show();
                $(father)[0].size=1;
                $(father).css("position","static");
            }
        })

    }
    var mychoose=function(that){
        var o=that;
        $(o).keyup(function(){
            fuck(o);
        });
    }
    var showTips=function(bool,strs,mytimes){
        var mytimes=mytimes||1000;
        if($("#mytips_show_hide_for_action").length>0){
            $("#mytips_show_hide_for_action h1").html(strs);
        }else{
            var str="<div id='mytips_show_hide_for_action' style='position:fixed;top:-42px;left:50%;z-index:9999;background:#999;height: 40px;width:200px;border-radius: 0 0 5px 5px;margin-left: -100px;'>";
            str+="<h1 style='font-size:13px;background:#fff;color:#393;border-radius: 0 0 5px 5px;position:absolute;margin-left:5px;width:190px;max-width:190px;line-height:35px;text-align: center;height:35px;'>"+strs+"</h1>";
            str+="</div>";
            $("body").prepend(str);
        }
        var O=$("#mytips_show_hide_for_action");
        if(!bool){
            $("#mytips_show_hide_for_action h1").css("color","#f00");
        }else{
            $("#mytips_show_hide_for_action h1").css("color","#393");
        }

        O.show();
        O.animate({"top":"0px"},'800',function(){
            setTimeout(function(){
                $("#mytips_show_hide_for_action").animate({"top":"-42px"},'1000',function(){
                    $("#mytips_show_hide_for_action").hide();
                });
            },mytimes);
        });
    };
    var chosedlist=function(o,select){
        var vals=$.trim(o.value);
        if(vals==""){return;}
        var caseVals=vals.toUpperCase();
        //var nextselect=select;
        //var savedate='';
        var listr='';
        select.find("option").each(function(){
            var data=$(this).html();
            var dataU=data.toUpperCase();
            var val=$(this).val();
            if(data.indexOf(vals)!=-1||dataU.indexOf(caseVals)!=-1){
                var re=new RegExp(vals,'i');
                data=data.replace(re,"<b style='color:#0066cc'>"+vals+'</b>');
                //data=data.replace("/"+vals+"/i","<b style='color:#0066cc'>"+vals+'</b>');
                //data=data.replace(caseVals,"<b style='color:#0066cc'>"+caseVals+'</b>');
                listr+="<li val='"+val+"'>"+data+"</li>";
            }
        })
        //alert(listr);
        var ul=select.next('div').find('ul');
        ul.html($(listr));
        if(listr.length>0){
            select.next('div').show();
        }else{
            select.next('div').hide();
            return;
        }
        ul.children("li").bind('click',function(){
            select.val($(this).attr('val')).change();
            ul.children("li").unbind().remove();
            select.next('div').hide();
            $(o).val('').unbind();
        });


    };
    var findselect=function(that){
        var o=that;
        $(o).bind('blur',function(){
            $(this).unbind();
        })
        $(o).bind('keyup',function(e){
            if($.trim($(this).val())==''){
                $(this).next('select').next(".c_list").hide();
                return;
            }
            var select=$(this).next('select');
            chosedlist(o,select);
        });
    };

    var showImgbox=function(that){
        if($("#showMycheckImgbox").css('display')){
            var src=$("#showMycheckImgbox img").attr("src");//正在显示的图片
            $("#showMycheckImgbox b").unbind();
            $("#showMycheckImgbox").remove();
            if(src==that.src){
                return;
            }
        }
        var h=that.height,w=that.width;
        if(w>600){h=600*h/w;w=600;}
        if(h>600){w=600*w/h;h=600;}
        var mh="-"+h/2+'px',mw="-"+w/2+'px';
        var strbox = "<div id='showMycheckImgbox' style='margin-left:"+mw+";margin-top:"+mh+";position:fixed;z-index:9999;padding:10px;top:50%;left:50%;background:url("+'"'+"themes/Sugar5/images/maskbgs.png"+'"'+");'>";
        strbox+="<div><b style='position:absolute;cursor: pointer;height:27px;width:27px;background:#BABABA;text-indent:10px;line-height: 27px;margin:-10px;'>x</b>";
        strbox += "<img src='" + that.src + "' style='height:" + h + "px;width:" + w + "px'/>";
        strbox += "</div></div>";
        $("body").prepend($(strbox));
        $("#showMycheckImgbox b").bind('click',function(){
            $("#showMycheckImgbox b").unbind();
            $("#showMycheckImgbox").remove();
        })
        //alert(that.height+','+that.width);
    };

    var showImgloadError=function(that){
        showTips(false,'图片载入出错!',1500);
    }

    var showImg=function(that){
        var src=that.src||$(that).attr("src");
        var img=new Image();
        //alert(src);
        img.src=src;
        img.onload=function(){
            showImgbox(this);
        };
        img.onerror=function(){
            showImgloadError(this);
        }


    };

    var creatMask=function(){
        var mask='';
        if(!$("#bodyMask").css('display')){
            var str="<div id='bodyMask' style=\'display:none;height: 100%;width:100%;background:url(./Public/admin/img/min-img/maskbgs.png);position: absolute;z-index:100;top:0;left:0;\'></div>";
            $("body").append($(str));
        }
        mask=$("#bodyMask");
        mask.height($(document).height()).width($(document).width());
        return mask;
    };

    var hideBodyMask=function(){
        //$("html").css('overflow','scroll');
        $("#bodyMask").hide();
        $("html").css({overflowY:'auto'})
    };

    var showBodyMask=function(){
        var mask=creatMask();
        mask.show();
        $("html").css({overflow:'hidden'})
    };
    var creatModbox=function(title,h,w){
        if(!$("#showModbox").css('display')){//不存在
            var str="<div id='showModbox' style='display:none;height:"+h+"px;width:"+w+"px;position:fixed;z-index:200;top:50%;left:50%;";
            str+="background:#fff;border:2px solid #999988;margin-left:-"+w/2+"px;margin-top:-"+h/2+"px'>";
            str+="<div class='Modboxtitlebox'><span class='Modboxtitle'>"+title+"</span><span class='btn_close' onclick='funsTool.deleteModBox()'></span><div class='clear'></div></div>";
            str+="<div class='ModboxContent'>" +
                "<div id='ModboxContent' style='height:"+(h-30)+"px'><img class='loading' src='themes/Sugar5/images/loading.gif'/></div>"
            "</div>";
            str+="</div>";
            $("body").append($(str));
        }
        return $("#showModbox");
    };

    var deleteModBox=function(){
        $("#ModboxContent").html("<img class='loading' src='themes/Sugar5/images/loading.gif'/>");
        $("#showModbox").remove();
        hideBodyMask();

    };
    var insertModBox=function(data,margin){
        margin=margin||false;
        if(margin){
            data='<div style="margin:15px;">'+data+'</div>'
        }
        $("#ModboxContent").html(data);
    };
    var showModbox=function(title,height,wight,callback){
        var box=creatModbox(title,height,wight);
        showBodyMask();
        box.show();
        callback();
    };

    var checkPower=function(data){
        var data=data.toString();
        if(data.indexOf('-100')===0){
            showTips(false,'您无权做此操作',1400);
            return false;
        }else{
            return true;
        }
    };

    var creatListMenu=function(Jq,id,data){
        var html="<ul class=\"funsToolListUl\" id=\""+id+"\"><{li}></ul>";
        var li='';
        for(var i= 0,l=data.length;i<l;i++){
            li+="<li>"+data[i]+"</li>";
        }
        html=html.replace('<{li}>',li);
        Jq.after($(html));
    };

    var listMenu=function(that,data){
        var id=$(that).attr('id');
        if($("#ul"+id).html()!=null){
            //console.log($("#ul"+id).html());
            return false;
        }
        creatListMenu($(that),'ul'+id,data);
    };

    return {
        showTips:showTips,
        mychoose:mychoose,
        findselect:findselect,
        showImg:showImg,
        showModbox:showModbox,
        deleteModBox:deleteModBox,
        insertModBox:insertModBox,
        checkPower:checkPower,
        showBodyMask:showBodyMask,
        hideBodyMask:hideBodyMask,
        listMenu:listMenu
        //showBodyMask:showBodyMask,
        //hideBodyMask:hideBodyMask
    }
})();
