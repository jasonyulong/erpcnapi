<?php
namespace Package\Model;

use Think\Model;

class PickFeeModel extends Model
{

    protected $tableName = "pick_fee";

    /**
     * 通用查询方法
     * @param $where 查询条件
     * @param $fields 查询字段
     * @return array
     * @author xiao
     * @date 2018-04-06
     */
    public function getAll($where,$fields){
        $data = $this->where($where)
            ->field($fields)
            ->select();
        return $data;
    }

}