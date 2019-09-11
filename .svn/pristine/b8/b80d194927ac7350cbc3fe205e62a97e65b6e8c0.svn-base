<?php
/**
 * User: 王模刚
 * Date: 2017/11/10
 * Time: 16:42
 */

namespace Common\Model;


use Think\Model;

class EbayOnhandleModel extends Model
{
    protected $tableName;

    public function __construct($store_id,$name='',$tablePrefix='',$connection='')
    {
        $this->tableName = 'ebay_onhandle_'.$store_id;
        parent::__construct($name,$tablePrefix,$connection);
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

    /**
     * @desc 更新库位
     * @param $data
     * @return bool
     * @author Shawn
     * @date 2019/4/17
     */
    public function updateLocation($data)
    {
        $result = false;
        if(empty($data)){
            return $result;
        }
        $action     = $data['action'];
        $sku        = $data['data']['sku'];
        $location   = $data['data']['new_location'];
        if($action == 'del'){
            return true;
        }else{
            $map['goods_sn']    = strtoupper(trim($sku));
            $save['g_location'] = $location;
            $result = $this->where($map)->save($save);
        }
        $bol = ($result !== false) ? true :false;
        return $bol;
    }
}