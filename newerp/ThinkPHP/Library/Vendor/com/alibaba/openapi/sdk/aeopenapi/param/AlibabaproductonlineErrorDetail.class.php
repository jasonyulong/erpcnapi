<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class AlibabaproductonlineErrorDetail extends SDKDomain {

       	
    private $errorCode;
    
        /**
    * @return 错误码
    */
        public function getErrorCode() {
        return $this->errorCode;
    }
    
    /**
     * 设置错误码     
     * @param String $errorCode     
     * 参数示例：<pre>11015111</pre>     
     * 此参数必填     */
    public function setErrorCode( $errorCode) {
        $this->errorCode = $errorCode;
    }
    
        	
    private $productIds;
    
        /**
    * @return 产品ID列表
    */
        public function getProductIds() {
        return $this->productIds;
    }
    
    /**
     * 设置产品ID列表     
     * @param array include @see Long[] $productIds     
     * 参数示例：<pre>[50001056157, 50001056153]</pre>     
     * 此参数必填     */
    public function setProductIds( $productIds) {
        $this->productIds = $productIds;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "errorCode", $this->stdResult )) {
    				$this->errorCode = $this->stdResult->{"errorCode"};
    			}
    			    		    				    			    			if (array_key_exists ( "productIds", $this->stdResult )) {
    				$this->productIds = $this->stdResult->{"productIds"};
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "errorCode", $this->arrayResult )) {
    			$this->errorCode = $arrayResult['errorCode'];
    			}
    		    	    			    		    			if (array_key_exists ( "productIds", $this->arrayResult )) {
    			$this->productIds = $arrayResult['productIds'];
    			}
    		    	    		}
 
   
}
?>