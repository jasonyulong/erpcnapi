<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pdf2png</title>
    <style>
        html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, font, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,p {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            font-size: 100%;
            vertical-align: baseline;
            background: transparent;
            list-style:none;
        }
        body{padding:0;margin:0;}/*.blod{font-weight:bold;}*/
        table{border-top:1px #000 solid;border-left:1px #000 solid}.mleft{margin-left:5mm;}
        table td{border-right:1px #000 solid;border-bottom:1px #000 solid;padding:2px;}
        td{padding-top:10px}
        .view{height:100mm;width:100mm;}
        .noright{border-right:none}
        .nobottom{border-bottom:none}
        .f12{font-size: 12px;}
        h2,h3{padding:0;margin:0;text-align:center}
        h3{font-size:10pt;}
        h2{font-size:9pt;}
        .font6{font-size:6pt;}
        .font7{font-size:7pt;}
        .font8{font-size:8pt;}
        .font9{font-size:9pt;}
        .font10{font-size:10pt;}
        .left{float:left;text-align:center;}
        .height1{height:10mm;}
        .height2{height:6mm;}
        .height3{height:5mm;}
        .ffa{font-family:helvetica;}
        .blod{font-weight:bold;}
        .ffa2{font-family:stsongstdlight;}
        .ttt{height:1mm}
        .barcode{height:100%;}
        .error{font-size:35px;color:#911;}
    </style>
    <style media=print>
        .Noprint{display:none;}
        .PageNext{page-break-after: always;}
    </style>
</head>
<body>

<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" data="" type="">
    <embed id="LODOP_EM" src="" pluginspage="install_lodop32.exe" type="application/x-print-lodop" width="0" height="0">
    </embed>
</object>
<script>
    var LODOP ;

    function getLodop(oOBJECT,oEMBED){
        var strHtml1="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='install_lodop32.exe'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtml2="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='install_lodop32.exe'>执行升级</a>,升级后请重新进入。</font>";
        var strHtml3="<br><br><font color='#FF00FF'>(注：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】中先卸载它)</font>";
        var LODOP=oEMBED;
        try{
            if (navigator.appVersion.indexOf("MSIE")>=0) LODOP=oOBJECT;

            if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
                if (navigator.userAgent.indexOf('Firefox')>=0)
                    document.documentElement.innerHTML=strHtml3+document.documentElement.innerHTML;
                if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtml1); else
                    document.documentElement.innerHTML=strHtml1+document.documentElement.innerHTML;
            } else if (LODOP.VERSION<"6.0.0.1") {
                if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtml2); else
                    document.documentElement.innerHTML=strHtml2+document.documentElement.innerHTML;
            }
//*****如下空白位置适合调用统一功能:*********
//            LODOP.SET_LICENSES (strCompanyName, strLicense, strLicenseA,strLicenseB);
            LODOP.SET_LICENSES("","846425356495460535763645612890","688858710010010811411756128900","");
//*****************************************
            return LODOP;
        }catch(err){
            document.documentElement.innerHTML="Error:"+strHtml1+document.documentElement.innerHTML;
            return LODOP;
        }
    }
</script>

<?php
include "Pdf.class.php";
$Pdf=new PdfToimg();

//$sourcePdf=dirname(__FILE__).'/20170417220913.pdf';
$sourcePdf='http://47.90.38.119/cache/pdf/eubpdf/201704191854389021eub.pdf';
$nameArr=explode('/',$sourcePdf);
$name=$nameArr[count($nameArr)-1];
if(!copy($sourcePdf,'./temppdf/'.$name)){
    echo '<div class="error">PDF获取失败!</div>';
    echo $sourcePdf;
    die();
}

$sourcePdf ='./temppdf/'.$name;
//$sourceImg=dirname(__FILE__).'/pngLable/20170417220913.png';
$logo=dirname(__FILE__).'/num/n_27.png';

$Return=$Pdf->pdf2png($sourcePdf,$logo,0,2,83);


if(!is_array($Return)||!preg_match('/(\.png|.jpg|.jpeg|.gif)/i',$Return[0])){
    echo '<div class="error">图片转化失败!</div>';die();
}

foreach($Return as $list){
   // $list=trim($list,'.');
    echo '<div class="view"><img  class="view" src="'.$list.'"></div>';
}

?>

</body>
</html>