<?php
namespace Common\Model;

use Products\Model\ProductsCombineModel;
use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  订单共用的一些操作   by  谭联星
 */
class OrderModel extends Model
{
    protected $tableName = 'erp_ebay_order';

    function getOrdersnByid($ebay_id) {
        return $this->where("ebay_id='$ebay_id'")->getField('ebay_ordersn');
    }

    /**
     * 这个方法是 供给海外仓提交给第三方 拆解 订单用的
     *
     * @param string $ebay_id  订单编号
     * @param string $ebay_ordersn  订单主表子表的关联
     * @return array  返回分解后的最小SKU因子
     * 返回值 有点复杂 请仔细看
     *
     * 0=>sku个数
     * 1=>sku中文名
     * 2=>sku订单售价（如果是组合品会不准）
     * 3=>订单ID
     * 4=>产品在 平台上的itemid
     * 5=>sku英文申报名
     * 6=>sku申报价值（TODO 注意，这个通常没什么用，如果你正在做一个 物流对接，
     * TODO 一定要忽略这个东西，如果物流强制你上传申报价值的时候，
     * TODO 你需要重新将订单中所有（上传的）的产品申报价的总价控制在19美元以内，如果看不懂这些，请联系 谭联星）
     * 7=>sku的图片
     * 8=>sku的重量
     * 9=>单条Orderdetail 的交易号
     * 10=>中文申报名字
     * 11=>海关编码
     * 12=>是否去包装
     */
    function OrderResolve($ebay_id='',$ebay_ordersn=''){
        if($ebay_ordersn==''&&$ebay_id==''){
            return false;
        }
        if($ebay_ordersn==''&&$ebay_id!=''){
            $ebay_ordersn=$this->getOrdersnByid($ebay_id);
        }

        $detail= M('erp_ebay_order_detail')->where("ebay_ordersn='$ebay_ordersn'")
            ->field('sku,ebay_itemprice,id as ebay_id,ebay_itemid,ebay_amount as c,ebay_tid')->select();
        $skuArr=array(); // 以sku 为索引的 数组
        foreach($detail as $v){
            $goods_sn   =strtoupper(trim($v['sku']));
            $c          =$v['c'];
            $price      =$v['ebay_itemprice'];
            $ebay_id    =$v['ebay_id'];
            $itemid     =$v['ebay_itemid'];
            $ebay_tid   = $v['ebay_tid'];
            $skuRs=$this->SelectGoods($goods_sn);
            if(count($skuRs)>0){
                if(!empty($skuArr[$goods_sn])){
                    $skuArr[$goods_sn][0]+=$c;
                }else{
                    $skuArr[$goods_sn]=array($c,$skuRs['goods_name'],$price,$ebay_id,$itemid,$skuRs['goods_ywsbmc'],$skuRs['goods_sbjz'],$skuRs['goods_pic'],$skuRs['goods_weight'],$ebay_tid,$skuRs['goods_zysbmc'],$skuRs['goods_hgbm'],$skuRs['isnopackaging']);
                }
            }else{
                $skuArr=$this->ResolveCom($goods_sn,$c,$skuArr,$price,$ebay_id,$itemid,$ebay_tid);
            }
        }
        return $skuArr;
    }

    function getOrderinfo($ebay_id) {
        $field = 'ebay_id,ebay_carrier,ebay_orderqk,ebay_ordersn,recordnumber,ebay_status,ebay_paidtime,';
        $field .= 'ebay_userid,ebay_username,ebay_account,ebay_usermail,ebay_usermail,ebay_street,ebay_street1,';
        $field .= 'ebay_note,ebay_city,ebay_state,ebay_couny,ebay_countryname,ebay_postcode,ebay_tracknumber,';
        $field .= 'ebay_phone,ebay_phone1,ebay_currency,ebay_total,ebay_ordertype,pxorderid,ebay_warehouse,ebay_company,ebay_addtime';
        return $this->where("ebay_id='$ebay_id'")->field($field)->select();
    }

    function updatePxordeid($pxorderid, $ebay_id) {
        $save['pxorderid'] = $pxorderid;
        return $this->where("ebay_id='$ebay_id'")->save($save);
    }

