<?php

/**
 * 请求erp接口
 * @author Simon 2017/11/10
 */
class RequestErp
{
    private $_C_WH_ID = 'WH196';
    private $_CLI_KEY = '';                #Key
    private $_TOKEN = '';                #Token
    private $_postUrl = '';                #ERP api url
    //wms flag
    const WMS_FLAG_DEFAULT = 0;
    const WMS_FLAG_OK = 1;
    const WMS_ABNORMAL = 2;
//    const ENV = 'test';
    const ENV = 'product';

    public function __construct() {
        if('test' == self::ENV){
            $this->_CLI_KEY = 'D9510A26FA3057D23B1570247F7EB277';
            $this->_TOKEN   = '0AF0FAE0519E5B25540A27EC3C6FFA1E';
            $this->_postUrl = 'http://local.erp.com/t.php?s=/Apiwms/';
        }elseif('product' == self::ENV){
            $this->_CLI_KEY = 'D9510A26FA3057D23B1577897F7EB277';
            $this->_TOKEN   = '0AF0FAE0519E5B25456A27EC3C6FFA1E';
            $this->_postUrl = 'http://erp.spocoo.com/t.php?s=/Apiwms/';
        }

//        if ('development' == APP_ENV) {
//            $this->_CLI_KEY = C('CLI_KEY');
//            $this->_TOKEN 	= C('TOKEN');
//            $this->_postUrl = 'http://local.erp.com/t.php?s=/Apiwms/';
//        }elseif ('test' == APP_ENV) {
//            $this->_CLI_KEY = C('CLI_KEY');
//            $this->_TOKEN 	= C('TOKEN');
//            $this->_postUrl = 'http://192.168.1.53/t.php?s=/Apiwms/';
//        }elseif ('product' == APP_ENV) {
//            $this->_CLI_KEY = C('CLI_KEY');
//            $this->_TOKEN 	= C('TOKEN');
//            $this->_postUrl = 'http://terryzhang.vicp.io/t.php?s=/Apiwms/';
//        }
    }

    //demo
    //http://local.erp.com/t.php?s=/Apiwms/sku/getSkuList/WHID/WH196/timestamp/2017-10-16_15:10:59/token/0AF0FAE0519E5B25540A27EC3C6FFA1E/sign/DFEE
    /**
     * 得到 erp 数据
     * @param    $requestData    array    请求参数
     * @param    $action         string  方法
     * @return    bool or array
     * @author    Rex
     * @since     2017-10-25 09:23
     */
    protected function getErpData($requestData, $action) {
        if (empty($requestData) || empty($action)) {
            return false;
        }
        $requestData = $this->getRequestData($requestData);
        $ret         = $this->curlPost($requestData, $this->_postUrl . $action);
        var_dump($ret);
        $ret = json_decode($ret, true);
        return $ret;
    }

    /**
     * 组合 request data
     * @param    $requestData    array
     * @return    bool or array
     * @author    Rex
     * @since     2017-10-25 09:26
     */
    private function getRequestData($requestData) {
        $date        = date('Y-m-d H:i:s');
        $_initArr    = array(
            'WHID'      => $this->_C_WH_ID,
            'timestamp' => $date,
            'token'     => $this->_TOKEN,
            'sign'      => $this->getSign($date),
        );
        $requestData = array_merge($_initArr, $requestData);
        return $requestData;
    }

    /**
     * 设置 sing
     * @return    string
     * @author    Rex
     * @since     2017-10-25 11:04
     */
    private function getSign($timestamp) {
        $sign = md5($this->_C_WH_ID . $this->_CLI_KEY . $timestamp . $this->_TOKEN);
        return strtoupper($sign);
    }

    private function curlPost($params, $url) {
        if (empty($params) || empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}