<?php
namespace Mid\Service;

class ProductService extends BaseService
{
    /**
     * 获取并保存产品
     * @author Simon 2017/11/21
     */
    public function getSyncSkuList($limit = 100) {
        $action   = 'Sku/getSkuList/wid/'.$this->currentid;
        $time1    = time();
        $response = $this->getErpData(['limit' => $limit], $action);
        echo '-----获取成功,共消耗' . (time() - $time1) . 's,共获取' . count($response['data']) . "条数据<br>\n";
        if ($response['ret'] != 100) {
            $message = $response['msg'] ?: '获取数据失败';
            exit($message);
        }
        if (empty($response['data'])) {
            exit('No Data');
        }
        $ebayGoodsModel = new \Common\Model\ErpEbayGoodsModel();
        $time1          = time();
        $successItems   = $ebayGoodsModel->updates($response['data']);
        echo '-----更新完成,共消耗' . (time() - $time1) . 's,共更新' . count($successItems) . "条数据<br>\n";
        $action   = 'Sku/updateSkuSyncStatus/wid/'.$this->currentid;
        $time1    = time();
        $response = $this->getErpData(['skuS' => implode(',', array_column($successItems, 'goods_sn'))], $action);
        echo '-----回传完成,共消耗' . (time() - $time1) . 's,共回传' . count($successItems) . "条数据<br>\n";
        if ($response['ret'] != 100) {
            $message = $response['msg'] ?: '回传更新状态失败';
            dump($action);
            dump($response);
            exit($message);
        }
        echo '';
    }

    /**
     * 同步组合产品
     * @author Simon 2017/11/21
     */
    public function getSyncCombineSkuList($limit = 50) {
        $action   = 'Sku/getCombineSkuList/wid/'.$this->currentid;
        $time1    = time();
        $response = $this->getErpData(['limit' => $limit], $action);
        dump($response);
        echo '-----获取成功,共消耗' . (time() - $time1) . 's,共获取' . count($response['data']) . "条数据<br>\n";
        if ($response['ret'] != 100) {
            $message = $response['msg'] ?: '获取数据失败';
            exit($message);
        }
        if (empty($response['data'])) {
            exit('No Data');
        }
        $combineModel = new \Products\Model\ProductsCombineModel();
        $time1        = time();
        $successSku = $combineModel->updates($response['data']);
        echo '-----更新完成,共消耗' . (time() - $time1) . 's,共更新' . count($successSku) . "条数据<br>\n";
        $action   = 'Sku/updateCombineSkuSyncStatus/wid/'.$this->currentid;
        $time1    = time();
        $response = $this->getErpData(['skuS' => implode(',',$successSku)], $action);
        echo '-----回传完成,共消耗' . (time() - $time1) . 's,共回传' . count($successSku) . "条数据<br>\n";
        if ($response['ret'] != 100) {
            $message = $response['msg'] ?: '回传更新状态失败';
            dump($action);
            dump($response);
            exit($message);
        }
        echo '';
    }
}