    function updateTracknumber($ebay_id, $tracknumber, $fee = 0) {
        $save['ebay_tracknumber'] = $tracknumber;
        if ($fee > 0) {
            $save['ordershipfee'] = $fee;
        }
        return $this->where("ebay_id=$ebay_id")->save($save);
    }

    function getPxordeidByid($ebay_id) {
        return $this->where("ebay_id='$ebay_id'")->getField('pxorderid');
    }

    /**
     *测试人员谭 2017-05-08 15:33:41
     *说明: 当一个库位 有多个 sku 的时候 排序的时候 也要考虑 sku
     */
    function getOrderMainSKULocal($orderid, $storeid) {
        $orderDetailModel = new \Order\Model\EbayOrderDetailModel();
        if (!is_numeric($orderid)) {
            return '';
        }
        $orderInfo = $this->where("ebay_id='$orderid'")->field('ebay_ordersn')->find();
        if (count($orderInfo) != 1) {
            return '';
        }
        $ordersn         = $orderInfo['ebay_ordersn'];
        $orderDetailInfo = $orderDetailModel->where("ebay_ordersn='$ordersn'")->find();
        //debug($orderDetailInfo);
        if (!$orderDetailInfo) {
            return '';
        }
        $sku = $orderDetailInfo['sku'];
        /*
         * @SKU浪漫地寻找库位～～～～
         * @ 先找到仓库表
         * */
        $onhandleModel  = new \Task\Model\EbayHandleModel();
        $skuStoreDetail = $onhandleModel->table("ebay_onhandle_" . $storeid)->where("goods_sn='$sku'")->find();
        // debug($skuStoreDetail);
        /*
         * @ 没有在仓库表 找到 怎么办～～～
         * @ 快到组合表 的碗里来～～～～
         * */
        if (count($skuStoreDetail) == 0) {
            $goodsCombineModel = new \Products\Model\ProductsCombineModel();
            $getSkuInfo        = $goodsCombineModel->where("goods_sn='$sku'")->find();
            if (count($getSkuInfo) == 0) {
                return '';
            }
            $skucombine     = $getSkuInfo['goods_sncombine'];
            $skucombine     = explode('*', $skucombine);
            $sku            = trim($skucombine[0]);
            $skuStoreDetail = $onhandleModel->table("ebay_onhandle_" . $storeid)->where("goods_sn='$sku'")->find();
            if (count($getSkuInfo) == 0 || empty($skuStoreDetail['g_location'])) {
                return '';
            } else {
                return strtoupper($skuStoreDetail['g_location'] . $sku);
            }
        } else {
            if (empty($skuStoreDetail['g_location'])) {
                return '';
            }
            return strtoupper($skuStoreDetail['g_location'] . $sku);
        }
    }

    private function SelectGoods($goods_sn){
        $filed='goods_name,goods_ywsbmc,goods_zysbmc,goods_location,goods_hgbm,goods_sbjz, goods_pic,goods_weight,isnopackaging';
        $rs=M('ebay_goods')->field($filed)->where("goods_sn='$goods_sn'")->find();
        return $rs;
    }

    private function ResolveCom($goods_sn_b,$c,$skuArr,$price,$ebay_id,$itemid,$ebay_tid){
        $rs=M('ebay_productscombine')->where("goods_sn='$goods_sn_b'")->field('goods_sncombine')->limit(1)->select();

        if(count($rs)>0){
            $goods_sncombine	= $rs[0]['goods_sncombine'];
            $goods_sncombine    = explode(',',$goods_sncombine);
            foreach($goods_sncombine as $v){
                $pline			= explode('*',$v);
                $goods_sn		= $pline[0];
                $goods_number	= $pline[1];
                $cc=$goods_number*$c;
                $skuRs=$this->SelectGoods($goods_sn);
                if(!empty($skuArr[$goods_sn])){
                    $skuArr[$goods_sn][0]+=$cc;
                }else{
                    $skuArr[$goods_sn]=array($cc,$skuRs['goods_name'],$price,$ebay_id,$itemid,$skuRs['goods_ywsbmc'],$skuRs['goods_sbjz'],$skuRs['goods_pic'],$skuRs['goods_weight'],$ebay_tid,$skuRs['goods_zysbmc'],$skuRs['goods_hgbm'],$skuRs['isnopackaging']);
                }
            }
        }
        return $skuArr;
    }

}