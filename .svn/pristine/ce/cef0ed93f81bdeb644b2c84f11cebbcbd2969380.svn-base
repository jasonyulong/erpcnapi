<?php
namespace Package\Model;

use Think\Model;

class TopMenuModel extends Model
{

    protected $tableName = "ebay_topmenu";


    function getMenuName(){
        $rs=$this->getField('id,name');
        $rs[0]='未付款';
        $rs[1]='待处理';
        $rs[2]='已发货';
        return $rs;
    }

}