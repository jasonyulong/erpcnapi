<?php
namespace Mid\Controller;

use Mid\Service\GoodsOutWeightService;
use Think\Controller;

class GetProductController extends Controller
{
    public function index()
    {
        echo 'index';
    }

    /**
     * get product list
     * @author    Rex
     * @since     2017-10-24 21:30
     * @link      http://local.erpanapi.com/t.php?s=/Mid/GetProduct/getSkuList
     */
    public function getSkuList()
    {
        echo '<pre>';
        $productService = new \Mid\Service\ProductService();
        $productService->getSyncSkuList($_REQUEST['limit'] ?: 50);
    }

    /**get Combine Sku List 不需要中间表进行转
     * @author 王模刚
     * @since  2017 10 24 17:16
     * @link   http://local.erpanapi.com/t.php?s=/Mid/GetProduct/getCombineSkuList
     */
    public function getCombineSkuList()
    {
        echo '<pre>';
        $productService = new \Mid\Service\ProductService();
        $productService->getSyncCombineSkuList($_REQUEST['limit'] ?: 50);
    }

    /**
     * 同步goods_out_weight
     * @since   2018-01-12 10:08:22
     * @author Simon
     */
    public function getGoodsOutWeight()
    {
        echo '<pre>';
        $service = new GoodsOutWeightService();
        $service->getGoodsOutWeightList($_REQUEST['limit'] ?: 50);
    }
}
