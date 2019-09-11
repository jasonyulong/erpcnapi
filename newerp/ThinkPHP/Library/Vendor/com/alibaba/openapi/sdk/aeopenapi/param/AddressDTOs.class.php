<?php

include_once ('com/alibaba/openapi/client/entity/SDKDomain.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AddressDTO.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AddressDTO.class.php');
include_once ('com/alibaba/openapi/sdk/aeopenapi/param/AddressDTO.class.php');

class AddressDTOs extends SDKDomain {

       	
    private $receiver;
    
        /**
    * @return 收货人信息
    */
        public function getReceiver() {
        return $this->receiver;
    }
    
    /**
     * 设置收货人信息     
     * @param AddressDTO $receiver     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setReceiver(AddressDTO $receiver) {
        $this->receiver = $receiver;
    }
    
        	
    private $sender;
    
        /**
    * @return 发货人信息
    */
        public function getSender() {
        return $this->sender;
    }
    
    /**
     * 设置发货人信息     
     * @param AddressDTO $sender     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setSender(AddressDTO $sender) {
        $this->sender = $sender;
    }
    
        	
    private $pickup;
    
        /**
    * @return 揽收人信息
    */
        public function getPickup() {
        return $this->pickup;
    }
    
    /**
     * 设置揽收人信息     
     * @param AddressDTO $pickup     
     * 参数示例：<pre></pre>     
     * 此参数必填     */
    public function setPickup(AddressDTO $pickup) {
        $this->pickup = $pickup;
    }
    
    	
	private $stdResult;
	
	public function setStdResult($stdResult) {
		$this->stdResult = $stdResult;
					    			    			if (array_key_exists ( "receiver", $this->stdResult )) {
    				$receiverResult=$this->stdResult->{"receiver"};
    				$this->receiver = new AddressDTO();
    				$this->receiver->setStdResult ( $receiverResult);
    			}
    			    		    				    			    			if (array_key_exists ( "sender", $this->stdResult )) {
    				$senderResult=$this->stdResult->{"sender"};
    				$this->sender = new AddressDTO();
    				$this->sender->setStdResult ( $senderResult);
    			}
    			    		    				    			    			if (array_key_exists ( "pickup", $this->stdResult )) {
    				$pickupResult=$this->stdResult->{"pickup"};
    				$this->pickup = new AddressDTO();
    				$this->pickup->setStdResult ( $pickupResult);
    			}
    			    		    		}
	
	private $arrayResult;
	public function setArrayResult($arrayResult) {
		$this->arrayResult = $arrayResult;
				    		    		if (array_key_exists ( "receiver", $this->arrayResult )) {
    		$receiverResult=$arrayResult['receiver'];
    			    			$this->receiver = new AddressDTO();
    			    			$this->receiver->$this->setStdResult ( $receiverResult);
    		}
    		    	    			    		    		if (array_key_exists ( "sender", $this->arrayResult )) {
    		$senderResult=$arrayResult['sender'];
    			    			$this->sender = new AddressDTO();
    			    			$this->sender->$this->setStdResult ( $senderResult);
    		}
    		    	    			    		    		if (array_key_exists ( "pickup", $this->arrayResult )) {
    		$pickupResult=$arrayResult['pickup'];
    			    			$this->pickup = new AddressDTO();
    			    			$this->pickup->$this->setStdResult ( $pickupResult);
    		}
    		    	    		}
 
   
}
?>