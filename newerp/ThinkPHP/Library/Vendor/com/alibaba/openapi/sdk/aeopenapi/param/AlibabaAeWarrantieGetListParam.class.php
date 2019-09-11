<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class AlibabaAeWarrantieGetListParam {

        
        /**
    * @return 供应商id
    */
        public function getSupplierId() {
        $tempResult = $this->sdkStdResult["supplierId"];
        return $tempResult;
    }
    
    /**
     * 设置供应商id     
     * @param String $supplierId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setSupplierId( $supplierId) {
        $this->sdkStdResult["supplierId"] = $supplierId;
    }
    
        
        /**
    * @return 订单id
    */
        public function getOrderId() {
        $tempResult = $this->sdkStdResult["orderId"];
        return $tempResult;
    }
    
    /**
     * 设置订单id     
     * @param Long $orderId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setOrderId( $orderId) {
        $this->sdkStdResult["orderId"] = $orderId;
    }
    
        
        /**
    * @return 服务购买开始时间
    */
        public function getStartBuyTime() {
        $tempResult = $this->sdkStdResult["startBuyTime"];
        return $tempResult;
    }
    
    /**
     * 设置服务购买开始时间     
     * @param String $startBuyTime     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setStartBuyTime( $startBuyTime) {
        $this->sdkStdResult["startBuyTime"] = $startBuyTime;
    }
    
        
        /**
    * @return 服务购买结束时间
    */
        public function getEndBuyTime() {
        $tempResult = $this->sdkStdResult["endBuyTime"];
        return $tempResult;
    }
    
    /**
     * 设置服务购买结束时间     
     * @param String $endBuyTime     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setEndBuyTime( $endBuyTime) {
        $this->sdkStdResult["endBuyTime"] = $endBuyTime;
    }
    
        
        /**
    * @return 服务判定生效开始时间
    */
        public function getStartCreateTime() {
        $tempResult = $this->sdkStdResult["startCreateTime"];
        return $tempResult;
    }
    
    /**
     * 设置服务判定生效开始时间     
     * @param String $startCreateTime     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setStartCreateTime( $startCreateTime) {
        $this->sdkStdResult["startCreateTime"] = $startCreateTime;
    }
    
        
        /**
    * @return 服务判定生效结束时间
    */
        public function getEndCreateTime() {
        $tempResult = $this->sdkStdResult["endCreateTime"];
        return $tempResult;
    }
    
    /**
     * 设置服务判定生效结束时间     
     * @param String $endCreateTime     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setEndCreateTime( $endCreateTime) {
        $this->sdkStdResult["endCreateTime"] = $endCreateTime;
    }
    
        
        /**
    * @return 每页获取条数
    */
        public function getPageSize() {
        $tempResult = $this->sdkStdResult["pageSize"];
        return $tempResult;
    }
    
    /**
     * 设置每页获取条数     
     * @param Integer $pageSize     
     * 参数示例：<pre>200</pre>     
     * 此参数必填     */
    public function setPageSize( $pageSize) {
        $this->sdkStdResult["pageSize"] = $pageSize;
    }
    
        
        /**
    * @return 页码
    */
        public function getPageNo() {
        $tempResult = $this->sdkStdResult["pageNo"];
        return $tempResult;
    }
    
    /**
     * 设置页码     
     * @param Integer $pageNo     
     * 参数示例：<pre>1</pre>     
     * 此参数必填     */
    public function setPageNo( $pageNo) {
        $this->sdkStdResult["pageNo"] = $pageNo;
    }
    
        
    private $sdkStdResult=array();
    
    public function getSdkStdResult(){
    	return $this->sdkStdResult;
    }

}
?>