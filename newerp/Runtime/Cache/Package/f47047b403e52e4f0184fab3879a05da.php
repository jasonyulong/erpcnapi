<?php if (!defined('THINK_PATH')) exit(); include "include/config.php"; include "top.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css">
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/custom.css">
    <link href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.css" rel="stylesheet">
    <script src="/newerp/Public/js/jquery.js"></script>
    <script src="/newerp/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.jquery.min.js"></script>
    <script src="/newerp/Public/js/plugins/layer/laydate/laydate.js"></script>
    <style>
        th[type=sort] {
            cursor: pointer;
        }

        .text-primary {
            color: #1ab394;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .ibox-content {
            padding: 0
        }

        .search-bar > div > .searchTitle, .search-bar > div > div > .searchTitle {
            line-height: 30px;
        }

        .custom-table th {
            font-weight: normal;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-table td {
            border-bottom: 1px solid #f0f0f0;
        }

        /*.chosen-container .chosen-single {*/
            /*height: 30px;*/
            /*line-height: 30px;*/
            /*border: 1px solid #ccc;*/
        /*}*/

        .input-group {
            margin: 5px 5px;
        }

        .form-control {
            border: 1px solid #ccc;
            padding: 0px 12px;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }
        .chosen-container .chosen-single {
            height: 30px;
            line-height: 30px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>



    <style>
        /*主页面样式begin*/
        .mainData{text-align:center;height: 100%;width: 100%;margin: 0;padding: 0;}
        .ruleData{table-layout:fixed;empty-cells:show;border-collapse: collapse;margin:0 auto;width: 800px;}
        .title td{padding: 8px;height: 50px;background-color: #e0a9af;color: #8E2323;width: 100%;font-size: 14px;}
        .addButton td,.subButton td{height: 50px;float: left;margin: 15px 0px -10px 0px;}
        .addButton td .addRule{width: 60px;height: 30px;background: #FFFFFF;color: #000000;}
        .ruleEdit{width: 100%;}
        .ruleEdit td{white-space: nowrap;height: 40px;word-wrap:break-word;border: 1px solid #CCCCCC;background: #abc3d7;text-align: left;font-size: 14px;}
        .ruleEdit td input[type='text']{width:120px;}
        .subButton .subRule{background:#008000;width: 60px;height: 30px;color: #000000;}
        .error{color: #FF0000;}
        .success{color:#008000;}
        input:disabled{border:1px solid #DDD;background-color:#CCCCCC;color:#FF0000;}
        /*主页面样式end*/
        /*提示框样式begin*/
        *{ padding:0; margin:0; font-size:12px}
        .showMsg .guery {white-space: pre-wrap; /* css-3 */white-space: -moz-pre-wrap; /* Mozilla, since 1999 */white-space: -pre-wrap; /* Opera 4-6 */white-space: -o-pre-wrap; /* Opera 7 */	word-wrap: break-word; /* Internet Explorer 5.5+ */}
        a:link,a:visited{text-decoration:none;color:#0068a6}
        a:hover,a:active{color:#ff6600;text-decoration: underline}
        .showMsg{border: 1px solid #1e64c8; zoom:1; width:450px; height:174px;position:absolute;top:50%;left:50%;margin:-87px 0 0 -225px;background-color: #FFFFFF;}
        .showMsg h5{ color:#FF0000;  height:25px; line-height:26px;*line-height:28px; overflow:hidden; font-size:14px; text-align:center;}
        .showMsg .content{ padding:20px 30px 10px 37px; font-size:14px; height:66px;color: #008000}
        .showMsg .bottom{ background:#e4ecf7; margin: 0 1px 1px 1px;line-height:26px; *line-height:30px; height:26px; text-align:center}
        .showMsg .guery{background-position: left -460px;}
        .rule_explain a {text-decoration: none;font-size: 30px;color: red;font-weight: bold}
        .testswitch {
            position: absolute;
            width: 90px;
            -webkit-user-select:none;
            -moz-user-select:none;
            -ms-user-select: none;
            display: block;
            left:50%;
            margin: 10px;


        }

        .testswitch-checkbox {
            display: none;
        }

        .testswitch-label {
            display: block;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #999999;
            border-radius: 20px;
        }

        .testswitch-inner {
            display: block;
            width: 200%;
            margin-left: -100%;
            transition: margin 0.3s ease-in 0s;
        }

        .testswitch-inner::before, .testswitch-inner::after {
            display: block;
            float: right;
            width: 50%;
            height: 30px;
            padding: 0;
            line-height: 30px;
            font-size: 14px;
            color: white;
            font-family:Trebuchet, Arial, sans-serif;
            font-weight: bold;
            box-sizing: border-box;
        }

        .testswitch-inner::after {
            content: attr(data-on);
            padding-left: 10px;
            background-color: #00e500;
            color: #FFFFFF;
        }

        .testswitch-inner::before {
            content: attr(data-off);
            padding-right: 10px;
            background-color: #EEEEEE;
            color: #999999;
            text-align: right;
        }

        .testswitch-switch {
            position: absolute;
            display: block;
            width: 22px;
            height: 22px;
            margin: 4px;
            background: #FFFFFF;
            top: 0;
            bottom: 0;
            right: 56px;
            border: 2px solid #999999;
            border-radius: 20px;
            transition: all 0.3s ease-in 0s;
        }

        .testswitch-checkbox:checked + .testswitch-label .testswitch-inner {
            margin-left: 0;
        }

        .testswitch-checkbox:checked + .testswitch-label .testswitch-switch {
            right: 0px;
        }
        .sap{
            position: absolute;
            left:0;

        }
        /*提示框样式end*/
    </style>
    <div class="mainData">
        <p class="rule_explain"><a href="http://192.168.1.9/web/#/2?page_id=103" target="_blank">平均重量限制说明</a></p>
        <table class="ruleData" id="ruleData">
            <tr>
                <td>
                    <div style="position:relative;width: 200px;height: 50px;line-height: 50px;">
                        <span class="sap">是否开启限重功能：</span>
                        <div class="testswitch">
                            <input class="testswitch-checkbox" id="onoffswitch" type="checkbox" <?php if(($config['limit_weight_status']) == "1"): ?>checked<?php endif; ?> />
                            <label class="testswitch-label" for="onoffswitch">
                                <span class="testswitch-inner" data-on="ON" data-off="OFF"></span>
                                <span class="testswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="config_id" value="<?php echo ($config['id']); ?>" />
                </td>
            </tr>
            <tr class="title">
                <td>请按照重量大小顺序合理添加范围和差异值 所有操作必须点击确定按钮生效。重量区间为【开始重量，结束重量】</td>
            </tr>
            <tr class="addButton">
                <td><input type="button" value="＋添加" class="addRule"></td>
            </tr>
            <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr class="ruleEdit" isEdit="0" data="<?php echo ($vo["id"]); ?>"><td>订单重量范围：<input name="min" type="text" value="<?php echo ($vo["weight_begin"]); ?>" onKeyUp="check(this);" disabled />g-<input name="max" type="text" value="<?php echo ($vo["weight_end"]); ?>" onKeyUp="check(this);" disabled />g；允许的差异值：<input name="dif" type="text" value="<?php echo ($vo["allow_dif"]); ?>" onKeyUp="check(this);" disabled />g<input type="button" value="Ｘ删除" onclick="deleteRule(this);" /><input type="button" value="修改" onclick="updateRule(this);" /></td></tr><?php endforeach; endif; ?>
            <tr class="subButton">
                <td><input type="button" value="确定" class="subRule"></td>
            </tr>
        </table>
    </div>
    <div class="showMsg" id="sh" style="text-align:center;display:none;">
        <h5>提示信息</h5>
        <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline; max-width:280px"></div>
        <div class="bottom"><a href="javascript:void(0); " onclick="showMsg(false);" >关闭</a></div>
    </div>
    <script>
        document.title = '出库称重规则设置';
        //添加功能
        $(".addRule").unbind("click").click(function(){
            var html = '<tr class="ruleEdit" isEdit="0" data="0">';
            html+='<td>订单重量范围：<input name="min" type="text" value="0.0" onKeyUp="check(this);" />g-';
            html+='<input name="max" type="text" value="0.0" onKeyUp="check(this);" />g；允许的差异值：';
            html+='<input name="dif" type="text" value="0.0" onKeyUp="check(this);" />g<input type="button" value="Ｘ删除" onclick="deleteRule(this);" /></td>';
            $(".subButton").before(html);
        });
        //保存功能
        $(".subRule").unbind("click").click(function(){
            //获取当前页面所有输入框
            $(".ruleData .ruleEdit").each(function(){
                var self = $(this);
                var isEdit = parseInt(self.attr("isEdit"));
                //主键id
                var data = parseInt(self.attr("data"));
                var min = self.children().children("input[name='min']").val();
                var max = self.children().children("input[name='max']").val();
                var dif = self.children().children("input[name='dif']").val();
                self.children("td").find(".error").remove();
                self.children("td").find(".success").remove();
                //表示有修改的
                if(isEdit == 1){
                    $.post('/t.php?s=/Package/WeighSystem/ruleSet',{data:data,min:min,max:max,dif:dif},function(e){
                            if(e.status == 1){
                                self.children().children("input[name='min']").val(e.data.weight_begin);
                                self.children().children("input[name='min']").attr("disabled",true);
                                self.children().children("input[name='max']").val(e.data.weight_end);
                                self.children().children("input[name='max']").attr("disabled",true);
                                self.children().children("input[name='dif']").val(e.data.allow_dif);
                                self.children().children("input[name='dif']").attr("disabled",true);
                                self.attr("data",e.data.id);
                                self.attr("isEdit",0);
                                var msg = "<span class='success'>"+e.msg+"</span>";
                                self.children("td").append(msg);
                            }else{
                                var msg = "<span class='error'>"+e.msg+"</span>";
                                self.children("td").append(msg);
                            }
                    },"json")
                }else{
                    // var msg = "<span class='error'>请确认有进行修改</span>";
                    // self.children("td").append(msg);
                }
            })
        });
        function updateRule(obj){
            var str = $(obj).val();
            var parentObj = $(obj).parent().parent('.ruleEdit');
            var data = parseInt(parentObj.attr("data"));

            if(str == "修改"){
                parentObj.find("input").attr("disabled",false);
                $(obj).val("保存");
            }else{
                parentObj.find("input").attr("disabled",true);
                $(obj).attr("disabled",false);
                $(obj).val("修改");

                var min = parentObj.find("input[name='min']").val();
                var max = parentObj.find("input[name='max']").val();
                var dif = parentObj.find("input[name='dif']").val();

                $.post('/t.php?s=/Package/WeighSystem/ruleSet',{data:data,min:min,max:max,dif:dif},function(e){
                    if(e.status == 1){
                        $(".showMsg .content").html(e.msg);
                        showMsg(true);
                        setTimeout(function () {
                            showMsg(false);
                        },1500);
                        setTimeout(function(){window.location.href="/t.php?s=/Package/WeighSystem/ruleIndex";},1500);
                    }else{
                        $(".showMsg .content").html(e.msg);
                        showMsg(true);
                        setTimeout(function () {
                            showMsg(false);
                        },1500);
                        setTimeout(function(){window.location.href="/t.php?s=/Package/WeighSystem/ruleIndex";},1500);
                    }
                },"json")
            }
        }
        //删除
        function deleteRule(obj){
            var bool = confirm('确认是否删除？');
            //如果确认删除，调用接口进行删除
            if(bool){
                var delObj = $(obj).parent().parent('.ruleEdit');
                var data = parseInt(delObj.attr("data"));
                if(data > 0){
                    //需要请求后台进行删除操作
                    $.post('/t.php?s=/Package/WeighSystem/ruleDelete',{data:data},function(e){
                        if(e.status == 1){
                            delObj.remove();
                        }
                        $(".showMsg .content").html(e.msg);
                        showMsg(true);
                        setTimeout(function () {
                            showMsg(false);
                        },1500);
                    },"json")
                }else{
                    $(".showMsg .content").html('删除成功');
                    showMsg(true);
                    delObj.remove();
                    setTimeout(function () {
                        showMsg(false);
                    },1500);
                }
            }
        }
        //防止输入非数字
        function check(obj){
            obj.value = obj.value.replace(/[^\d\.]/g,'');
            //证明有修改加个标记，保存的时候只提交这些数据
            $(obj).parent().parent(".ruleEdit").attr("isEdit","1");
        }
        //提示信息窗口
        function showMsg(type){
            if(type){
                document.getElementById("sh").style.display="block";
            }else{
                document.getElementById("sh").style.display="none";
            }
        }

        /**
         * 监听修改状态
         * @author Shawn
         * @date 2018-12-24
         */
        $(document).ready(function() {
            $("#onoffswitch").on('click', function(){
                clickSwitch()
            });

            var clickSwitch = function() {
                var status = '';
                if ($("#onoffswitch").is(':checked')) {
                    status = 1;
                } else {
                    status = 0;
                }
                var url='<?php echo U("Package/WeighSystem/changeLimitWeightStatus");?>';
                var id = $("#config_id").val();
                $.post(url,{status:status,id:id},function (e) {
                    if(e.status == 1){
                        layer.msg(e.msg,{icon:1});
                    }else{
                        layer.msg(e.msg,{icon:2});
                    }
                    setTimeout(function () {
                        location.reload();
                    },2500);
                },"json")
            };
        });
    </script>

</body>
</html>