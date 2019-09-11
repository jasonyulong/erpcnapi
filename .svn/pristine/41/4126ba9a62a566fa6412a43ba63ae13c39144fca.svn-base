<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class ApiUploadImage4SDKParam {

        
        /**
    * @return 上传文件名称，长度不要超过256个字符。
    */
        public function getFileName() {
        $tempResult = $this->sdkStdResult["fileName"];
        return $tempResult;
    }
    
    /**
     * 设置上传文件名称，长度不要超过256个字符。     
     * @param String $fileName     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setFileName( $fileName) {
        $this->sdkStdResult["fileName"] = $fileName;
    }
    
        
        /**
    * @return 图片文件的字节流
图片大小限制：3MB
    */
        public function getImageBytes() {
        $tempResult = $this->sdkStdResult["imageBytes"];
        return $tempResult;
    }
    
    /**
     * 设置图片文件的字节流
图片大小限制：3MB     
     * @param array include @see Byte[] $imageBytes     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setImageBytes( $imageBytes) {
        $this->sdkStdResult["imageBytes"] = $imageBytes;
    }
    
        
        /**
    * @return 图片保存的图片组，groupId为空，则图片保存在Other组中。
    */
        public function getGroupId() {
        $tempResult = $this->sdkStdResult["groupId"];
        return $tempResult;
    }
    
    /**
     * 设置图片保存的图片组，groupId为空，则图片保存在Other组中。     
     * @param String $groupId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setGroupId( $groupId) {
        $this->sdkStdResult["groupId"] = $groupId;
    }
    
        
    private $sdkStdResult=array();
    
    public function getSdkStdResult(){
    	return $this->sdkStdResult;
    }

}
?>