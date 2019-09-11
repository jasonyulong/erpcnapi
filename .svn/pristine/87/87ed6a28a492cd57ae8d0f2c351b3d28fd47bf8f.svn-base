<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name EbayCountriesAliasModel.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/4/17
 * @Time: 14:47
 * @Description ebay_countries_alias
 */

namespace Mid\Model;

class EbayCountriesAliasModel Extends BaseModel
{
    protected $tableName = 'ebay_countries_alias';

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