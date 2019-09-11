<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AlibabaproductonlineErrorDetail.class.php');

class AlibabaaeproductAeopModifyProductResponse extends SDKDomain {

       	
    private $isSuccess;
    
        /**
    * @return 是否操作成功
    */
        public function getIsSuccess() {
        return $this->isSuccess;
    }
    
    /**
     * 设置是否操作成功     
     * @param Boolean $isSuccess     
     * 参数示例：<pre>true</pre>     
     * 此参数必填     */
    public function setIsSuccess( $isSuccess) {
        $this->isSuccess = $isSuccess;
    }
    
        	
    private $productId;
    
        /**
    * @return 操作的商品id
    */
        public function getProductId() {
        return $this->productId;
    }
    
    /**
     * 设置操作的商品id     
     * @param Long $productId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setProductId( $productId) {
        $this->productId = $productId;
    }
    
        	
    private $modifyCount;
    
        /**
    * @return 成功个数
    */
        public function getModifyCount() {
        return $this->modifyCount;
    }
    
    /**
     * 设置成功个数     
     * @param Integer $modifyCount     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setModifyCount( $modifyCount) {
        $this->modifyCount = $modifyCount;
    }
    
        	
    private $errorDetails;
    
        /**
    * @return 错误详情
    */
        public function getErrorDetails() {
        return $this->errorDetails;
    }
    
    /**
     * 设置错误详情     
     * @param array include @see AlibabaproductonlineErrorDetail[] $errorDetails     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setErrorDetails(AlibabaproductonlineErrorDetail $errorDetails) {
        $this->errorDetails = $errorDetails;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "isSuccess", $this->stdResult )) {
    				$this->isSuccess = $this->stdResult->{"isSuccess"};
    			}
    			    		    				    			    			if (array_key_exists ( "productId", $this->stdResult )) {
    				$this->productId = $this->stdResult->{"productId"};
    			}
    			    		    				    			    			if (array_key_exists ( "modifyCount", $this->stdResult )) {
    				$this->modifyCount = $this->stdResult->{"modifyCount"};
    			}
    			    		    				    			    			if (array_key_exists ( "errorDetails", $this->stdResult )) {
    			$errorDetailsResult=$this->stdResult->{"errorDetails"};
    				$object = json_decode ( json_encode ( $errorDetailsResult ), true );
					$this->errorDetails = array ();
					for($i = 0; $i < count ( $object ); $i ++) {
						$arrayobject = new ArrayObject ( $object [$i] );
						$AlibabaproductonlineErrorDetailResult=new AlibabaproductonlineErrorDetail();
						$AlibabaproductonlineErrorDetailResult->setArrayResult($arrayobject );
						$this->errorDetails [$i] = $AlibabaproductonlineErrorDetailResult;
					}
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "isSuccess", $this->arrayResult )) {
    			$this->isSuccess = $arrayResult['isSuccess'];
    			}
    		    	    			    		    			if (array_key_exists ( "productId", $this->arrayResult )) {
    			$this->productId = $arrayResult['productId'];
    			}
    		    	    			    		    			if (array_key_exists ( "modifyCount", $this->arrayResult )) {
    			$this->modifyCount = $arrayResult['modifyCount'];
    			}
    		    	    			    		    		if (array_key_exists ( "errorDetails", $this->arrayResult )) {
    		$errorDetailsResult=$arrayResult['errorDetails'];
    			$this->errorDetails = AlibabaproductonlineErrorDetail();
    			$this->errorDetails->$this->setStdResult ( $errorDetailsResult);
    		}
    		    	    		}
 
   
}
?>