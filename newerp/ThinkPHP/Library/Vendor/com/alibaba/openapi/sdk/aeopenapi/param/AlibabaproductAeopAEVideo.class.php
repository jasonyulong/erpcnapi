<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class AlibabaproductAeopAEVideo extends SDKDomain {

       	
    private $aliMemberId;
    
        /**
    * @return 卖家主账户ID
    */
        public function getAliMemberId() {
        return $this->aliMemberId;
    }
    
    /**
     * 设置卖家主账户ID     
     * @param Long $aliMemberId     
     * 参数示例：<pre>1006680305</pre>     
     * 此参数必填     */
    public function setAliMemberId( $aliMemberId) {
        $this->aliMemberId = $aliMemberId;
    }
    
        	
    private $mediaId;
    
        /**
    * @return 视频ID
    */
        public function getMediaId() {
        return $this->mediaId;
    }
    
    /**
     * 设置视频ID     
     * @param Long $mediaId     
     * 参数示例：<pre>12345678</pre>     
     * 此参数必填     */
    public function setMediaId( $mediaId) {
        $this->mediaId = $mediaId;
    }
    
        	
    private $mediaType;
    
        /**
    * @return 视频的类型
    */
        public function getMediaType() {
        return $this->mediaType;
    }
    
    /**
     * 设置视频的类型     
     * @param String $mediaType     
     * 参数示例：<pre>video</pre>     
     * 此参数必填     */
    public function setMediaType( $mediaType) {
        $this->mediaType = $mediaType;
    }
    
        	
    private $mediaStatus;
    
        /**
    * @return 视频的状态
    */
        public function getMediaStatus() {
        return $this->mediaStatus;
    }
    
    /**
     * 设置视频的状态     
     * @param String $mediaStatus     
     * 参数示例：<pre>approved</pre>     
     * 此参数必填     */
    public function setMediaStatus( $mediaStatus) {
        $this->mediaStatus = $mediaStatus;
    }
    
        	
    private $posterUrl;
    
        /**
    * @return 视频封面图片的URL
    */
        public function getPosterUrl() {
        return $this->posterUrl;
    }
    
    /**
     * 设置视频封面图片的URL     
     * @param String $posterUrl     
     * 参数示例：<pre>http://img01.taobaocdn.com/bao/uploaded/TB1rNdGIVXXXXbTXFXXXXXXXXXX.jpg</pre>     
     * 此参数必填     */
    public function setPosterUrl( $posterUrl) {
        $this->posterUrl = $posterUrl;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "aliMemberId", $this->stdResult )) {
    				$this->aliMemberId = $this->stdResult->{"aliMemberId"};
    			}
    			    		    				    			    			if (array_key_exists ( "mediaId", $this->stdResult )) {
    				$this->mediaId = $this->stdResult->{"mediaId"};
    			}
    			    		    				    			    			if (array_key_exists ( "mediaType", $this->stdResult )) {
    				$this->mediaType = $this->stdResult->{"mediaType"};
    			}
    			    		    				    			    			if (array_key_exists ( "mediaStatus", $this->stdResult )) {
    				$this->mediaStatus = $this->stdResult->{"mediaStatus"};
    			}
    			    		    				    			    			if (array_key_exists ( "posterUrl", $this->stdResult )) {
    				$this->posterUrl = $this->stdResult->{"posterUrl"};
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "aliMemberId", $this->arrayResult )) {
    			$this->aliMemberId = $arrayResult['aliMemberId'];
    			}
    		    	    			    		    			if (array_key_exists ( "mediaId", $this->arrayResult )) {
    			$this->mediaId = $arrayResult['mediaId'];
    			}
    		    	    			    		    			if (array_key_exists ( "mediaType", $this->arrayResult )) {
    			$this->mediaType = $arrayResult['mediaType'];
    			}
    		    	    			    		    			if (array_key_exists ( "mediaStatus", $this->arrayResult )) {
    			$this->mediaStatus = $arrayResult['mediaStatus'];
    			}
    		    	    			    		    			if (array_key_exists ( "posterUrl", $this->arrayResult )) {
    			$this->posterUrl = $arrayResult['posterUrl'];
    			}
    		    	    		}
 
   
}
?>