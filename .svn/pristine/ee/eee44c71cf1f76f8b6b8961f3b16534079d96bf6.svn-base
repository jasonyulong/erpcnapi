<?php
 namespace Mid\Controller;
 use Think\Controller;
 /**order
  * @since 2018 1 29
  */
 class GetCarrierController extends Controller {
	 public function index(){
		 echo (11111);
	 }

	 /**
	  *  同步物流
	  */
 	public  function GetCarrier(){
		$orderService = new \Mid\Service\GetCarrierService();
		$list = $orderService->getCarrier();
		echo '<br/>End';
	}
 }