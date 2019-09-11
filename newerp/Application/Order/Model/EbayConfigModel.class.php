<?php

/**
 * Created by PhpStorm.
 * User: Cx
 * Date: 2016/11/15
 * Time: 17:52
 */
namespace Order\Model;

use \Think\Model;

/**
 * Class EbayConfigModel
 * @package Order\Model
 */
class EbayConfigModel extends Model
{
    const SET_TYPE_SHIPFEE = '1987';


    protected $tablePrefix = 'ebay_';
    protected $tableName = 'config00';


    /**
     * 根据仓库的ID编号来获取相应的仓库设置信息，若不传递参数则返回所有仓库的设置信息
     *
     * @param null $store_id : 仓库的ID编号
     * @param null $id       : 传回的记录id
     * @param bool $original_format : 传回的数据保留原有格式，就是不要拆分 仓库ID和类型的自合键
     *                                 只有在传回全部数据时才支持
     * @return mixed
     */
    public function getStoreSets($store_id = null, &$id = null, $original_format = false)
    {
        $re = $this -> field('id,ordershipfee') -> find();
        $id = $re['id'];
        $sets_arr = unserialize($re['ordershipfee']);

        foreach ($sets_arr as $k_store_id => $platform) {
            foreach ($platform as $k_platform => $platform_set) {
                $store_and_type = explode('&', $k_store_id);
                if ($store_and_type[1] != self::SET_TYPE_SHIPFEE) {
                    unset($sets_arr[$k_store_id]);
                }
            }
        }

        if ($store_id && $sets_arr) {
            return $sets_arr[$store_id.'&'.self::SET_TYPE_SHIPFEE];
        }

        # 保存原来的外层组合键名格式
        if ($original_format) {
            return $sets_arr;
        }

        return $this -> formatSetArr($sets_arr);
    }


    /**
     * 将仓库id和设置类型组成的组合键名转换为单一的仓库id作为键名
     *
     * @param $set_arr
     * @return array
     */
    private static function formatSetArr(&$set_arr)
    {
        $result = [];
        foreach($set_arr as $key => $val) {
            $store_and_type = explode('&', $key);
            $result[$store_and_type[0]] = $val;
        }
        return $result;
    }


    /**
     * 保存新的 设定订单不包邮的运费金额和账号
     *
     * @param $store_id       :  规则设定的仓库
     * @param $fee            :  设定的金额，超过其指定的金额将不能免运费
     * @param array $accounts :  规则适用于该指定的账号上
     * @return array
     */
    public function saveNewSetter($store_id, $fee, $accounts)
    {
        $re_message  = ['status' => false, 'message' => ''];

        if (!is_store($store_id)) {
            $message['message'] = '未能识别的仓库.';
            return $message;
        }

        # 将新提交过来的数据添加入原来的结果数组
        $accounts = explode(',', trim($accounts, ','));
        $is_same_platform = $this -> isAccountsWithSamePlatform($accounts, $platform);  # 判断所有的提交账号是不是属于同一个平台
        if (!$is_same_platform['status']) {
            return $is_same_platform;
        }
        # 判断是否已经添加过
        $had_add = $this -> getStoreSets($store_id,$accounts);
        $had_platform = $had_add[$platform];
        if ($had_platform) {
            $re_message['message'] = '指定仓库和平台的设定已经存在，请直接去修改!';
            return $re_message;
        }
        $origin_uns_data = $this -> getStoreSets(null, $id, true);

        # 以 store_id 和 设置类型 的组合作为键名 从而降低数组维度
        $origin_uns_data[$store_id.'&'.self::SET_TYPE_SHIPFEE][$platform] = [$fee, $accounts];
        $new_ser_data = serialize($origin_uns_data);
        $data = [
            'ordershipfee' => $new_ser_data,
            'id'           => $id,
        ];
        $save_result     = $this -> data($data) -> save();

        # 保存数据的结果分析
        if ($save_result === false) {
            $re_message['message'] = '数据保存失败!请重新提交数据.';
            return $re_message;
        } elseif ($save_result === 0) {
            $re_message['status'] = true;
            $re_message['message'] = '数据没有变化.';
            return $re_message;
        } else {
            $re_message = ['status'=>true, 'message'=>'数据保存成功！'];
            return $re_message;
        }
    }


    /**
     * 执行原有规则的更新操作
     *
     * @param $store_id  : 仓库的id编号
     * @param $fee       : 修改的临界费用
     * @param $accounts  : array 规则用于的账号
     * @return array     : 返回保存执行的结果
     */
    public function update($store_id, $fee, $accounts)
    {
        $message = ['status' => false, 'message' => ''];
        # 仓库存在性验证
        if (!is_store($store_id)) {
            $message['message'] = '未能识别的仓库.';
            return $message;
        }

        # 获取仓库的原设定信息
        $set_val    = $this -> getStoreSets($store_id);
        $set_val[0] = $fee;
        $set_val[1] = explode(',', trim($accounts, ','));
        $result = $this -> isAccountsWithSamePlatform($set_val[1], $platform);
        if (!$result['status']) {
            return $result;
        }

        $all_set    = $this -> getStoreSets(null, $id, true);
        $all_set[$store_id.'&'.self::SET_TYPE_SHIPFEE][$platform] = $set_val;
        $serialized_set = serialize($all_set);
        $data = [
            'ordershipfee'  =>  $serialized_set,
            'id'            =>  $id,
        ];
        $save_result = $this -> data($data) -> save();

        # 保存结果的分析
        if ($save_result === false) {
            $message['message'] = '数据更新保存失败，请重新提交.';
            return $message;
        } elseif ($save_result === 0) {
            $message['status']  = true;
            $message['message'] = '数据信息没有改变';
            return $message;
        } else {
            $message['status']  = true;
            $message['message'] = '数据更新成功！';
            return $message;
        }
    }


    /**
     * 验证参数所给的一个账号数组是不是都属于同一个平台
     *
     * @param array $accounts  ： 账号数组
     */
    private function isAccountsWithSamePlatform($accounts, &$_platform = null)
    {
        $platform = null;
        foreach ($accounts as $account) {
            $re = D('EbayAccount') -> getPlatformByAccount($account);
            if ($re['status'] && $platform === null ) {
                 $platform = $re['message'];
            } elseif ($re['status'] && $platform == $re['message']) {
                continue;
            } elseif(!$re['status']) {
                return ['status' => false, 'message' => "账号:{$account} ".$re['message']];
            } else {
                return ['status' => false, 'message' => "账号:{$account} 平台有差别."];
            }
        }
        $_platform = $platform;
        return ['status'=>true, 'message'=>'success'];
    }
}

