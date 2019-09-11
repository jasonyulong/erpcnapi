<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 14:09
 */
namespace Common\Model;

use Think\Model;

class CurrentLocationLogModel extends Model
{
    protected $tableName = 'current_location_log';

    //添加一条记录
    function addOneNote($location,$note,$sku='',$ebay_id=0){
        $user = IS_CLI ? 'system' : $_SESSION['truename'];

        $arr = [];
        $arr['location'] = $location;
        $arr['sku'] = $sku;
        $arr['ebay_id'] = $ebay_id;
        $arr['note'] = $note;
        $arr['adduser'] = $user;
        $this->add($arr);
    }
}