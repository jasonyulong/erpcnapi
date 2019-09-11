<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Transport\Model;

use Think\Model;

class EbayAccountModel extends Model
{

    protected $tableName = "ebay_account";


    /**
     * 字段映射关系
     * @var array
     */
    protected $field = [
        'ebay_token'        => 'ebay',
        'AWS_ACCESS_KEY_ID' => 'aws',
        'appkey'            => 'smt',
        'wish_token'        => 'wish',
        'cdtoken'           => 'cd',
        'pm_token'          => 'pm',
        'walmartid'         => 'wt',
        'joom_secret'       => 'Jm',
    ];


    /**
     * 根据相应的账号名称获取账号所属的平台
     * creator : Cx
     * @param $account     :  账号名称
     * @return array       :  账号所属的平台信息或错误信息
     */
    public function getPlatformByAccount($account)
    {
        if (!$account) {
            return ['status'=>false, 'message'=>'账号不能为空.'];
        }

        $is_find = $this -> field($this -> field) -> where("ebay_account = '{$account}'") -> find();
        if (!$is_find) {
            return ['status'=>false, 'message'=>'未找到该账号.'];
        }

        $col_alias = array_values($this -> field);

        # 检索查询出的记录里的那一条记录里那个列的标识值不为空，就将对应的平台标识返回出来
        # 为防止循环时覆盖用点号连接之，其实永远都会只有一个有值所以连接并不会产生真的造成
        # 两个平台名连在一起的后果
        $platform = array_reduce($col_alias, function($co, $item) use ($is_find) {
            return $co . (trim($is_find[$item]) ? $item : null);
        });

        if ($platform) {
            return ['status' => true, 'message'=>$platform];
        }
        return ['status' => false, 'message'=>'没有找到对应的平台.'];
    }


    function getAllAccount(){
        $ss=$this->where("ebay_account!=''")->field('ebay_account')->order('ebay_account')->select();
        return $ss;
    }



    /**
     * 获取账号并且按照平台分组
     *
     */
    public function getAccountByGroup()
    {
        $accounts = [];

    /*    foreach ($this -> field as $key => $val) {
            $group_accounts = $this -> where("{$key} is not null and $key != ''") -> field('ebay_account') -> select();
            array_walk($group_accounts, function($item) use (&$accounts, $val) {
                $accounts[$val][] = $item['ebay_account'];
            });
        }*/
        $rs=$this->field('ebay_account,platform')->select();
        foreach($rs as $list){
            $ebay_account=$list['ebay_account'];
            $platform=$list['platform'];
            $accounts[$platform][]=$ebay_account;
        }
        return $accounts;
    }
}