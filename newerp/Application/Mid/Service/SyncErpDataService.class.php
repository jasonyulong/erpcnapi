<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name SyncErpDataService.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/4/11
 * @Time: 15:09
 * @Description 同步erp数据服务层
 */
namespace Mid\Service;
use Think\Cache\Driver\Redis;
use Think\Exception;

class SyncErpDataService extends BaseService{

    /**
     * 返回数据
     * @var array
     */
    protected $backData = array(
        "status"    => true,
        "data"      => array(),
        "msg"       => '请求成功'
    );
    /**
     * 允许的操作类型
     * @var array
     */
    protected $actionArr = array(
        'update','del','add'
    );

    /**
     * @desc 更新ebay_goods数据
     * @param $data
     * @return mixed
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayGoods($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $ebayGoodsModel = new \Mid\Model\EbayGoodsModel();
        try{
            $result = $ebayGoodsModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$ebayGoodsModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($ebayGoodsModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($ebayGoodsModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_productscombine表数据
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayProductsCombine($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $combineModel = new \Mid\Model\EbayProductsCombineModel();
        try{
            $result = $combineModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$combineModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($combineModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($combineModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新ebay_user数据
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayUser($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $erpEbayUserModel = new \Mid\Model\EbayUserModel();
        try{
            $result = $erpEbayUserModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$erpEbayUserModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($erpEbayUserModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($erpEbayUserModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新ebay_onhandle_196表
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayOnHandle196($data)
    {
        return $this->updateEbayOnHandle($data,196);
    }
    /**
     * @desc 更新ebay_onhandle_234表
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayOnHandle234($data)
    {
        return $this->updateEbayOnHandle($data,234);
    }

    /**
     * @desc 更新onhandle表公用方法
     * @param $data
     * @param $storeId
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayOnHandle($data,$storeId)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $ebayOnhandleModel   = new \Common\Model\EbayOnhandleModel($storeId);
        try{
            $result = $ebayOnhandleModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$ebayOnhandleModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($ebayOnhandleModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($ebayOnhandleModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新库位
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateLocation($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $ebayOnhandleModel   = new \Common\Model\EbayOnhandleModel($this->currentid);
        try{
            $result = $ebayOnhandleModel->updateLocation($data);
            $this->addRecord(__FUNCTION__,$ebayOnhandleModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($ebayOnhandleModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($ebayOnhandleModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_carrier
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayCarrier($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $carrierModel   = new \Transport\Model\CarrierModel();
        try{
            $result = $carrierModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$carrierModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($carrierModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($carrierModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 goods_location_picker_region
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updatePickerRegion($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $regionModel   = new \Mid\Model\GoodsLocationPickerRegionModel();
        try{
            $result = $regionModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$regionModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($regionModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($regionModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_carrier_company
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateEbayCarrierCompany($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $companyModel   = new \Transport\Model\CarrierCompanyModel();
        try{
            $result = $companyModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$companyModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($companyModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($companyModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 internal_store_sku
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/12
     */
    public function updateInternalStoreSkuData($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $storeSkuModel   = new \Mid\Model\MidInternalStoreSkuModel();
        try{
            $result = $storeSkuModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$storeSkuModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($storeSkuModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($storeSkuModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_countries
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/17
     */
    public function updateEbayCountriesData($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $countriesModel   = new \Mid\Model\EbayCountriesModel();
        try{
            $result = $countriesModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$countriesModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($countriesModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($countriesModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_countries_alias
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/17
     */
    public function updateEbayCountriesAliasData($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $countriesAliasModel   = new \Mid\Model\EbayCountriesAliasModel();
        try{
            $result = $countriesAliasModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$countriesAliasModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($countriesAliasModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($countriesAliasModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_currency
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/17
     */
    public function updateEbayCurrencyData($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $currencyModel   = new \Mid\Model\EbayCurrencyModel();
        try{
            $result = $currencyModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$currencyModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($currencyModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($currencyModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }

    /**
     * @desc 更新 ebay_glock
     * @param $data
     * @return array
     * @author Shawn
     * @date 2019/4/17
     */
    public function updateEbayGlockData($data)
    {
        //检查数据
        if(!$this->checkData($data)){
            return $this->backData;
        }
        $glockModel   = new \Mid\Model\EbayGlockModel();
        try{
            $result = $glockModel->handleSyncData($data);
            $this->addRecord(__FUNCTION__,$glockModel->_sql());
        }catch (Exception $e){
            $message = $e->getMessage();
            $this->addAbnormal($glockModel->getTableName(),$data,$message);
            $this->setBack(false,$message);
            return $this->backData;
        }
        if(!$result){
            $msg = '数据库操作失败';
            $this->addAbnormal($glockModel->getTableName(),$data,$msg);
            $this->setBack(false,$msg);
            return $this->backData;
        }
        $this->setBack(true,"数据库操作完成");
        return $this->backData;
    }
    /**
     * @desc 设置返回值
     * @param $status
     * @param $msg
     * @return bool
     * @author Shawn
     * @date 2019/4/12
     */
    private function setBack($status,$msg)
    {
        $this->backData['status']   = $status;
        $this->backData['msg']      = $msg;
        return true;
    }

    /**
     * @desc 添加异常
     * @param $tableName
     * @param $data
     * @param $message
     * @return mixed
     * @author Shawn
     * @date 2019/4/16
     */
    public function addAbnormal($tableName,$data,$message)
    {
        $abnormal['table']      = $tableName;
        $abnormal['data']       = $data['data'];
        $abnormal['action']     = $data['action'];
        $abnormal['message']    = $message;
        $redis = new Redis();
        $key = "sync_abnormal:".$this->currentid;
        $data = json_encode($abnormal);
        $result = $redis->LPUSH($key,$data);
        return $result;
    }


    /**
     * @desc 读取异常列表
     * @param $limit
     * @return mixed
     * @author Shawn
     * @date 2019/4/12
     */
    public function queueAbnormal($limit)
    {
        $back['msg']    = '不存在异常数据';
        $back['data']   = array();
        $redis          = new Redis();
        $key            = "sync_abnormal:".$this->currentid;
        if($redis->exists($key)){
            $len = $redis->Llen($key);
            if($len <= 0){
                $back['msg'] = "暂无异常数据";
                return $back;
            }
            $queueLength = ($len <= $limit) ? $len : $limit;
            for($i=0;$i<=$queueLength;$i++){
                $pop = $redis->BRPOP($key, 1);
                if (empty($pop) || !is_array($pop)) {
                    $back['msg'] = "异常数据数据为空";
                    return $back;
                }
                $tempData = json_decode($pop[1], true);
                if(empty($tempData)){
                    continue;
                }
                $back['data'][] = $tempData;
            }
            $back['msg'] = '获取异常数据成功';
        }
        return $back;
    }

    /**
     * @desc 检查数据
     * @param $data
     * @return bool
     * @author Shawn
     * @date 2019/4/16
     */
    public function checkData($data)
    {
        if(empty($data)){
            $this->setBack(false,'传递参数为空');
            return false;
        }
        if(empty($data['data'])){
            $this->setBack(false,'需要操作数据为空');
            return false;
        }
        if(empty($data['action']) || !in_array($data['action'],$this->actionArr)){
            $msg = '非规范操作类型';
            $this->addAbnormal('',$data,$msg);
            $this->setBack(false,$msg);
            return false;
        }
        return true;
    }

    /**
     * @desc 添加日志
     * @param $function
     * @param $sql
     * @return bool
     * @author Shawn
     * @date 2019/4/22
     */
    public function addRecord($function,$sql)
    {
        $file = dirname(dirname(THINK_PATH))."/log/mid/".date('Ymd').$function.".txt";
        writeFile($file,$sql.PHP_EOL);
        return true;
    }


}