<?php
error_reporting(0);
date_default_timezone_set ("Asia/Chongqing");
include "Pdf.class.php";
$Pdf=new PdfToimg();

// curl post http://15633a07j3.51mypc.cn:30553/erpcnapi/pdf2img/API.php
//  path =cache/pdf/ost/5783665_label.pdf
$sourceURL='http://hkerp.wisstone.com/';

//$imgUrl='http://15633a07j3.51mypc.cn:30553/erpcnapi/pdf2img/';
$imgUrl='http://erpcnapi.com:8081/erpcnapi/pdf2img/';


$FilePath=$_POST['path'];

$issave=(int)$_GET['save'];

$sourcePdf=$sourceURL.$FilePath;

$nameArr=explode('/',$sourcePdf);

$name=$nameArr[count($nameArr)-1];


$data=['status'=>1,'msg'=>'','data'];
//echo $sourcePdf;
if(!copy($sourcePdf.'?t='.time().rand(1111,9999),'./temppdf/'.$name)){
    $data['status']=0;
    $data['msg']='PDF获取失败'.$sourcePdf;
    echo json_encode($data);
    die();
}

$sourcePdf ='./temppdf/'.$name;

$ebayids=explode('.',$name);
$ebay_id=$ebayids[0];

$isSLS=false;
if(strstr($FilePath,'SLS/')){
    $isSLS=true;
}

$dir=date('ymd');
$Return=$Pdf->pdf2png($sourcePdf,$isSLS,$dir);

if($Return!=false||is_array($Return)){
    foreach($Return as $list){
        $data['data'][]=$imgUrl.$list;
    }
    $data['jsonfile']='pdf2img/pngLable/'.$dir.'/'.$ebay_id.'.json';
}else{
    $data['status']=0;
    $data['msg']='PDF转化失败';
    $data['jsonfile']='';
}



$json=json_encode($data);

if(!empty($data['jsonfile']) && $issave){
    file_put_contents(dirname(__DIR__).'/'.$data['jsonfile'],$json);
}

echo $json;
die();