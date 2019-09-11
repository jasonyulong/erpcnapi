<?php
namespace Package\Model;

use Think\Model;

/** 警告--------------------------------------------
/** 警告--------------------------------------------
/** 警告--------------------------------------------
/** 警告--------------------------------------------
/** 警告--------------------------------------------
/** 警告--------------------------------------------
 *  看表名
 *  看表名
 *  看表名
 *  看表名
 *  看表名
 * ebay_orderslog 的压力太大 by  *测试人员谭 2017-03-23 22:32:26
 */

class OrderslogModel extends Model {

    protected $tableName = 'orderslog';


    /**
     * @param $ebayid      订单主键ID
     * @param $notes      日志
     * @param int $types  类型
     * @return mixed
     *
     *  订单操作的日志记录
     * 5=>订单状态修改
    6=>订单审核通过
    7=>系统自动操作
    8=>补发订单操作
    9=>订单运输方式
    11=>订单出库动作
    12=>订单通过扫描
    13=>订单选择打印
    19=>订单创建
    2=>sku成本价修改
    1=>sku预设价修改
    15=>sku建议采购价修改
    14=>sku审核通过
    3=>sku过审前状态操作
    16=>大客户订单
    17=>采购单操作日志
    99=>未分类日志
     */
    public function addordernote($ebayid,$notes,$types=99){

        if(empty($_SESSION['truename'])){
            $tuser='system';
        }else{
            $tuser=$_SESSION['truename'];
        }
        $map['ebay_id']=$ebayid;
        $map['notes']=$notes;
        $map['types']=$types;
        $map['operationtime']=time();
        $map['operationuser']=$tuser;
        $rr=$this->add($map);
        return $rr;
    }
}