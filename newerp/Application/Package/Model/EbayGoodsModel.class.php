<?php
namespace Package\Model;

use Think\Model;

class EbayGoodsModel extends Model
{

    protected $tableName = "ebay_goods";

    /**
     * 获取商品信息
     */
    public function getGoodsInfo($goods_sn,$storeid=null){
        if(is_null($storeid)){
            $storeid = C("CURRENT_STORE_ID");
        }
    	$where['a.goods_sn'] = $goods_sn;
//    	$where['ebay_user'] = 'vipadmin';
        /**
        *测试人员谭 2017-05-24 10:59:30
        *说明:这里为了 性能 抛弃了查询 onhandle 表 如果修改库位的逻辑没有问题 那么 在 goods 表里面查库位也是ok 的
         * （仅适用196仓库）
        SELECT  a.goods_sn,a.lastsoldtime,a.goods_location,b.g_location FROM ebay_goods a
        JOIN ebay_onhandle_196 b
        USING(goods_sn)
        ORDER BY a.goods_id DESC
        LIMIT 19999
        */

        /**
        *测试人员谭 2017-11-07 15:35:48
        *说明: 这里的 join 是为了 取得 location 的时候始终是 ebay_onhandle_196 的 数据表
         *  TODO : 注意 这个文件，以后 WMS 复制一个出来的时候 一定要修改掉 196 ！！！！ 调用者 必须穿非196
         * */
    	return $this->alias('a')
            ->join('inner join ebay_onhandle_'.$storeid.' b using(goods_sn)')
            ->field('a.goods_name,a.goods_pic,b.g_location as goods_location')->where($where)->find();
    }

}