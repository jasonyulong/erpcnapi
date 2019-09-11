<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AlibabaaeproductAeopModifyProductResponse.class.php');

class AlibabaAeProductRenewExpireResult {

        	
    private $modifyResponse;
    
        /**
    * @return 
    */
        public function getModifyResponse() {
        return $this->modifyResponse;
    }
    
    /**
     * 设置     
     * @param AlibabaaeproductAeopModifyProductResponse $modifyResponse     
          
     * 此参数必填     */
    public function setModifyResponse(AlibabaaeproductAeopModifyProductResponse $modifyResponse) {
        $this->modifyResponse = $modifyResponse;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "modifyResponse", $this->stdResult )) {
    				$modifyResponseResult=$this->stdResult->{"modifyResponse"};
    				$this->modifyResponse = new AlibabaaeproductAeopModifyProductResponse();
    				$this->modifyResponse->setStdResult ( $modifyResponseResult);
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    		if (array_key_exists ( "modifyResponse", $this->arrayResult )) {
    		$modifyResponseResult=$arrayResult['modifyResponse'];
    			    			$this->modifyResponse = new AlibabaaeproductAeopModifyProductResponse();
    			    			$this->modifyResponse->$this->setStdResult ( $modifyResponseResult);
    		}
    		    	    		}

}
?>