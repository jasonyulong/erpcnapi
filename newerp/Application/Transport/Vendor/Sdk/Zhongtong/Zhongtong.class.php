<?php
/**
 * 中通物流接口api
 * @license   
 * @copyright Copyright (c) 2018
 * @version   Beta 1.0
 * @author    mina
 * @date      2018-8-14
 */
namespace Sdk\Zhongtong;

use Sdk\CBase;
use Sdk\Main;

/**
 * @desc 中通物流接口api
 * Class Zhongtong.class.php
 * @package Sdk\Zhongtong
 */
class Zhongtong extends CBase
{
	/**
     * @desc 配置信息
     * @var string
     * @access public 
     */
	protected $_config = array();
	
	/**
	 * @desc 网址
	 * @var string
	 * @access public 
	 */
	protected $_url = array();

	/**
	 * @desc 订单前缀
	 * @var string
	 * @access public 
	 */
	private $tag = 'ZS';

	/**
	 * @desc 初始化
	 * @access public 
	 * @reutrn array
	 */
	public function __construct($config = array())
	{
	    $this->_config = Main::config('zhongtong.api');   
	    $this->_url = Main::config('zhongtong.url'); 
	    $this->_out = 'xml';
	}

	/**
	 * @desc   创建物流订单
	 * @access public
	 * @param  void
	 * @return array
	 */
	public function getTrackno($data)
	{
		$params = [
			'Content' => $this->_setCreateOrderData($data),
			'UserId'  => $this->_config['userId'],
			'UserPassword' => $this->_config['password'],
		];
		$params['Key'] = $this->_getSign($params['Content']);
		$header = $this->_getHeader();
		$params = http_build_query($params);
		$response = $this->post($this->_url['url'], $params, $header);
		if($_GET['debug'])
		{
			print_r($params);
			print_r($response);
		}
		if($response['responseitems']['response']['success'] == 'true')
		{
			$retData = [
				'trackNo' => $response['responseitems']['response']['jobNo'],
				'pxorderid' => $response['responseitems']['response']['jobNo'],
			];
			return $this->back(1, $response['message'], $retData);
		}
		else
		{
			$errMsg = $this->_getError($response);
			return $this->back(0, $errMsg);
		}
	}

	/**
	 * @desc   获取签名key
	 * @author mina
	 * @param  请求的content内容
	 * @return string
	 */
	private function _getSign($content)
	{
		return strtoupper(md5($content . $this->_config['key']));
	}

	/**
	 * @desc   http 请求头设置
	 * @author mina
	 * @param  void
	 * @return array
	 */
	private function _getHeader()
	{
		return ['Content-Type:application/x-www-form-urlencoded; charset=UTF-8'];
	}

	/**
	 * @desc   创建订单组合数据
	 * @author mina
	 * @param  
	 * @return
	 */
	private function _setCreateOrderData($data)
	{
		$skuName = $data['detail'][0]['sku'];
		$goods = $data['goods'][$skuName];
		if(isset($data['goods'][$skuName][0]))
		{
			$goods = $data['goods'][$skuName][0];
		}
		$nowTime = date('Y-m-d H:i:s');
		return "<?xml version=\"1.0\" encoding=\"utf-8\"?><logisticsEventsRequest><logisticsEvent><eventHeader><eventType>LOGISTICS_PACKAGE_SEND</eventType><eventTime>{$nowTime}</eventTime><eventMessageId>{$data['ebay_id']}</eventMessageId><eventSource>XXX</eventSource><eventTarget>COE</eventTarget></eventHeader><eventBody><orders><order><referenceID>{$data['ebay_id']}</referenceID><paymentType>PP</paymentType><pcs>1</pcs><destNo>{$data['ebay_couny']}</destNo><destName>{$data['ebay_city']}</destName><date>{$nowTime}</date><custNo>{$this->_config['userId']}</custNo><weight>{$data['orderweight']}</weight><hub>{$data['carrier']['stnames']}</hub><productType>普货</productType><cocustomType>其他</cocustomType><declaredValue>{$data['sb_money']}</declaredValue><declaredCurrency>USD</declaredCurrency><contents>{$goods['goods_ywsbmc']}</contents><isReturnLabel>0</isReturnLabel><remark>{$data['ebay_id']}</remark><sendContact><companyName>{$data['receive']['receive_from']}</companyName><personName>{$data['receive']['receive_from']}</personName><nameCn>{$data['receive']['receive_from']}</nameCn><phoneNumber>{$data['receive']['receive_phone']}</phoneNumber><countryCode>CN</countryCode><countryName>{$data['receive']['receive_province']}</countryName><city>{$data['receive']['receive_city']}</city><address1>{$data['receive']['receive_addres']}</address1></sendContact><receiverContact><companyName>{$data['ebay_username']}</companyName><personName>{$data['ebay_username']}</personName><nameCn>{$data['ebay_username']}</nameCn><phoneNumber>{$data['ebay_phone']}</phoneNumber><countryCode>{$data['ebay_couny']}</countryCode><countryName>{$data['ebay_countryname']}</countryName><divisioinCode>{$data['ebay_state']}</divisioinCode><city>{$data['ebay_city']}</city><address1>{$data['ebay_street']}</address1><address2>{$data['ebay_street1']}</address2><postalCode>{$data['ebay_postcode']}</postalCode></receiverContact><items><item><descrName>{$goods['goods_ywsbmc']}</descrName><itemID>{$goods['goods_sn']}</itemID><pcs>1</pcs><quantity>1</quantity><unitPrice>{$data['sb_money']}</unitPrice><totalPrice>{$data['sb_money']}</totalPrice><cur>USD</cur></item></items></order></orders></eventBody></logisticsEvent></logisticsEventsRequest>";
	}

	/**
	 * @desc   获取接口返回错误提示
	 * @author mina
	 * @param  array $response
	 * @return array
	 */
	private function _getError($response)
	{
		$errMsg = '';
		if(empty($response)) $errMsg .= "CURL请求服务器无响应。";
		$response = $response['responseitems']['response'];
        if($response['errorInfo'] != '')    $errMsg .= $response['errorInfo'];
        return $errMsg;
	}
}