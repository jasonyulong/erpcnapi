<?php
/**
 * 出库称重规则设置表
 * Created by xiao.
 * Date: 2018/4/11
 * Time: 14:58
 */
namespace Package\Model;

use Think\Model;

class WeighRuleModel extends Model
{
    protected $tableName = 'weigh_rule';

    /**
     * 获取单条数据通用方法
     * @param $field 查询字段
     * @param $where 查询条件
     * @return array
     * @author xiao
     * @date 2018-04-12
     */
    public function getOne($field,$where){
        $data = $this->where($where)
            ->field($field)
            ->find();
        return $data;
    }

    /**
     * 获取多个数据通用方法
     * @param $where 查询条件
     * @param $field 查询字段
     * @param $orderBy 排序字段
     * @return array 返回值
     * @author xiao
     * @date 208-04-11
     */
    public function getAll($field,$orderBy=null,$where=null){
        $query = $this->field($field);
        if(!is_null($where)){
            $query = $this->where($where);
        }
        if(!is_null($orderBy)){
            $query->order($orderBy);
        }
        $data = $query->select();
        return $data;
    }

    /**
     * 插入数据通用方法
     * @param $data 需要添加数据
     * @return id 主键id
     * @author xiao
     * @date 2018-04-12
     */
    public function insertData($data){
        $id = $this->add($data);
        return $id;
    }

    /**
     * 更新数据通用方法
     * @param $where 更新条件
     * @param $data 更新数据
     * @return bool
     * @author xiao
     * @date 2018-04-12
     */
    public function saveData($where,$data){
        $result = $this->where($where)
            ->save($data);
        return $result;
    }
    /**
     * 删除数据通用方法（可删除多个和单个）
     * @param $ids 主键字符串
     * @return bool
     * @author xiao
     * @date 2018-04-12
     */
    public function deleteData($ids){
        $result = $this->delete($ids);
        return $result;
    }

}
