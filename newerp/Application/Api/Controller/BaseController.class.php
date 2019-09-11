<?php

namespace Api\Controller;

use Think\Controller;

class BaseController extends Controller{

    //验证订单 是不是可以取消 终止的状态
    function checkAuth(){
        $token=$_POST['TOKEN'];
        $cli_key=$_POST['CLI_KEY'];

        $wms_token=C('TOKEN');
        $wms_cli_key=C('CLI_KEY');

        if($wms_cli_key!=$cli_key||$token!=$wms_token){
            echo json_encode(['status'=>0,'msg'=>'验证信息有误,请检查 密钥 和 token']);
            die();
        }
        return true;
    }

}