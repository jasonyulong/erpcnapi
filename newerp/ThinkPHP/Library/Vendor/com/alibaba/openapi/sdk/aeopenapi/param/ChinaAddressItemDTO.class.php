<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class ChinaAddressItemDTO extends SDKDomain {

       	
    private $areaId;
    
        /**
    * @return 地址区域的ID
    */
        public function getAreaId() {
        return $this->areaId;
    }
    
    /**
     * 设置地址区域的ID     
     * @param Long $areaId     
     * 参数示例：<pre>10001</pre>     
     * 此参数必填     */
    public function setAreaId( $areaId) {
        $this->areaId = $areaId;
    }
    
        	
    private $cnDiplayName;
    
        /**
    * @return 中文展示名称
    */
        public function getCnDiplayName() {
        return $this->cnDiplayName;
    }
    
    /**
     * 设置中文展示名称     
     * @param String $cnDiplayName     
     * 参数示例：<pre>北京市</pre>     
     * 此参数必填     */
    public function setCnDiplayName( $cnDiplayName) {
        $this->cnDiplayName = $cnDiplayName;
    }
    
        	
    private $pyDiplayName;
    
        /**
    * @return 英文文展示名称
    */
        public function getPyDiplayName() {
        return $this->pyDiplayName;
    }
    
    /**
     * 设置英文文展示名称     
     * @param String $pyDiplayName     
     * 参数示例：<pre>bei jing shi</pre>     
     * 此参数必填     */
    public function setPyDiplayName( $pyDiplayName) {
        $this->pyDiplayName = $pyDiplayName;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "areaId", $this->stdResult )) {
    				$this->areaId = $this->stdResult->{"areaId"};
    			}
    			    		    				    			    			if (array_key_exists ( "cnDiplayName", $this->stdResult )) {
    				$this->cnDiplayName = $this->stdResult->{"cnDiplayName"};
    			}
    			    		    				    			    			if (array_key_exists ( "pyDiplayName", $this->stdResult )) {
    				$this->pyDiplayName = $this->stdResult->{"pyDiplayName"};
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "areaId", $this->arrayResult )) {
    			$this->areaId = $arrayResult['areaId'];
    			}
    		    	    			    		    			if (array_key_exists ( "cnDiplayName", $this->arrayResult )) {
    			$this->cnDiplayName = $arrayResult['cnDiplayName'];
    			}
    		    	    			    		    			if (array_key_exists ( "pyDiplayName", $this->arrayResult )) {
    			$this->pyDiplayName = $arrayResult['pyDiplayName'];
    			}
    		    	    		}
 
   
}
?>