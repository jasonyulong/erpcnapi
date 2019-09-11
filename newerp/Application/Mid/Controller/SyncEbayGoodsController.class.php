<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 20:15
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayGoodsController extends Controller
{
    /**
     * mid_ebay_good 同步到 ebay_goods
     * @author 王模刚
     * @since  2017 10 26
     * @link  http://local.erpanapi.com/t.php?s=/Mid/SyncEbayGoods/syncEbayGoods
     */
    public function syncEbayGoods(){

        $productService = new \Mid\Service\ProductService();
        $productService->syncGoods();

    }

    /**
     * mid_ebay_product_combine 同步到  erp_ebay_product_combine  此方法废除，不再需要中间表
     * @author 王模刚
     * @since  2017 10 27
     * @link  http://local.erpanapi.com/t.php?s=/Mid/SyncEbayGoods/syncEbayCombineProduct
     */
    public function syncEbayCombineProduct(){
        $midProductCombineModel = new \Mid\Model\MidEbayProductCombineModel();
        $erpProductCombineModel = new \Products\Model\ProductsCombineModel();
        $Info = $midProductCombineModel->select();
        foreach($Info as $k => $v){
            $date = date('Y-m-d H:i:s');
            $saveData = array(
                'goods_sn' => $v['goods_sn'],
				'goods_sncombine' => $v['goods_sncombine']?:'',
				'combinestr' => $v['combinestr']?:'',
				'notes' => $v['notes']?:'',
				'salesuser' => $v['salesuser']?:'',
				'cguser' => $v['cguser']?:'',
				'kfuser' => $v['kfuser']?:'',
				'ebay_user' => $v['ebay_user']?:'',
				'add_time' => $v['add_time']?:'',
				'cateid' => $v['cateid']?:'',
				'istock' => $v['istock']?:'',
				'wms_add_time' => $date,
				'wms_update_time' => $date

            );
            $row = $erpProductCombineModel->where(['goods_sn'=>$v['goods_sn']])->find();
            if($row){
                $res = $erpProductCombineModel->where(['goods_sn'=>$v['goods_sn']])->save($saveData);
                if($res === false){
                    $return = $v['goods_sn'].'修改组合产品失败！';
                }else{
                    $return = $v['goods_sn'].'修改组合产品成功！';
                }
            }else{
                $res = $erpProductCombineModel->add($saveData);
                if($res === false){
                    $return = $v['goods_sn'].'添加组合产品失败！';
                }else{
                    $return = $v['goods_sn'].'添加组合产品成功！';
                }
            }
            dump($return);
        }
    }

    /**
     * mid_goods_sale_detail 同步到 erp_goods_sale_detail   此方法废除，不再需要中间表
     * @author 王模刚
     * @since 2017 10 27
     * @link  http://local.erpanapi.com/t.php?s=/Mid/SyncEbayGoods/syncEbaySaleDetail
     */
    public function syncEbaySaleDetail(){
        $midGoodsSaleDetailModel = new \Mid\Model\MidGoodsSaleDetailModel();
        $erpGoodsSaleDetailModel = new \Products\Model\GoodsSaleDetailModel();
        $Info = $midGoodsSaleDetailModel->select();
        foreach($Info as $k => $v){
            $saveData = array(
                'id' => $v['id'],
                'sku' => $v['sku'],
                'storeid' => $v['storeid'],
                'ebay_id' => $v['ebay_id'],
                'qty' => $v['qty'],
                'paytime' => $v['paytime'],
                'addtime' => $v['addtime'],
                'erp_addtime' => $v['erp_addtime'],
                'account' => $v['account']
            );
            $row = $erpGoodsSaleDetailModel->where(['ebay_id'=>$v['ebay_id'],'sku'=>$v['sku']])->find();
            if($row){
                $res = $erpGoodsSaleDetailModel->where(['ebay_id'=>$v['ebay_id'],'sku'=>$v['sku']])->save($saveData);
                if($res === false){
                    $return = $v['ebay_id'].','.$v['sku'].'修改销售详情失败！';
                }else{
                    $return = $v['ebay_id'].','.$v['sku'].'修改销售详情成功！';
                }
            }else{
                $res = $erpGoodsSaleDetailModel->add($saveData);
                if($res === false){
                    $return = $v['ebay_id'].','.$v['goods_sn'].'添加销售详情失败！';
                }else{
                    $return = $v['ebay_id'].','.$v['goods_sn'].'添加销售详情成功！';
                }
            }
            dump($return);
        }
    }


}