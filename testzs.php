<?php
include "include/GetOrder.php";
$getOrder = new GetOrder();
$ebay_id  = '';
$data     = $getOrder->getOrderByEbayId('9636915');
var_dump($data);