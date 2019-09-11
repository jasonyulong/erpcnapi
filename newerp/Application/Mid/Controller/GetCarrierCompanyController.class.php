<?php
 namespace Mid\Controller;
 use Think\Controller;
 /**order
  * @since 2018年8月3日16:16:31
  */
 class GetCarrierCompanyController extends Controller {
	 public function index(){
		 echo (11111);
	 }

	 /**
	  *  同步物流
	  */
 	public  function GetCarrierCompany(){
		$orderService = new \Mid\Service\GetCarrierCompanyService();
		$list = $orderService->getCarrierCompany();
		echo '<br/>End';
	}
 }