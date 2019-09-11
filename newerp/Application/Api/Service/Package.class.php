<?php
/**
 * 
 * @copyright Copyright (c) 2018
 * @license   
 * @version   Beta 1.0
 * @author    mina
 * @date      2018-03-21
 */
namespace Api\Service;

use Common\Model\OrderModel;
use Common\Model\CarrierModel;
use Order\Model\ApiBagsModel;
use Api\Model\OrderWeightModel;
use Api\Model\EbayCountriesModel;

/**
 * @desc  包裹数据服务
 * Class  Package
 * @package Api\Service
 */
class Package
{	
	/**
     * @desc   返回数据
     * @author mina
     * @param  int $status 0失败 1成功  
     * @param  string $msg 提示语
     * @param  array $data 数据
     * @return array
     */
    private function _back($status = 0, $msg = '', $data)
    {
        $return = ['status' => $status, 'msg' => $msg];
        if(!empty($data))
        {
            $return['data'] = $data;
        }
        return $return;
    }

	/**
	 * @desc   根据条件查询包裹信息
	 * @author mina
	 * @param  array $param 参数
	 * @return array
	 */
	public function getPackageOrder($param)
	{
		if($param['companyId'])
		{
			$carrierModel = new CarrierModel('', '', 'DB_CONFIG_READ');
			$where = "CompanyName='{$param['companyId']}'";
			$carrier = $carrierModel->where($where)->getField('name', true);
		}
		if(!empty($param['carrier']))
		{
			$carrier = [$param['carrier']];
		}
		$where = [
			'ebay_carrier' => ['in', $carrier],
			'b.delivery_status' => 1,
			'b.delivery_time' => ['between', [$param['stime'], $param['etime']]],
		];
		$field = "c.ebay_id, ebay_carrier, pxorderid, ebay_tracknumber, ebay_status, orderweight, a.weight, a.scantime,ebay_couny,ebay_countryname,ebay_username,ebay_state,ebay_city,ebay_street,ebay_postcode,ebay_usermail,ebay_phone,ebay_phone1";
		$limit = 20000;
		$model = new OrderWeightModel('', '', 'DB_CONFIG_READ');
		$list  = $model->alias('a')
					  ->join('INNER JOIN api_bags b ON a.bag_mark=b.mark_code')
					  ->join('INNER JOIN erp_ebay_order c ON a.ebay_id=c.ebay_id')
					  ->where($where)
					  ->field($field)
					  ->limit($limit)
					  ->select();
		if(empty($list))
		{
			return $this->_back(0, '记录为空。');
		}
		ini_set('memory_limit', '1024M');
		$countryCode = array_unique(array_column($list, 'ebay_couny'));
		$chinaCounty = $this->getChinaNameByCountrycode($countryCode);
		$xlsBody[] = [
			'跟踪号',
			'渠道',
			'发货日期',
			'收货国家中文名称',
			'收货国家英文名称',
			'买家姓名',
			'收货州省',
			'收货城市',
			'完整收货地址',
			'收货邮编',
			'买家电话',
			'买家手机',
			'总商品数量',
			'称重重量,单位:g',
			'完整英文报关名',
			'完整中文报关名',
			'总申报价值',
		];
		foreach ($list as $key => $value)
		{
			$xlsBody[] = [
				' ' . $value['ebay_tracknumber'],
				$value['ebay_carrier'],
				date('Y-m-d H:i:s', $value['scantime']),
				isset($chinaCounty[$value['ebay_couny']]) ? $chinaCounty[$value['ebay_couny']] : '',
				$value['ebay_countryname'],
				$value['ebay_username'],
				$value['ebay_state'],
				$value['ebay_city'],
				$value['ebay_street'],
				$value['ebay_postcode'],
				$value['ebay_phone'],
				$value['ebay_phone1'],
				'1',
				$value['weight'],
				'',
				'',
				'',
			];
		}
		$filePath = '/cache/xls/';
		$xlsPath = ROOT_PATH . $filePath;
		$xlsFileName = 'package-order.xls';
		if(!file_exists($xlsPath))
		{
			mkdir($xlsPath);
			chmod($xlsPath, 0777);
		}
		$file = export_excel($xlsFileName, '包裹订单列表', $xlsBody, 'file', $xlsPath);
		if($file)
		{
			$fileName = $filePath . $xlsFileName;
			$retData = [
				'file' => $fileName,
				'count' => count($list),
			];
			return $this->_back(1, '', $retData);
		}
		else
		{
			return $this->_back(1, '导出失败。');
		}
	}

	/**
	 * @desc   根据国家二字码查询中文国家名称
	 * @author mina
	 * @param  array $countryCode 二字码
	 * @return array
	 */
	public function getChinaNameByCountrycode($countryCode)
	{
		if(empty($countryCode)) return [];
		$model = new EbayCountriesModel('', '', 'DB_CONFIG_READ');
		$where = [
			'char_code' => ['in', $countryCode],
		];
		$list = $model->where($where)->getField('char_code, name', true);
		return $list;
	}
}