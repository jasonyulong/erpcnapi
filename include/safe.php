<?php
$url_arr=array(
    'xss'=>"\\=\\+\\/v(?:8|9|\\+|\\/)|\\%0acontent\\-(?:id|location|type|transfer\\-encoding)",
);

$args_arr=array(
    'xss'=>"[\\'\\\"\\;\\*\\<\\>].*\\bon[a-zA-Z]{3,15}[\\s\\r\\n\\v\\f]*\\=|\\b(?:expression)\\(|\\<script[\\s\\\\\\/]|\\<\\!\\[cdata\\[|\\b(?:eval|alert|prompt|msgbox)\\s*\\(|url\\((?:\\#|data|javascript)",

    'sql'=>"[^\\{\\s]{1}(\\s|\\b)+(?:select\\b|update\\b|insert(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+into\\b).+?(?:from\\b|set\\b)|[^\\{\\s]{1}(\\s|\\b)+(?:create|delete|drop|truncate|rename|desc)(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+(?:table\\b|from\\b|database\\b)|into(?:(\\/\\*.*?\\*\\/)|\\s|\\+)+(?:dump|out)file\\b|\\bsleep\\([\\s]*[\\d]+[\\s]*\\)|benchmark\\(([^\\,]*)\\,([^\\,]*)\\)|(?:declare|select)\\b.*@|union\\b.*(?:select|all)\\b|(?:select|update|insert|create|delete|drop|grant|truncate|rename|exec|desc|from|table|database|set|where)\\b.*(charset|ascii|bin|char|uncompress|concat|concat_ws|conv|export_set|hex|instr|left|load_file|locate|mid|sub|substring|oct|reverse|right|unhex)\\(|(?:master\\.\\.sysdatabases|msysaccessobjects|msysqueries|sysmodules|mysql\\.db|sys\\.database_name|information_schema\\.|sysobjects|sp_makewebtask|xp_cmdshell|sp_oamethod|sp_addextendedproc|sp_oacreate|xp_regread|sys\\.dbms_export_extension)",

    'other'=>"\\.\\.[\\\\\\/].*\\%00([^0-9a-fA-F]|$)|%00[\\'\\\"\\.]");

$referer=empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);
$query_string=empty($_SERVER["QUERY_STRING"]) ? array() : array($_SERVER["QUERY_STRING"]);

check_data_safe_requst($query_string,$url_arr);
check_data_safe_requst($_GET,$args_arr);
check_data_safe_requst($_POST,$args_arr);
check_data_safe_requst($_COOKIE,$args_arr);
check_data_safe_requst($referer,$args_arr);

function W_log($log) {
    $logpath = $_SERVER["DOCUMENT_ROOT"] . "/logaaaaa.txt";
    $log_f   = fopen($logpath, "a+");
    fputs($log_f, $log . "\r\n");
    fclose($log_f);
}

function check_data_safe_requst($arr, $v) {  #print_r($arr);
    foreach ($arr as $key => $value) {
        if (!is_array($key)) {
            check_data_excute($key, $v);
        } else {
            check_data_safe_requst($key, $v);
        }

        if (!is_array($value)) {
            check_data_excute($value, $v);
        } else {
            check_data_safe_requst($value, $v);
        }
    }
}

function check_data_excute($str,$v){
    foreach($v as $key=>$value){
        //发邮件这里容易被误杀
        if(strstr($_SERVER["PHP_SELF"],'getajax.php')!==false){
            return;
        }
        if (preg_match("/".$value."/is",$str,$m)==1||preg_match("/".$value."/is",urlencode($str),$m)==1){
            print_r($m);
            W_log("<br>IP: ".$_SERVER["REMOTE_ADDR"]."<br>: ".strftime("%Y-%m-%d %H:%M:%S")."<br>:".$_SERVER["PHP_SELF"]."<br>: ".$_SERVER["REQUEST_METHOD"]."<br>: ".$str);
            print " input error!!!!!";
            exit();
        }
    }
}