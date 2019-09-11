<?php
@session_start();
error_reporting(0);
//========================function()
function userloginLog($id,$ip){
    global $db;
    $time=time();
    $user="insert into ebay_userlog(userid,ip,`time`)value($id,'$ip',$time)";
    $db->execute($user);
}

function getStoreArrIndexID(){
    global $db;
    $arr=array();
    $vv = "select store_name,id from ebay_store";
    $vv		= $db->execute($vv);
    $vv		= $db->getResultArray($vv);
    foreach($vv as $v){
        $arr[$v['id']]=$v['store_name'];
    }
    return $arr;
}

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function sqlzhuru($str){
    echo $str,' loading...','<br>';
    $str=str_replace('\'','',$str);
    $str=preg_replace('/select|insert|update|delete|union|into|load_file|outfile|#|%/i','',$str);
    return $str;
}


include "include/dbconnect.php";
$db		= new DBClass();		//die();
$name		 = mysql_real_escape_string(sqlzhuru(trim($_POST['wst_name'])));
// 使用工号登录
if (preg_match('/^\d+$/', $name))
{
    $sql = "select username from ebay_user where id='$name' limit 1";
    $sqla		 = $db->execute($sql);
    $sql		 = $db->getResultArray($sqla);
    if ($sql) $name = $sql[0]['username'];
}



$pass		 = mysql_real_escape_string(trim($_POST['password']));
$pass        = md5(md5($pass));
$sql		 = "select id,user,power,ebayaccounts,message,record,truename,viewstore,vieworder from ebay_user where username='$name' and password='$pass' limit 1";
$sqla		 = $db->execute($sql);
$sql		 = $db->getResultArray($sqla);

$sql1 = "select id,user,power,ebayaccounts,message,record,truename,viewstore,vieworder,username from ebay_user where id='$name' and password='$pass' and truename like '%仓库' limit 1";
$sqla1 = $db->execute($sql1);
$sql1 =  $db->getResultArray($sqla1);

/* 释放mysql 系统资源*/
$db->free_result($sqla); $db->free_result($sqla1);
if($name !="" && $pass!=""){
    if(count($sql) >0 || count($sql1)>0){

        if(count($sql)<=0 && count($sql1)>0){
            $sql = $sql1;
            $name = $sql[0]['username'];
        }

        /**
         *测试人员谭 2017-12-19 18:09:53
         *说明: 外网访问人员
         */
        $Allow_Users=include './include/allow_users.php';
        $host=$_SERVER['HTTP_HOST'];

        if($host=='terryzhang.vicp.io'||$host=='sukidong.iask.in'){
            if(!in_array($name,$Allow_Users)){
                echo '<meta charset="utf-8"><div>登录未授权!请联系IT!</div>';
                die();
            }
        }

        $_SESSION['id']             = $sql[0]['id'];
        $_SESSION['user']			= $sql[0]['user'];
        $_SESSION['power'] 			= $sql[0]['power'];
        $_SESSION['truename']		= $name;
        $_SESSION['ebayaccounts']	= $sql[0]['ebayaccounts'];
        $_SESSION['messages']		= $sql[0]['message'];
        $_SESSION['pagesize']		= $sql[0]['record']?$sql[0]['record']:25;
        $_SESSION['tname']          = $sql[0]['truename'];
        $_SESSION['vieworderstatus']= $sql[0]['vieworder'];//可见订单的权限
        $viewstore                   = trim($sql[0]['viewstore'],',');
        /*************************************************************************
         *
         *     可见仓库的权限
         *
         *************************************************************/
        $storeArr=getStoreArrIndexID();
        $viewStoreArr=explode(',',$viewstore);
        # print_r($viewstore);
        if(count($viewStoreArr)==0){
            $_SESSION['viewstore']=" and a.ebay_warehouse='' ";
        }

        if(count($viewStoreArr)>=1){
            $temp=str_replace(',','\' or a.ebay_warehouse=\'',$viewstore);
            $_SESSION['viewstore']=' and ( a.ebay_warehouse=\''.$temp.'\')';
        }

        if(count($storeArr)==count($viewStoreArr)){
            $_SESSION['viewstore']='';
        }

        #echo $_SESSION['viewstore'].'@@@';die();

        /*************************************************************************
         *
         *     可见仓库的权限  end
         *
         *************************************************************/
        date_default_timezone_set ("Asia/Chongqing");
        $ip					= getRealIpAddr();
        $time				= date('Y-m-d H:i:s');



        $vvsql				= "update ebay_user set logtime='$time',ip='$ip' where username	='$name' ";
        $db->execute($vvsql);
        userloginLog($_SESSION['id'],$ip);
        $db->close();


        $login_success_url = "./t.php?s=/Order/Order/index&ebay_status=1723,1745,1724,2009&deliver_goods=1";
        // 老首页来的
        //$login_success_url = "orderindex.php?module=orders&ostatus=0&action=未付款";
        /**
         *测试人员谭 2017-07-21 09:55:12
         *说明: 包装元 自动跳转到 包状台
         */
        if (strstr($name, '包装员')) {
            $login_success_url = 't.php?s=/Package/MakeBale';
        }

        $html = <<<str
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type='text/javascript'>
window.location.href='$login_success_url';
</script>
</head>
<body>
</body>
</html>
str;

        echo $html;

    }else{


        $errormessage	= "用户名或密码错误!";
        echo "<script>alert('".$errormessage."');history.go(-1);</script>";


    }
}else{


    $errormessage	= "用户名或密码不能为空!";
    echo "<script>alert('".$errormessage."');history.go(-1);</script>";
}

?>

