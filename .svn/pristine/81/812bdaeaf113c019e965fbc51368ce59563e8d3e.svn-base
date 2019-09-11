<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class ApiUploadTempImage4SDKParam {

        
        /**
    * @return 图片原名
    */
        public function getSrcFileName() {
        $tempResult = $this->sdkStdResult["srcFileName"];
        return $tempResult;
    }
    
    /**
     * 设置图片原名     
     * @param String $srcFileName     
     * 参数示例：<pre>1.jpg</pre>     
     * 此参数必填     */
    public function setSrcFileName( $srcFileName) {
        $this->sdkStdResult["srcFileName"] = $srcFileName;
    }
    
        
        /**
    * @return 字符串形式的图片文件二进制数据流
    */
        public function getFileData() {
        $tempResult = $this->sdkStdResult["fileData"];
        return $tempResult;
    }
    
    /**
     * 设置字符串形式的图片文件二进制数据流     
     * @param array include @see Byte[] $fileData     
     * 参数示例：<pre>aff3fadfafd3fdd00123</pre>     
     * 此参数必填     */
    public function setFileData( $fileData) {
        $this->sdkStdResult["fileData"] = $fileData;
    }
    
        
    private $sdkStdResult=array();
    
    public function getSdkStdResult(){
    	return $this->sdkStdResult;
    }

}
?>