<meta charset="utf-8">
<style>
    @font-face {
        font-family: 'IDAutomationC128S';
        src: url('toxls/IDAutomationC128S.eot'); /* IE9 Compat Modes */
        src: url('toxls/IDAutomationC128S.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
        url('./toxls/IDAutomationC128S.woff') format('woff'), /* Modern Browsers */
        url('./toxls/IDAutomationC128S.ttf')  format('truetype'), /* Safari, Android, iOS */
        url('./toxls/IDAutomationC128S.svg#svgFontName') format('svg'); /* Legacy iOS */
    }
</style>
<?php
function str2barcode($readableString){
    /*
    Code128A不支持小写字母编码，Code128Auto中通常仅适用Code128B与Code128C进行混合编码。
    如果可读字符长度（设为L）大于等于4且全部为数字型字符，则Code128Auto以Code128C作为起始编码，注意：Code128C仅支持对偶数个数字型字符进行编码。
        如果L为偶数，则直接全部Code128C编码至结束。
        如果L为奇数，则将前L-1个字符以Code128C编码，第L个字符以Code128B编码。
    如果长度小于4或字符串中包含非数字型字符，则Code128Auto以Code128B作为起始编码。
    以Code128B起始编码时，发现长度（设为L）不少于4的连续的数字型字符时，
        若L为偶数，则切换至Code128C进行编码；
        若L为奇数，则首个数字型字符以Code128B编码，之后切换为Code128C进行编码；
        切换至Code128C编码完成后，后续还有字符时（肯定是非数字型），切换回Code128B继续编码，直至发现下一个长度不少于4的连续的数字型字符。
    */
//$code128aMapping数组用于以可读字符为索引查找条码字体的Code128A编码字符。
    $code128aMapping = array(' '=>' ','!'=>'!','"'=>'"','#'=>'#','$'=>'$','%'=>'%','&'=>'&','\''=>'\'','('=>'(',')'=>')','*'=>'*','+'=>'+',','=>',','-'=>'-','.'=>'.','/'=>'/','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',':'=>':',';'=>';','<'=>'<','='=>'=','>'=>'>','?'=>'?','@'=>'@','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','W'=>'W','X'=>'X','Y'=>'Y','Z'=>'Z','['=>'[','\\'=>'\\',']'=>']','^'=>'^','_'=>'_');

//$code128bMapping数组用于以可读字符为索引查找条码字体的Code128B编码字符。
    $code128bMapping = array(' '=>' ','!'=>'!','"'=>'"','#'=>'#','$'=>'$','%'=>'%','&'=>'&','\''=>'\'','('=>'(',')'=>')','*'=>'*','+'=>'+',','=>',','-'=>'-','.'=>'.','/'=>'/','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',':'=>':',';'=>';','<'=>'<','='=>'=','>'=>'>','?'=>'?','@'=>'@','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','W'=>'W','X'=>'X','Y'=>'Y','Z'=>'Z','['=>'[','\\'=>'\\',']'=>']','^'=>'^','_'=>'_','`'=>'`','a'=>'a','b'=>'b','c'=>'c','d'=>'d','e'=>'e','f'=>'f','g'=>'g','h'=>'h','i'=>'i','j'=>'j','k'=>'k','l'=>'l','m'=>'m','n'=>'n','o'=>'o','p'=>'p','q'=>'q','r'=>'r','s'=>'s','t'=>'t','u'=>'u','v'=>'v','w'=>'w','x'=>'x','y'=>'y','z'=>'z','{'=>'{','|'=>'|','}'=>'}','~'=>'~');

//$code128cMapping数组用于以可读字符为索引查找条码字体的Code128C编码字符。
    $code128cMapping = array('00'=>'Â','01'=>'!','02'=>'"','03'=>'#','04'=>'$','05'=>'%','06'=>'&','07'=>'\'','08'=>'(','09'=>')','10'=>'*','11'=>'+','12'=>',','13'=>'-','14'=>'.','15'=>'/','16'=>'0','17'=>'1','18'=>'2','19'=>'3','20'=>'4','21'=>'5','22'=>'6','23'=>'7','24'=>'8','25'=>'9','26'=>':','27'=>';','28'=>'<','29'=>'=','30'=>'>','31'=>'?','32'=>'@','33'=>'A','34'=>'B','35'=>'C','36'=>'D','37'=>'E','38'=>'F','39'=>'G','40'=>'H','41'=>'I','42'=>'J','43'=>'K','44'=>'L','45'=>'M','46'=>'N','47'=>'O','48'=>'P','49'=>'Q','50'=>'R','51'=>'S','52'=>'T','53'=>'U','54'=>'V','55'=>'W','56'=>'X','57'=>'Y','58'=>'Z','59'=>'[','60'=>'\\','61'=>']','62'=>'^','63'=>'_','64'=>'`','65'=>'a','66'=>'b','67'=>'c','68'=>'d','69'=>'e','70'=>'f','71'=>'g','72'=>'h','73'=>'i','74'=>'j','75'=>'k','76'=>'l','77'=>'m','78'=>'n','79'=>'o','80'=>'p','81'=>'q','82'=>'r','83'=>'s','84'=>'t','85'=>'u','86'=>'v','87'=>'w','88'=>'x','89'=>'y','90'=>'z','91'=>'{','92'=>'|','93'=>'}','94'=>'~','95'=>'Ã','96'=>'Ä','97'=>'Å','98'=>'Æ','99'=>'Ç');

//$idToBarFontCharMapping数组用于通过ID查找对应的编码字符
    $idToBarFontCharMapping=array('Â','!','"','#','$','%','&','\'','(',')','*','+',',','-','.','/','0','1','2','3','4','5','6','7','8','9',':',';','<','=','>','?','@','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[','\\',']','^','_','`','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','{','|','}','~','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í');

//$barFontCharToIdMapping数组用于通过编码字符查找其所对应的ID，array_flip函数功能是将数组索引与数值进行互换。
    $barFontCharToIdMapping=array_flip($idToBarFontCharMapping);

//$controlCharMapping用于查找不同控制命令的编码字符。
    $controlCharMapping = array('Code C'=>'Ç','Code B'=>'È','Code A'=>'É','FNC1'=>'Ê','Start A'=>'Ë','Start B'=>'Ì','Start C'=>'Í','Stop'=>'Î');

    $STARTB=false;//记录编码是否由Code128B起始
    $STARTC=false;//记录编码是否由Code128C起始

    $readableStringLenth=strlen($readableString);//获得可读字符串长度

    //判断应该以Code128B起始，还是以Code128C起始--Start
    if($readableStringLenth>=4){
        $i=0;
        while(is_numeric($readableString[$i]) && $i<$readableStringLenth){
            $i++;
        }
        //echo "i=".$i."<br>";
        if($i==$readableStringLenth){
            //可读字符长度不小于4且全部都是数字型字符，以Code128C起始。
            $STARTC=true;
        }else{
            //可读字符长度不小于4但其中包含非数字型字符，以Code128B起始
            $STARTB=true;
        }
    }else{
        ////可读字符长度小于4，字符，以Code128B起始
        $STARTB=true;
    }
    //判断应该以Code128B起始，还是以Code128C起始--End

    $barFontString="";//存储编码后的字符串，初始设为空字符串。
    $barFontCharIDarray=array();//存储编码后字符对应的ID，初始设为空数组。
    $barFontCharArray=array();//存储编码后字符数组，仅用于说明算法过程实际使用中可注释。
    $readableStringSplited=array(); //该数组用来存储拆分后的可读字符，每个拆分出来的部分对应一个编码字符，仅用于说明算法过程实际使用可注释掉。

    if($STARTC){
        $barFontChar=$controlCharMapping['Start C'];//找到Code128C编码的起始符
        $barFontString.=$barFontCharArray[]=$barFontChar;//向$barFontString中添加首个编码字符。
        $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];//向$barFontCharIDarray中添加首个编码字符对应的ID。
        $readableStringSplited[]="Code128C起始符";//向$readableStringSplited中添加当前编码字符对应的含义，仅用于辅助算法过程说明，实际使用可注释掉。
        for($i=0;$i<$readableStringLenth-1;$i+=2){//每两个字符做一次循环，得到一个Code128C字符。
            $code128cKey=$readableString[$i].$readableString[$i+1];//将两位数字型字符连接得到Code128C的编码索引。
            $barFontChar=$code128cMapping[$code128cKey];//通过Code128C索引获得Code128C的编码字符。
            $barFontString.=$barFontCharArray[]=$barFontChar;//将Code128C编码字符加入$barFontCharArray数组，并连接到$barFontString右侧。
            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];//将Code128C的编码字符对应的ID存入$barFontCharIDarray数组。
            $readableStringSplited[]=$code128cKey;//将拆分的可读字符（即索引）存入$readableStringSplited，仅用于辅助算法过程说明，实际使用可注释掉。
        }
        if($readableStringLenth%2) {//纯数字型可读字符串长度为奇数，最后1位需要切换至Code128B编码
            $barFontChar=$controlCharMapping['Code B'];//切换至Code128B编码，注意，编码切换符与起始符是有区别的。
            $barFontString.=$barFontCharArray[]=$barFontChar;
            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
            $readableStringSplited[]="切换至Code128B";
            $barFontChar=$code128bMapping[$readableString[$readableStringLenth-1]];
            $barFontString.=$barFontCharArray[]=$barFontChar;
            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
            $readableStringSplited[]=$readableString[$readableStringLenth-1];
        }
    }

    if($STARTB){
        $barFontChar=$controlCharMapping['Start B'];
        $barFontString.=$barFontCharArray[]=$barFontChar;
        $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
        $readableStringSplited[]="Code128B起始符";
        //连续数字型字符长度(L)大于等于4时，使用Code128C编码，L为奇数时，最左侧的以Code128B编码。
        $i=0;
        while($i<$readableStringLenth){ //对可读字符开始扫描，用while的原因是循环步长受循环过程控制。
            $j=$i;
            while(is_numeric($readableString[$j])){//探测连续数字型字符串长度
                $j++;
                //echo $j."<br>";
            }
            $continuousNumericCharEndPos=$j;
            $continuousNumericStringLen=$j-$i;//得到连续数字型字符串长度
            if($continuousNumericStringLen>=4){//当前字符位置向右存在长度不小于4的连续数字型字符
                if($continuousNumericStringLen%2){//连续数字型字符长度为奇数，则首个数字型字符以Code128B编码
                    $barFontChar=$code128bMapping[$readableString[$i]];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]=$readableString[$i];
                    $i++;
                }
                //切换至Code128C起始编码
                $barFontChar=$controlCharMapping['Code C'];
                $barFontString.=$barFontCharArray[]=$barFontChar;
                $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                $readableStringSplited[]="切换至Code128C";
                //echo "continuousNumericCharEndPos=".$continuousNumericCharEndPos;

                //每2个字符一次循环，得到Code128C编码字符，参见本文if($STARTC)部分。
                for(;$i<$continuousNumericCharEndPos;$i+=2){
                    //echo $i."======".$readableString[$i]."======".$readableString[$i+1]."<br>";
                    $barFontChar=$code128cMapping[$readableString[$i].$readableString[$i+1]];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]=$readableString[$i].$readableString[$i+1];
                }
                if($i<$readableStringLenth){//连续数字型字符串编码完毕后后面还有剩余字符，则需要切换回Code128B继续编码。
                    $barFontChar=$controlCharMapping['Code B'];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]="切换回Code128B";
                    //此处仅需要切换回Code128C，对后续字符进行编码的工作，在下一次While循环中进行。
                }
            }else{//当前字符位置向右不存在长度不小于4的连续数字型字符，直接以Code128B对当前字符编码。
                $barFontChar=$code128bMapping[$readableString[$i]];
                $barFontString.=$barFontCharArray[]=$barFontChar;
                $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                $readableStringSplited[]=$readableString[$i];
                $i++;
            }
        }
    }

    //计算校验位ID
    /*校验位的算法：
    设：	Cn=编码后字符串（不计起始符）的第n位字符对应的ID值（并设共有N位，不包含起始符，不包含校验位，不包含结束符）
        CL=起始符对应的ID值
        CID=校验位ID值
        则：CID = ( CL + C1 * 1 + C2 * 2 + C3 * 3 + … + CN * N ) % 103
    */
    $checkSum=$barFontCharIDarray[0]; //设置起始值为编码后字符串中第1个字符对应的ID，即起始符ID，即上述公式中的CL
    //累加 Cn*n (n=1…N)
    for($i=1;$i<=count($barFontCharIDarray)-1;$i++){ //count($barFontCharIDarray)-1就是N值
        $checkSum+=$barFontCharIDarray[$i]*$i;
    }
    $checkNoBarFontCharID=$checkSum % 103;//取余数后得到校验位的ID
    $barFontCharIDarray[]=$checkNoBarFontCharID;//将校验位ID存入$barFontCharIDarray
    $checkNoBarFontChar=$idToBarFontCharMapping[$checkNoBarFontCharID];//通过校验位ID得到校验位的编码字符。
    $barFontString.=$barFontCharArray[]=$checkNoBarFontChar;//将校验位编码字符连接至$barFontString字符串后端（右侧）。
    $readableStringSplited[]="校验位";

    //添加结束符
    $barFontString.=$barFontCharArray[]= $controlCharMapping['Stop'];
    $readableStringSplited[]="结束符";
    return array($barFontString,$readableStringSplited,$barFontCharArray,$barFontCharIDarray);
}



for($i=1;$i<=72;$i++){
    $str='$'.$i.'$';
    $Arr=str2barcode($str);
    ?>
    <div style="width:100mm;min-height:60mm;max-height:60mm;border:1px dashed black;padding:1mm;font-size:12px; margin:1px;overflow:hidden;">
        <div style="text-align: center;">
            <div style="font-family: IDAutomationC128S;font-size:70px;height:20mm;overflow:hidden;">
                <?php echo htmlspecialchars($Arr[0]);?>
            </div>
             <span style="font-size:130px">
                <?php echo trim($str,'$');?>
             </span>
        </div>
    </div>
<?php
}