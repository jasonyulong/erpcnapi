<?php
class LocalApi{
//    public  $baseUrl='http://hkerp.wisstone.com';
    public  $baseUrl='http://erp.spocoo.com';

    function __construct(){

    }



    function send($action,$data,$id,$val){
        $data=$this->getVerifyData($id,$val,$data);
        $url=$this->getUrl($action);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        $return_data = curl_exec($ch);
        print_r($return_data);
        curl_close($ch);
        $json=json_decode($return_data,true);
        if(isset($json['status'])){
            return $json;
        }
        return false;
    }

    private function getVerifyData($id,$val,$data){
         $strtime=time();
         $str=md5($id.$val.$strtime);
         $data['Verify']=$id;
         $data['time']=$strtime;
         $data['Verifystr']=$str;
         $data['data']=json_encode($data['data']);
         return $data;
    }

    private function getUrl($action){
        $action=strtolower($action);
        $arr=array(
            'sendpkuser'=>$this->baseUrl.'/API/acceptpkuser.php',
            'sendpkuserbyminzhi'=>$this->baseUrl.'/t.php?s=/Order/AcceptPKUserMinzhi/insertQueue',
            'sendweightbyminzhi'=>$this->baseUrl.'/API/acceptweight.php'
        );

        return $arr[$action];
    }

    function writeFile($file, $str) {
        $index = strripos($file, '/');
        if (!file_exists($file) && strripos($file, '/') !== false) {
            $fileDir = substr($file, 0, $index);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
            }
        }
        file_put_contents($file, "\xEF\xBB\xBF" . $str, FILE_APPEND);
    }





}