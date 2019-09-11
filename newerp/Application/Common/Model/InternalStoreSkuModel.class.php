<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 10:39
 */
namespace Common\Model;
use Think\Model;

class InternalStoreSkuModel extends Model
{
    protected $tableName = 'internal_store_sku';

    private $default_storeid=196;  // 金科  TODO: 金仓  注意 如果复制到 坑仓 中 要调换 这两个值

    private $internal_storeid=234;// 章坑  TODO: 坑仓

    /**
     * @desc 初始化
     * @author Shawn
     * @date 2019/4/18
     */
    public function _initialize()
    {
        $currStoreId    = C("CURRENT_STORE_ID");
        $mergeStoreId   = C("MERGE_STORE_ID");
        if(!empty($currStoreId)){
            $this->default_storeid = $currStoreId;
        }
        if(!empty($mergeStoreId)){
            $this->internal_storeid = $mergeStoreId;
        }
    }

    // 获取订单里面的sku的仓库
    public function getOrderSkuStore($sku,$storeid){
        $Rs= $this->where(compact('sku'))->field('storeid')->order('storeid')->select();

        // 一条数据都没有当然就是初始仓库啦！
        if(count($Rs)==0){
            return $storeid;
        }

        // 仓库信息只有一调数据的时候，当然就是这个仓库啦！
        if(count($Rs)==1){
            return $Rs[0]['storeid'];
        }

        return $storeid;

    }

    // 获取订单的主仓库 民治 或者樟坑仓 其他仓库!
    //TODO： 注意：只有订单主仓初步判断是196 的时候 才需要调用这个玩意
    public function getOrderMainStore($skus){
        // 如果两个 仓库里面都存在这个sku  程序不会将此sku作为判断仓库依据

        $skuArr=array_keys($skus);
        if(count($skus)==0){
            return $this->default_storeid;
        }
        //print_r($skuArr);die();
        //避免循环查库
        $Rs= $this->where(['sku'=>['in',$skuArr]])->field('sku,storeid')->select();

        // 这个表里面不存在，那就是金仓了
        if(count($Rs)==0){
            return $this->default_storeid;
        }

        //订单是单品，仓库sku 所在表 不存在 就是金仓
        if(count($skuArr)==1&&count($Rs)==0){
            return $this->default_storeid;
        }

        // 订单是单品  仓库sku 所在表 只有一个有!
        if(count($skuArr)==1&&count($Rs)==1){
            return $Rs[0]['storeid'];
        }

        //订单是单品，仓库sku 所在表有多个，表示两个仓都有货物  当然是 金仓啦
        if(count($skuArr)==1&&count($Rs)>1){
            return $this->default_storeid;
        }



        //订单是多品的时候  这尼玛 就复杂了-------------Start-------
        //格式化一下 查询的结果 sku 为索引 仓库（可多） 为索引
        $sku_store=[];
        foreach($Rs as $list){
            $sku_store[$list['sku']][]=$list['storeid'];
        }


        // 至少有一个sku 没有在 设置表里面存在 这种情况下 金仓！
        if(count($skuArr)>count($sku_store)){
            return $this->default_storeid;
        }


        $store1=0; // 金仓sku 知多少
        $store2=0; // 坑仓 sku 知多少

        //这里skuArr 和 $sku_store 长度一定是相等的
        foreach($skuArr as $sku){
            if(count($sku_store[$sku])>1){
                // 两个仓库都有，大家都不加分
                continue;
            }
            if($sku_store[$sku][0]==$this->internal_storeid){
                $store2++; // 坑仓代表 加1分
            }else{
                $store1++; // 金仓代表 加1分
            }
        }



        /**
         *测试人员谭 2017-12-07 16:06:55
         *说明: 如果 金仓没有sku， 坑仓有sku 则就是坑仓了
         */
        if($store1==0&&$store2>0){
            return $this->internal_storeid;
        }

        /**
         *测试人员谭 2017-12-07 16:07:31
         *说明: 一旦金仓 有sku 不管坑仓有木有都是金仓
         */
        return $this->default_storeid;

    }


}