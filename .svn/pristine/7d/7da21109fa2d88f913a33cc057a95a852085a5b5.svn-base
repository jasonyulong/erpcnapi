<?php
/**
 * 
 * @copyright Copyright (c) 2018
 * @license   
 * @version   Beta 1.0
 * @author    mina
 * @date      2018-01-10
 */
namespace Transport\Service;

/**
 * @desc 物流商分拣代码处理
 * Class SortingCode
 * @package Transport\Service
 */
class SortingCode
{
	/**
	 * @desc  是否实例化
	 * @var   boolen
	 */
	private static $instance;

	/**
	 * @desc  订单信息
	 * @var   array
	 */
	private $_order;

	/**
	 * @desc  
	 * @author mina
	 * @param  
	 * @return
	 */
	private function __construct()
	{

	}

	/**
	 * @desc   静态实例化
	 * @author mina
	 * @param  void
	 * @return object
	 */
	public static function getInstance()
	{
		if(!self::$instance instanceof SortingCode)
        {
            self::$instance = new SortingCode();
        }
        return self::$instance;
	}

	/**
	 * @desc   查询分拣代码
	 * @author mina
	 * @param  array $data 渠道信息
	 * @return string
	 */
	public function getSortingCode($data)
	{
		$action = $data['carrier_mark_code'];
		if (is_callable(array($this, $action)))
		{
			$this->_order = $data;
			return $this->$action();
		}
		else
		{
			return $data['postnum'];
		}
	}

	/**
	 * @desc   	美国虚拟仓普货-LZ 分拣代码
	 * @author mina
	 * @param  
	 * @return
	 */
	public function zx_usps_lz()
	{
		$tag = substr($this->_order['ebay_tracknumber'], 0, 2);
		$code = [
			'ZH' => '1:NY',
			'ZD' => '1:NY',
			'LH' => '2:LA',
			'LD' => '2:LA',
			'TH' => '3:TX',
			'TD' => '3:TX',
			'FH' => '4:FL',
			'FD' => '4:FL',
			'CH' => '5:CH',
			'CD' => '5:CH',
		];
		if(array_key_exists($tag, $code))
		{
			return $code[$tag];
		}
		else
		{
			return false;
		}
	}
}