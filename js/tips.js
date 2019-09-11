var fun=(function(){
    var showTips=function(jq,str){
        var str="<div class='class_tips' style='position:absolute;width:350px;border:1px solid slategray;padding:5px;border-radius:5px;background:skyblue;'>"+
            "<h1 style='font-size:13px;line-height:15px;color:#333'>"+str+"</h1>"+
            "<span></span>"+
            "<i class='class_tips_z' style='transform:rotate(-132deg);-ms-transform:rotate(-132deg);-moz-transform:rotate(-132deg);-webkit-transform:rotate(-132deg);-o-transform:rotate(-132deg); background:skyblue;display:block;height:8px;width:8px;position:absolute;margin:1px 0 0 2px;border-left:1px solid slategray;border-top:1px solid slategray;'></i>"+
            "</div>";
        jq.focus(function(){
            if($(this).parent().find(".class_tips").length<=0)$(this).before(str);
            var h=-$(this).parent().find(".class_tips h1").height()-18+"px";
            var w=$(this).width();
            $(this).parent().find(".class_tips").css({marginTop:h,marginLeft:w-20});

        }).blur(function(){ //return;
            $(this).parent().find(".class_tips").remove();
        });
    };
    var log=function(str){
        console.log(str);
    };

    var showleftfloatbox=function(that,leftstart,leftend){// left 收起的left值
        var mainbox=$("#floatsearchbox");
        var mleft=parseInt(mainbox.css("marginLeft"));
        //log(mleft);
        var el=$(that);
        var leftend=leftend||-223;
        var leftstart=leftstart||-10;
        var inhtml='';
        if(mleft<leftstart){// 关闭状态
            inhtml='◄';
            mainbox.animate({"marginLeft":leftstart},'fast');
        }else{//开启状态
            inhtml='►';
            mainbox.animate({"marginLeft":leftend},'fast');
        }
        el.html(inhtml);
    };

    return {
        showTips:showTips,
        showleftfloatbox:showleftfloatbox
    }
})();