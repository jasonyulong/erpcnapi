<?php
namespace Mid\Service;
class SetGoodsWeightService  extends BaseService{
    const _MODULENAME = 'SetGoodsWeight';
    /**
     * 同步仓库的商品总量信息到erp
     * @return bool
     */
    public function setGoodsWeight($data,$weight){
        $action     = "SetGoodsWeight/SetGoodsWeight/wid/234";
        $data['xiugaiqian'] = $weight;
        $data['username'] = session('truename');
        $data['weight'] = $data['weight']/1000;
        $getData = $this->getErpData($data, $action);
        if(!empty($getData) && $getData['ret'] == 100 ){
            if($getData['data'] == 'ok'){
                self::writeLog($data,1,$weight);
            }elseif($getData['data'] == 'no'){
                self::writeLog($data,'-1');
            }
        }
        return $getData;
    }

    /**
     * @param $skuArr       sku名称
     * @param $flg        是否成功 1 成功  -1 失败
     */
    private static function writeLog($skuArr,$flg,$weight=''){
        if($flg == 1){
            $msg = 'SKU：'.$skuArr['sku'].' 修改前重量：'.$weight.' 修改后重量：'.$skuArr['weight'].'修改成功，记录时间为：'.date('Y-m-d H:i:s');
        }elseif($flg == '-1') {
            $msg = 'SKU：' . $skuArr['sku'] . ' 修改前重量：' . $skuArr['weight'] . ' 预修改重量：'.$skuArr['weight']. '修改失败，记录时间为：' . date('Y-m-d H:i:s');
        }
        $logDir = self::_MODULENAME;
        self::log($msg,$logDir,$flg);

    }

    //记录日志
    private static function log($msg, $logDir, $res = false){
//        $color = $res ? "#393" : "#933";
//        echo '<meta charset="utf-8">';
//        echo '<div style="color:' . $color . '">' . $msg . '</div><br/>';
        $basePath = dirname(dirname(THINK_PATH)) . '/log/' . $logDir;
        $str      = $res ? '/success/' : '/error/';
        $fileName = $basePath . $str . date('Ymd') . '.log';
        $index    = strripos($fileName, '/');
        if (!file_exists($fileName) && strripos($fileName, '/') !== false) {
            $fileDir = substr($fileName, 0, $index);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
            }
        }
        $log = $msg . date('Y-m-d H:i:s', time());
        file_put_contents($fileName, "\xEF\xBB\xBF" . $log . PHP_EOL, FILE_APPEND);
    }
}


