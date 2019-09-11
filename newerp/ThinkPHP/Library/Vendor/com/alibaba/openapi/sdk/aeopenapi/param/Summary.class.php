<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class Summary extends SDKDomain {

       	
    private $productName;
    
        /**
    * @return 产品名
    */
        public function getProductName() {
        return $this->productName;
    }
    
    /**
     * 设置产品名     
     * @param String $productName     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setProductName( $productName) {
        $this->productName = $productName;
    }
    
        	
    private $productImageUrl;
    
        /**
    * @return 产品图片链接
    */
        public function getProductImageUrl() {
        return $this->productImageUrl;
    }
    
    /**
     * 设置产品图片链接     
     * @param String $productImageUrl     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setProductImageUrl( $productImageUrl) {
        $this->productImageUrl = $productImageUrl;
    }
    
        	
    private $productDetailUrl;
    
        /**
    * @return 产品链接
    */
        public function getProductDetailUrl() {
        return $this->productDetailUrl;
    }
    
    /**
     * 设置产品链接     
     * @param String $productDetailUrl     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setProductDetailUrl( $productDetailUrl) {
        $this->productDetailUrl = $productDetailUrl;
    }
    
        	
    private $orderUrl;
    
        /**
    * @return 订单链接
    */
        public function getOrderUrl() {
        return $this->orderUrl;
    }
    
    /**
     * 设置订单链接     
     * @param String $orderUrl     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setOrderUrl( $orderUrl) {
        $this->orderUrl = $orderUrl;
    }
    
        	
    private $senderName;
    
        /**
    * @return 消息发送者名字
    */
        public function getSenderName() {
        return $this->senderName;
    }
    
    /**
     * 设置消息发送者名字     
     * @param String $senderName     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setSenderName( $senderName) {
        $this->senderName = $senderName;
    }
    
        	
    private $receiverName;
    
        /**
    * @return 消息接收者名字
    */
        public function getReceiverName() {
        return $this->receiverName;
    }
    
    /**
     * 设置消息接收者名字     
     * @param String $receiverName     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setReceiverName( $receiverName) {
        $this->receiverName = $receiverName;
    }
    
        	
    private $senderLoginId;
    
        /**
    * @return 消息发送者账号
    */
        public function getSenderLoginId() {
        return $this->senderLoginId;
    }
    
    /**
     * 设置消息发送者账号     
     * @param String $senderLoginId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setSenderLoginId( $senderLoginId) {
        $this->senderLoginId = $senderLoginId;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "productName", $this->stdResult )) {
    				$this->productName = $this->stdResult->{"productName"};
    			}
    			    		    				    			    			if (array_key_exists ( "productImageUrl", $this->stdResult )) {
    				$this->productImageUrl = $this->stdResult->{"productImageUrl"};
    			}
    			    		    				    			    			if (array_key_exists ( "productDetailUrl", $this->stdResult )) {
    				$this->productDetailUrl = $this->stdResult->{"productDetailUrl"};
    			}
    			    		    				    			    			if (array_key_exists ( "orderUrl", $this->stdResult )) {
    				$this->orderUrl = $this->stdResult->{"orderUrl"};
    			}
    			    		    				    			    			if (array_key_exists ( "senderName", $this->stdResult )) {
    				$this->senderName = $this->stdResult->{"senderName"};
    			}
    			    		    				    			    			if (array_key_exists ( "receiverName", $this->stdResult )) {
    				$this->receiverName = $this->stdResult->{"receiverName"};
    			}
    			    		    				    			    			if (array_key_exists ( "senderLoginId", $this->stdResult )) {
    				$this->senderLoginId = $this->stdResult->{"senderLoginId"};
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "productName", $this->arrayResult )) {
    			$this->productName = $arrayResult['productName'];
    			}
    		    	    			    		    			if (array_key_exists ( "productImageUrl", $this->arrayResult )) {
    			$this->productImageUrl = $arrayResult['productImageUrl'];
    			}
    		    	    			    		    			if (array_key_exists ( "productDetailUrl", $this->arrayResult )) {
    			$this->productDetailUrl = $arrayResult['productDetailUrl'];
    			}
    		    	    			    		    			if (array_key_exists ( "orderUrl", $this->arrayResult )) {
    			$this->orderUrl = $arrayResult['orderUrl'];
    			}
    		    	    			    		    			if (array_key_exists ( "senderName", $this->arrayResult )) {
    			$this->senderName = $arrayResult['senderName'];
    			}
    		    	    			    		    			if (array_key_exists ( "receiverName", $this->arrayResult )) {
    			$this->receiverName = $arrayResult['receiverName'];
    			}
    		    	    			    		    			if (array_key_exists ( "senderLoginId", $this->arrayResult )) {
    			$this->senderLoginId = $arrayResult['senderLoginId'];
    			}
    		    	    		}
 
   
}
?>