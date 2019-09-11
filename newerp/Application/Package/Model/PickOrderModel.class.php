<?php
namespace Package\Model;

use Think\Exception;
use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  订单共用的一些操作   by  谭联星
 */
class PickOrderModel extends Model {
    protected $tableName = 'pick_order';

    //PK 年月日 第几单，最大每天可容 9999 单  不够的时候往前补0
    // PK1705240001
    // PK1705240002
    // PK1705240012
    function CreateAordersn(){
        $qianzui='PK'.date('ymd');

        $rs=$this->field('ordersn')->where("ordersn like '$qianzui%'")->order('ordersn desc')->find();
        if(empty($rs)){
            $endStr=substr(strval(100001),-4);
            $newOrdersn=$qianzui.$endStr;
        }else{
            $maxOrdersn=$rs['ordersn'];
            $newEndStr=(int)substr($maxOrdersn,-4)+100001;
            $newEndStr=substr(strval($newEndStr),-4);
            $newOrdersn=$qianzui.$newEndStr;
        }

        $data['ordersn']=$newOrdersn;
        $data['carrier_company']=0;
        $data['type']=0;
        $data['addtime']=0;
        /**
         * 防止添加的一瞬间已经被生成了产生报错
         * @author Shawn
         * @date 2018-09-04
         */
        try{
            $rs = $this->add($data);
        }catch (Exception $e){
            return false;
        }
        if($rs){
            return $newOrdersn;
        }
        return false;
    }
}