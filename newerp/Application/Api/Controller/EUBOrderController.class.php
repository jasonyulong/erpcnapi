<?php
/**
*测试人员谭 2017-11-30 21:22:12
*说明: 垃圾 EUB 交运 老他妈不成功  去线上 get  pxorderid
*/
namespace Api\Controller;

use Api\Service\EUBService;
use Think\Controller;
class EUBOrderController extends  Controller{

   function updatePxorderid(){



       $EubService=new EUBService();

       $EubService->UpdateEubPxorderid();

   }
}