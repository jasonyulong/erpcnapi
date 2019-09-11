<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');

class AddressDTO extends SDKDomain {

       	
    private $city;
    
        /**
    * @return 城市
    */
        public function getCity() {
        return $this->city;
    }
    
    /**
     * 设置城市     
     * @param String $city     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setCity( $city) {
        $this->city = $city;
    }
    
        	
    private $country;
    
        /**
    * @return 国家
    */
        public function getCountry() {
        return $this->country;
    }
    
    /**
     * 设置国家     
     * @param String $country     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setCountry( $country) {
        $this->country = $country;
    }
    
        	
    private $email;
    
        /**
    * @return 邮箱
    */
        public function getEmail() {
        return $this->email;
    }
    
    /**
     * 设置邮箱     
     * @param String $email     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setEmail( $email) {
        $this->email = $email;
    }
    
        	
    private $fax;
    
        /**
    * @return 传真
    */
        public function getFax() {
        return $this->fax;
    }
    
    /**
     * 设置传真     
     * @param String $fax     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setFax( $fax) {
        $this->fax = $fax;
    }
    
        	
    private $memberType;
    
        /**
    * @return 类型
    */
        public function getMemberType() {
        return $this->memberType;
    }
    
    /**
     * 设置类型     
     * @param String $memberType     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setMemberType( $memberType) {
        $this->memberType = $memberType;
    }
    
        	
    private $mobile;
    
        /**
    * @return 电话
    */
        public function getMobile() {
        return $this->mobile;
    }
    
    /**
     * 设置电话     
     * @param String $mobile     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setMobile( $mobile) {
        $this->mobile = $mobile;
    }
    
        	
    private $name;
    
        /**
    * @return 姓名
    */
        public function getName() {
        return $this->name;
    }
    
    /**
     * 设置姓名     
     * @param String $name     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setName( $name) {
        $this->name = $name;
    }
    
        	
    private $phone;
    
        /**
    * @return 电话
    */
        public function getPhone() {
        return $this->phone;
    }
    
    /**
     * 设置电话     
     * @param String $phone     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setPhone( $phone) {
        $this->phone = $phone;
    }
    
        	
    private $postcode;
    
        /**
    * @return 邮编
    */
        public function getPostcode() {
        return $this->postcode;
    }
    
    /**
     * 设置邮编     
     * @param String $postcode     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setPostcode( $postcode) {
        $this->postcode = $postcode;
    }
    
        	
    private $province;
    
        /**
    * @return 省份
    */
        public function getProvince() {
        return $this->province;
    }
    
    /**
     * 设置省份     
     * @param String $province     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setProvince( $province) {
        $this->province = $province;
    }
    
        	
    private $streetAddress;
    
        /**
    * @return 详细地址
    */
        public function getStreetAddress() {
        return $this->streetAddress;
    }
    
    /**
     * 设置详细地址     
     * @param String $streetAddress     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setStreetAddress( $streetAddress) {
        $this->streetAddress = $streetAddress;
    }
    
        	
    private $trademanageId;
    
        /**
    * @return 旺旺
    */
        public function getTrademanageId() {
        return $this->trademanageId;
    }
    
    /**
     * 设置旺旺     
     * @param String $trademanageId     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setTrademanageId( $trademanageId) {
        $this->trademanageId = $trademanageId;
    }
    
        	
    private $county;
    
        /**
    * @return 区
    */
        public function getCounty() {
        return $this->county;
    }
    
    /**
     * 设置区     
     * @param String $county     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setCounty( $county) {
        $this->county = $county;
    }
    
        	
    private $street;
    
        /**
    * @return 街道
    */
        public function getStreet() {
        return $this->street;
    }
    
