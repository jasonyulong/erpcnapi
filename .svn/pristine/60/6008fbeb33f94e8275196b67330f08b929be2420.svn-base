<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Transport\Model;

use Think\Model;

class CarrierCompanyModel extends Model
{

    protected $tableName = "ebay_carrier_company";

    /**
    *测试人员谭 2017-04-20 19:09:35
    *说明: 获取所有物流公司简称 使用 id 索引
    */
    function getCompanyIndexId(){
        return $this->getField ('id,sup_abbr');
    }


    /**
     * 根据表的主键获取物流
     */
    public function getCompanyNameById($id)
    {
        $id = trim($id);
        if ($id) {
            return $this -> where(['id' => $id]) -> getField('sup_abbr');
        }
        return null;
    }

    /**
     * @desc 处理同步数据
     * @param $data
     * @return bool|mixed
     * @author Shawn
     * @date 2019/4/17
     */
    public function handleSyncData($data)
    {
        $result = false;
        if(empty($data)){
            return $result;
        }
        $action     = (string)$data['action'];
        $id         = $data['data']['id'];
        $map['id']  = $id;
        switch ($action){
            case 'del':
                $result = $this->where($map)->delete();
                break;
            case 'update':
                unset($data['data']['id']);
                $result = $this->where($map)->save($data['data']);
                break;
            case 'add':
                $result = $this->add($data['data']);
                break;
        }
        $bol = ($result !== false) ? true :false;
        return $bol;
    }
}