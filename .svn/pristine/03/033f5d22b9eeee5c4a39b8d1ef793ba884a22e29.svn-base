<?php
class eBaySession{
    private $requestToken;
    private $devID;
    private $appID;
    private $certID;
    private $serverUrl;
    private $compatLevel;
    private $siteID;
    private $verb;
    public function __construct($userRequestToken,$developerID,$applicationID,$certificateID,$serverUrl,$compatabilityLevel,$siteToUseID,$callName) {
        $this->requestToken = $userRequestToken;
        $this->devID = $developerID;
        $this->appID = $applicationID;
        $this->certID = $certificateID;
        $this->compatLevel = $compatabilityLevel;
        $this->siteID = $siteToUseID;
        $this->verb = $callName;
        $this->serverUrl = $serverUrl;
    }
    public function sendHttpRequest($requestBody){
        $headers = $this->buildEbayHeaders();
        $connection = curl_init();
        curl_setopt($connection,CURLOPT_URL,$this->serverUrl);
        curl_setopt($connection,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($connection,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($connection,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($connection,CURLOPT_POST,1);
        curl_setopt($connection,CURLOPT_POSTFIELDS,$requestBody);
        curl_setopt($connection,CURLOPT_RETURNTRANSFER,1);

        //curl_setopt($connection, CURLOPT_PROXY, "52.68.77.226"); //代理服务器地址
        //curl_setopt($connection, CURLOPT_PROXY, "54.68.161.171"); //代理服务器地址
/*        curl_setopt($connection, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        curl_setopt($connection, CURLOPT_PROXY, "ss5.xiaomo.cn"); //代理服务器地址
        curl_setopt($connection, CURLOPT_PROXYUSERPWD, "eric:123321"); //用户名密码
        curl_setopt($connection, CURLOPT_PROXYPORT, 10888); //代理服务器端口
        curl_setopt($connection, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); //使用http代理模式*/


        $response = curl_exec($connection);
        curl_close($connection);
        return $response;
    }
    private function buildEbayHeaders(){
        $headers = array (
            'X-EBAY-API-COMPATIBILITY-LEVEL: '.$this->compatLevel,
            'X-EBAY-API-DEV-NAME: '.$this->devID,
            'X-EBAY-API-APP-NAME: '.$this->appID,
            'X-EBAY-API-CERT-NAME: '.$this->certID,
            'X-EBAY-API-CALL-NAME: '.$this->verb,
            'X-EBAY-API-SITEID: '.$this->siteID,
        );
        //print_r($headers);
        return $headers;
    }
}