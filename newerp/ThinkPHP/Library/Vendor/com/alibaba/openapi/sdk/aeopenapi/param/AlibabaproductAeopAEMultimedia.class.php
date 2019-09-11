<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AlibabaproductAeopAEVideo.class.php');

class AlibabaproductAeopAEMultimedia extends SDKDomain {

       	
    private $aeopAEVideos;
    
        /**
    * @return 多媒体信息。
    */
        public function getAeopAEVideos() {
        return $this->aeopAEVideos;
    }
    
    /**
     * 设置多媒体信息。     
     * @param array include @see AlibabaproductAeopAEVideo[] $aeopAEVideos     
     * 参数示例：<pre>[
	{
		"aliMemberId": 117284237,
		"mediaId": 35683461,
		"mediaType": "video",
		"mediaStatus": "approved",
		"posterUrl": "http://img02.taobaocdn.com/bao/uploaded/TB1a7HKLVXXXXX5XVXXXXXXXXXX.jpg"
	}
]</pre>     
     * 此参数必填     */
    public function setAeopAEVideos(AlibabaproductAeopAEVideo $aeopAEVideos) {
        $this->aeopAEVideos = $aeopAEVideos;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "aeopAEVideos", $this->stdResult )) {
    			$aeopAEVideosResult=$this->stdResult->{"aeopAEVideos"};
    				$object = json_decode ( json_encode ( $aeopAEVideosResult ), true );
					$this->aeopAEVideos = array ();
					for($i = 0; $i < count ( $object ); $i ++) {
						$arrayobject = new ArrayObject ( $object [$i] );
						$AlibabaproductAeopAEVideoResult=new AlibabaproductAeopAEVideo();
						$AlibabaproductAeopAEVideoResult->setArrayResult($arrayobject );
						$this->aeopAEVideos [$i] = $AlibabaproductAeopAEVideoResult;
					}
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    		if (array_key_exists ( "aeopAEVideos", $this->arrayResult )) {
    		$aeopAEVideosResult=$arrayResult['aeopAEVideos'];
    			$this->aeopAEVideos = AlibabaproductAeopAEVideo();
    			$this->aeopAEVideos->$this->setStdResult ( $aeopAEVideosResult);
    		}
    		    	    		}
 
   
}
?>