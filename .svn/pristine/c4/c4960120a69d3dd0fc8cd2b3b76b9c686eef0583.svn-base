<?php
class PdfToimg{

    public function __construct(){}


    /**
     * @param $PDF  pdf文件
     */
    function pdf2png($PDF,$sls,$dir){

        $Path='./pngLable';
        # echo $PDF.'<br>';
        if(!file_exists($PDF)){
            echo 'not file';
            return false;
        }

        $bname=basename($PDF);
        $bname=str_ireplace('.pdf','',$bname);
        $IM =new imagick();
        $IM->setResolution(300,300);
        $IM->setCompressionQuality(100);

        try{
            $IM->readImage($PDF);
        }catch(Exception $e){
            echo '<pre>';
            print_r($e);
            die();
        }

        $Return=[];
        $i=0;

        if(!file_exists($Path.'/'.$dir)){
            mkdir($Path.'/'.$dir, 0777, true);
            chmod($Path.'/'.$dir,0777);
        }

        #print_r($IM);
        foreach($IM as $Key => $Var){
            $filename=date('YmdHis').rand(1111,9999);

            $Var->setImageFormat('png');
            $Filename = $Path.'/'.$dir.'/'.$filename.'.png';
            if(file_exists($Filename)){
                unlink($Filename);
            }

            if($Var->writeImage($Filename)==true){
                if($sls){
                    $height=$this->getImgcolor($Filename);
                    $Filename.='?height='.$height;
                }
                $Return[]= $Filename;
            }
            $i++;
        }

        return $Return;

    }


    function getImgcolor($file){
        $im = imagecreatefrompng($file);
        /**
         *测试人员谭 2017-12-09 13:58:01
         *说明: 第一次出现黑色的 宽度是什么
         */
        for($i=40;$i<130;$i++){
            $rgb = imagecolorat($im,200 , $i);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            // print_r([$r,$g,$b]);
            if($r<=2&&$g<=2&&$b<=2){
                //echo $i.'<br>';
                return $i;
            }
        }

        return 0;
    }

}