    /**
     * 设置街道     
     * @param String $street     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setStreet( $street) {
        $this->street = $street;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "city", $this->stdResult )) {
    				$this->city = $this->stdResult->{"city"};
    			}
    			    		    				    			    			if (array_key_exists ( "country", $this->stdResult )) {
    				$this->country = $this->stdResult->{"country"};
    			}
    			    		    				    			    			if (array_key_exists ( "email", $this->stdResult )) {
    				$this->email = $this->stdResult->{"email"};
    			}
    			    		    				    			    			if (array_key_exists ( "fax", $this->stdResult )) {
    				$this->fax = $this->stdResult->{"fax"};
    			}
    			    		    				    			    			if (array_key_exists ( "memberType", $this->stdResult )) {
    				$this->memberType = $this->stdResult->{"memberType"};
    			}
    			    		    				    			    			if (array_key_exists ( "mobile", $this->stdResult )) {
    				$this->mobile = $this->stdResult->{"mobile"};
    			}
    			    		    				    			    			if (array_key_exists ( "name", $this->stdResult )) {
    				$this->name = $this->stdResult->{"name"};
    			}
    			    		    				    			    			if (array_key_exists ( "phone", $this->stdResult )) {
    				$this->phone = $this->stdResult->{"phone"};
    			}
    			    		    				    			    			if (array_key_exists ( "postcode", $this->stdResult )) {
    				$this->postcode = $this->stdResult->{"postcode"};
    			}
    			    		    				    			    			if (array_key_exists ( "province", $this->stdResult )) {
    				$this->province = $this->stdResult->{"province"};
    			}
    			    		    				    			    			if (array_key_exists ( "streetAddress", $this->stdResult )) {
    				$this->streetAddress = $this->stdResult->{"streetAddress"};
    			}
    			    		    				    			    			if (array_key_exists ( "trademanageId", $this->stdResult )) {
    				$this->trademanageId = $this->stdResult->{"trademanageId"};
    			}
    			    		    				    			    			if (array_key_exists ( "county", $this->stdResult )) {
    				$this->county = $this->stdResult->{"county"};
    			}
    			    		    				    			    			if (array_key_exists ( "street", $this->stdResult )) {
    				$this->street = $this->stdResult->{"street"};
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    			if (array_key_exists ( "city", $this->arrayResult )) {
    			$this->city = $arrayResult['city'];
    			}
    		    	    			    		    			if (array_key_exists ( "country", $this->arrayResult )) {
    			$this->country = $arrayResult['country'];
    			}
    		    	    			    		    			if (array_key_exists ( "email", $this->arrayResult )) {
    			$this->email = $arrayResult['email'];
    			}
    		    	    			    		    			if (array_key_exists ( "fax", $this->arrayResult )) {
    			$this->fax = $arrayResult['fax'];
    			}
    		    	    			    		    			if (array_key_exists ( "memberType", $this->arrayResult )) {
    			$this->memberType = $arrayResult['memberType'];
    			}
    		    	    			    		    			if (array_key_exists ( "mobile", $this->arrayResult )) {
    			$this->mobile = $arrayResult['mobile'];
    			}
    		    	    			    		    			if (array_key_exists ( "name", $this->arrayResult )) {
    			$this->name = $arrayResult['name'];
    			}
    		    	    			    		    			if (array_key_exists ( "phone", $this->arrayResult )) {
    			$this->phone = $arrayResult['phone'];
    			}
    		    	    			    		    			if (array_key_exists ( "postcode", $this->arrayResult )) {
    			$this->postcode = $arrayResult['postcode'];
    			}
    		    	    			    		    			if (array_key_exists ( "province", $this->arrayResult )) {
    			$this->province = $arrayResult['province'];
    			}
    		    	    			    		    			if (array_key_exists ( "streetAddress", $this->arrayResult )) {
    			$this->streetAddress = $arrayResult['streetAddress'];
    			}
    		    	    			    		    			if (array_key_exists ( "trademanageId", $this->arrayResult )) {
    			$this->trademanageId = $arrayResult['trademanageId'];
    			}
    		    	    			    		    			if (array_key_exists ( "county", $this->arrayResult )) {
    			$this->county = $arrayResult['county'];
    			}
    		    	    			    		    			if (array_key_exists ( "street", $this->arrayResult )) {
    			$this->street = $arrayResult['street'];
    			}
    		    	    		}
 
   
}
?>