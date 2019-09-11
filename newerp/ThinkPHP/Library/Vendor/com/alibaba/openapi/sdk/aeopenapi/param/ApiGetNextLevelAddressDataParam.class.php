<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class ApiGetNextLevelAddressDataParam {

        
        /**
    * @return 区域Id
    */
        public function getAreaId() {
        $tempResult = $this->sdkStdResult["areaId"];
        return $tempResult;
    }
    
    /**
     * 设置区域Id     
     * @param Long $areaId     
     * 参数示例：<pre>1001</pre>     
     * 此参数必填     */
    public function setAreaId( $areaId) {
        $this->sdkStdResult["areaId"] = $areaId;
    }
    
        
    private $sdkStdResult=array();
    
    public function getSdkStdResult(){
    	return $this->sdkStdResult;
    }

}
?>