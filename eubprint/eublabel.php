<?php
$css = <<<KKK
    <meta charset="utf-8">
    <style>
        html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, font, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,p {  margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: top;background: transparent;list-style:none;  }
        body{padding:0;margin:0;font-family:helvetica;}
        table{border-top:1px #000 solid;border-left:1px #000 solid}.mleft{margin-left:5mm;}
        table td{border-right:1px #000 solid;border-bottom:1px #000 solid;padding:2px;}
        #next {border-style:none;padding:2px;}
        #next td{border-style:none;padding:2px;}
        #mytr td{border-top:1px #000 solid;border-left:1px #000 solid}
        td{padding-top:10px}
        .view{height:100mm;width:100mm;}
        .noright{border-right:none}
        .nobottom{border-bottom:none}
        .noleft{border-left:none}
        .notop{border-top:none}
        .f12{font-size: 12px;}
        h2,h3{padding:0;margin:0;text-align:center}
        h3{font-size:10pt;}
        h2{font-size:9pt;}
        .font5{font-size:5pt;}
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
        .barcodeImg{margin-left:10px;}
        .pcenter{text-align:center;font-size:12pt;}
        p.peihuo{width:80mm;white-space:normal;}
        .bigf{font-size:15mm;margin:3px;border:2px solid #000;height:15mm;width:15mm;}
        .pborder{margin:3px;border:2px solid #000;text-align:center;}
        .trancNoDiv{border-top:3px solid #000;border-bottom:3px solid #000;padding-top:3mm;}
        .bnum{font-size:30pt;margin-left:140px;margin-top:-80px;height:15mm;width:15mm;}
        .bdadd{font-size:9pt ;line-height:10pt;}
        .sku{white-space:normal;font-size:8pt;word-break:break-all;}
        .last{font-size:7pt;line-height:10px}
        .line{border-bottom:1px solid black;width:120px;}
    </style>
    <script type="text/javascript" src="../js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="../js/jquery-barcode-s.js"></script>
KKK;
echo $css;

function showTemplate($ebay_username,$ebay_street,$ebay_street1,$ebay_city,$ebay_state,$ebay_countryname,$ebay_postcode,$zipcode,$ebay_phone,$ebay_tracknumber,$skustr,$postnum,$ebay_id,$backAddress)
{
    $GDGuahao = <<<GGGG
<div style="width:100mm;min-height:100mm;max-height:100mm;border:1px dashed black;padding:1mm;font-size:12px; margin:1px;overflow:hidden;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                <td class='noright nobottom'><div class='bigf'>F</div></td>
                <td colspan="2" align="left" valign="top" class="noright nobottom">
                    <img src="../themes/Sugar5/images/hrb_logo.jpg" height="30" width="105"  style="clear:both;"/>
                    <div class="line"></div>
					<img src="us.jpg" height="30" width="105px">
                </td>
                <td class="nobottom">
                    <p class="pborder">
                        Airmail<br>
                        Postage Paid<br>
                        China Post<br>
                    </p>
                    <p class="pcenter">$postnum</p>
                </td>
                </tr>
                <tr>
                    <td align="left" width="20%" class="noright notop font9 blod">FROM</td>
                    <td  width="80%" colspan="3" class="ffa notop font9 blod">ePacket™</td>
                </tr>
                <tr>
                    <td width="60%" colspan="2" class="ffa">
                    <p class=" font8 blod" style="width:100%">
                        $backAddress
                        </p>
                        <p class="font5" style="width:100%">Customs information avaliable on attached CN22.
                        USPS Personnel Scan barcode below for delivery event information.  id:$ebay_id</p>
                    </td>
                    <td  width="40%"  colspan="2" class="font8 ffa" colspan="3">
                        <img src="../barcode128.class.php?data=420$zipcode" alt="" width="100%" height="29"/>
                        <p class="pcenter">ZIP:$zipcode</P>
                    </td>
                </tr>
                <tr>
                    <td width="20%" class="ffa nobottom"><B>To:</B></td>
                    <td width="80%" class="font10 blod nobottom" style="line-height: 10pt;" colspan="3">
                        $ebay_username<br/>
                        $ebay_street, $ebay_street1<br/>
                        $ebay_city, $ebay_state &nbsp;&nbsp;$ebay_postcode<br/>
                        $ebay_countryname<br>
                        PHONE: $ebay_phone</b>
                    </td>
                </tr>
                <tr>
                    <td class="font9" colspan="4">
                        <div class="trancNoDiv">
                            <p class="pcenter">USPS TRACKING ®</P>
                            <img src="../barcode128.class.php?data=$ebay_tracknumber" alt="" width="100%" height="40"/>
                            <p class="pcenter">$ebay_tracknumber</P>
                            <br>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="" style="width:100mm;min-height:100mm;max-height:100mm;border:1px dashed black;padding:1mm;font-size:12px; margin:1px;overflow:hidden;">
            <table width="100%" cellpadding="0" cellspacing="0" id="next">
                <tr>
                   <td >
                            <img src="../themes/Sugar5/images/hrb_logo.jpg" height="30" width="105"  style="clear:both;"/><br>
                            IMPORTANT:<br>
                            The item/parcel may be<br>
                            opened officially.<br>
                            Please print in English.<br>
                            <div class="bnum">
                            $postnum
                            </div>
                   </td>
                   <td>
                            <br>
                            <img src="../barcode128.class.php?data=$ebay_tracknumber" alt="" width="100%" height="40"/>
                            <p class="pcenter">$ebay_tracknumber</P><br>
                            <br>
                   </td>
                </tr>
                <tr id="mytr">
                    <td height="70" width="50%">
                    <p class=" font8 blod" style="width:100%">
                        $backAddress
                        <br>
                        PHONE:
                    </p>
                    </td>
                    <td rowspan="3">
                        <div class="bdadd" >
                            <b>SHIP TO: $ebay_username<br>
                            $ebay_street $ebay_street1<br>
                            $ebay_city, $ebay_state<br>
                            $ebay_countryname, $ebay_postcode<br>
                            PHONE: $ebay_phone</b>
                        </div>
                    </td>
                  </tr>
                  <tr id="mytr">
                    <td>Fees(US $):</td>
                  </tr>
                  <tr id="mytr">
                    <td>Certificate No.</td>
                  </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="tab">
			 <tr style="font-size:10px">
				<td width="5%" >No</td>
				<td width="5%">Qty</td>
				<td width="60%">Description of Contents</td>
				<td width="10%">KG</td>
				<td width="10%">Val(US $)</td>
				<td width="10%">Goods Origin</td>
			  </tr>
			  <tr>
				<td height="53">1</td>
				<td>1</td>
				<td>
					<div class="sku">
						$skustr
					</div>
				</td>
				<td>0.65</td>
				<td>12</td>
				<td>China</td>
			  </tr>
			  <tr style="font-size:10px">
				<td >&nbsp;</td>
				<td>1</td>
				<td>Total Gross Weight (Kg):</td>
				<td>0.65</td>
				<td>12</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="6">
					<div class="last">
					I certify the particulars given in this customs declaration are correct. This item does not contain any dangerous article, or articles prohibited by legislation or by postal or customs regulations. I have met all applicable export filing requirements under the Foreign Trade Regulations.
					Senders Signature & Date Signed:   CN22
					</div>
				</td>
			  </tr>
		</table>
        </div>
GGGG;

    echo $GDGuahao;
}
function OrderResolve($ebay_id){//分解订单为单个的sku    $other 是否返回其他信息
    global $dbcon;
    $sa="select ebay_ordersn from ebay_order where ebay_id='$ebay_id' limit 1";
    $sa=$dbcon->execute($sa);
    $sa=$dbcon->getResultArray($sa);//D($sql3);
    $ebay_ordersn=$sa[0]['ebay_ordersn'];
    $sql3="select sku,ebay_amount as c from ebay_orderdetail where ebay_ordersn='$ebay_ordersn' ";
    $sql3=$dbcon->execute($sql3);
    $sql3=$dbcon->getResultArray($sql3);//D($sql3);
    $skuArr=array(); // 以sku 为索引的 数组
    foreach($sql3 as $v){
        $goods_sn   =$v['sku'];
        $c          =$v['c'];
        $fromGoods="SELECT goods_name,goods_ywsbmc,goods_location,goods_weight FROM ebay_goods where goods_sn='$goods_sn ' limit 1"; //echo $fromGoods;

        $fromGoods				= $dbcon->execute($fromGoods);
        $fromGoods				= $dbcon->getResultArray($fromGoods);//D($bb);
        if(count($fromGoods)>0){
            //$skuArr['']
            if(!empty($skuArr[$goods_sn])){
                $skuArr[$goods_sn][0]+=$c;
            }else{
                $skuArr[$goods_sn]=array($c,$fromGoods[0]['goods_name'],$fromGoods[0]['goods_location'],$fromGoods[0]['goods_ywsbmc'],$fromGoods[0]['goods_weight']);
            }
        }else{
            $skuArr=ResolveCom($goods_sn,$c,$skuArr);
            //$skuArr=array_merge_recursive($skuArr,$a);
        }
    }

    return $skuArr;

}

function ResolveCom($goods_sn_b,$c,$skuArr){ //分解组合
    global $dbcon;
    $rr			= "select * from ebay_productscombine where goods_sn='$goods_sn_b' limit 1";//不在goods表 是否在组合表
    $rr			= $dbcon->execute($rr);
    $rr 	 	= $dbcon->getResultArray($rr);
    if(count($rr)>0){
        $goods_sncombine	= $rr[0]['goods_sncombine'];
        $goods_sncombine    = explode(',',$goods_sncombine);
        foreach($goods_sncombine as $v){
            $pline			= explode('*',$v);
            $goods_sn		= $pline[0];
            $goods_number	= $pline[1];
            $cc=$goods_number*$c;

            // get user
            $fromGoods="SELECT goods_name,goods_ywsbmc,goods_location,goods_weight FROM ebay_goods where goods_sn='$goods_sn ' limit 1";
            $fromGoods				= $dbcon->execute($fromGoods);
            $fromGoods				= $dbcon->getResultArray($fromGoods);//D($bb);
            if(!empty($skuArr[$goods_sn])){
                $skuArr[$goods_sn][0]+=$cc;
            }else{
                $skuArr[$goods_sn]=array($cc,$fromGoods[0]['goods_name'],$fromGoods[0]['goods_location'],$fromGoods[0]['goods_ywsbmc'],$fromGoods[0]['goods_weight']);
            }
        }
    }
    return $skuArr;
}
function debug($arr){
    echo '<pre>',print_r($arr,true),'</pre>';
}
function GetOrderMainSKULocal($orderid,$storeid){
    global $dbcon;
    if(!is_numeric($orderid)){
        return '';
    }
    $sql="select ebay_ordersn from ebay_order where ebay_id=$orderid limit 1";
    $sql=$dbcon->getResultArrayBySql($sql);
    if(count($sql)!=1){
        return '';
    }
    $ordersn=$sql[0]['ebay_ordersn'];
    $sql="select sku from ebay_orderdetail where ebay_ordersn='$ordersn' limit 1";
    $Rs=$dbcon->getResultArrayBySql($sql);

    if(count($Rs)!=1){
        return '';
    }
    $sku=$Rs[0]['sku'];
    /*
     * @SKU浪漫地寻找库位～～～～
     * @ 先找到仓库表
     * */
    $sq="select g_location from ebay_onhandle_".$storeid." where goods_sn='$sku' limit 1";
    $Rs=$dbcon->getResultArrayBySql($sq);
    /*
     * @ 没有在仓库表 找到 怎么办～～～
     * @ 快到组合表 的碗里来～～～～
     * */
    if(count($Rs)==0){
        $con="select goods_sncombine from ebay_productscombine where goods_sn='$sku' limit 1";
        $Rs=$dbcon->getResultArrayBySql($con);
        if(count($Rs)==0){
            return '';
        }
        $skucombine=$Rs[0]['goods_sncombine'];
        $skucombine=explode('*',$skucombine);
        $sku=trim($skucombine[0]);
        $sq="select g_location from ebay_onhandle_".$storeid." where goods_sn='$sku' limit 1";
        #echo $sq;
        $Rs=$dbcon->getResultArrayBySql($sq);
        if(count($Rs)==0){
            return '';
        }else{
            #echo $Rs[0]['g_location'];
            return strtoupper($Rs[0]['g_location']);
        }
    }else{
        return strtoupper($Rs[0]['g_location']);
    }


}

/*
 * EUB 分拣code
 *
 * */
function fenjianCode($postCode){
    $postCode=substr(trim($postCode),0,3);
    $arr=array(
        '1F'=>array('000','001','002','003','004','005','006','007','008','009','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028','029','030','031','032','033','034','035','036','037','038','039','040','041','042','043','044','045','046','047','048','049','050','051','052','053','054','055','056','057','058','059','060','061','062','063','064','065','066','067','068','069','074','075','076','077','078','080','081','082','083','084','085','086','087','090','091','092','093','094','095','096','097','098','099','105','106','107','108','109','115','117','118','119','120','121','122','123','124','125','126','127','128','129','130','131','132','133','134','135','136','137','138','139','140','141','142','143','144','145','146','147','148','149','150','151','152','153','154','155','156','157','158','159','160','161','162','163','164','165','166','167','168','169','170','171','172','173','174','175','176','177','178','179','180','181','182','183','184','185','186','187','188','189','190','191','192','193','194','195','196','197','198','199','200','201','202','203','204','205','206','207','208','209','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','270','271','272','273','274','275','276','277','278','279','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299'),
        '1P'=>array('103','110','111','112','113','114','116'),
        '1Q'=>array('070','071','072','073','079','088','089'),
        '1R'=>array('100','101','102','104'),
        '3F'=>array('400','401','402','403','404','405','406','407','408','409','410','411','412','413','414','415','416','417','418','419','420','421','422','423','424','425','426','427','428','429','430','431','432','433','437','438','439','450','451','452','453','454','455','456','457','458','459','470','471','475','476','477','480','483','484','485','490','491','493','494','495','496','497','500','501','502','503','504','505','506','507','508','509','510','511','512','513','514','515','516','517','518','519','520','521','522','523','524','525','526','527','528','529','533','536','540','546','547','548','550','551','552','553','554','555','556','557','558','559','560','561','562','563','564','565','566','567','568','569','570','571','572','573','574','575','576','577','578','579','580','581','582','583','584','585','586','587','588','589','590','591','592','593','594','595','596','597','598','599','600','601','602','603','604','605','606','607','608','609','612','617','618','619','621','624','632','635','640','641','642','643','644','645','646','647','648','649','650','651','652','653','654','655','656','657','658','659','660','661','662','663','664','665','666','667','668','669','670','671','672','673','674','675','676','677','678','679','680','681','682','683','684','685','686','687','688','689','690','691','692','693','694','695','696','697','698','699','740','741','742','743','744','745','746','747','748','749','750','751','752','753','754','755','756','757','758','760','761','762','763','764','765','766','767','768','769','770','771','772','785','786','787','789','790','791','792','793','794','795','796','797','798','799'),
        '3P'=>array('460','461','462','463','464','465','466','467','468','469','472','473','474','478','479'),
        '3Q'=>array('498','499','530','531','532','534','535','537','538','539','541','542','543','544','545','549','610','611'),
        '3R'=>array('759','773','774','775','776','777','778'),
        '3U'=>array('613','614','615','616','620','622','623','625','626','627','628','629','630','631','633','634','636','637','638','639'),
        '3C'=>array('434','435','436','481','482','486','487','488','489','492'),
        '3D'=>array('779','780','781','782','783','784','788'),
        '3H'=>array('440','441','442','443','444','445','446','447','448','449'),
        '4F'=>array('813','814','815','816','817','818','819','820','821','822','823','824','825','826','827','828','829','830','831','832','833','834','835','836','837','838','839','840','841','842','843','844','845','846','847','848','849','854','856','857','858','861','862','864','865','866','867','868','869','870','871','872','873','874','875','876','877','878','879','880','881','882','883','884','885','886','887','888','889','890','891','892','893','894','895','896','897','898','899','906','909','910','911','912','913','914','915','916','917','918','926','927','928','929','930','931','932','933','934','935','936','937','938','939'),
        '4P'=>array('900','901','902','903','904','905','907','908'),
        '4Q'=>array('850','851','852','853','855','859','860','863'),
        '4R'=>array('919','920','921'),
        '4U'=>array('922','923','924','925'),
        '2F'=>array('942','950','951','952','953','956','957','958','959','960','961','962','963','964','965','966','967','968','969','970','971','972','973','974','975','976','977','978','979','986','987','988','989','990','991','992','993','994','995','996','997','998','999'),
        '2P'=>array('980','981','982','983','984','985'),
        '2Q'=>array('800','801','802','803','804','805','806','807','808','809','810','811','812'),
        '2R'=>array('945','946','947','948'),
        '2U'=>array('940','941','943','944','949','954','955'),
        '5F'=>array('300','301','302','303','304','305','306','307','308','309','310','311','312','313','314','315','316','317','318','319','320','322','323','324','325','326','334','335','336','337','338','339','341','342','343','344','345','346','348','349','350','351','352','353','354','355','356','357','358','359','360','361','362','363','364','365','366','367','368','369','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','390','391','392','393','394','395','396','397','398','399','700','701','702','703','704','705','706','707','708','709','710','711','712','713','714','715','716','717','718','719','720','721','722','723','724','725','726','727','728','729','730','731','732','733','734','735','736','737','738','739'),
        '5P'=>array('330','331','332','333','340'),
        '5Q'=>array('321','327','328','329','347')
    );

    foreach($arr as $k=>$v){
         if(in_array($postCode,$v)){
             return $k;
         }
    }
    return false;
}


/*
function StrFuckToPHPcode($str){

    $arr=explode(',',$str);
    $RS=array();
    foreach($arr as $v){//无区间
        if(strstr($v,'-')===false){
            $RS[]="'".$v."'";
        }else{// 有区间
            $itemV=explode('-',$v);
            $start=(int)$itemV[0];
            $end=(int)$itemV[1];
            for(;$start<=$end;$start++){
                $code=$start;
                $RS[]="'".sprintf('%03s', $code)."'";
            }
        }
    }
    return implode(',',$RS);
}

$exp='000-069, 074-078, 080-087, 090-099, 105-109, 115, 117-299';
echo StrFuckToPHPcode($exp);

$arr=array(
    '1F'=>'000-069,074-078,080-087,090-099,105-109,115,117-299',
    '1P'=>'103,110-114,116',
    '1Q'=>'070-073,079,088-089',
    '1R'=>'100-102,104',
    '3F'=>'400-433,437-439,450-459,470-471,475-477,480,483-485,490-491,493-497,500-529,533,536,540,546-548,550-609,612,617-619,621,624,632,635,640-699,740-758,760-772,785-787,789-799',
    '3P'=>'460-469,472-474,478-479',
    '3Q'=>'498-499,530-532,534-535,537-539,541-545,549,610-611',
    '3R'=>'759,773-778',
    '3U'=>'613-616,620,622-623,625-631,633-634,636-639',
    '3C'=>'434-436,481-482,486-489,492',
    '3D'=>'779-784,788',
    '3H'=>'440-449',
    '4F'=>'813-849,854,856-858,861-862,864-899,906,909-918,926-939',
    '4P'=>'900-905,907-908',
    '4Q'=>'850-853,855,859-860,863',
    '4R'=>'919-921',
    '4U'=>'922-925',
    '2F'=>'942,950-953,956-979,986-999',
    '2P'=>'980-985',
    '2Q'=>'800-812',
    '2R'=>'945-948',
    '2U'=>'940-941,943-944,949,954-955',
    '5F'=>'300-320,322-326,334-339,341-346,348-399,700-739',
    '5P'=>'330-333,340',
    '5Q'=>'321,327-329,347'
);

echo 'array(<br>';
foreach($arr as $k=>$v){
    echo "'".$k."'=>array(".StrFuckToPHPcode($v)."),<br>";
}

echo ');';
*/