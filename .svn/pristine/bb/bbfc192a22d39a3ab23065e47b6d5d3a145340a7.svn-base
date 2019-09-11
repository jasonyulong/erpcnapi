<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Order\Model;

use Think\Model;

class EbayAccountModel extends Model
{

    protected $tableName = "ebay_account";

    /**
     * 根据相应的账号名称获取账号所属的平台
     * creator : Cx
     * @param $account     :  账号名称
     * @return array       :  账号所属的平台信息或错误信息
     */
    public function getPlatformByAccount($account)
    {
        $field = [
            'ebay_token'        => 'ebay',
            'AWS_ACCESS_KEY_ID' => 'aws',
            'appkey'            => 'smt',
            'wish_token'        => 'wish',
            'cdtoken'           => 'cd',
            'pm_token'          => 'pm',
        ];

        if (!$account) {
            return ['status'=>false, 'message'=>'账号不能为空.'];
        }

        $is_find = $this -> field($field) -> where("ebay_account = '{$account}'") -> find();
        if (!$is_find) {
            return ['status'=>false, 'message'=>'未找到该账号.'];
        }

        $col_alias = array_values($field);

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

    public function getData($map,$limit, $order){
        $rs=$this->where($map)->limit($limit)->order($order)->select();
        //echo $this->_sql();
        return $rs;
    }

    public function getCount($map){
        return $this->where($map)->count();
    }

    public function saveJoomAccount($sava){
        //debug($sava);
        $account=$sava['ebay_account'];
        $rr=$this->where("ebay_account='$account'")->field('id')->select();
        if($rr){
            $id=$rr[0]['id'];
            $sava['id']=$id;
            return $this->save($sava);
        }else{
            return $this->add($sava);
        }
    }

    /**
     * 添加Linio账户
     * @param $save
     */
    public function saveLinioAccount($sava){
        $account=$sava['ebay_account'];
        $rr=$this->where("ebay_account='$account'")->field('id')->select();
        if($rr){
            $id=$rr[0]['id'];
            $sava['id']=$id;
            return $this->save($sava);
        }else{
            return $this->add($sava);
        }
    }


    /**
     * 添加Lazada账户
     * @param $save
     */
    public function saveLazadaAccount($sava){
        $account=$sava['ebay_account'];
        $rr=$this->where("ebay_account='$account'")->field('id')->select();
        if($rr){
            $id=$rr[0]['id'];
            $sava['id']=$id;
            return $this->save($sava);
        }else{
            return $this->add($sava);
        }
    }

    /**
     * 添加shopee账户
     * @param $sava
     * @return bool|mixed
     */
    public function saveShopeeAccount($sava){
        $account=$sava['ebay_account'];
        $rr=$this->where("ebay_account='$account'")->field('id')->select();
        if($rr){
            $id=$rr[0]['id'];
            $sava['id']=$id;
            return $this->save($sava);
        }else{
            return $this->add($sava);
        }
    }

    /**
     * 获取帐号对应的id
     */
    public function getAccountId($accounts){
        if(count($accounts) < 1 || !is_array($accounts)){
            return false;
        }
        $where['ebay_account'] = array('in', $accounts);
        return $this->where($where)->getField('ebay_account,id', true);
    }
    
    public function getAllPlaforms(){
    	
    	return $this->where("platform !=''")->order('platform')->group('platform')->getField('platform',true);
    	
	}

}