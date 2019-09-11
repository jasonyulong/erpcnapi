<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<title></title> 
<link rel="stylesheet" type="text/css" href="cache/themes/Sugar5/css/yui.css" />
<link rel="stylesheet" type="text/css" href="cache/themes/Sugar5/css/deprecated.css" />
<link rel="stylesheet" type="text/css" href="cache/themes/Sugar5/css/style.css" /> 
<link rel="stylesheet" type="text/css" media="all" href="cache/themes/Sugar5/css/login.css">
<style type="text/css">
<!--
.STYLE1 {
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
</head>
<body >  
<div id="header"> 
    <div id="companyLogo">
      <div align="center"></div>
  </div>    
  <div id="globalLinks">					
    <ul> 
        <li> 
        </li> 
        <li> 
        </li> 
        <li>        </li> 
        </ul> 
</div>        <div class="clear"></div> 
        <div class="clear"></div> 
        <br /><br /> 
        <div id="moduleList"> 
<ul> 
    <li class="noBorder"></li> 
        </ul> 
</div>    <div class="clear"></div> 
    <div class="line"></div> 
    </div> 
 
<div id="main"> 
    <div id="content" class="noLeftColumn" > 
        <table style="width:100%"><tr><td>
<table cellpadding="0" align="center" width="100%" cellspacing="0" border="0"> 
	<tr> 
		<td align="center"> 
		<div class="dashletPanelMenu" style="width: 460px;"> 
		<div class="hd"><div class="tl"></div><div class="hd-center"></div><div class="tr"></div></div> 
		<div class="bd">	
		<div class="ml"></div> 
		<div class="bd-center"> 
			<div class="loginBox"> 
			<table cellpadding="0" cellspacing="0" border="0" align="center"> 
				<tr> 
					<td align="left">&nbsp;</td> 
				</tr> 
				<tr> 
					<td align="center"> 
						<div class="login"> 
							<form action="login_handle.php" method="post" name="DetailView" id="form"> 
								<table cellpadding="0" cellspacing="2" border="0" align="center" width="100%"> 
																		<tr> 
										<td colspan="2" scope="row"><strong><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=287311025&site=qq&menu=yes"></a></strong></td>
									</tr> 
																	<tr> 
										<td scope="row" colspan="2" style="font-size: 12px; font-weight: normal; padding-bottom: 4px;">&nbsp;</td> 
									</tr> 
									<tr> 
										<td scope="row" width="23%">用户名:</td> 
										<td width="77%"><input type="text" size='35' tabindex="1" id="user_name" name="wst_name"  value='' /></td>
									</tr> 
									<tr> 
										<td scope="row">密&nbsp;&nbsp;码:</td> 
										<td width="77%"><input type="password" size='26' tabindex="2" id="user_password" name="password" value='' /></td> 
									</tr> 
																		<tr> 
										<td>&nbsp;</td> 
										<td><input title="Log In " class="button primary" type="submit" tabindex="3" id="login_button" name="Login" value="登陆"><a style="margin-left: 0.5em;color:#666;text-decoration:none;" href="t.php?s=/Mobile/Login/login">移动端登录</a>
										<br>
										&nbsp;</td>		
									</tr>
<tr>
<td colspan="2"></td>
								    </tr> 
								</table> 
							</form> 
						</div>					</td> 
				</tr> 
			</table> 
			</div> 
			</div> 
			<div class="mr"></div> 
			</div> 
<div class="ft"><div class="bl"></div><div class="ft-center"></div><div class="br"></div></div> 
</div>		</td> 
	</tr> 
</table>
<br>
<br>
<br><br>
</td></tr></table> 
  </div> 
    <div class="clear"></div><!--粤ICP备15023543号
    <div class="tan_link" style="position:absolute;right:10px;bottom:10px;">粤ICP备15023543号</div>-->
</div>
<script type="text/javascript">

    (function(){

        function Errors(str){
            document.getElementById("form").style.display="none";
            document.write("<h2 style='color:#f33'>"+str+"</h2>");
        }
        //document.write(navigator.userAgent);
        var plas=undefined==navigator.plugins?[]:navigator.plugins;
        for(var i in plas){
            if(isNaN(i)){break;}
            if(plas[i].toString()=='[object Plugin]'&&!isNaN(i)){
                var jjj=plas[i];
                var plusName=undefined==jjj.name?'':jjj.name;
                //document.write(plusName+"-----------<br>");
                if(plusName.indexOf('360')>-1){
                    //Errors("本系统不适合360,搜狗之流的浏览器。请使用火狐，谷歌或高版本IE! 如果你的浏览器不是360请联系IT人员");
                    break;//about:plugins    chrome://plugins/
                }
            }
        }


        var b_name = navigator.appName;
        var b_version = navigator.appVersion;
        var version = b_version.split(";");
        if(version.length<=1){return;}
        var trim_version = version[1].replace(/[ ]/g, "");
        if (b_name == "Microsoft Internet Explorer") {
            /*如果是IE6或者IE7*/
            if (trim_version == "MSIE7.0" || trim_version == "MSIE6.0") {
                var   newNode		  =	document.createElement("div");
                newNode.style.cssText = "float:left;font-size:20px;color:#FF6666";
               // var   txtNode		  = document.createTextNode('您的浏览器版本过低,可能导致部分功能无法正常显示,建议升级最新版本IE或使用Firefox/Chrome');
               // newNode.appendChild(txtNode);
                //document.getElementById("companyLogo").insertBefore(newNode);
                Errors("您的浏览器版本过低,或者您浏览器是360等壳浏览器。会导致部分功能无法正常显示,建议升级最新版本IE或使用Firefox/Chrome");
            }
        }


    })();
</script>
</body>
 </html>
