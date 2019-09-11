<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 10:39
 */
namespace Common\Model;
use Think\Model;

class ErrorLocationLogModel extends Model
{
    protected $tableName = 'error_location_log';

    function addOneNote($location,$note,$sku='',$ebay_id=0){
        $user=IS_CLI?'system':$_SESSION['truename'];

        $arr=[];
        $arr['location']=$location;
        $arr['sku']=$sku;
        $arr['ebay_id']=$ebay_id;
        $arr['note']=$note;
        $arr['adduser']=$user;
        $this->add($arr);
    }
}