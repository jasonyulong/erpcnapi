<?php
namespace Package\Model;

use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  订单共用的一些操作   by  谭联星
 */
class PickOrderLogModel extends Model {

    protected $tableName = 'pick_order_log';

    function addOneLog($ordersn,$log,$type=1){
        $user=$_SESSION['truename'];
        if(empty($user)){
            $user='system';
        }
        $data['adduser']=$user;
        $data['addtime']=time();
        $data['ordersn']=$ordersn;
        $data['note']=$log;
        $data['type']=$type;
        $this->add($data);
    }
}