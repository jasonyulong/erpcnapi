<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/30
 * Time: 15:36
 */
namespace Api\Controller;

use Api\Model\OrderWeightModel;
use Order\Service\OrderInterceptService;
use Package\Model\TopMenuModel;
use Transport\Model\OrderTypeModel;

class ClearLableController
{

    public function clearLable(){

        $webRoot      = dirname(dirname(THINK_PATH));

        $file = $webRoot . '/log/cancel/' . date('YmdH') . '.txt';

        $ebay_id=$_POST['ebay_id'];
        $uid=$_POST['uid'];

        if(!is_numeric($ebay_id)||$ebay_id<10000000){
            echo json_encode(['status'=>false,'msg'=>'订单清空仓库pdf缓存失败!订单号有误']);
            return;
        }

        if(empty($uid)){
            echo json_encode(['status'=>false,'msg'=>'订单清空仓库pdf缓存失败!请求人没识别到']);
            return;
        }

        $orderTypeModel = new OrderTypeModel();

        $save['have_lable']=0;
        $save['lable_path']='';

        $log="用户id {$uid} 通过erp的请求，清空了仓库pdf 缓存:{$ebay_id}  ".date('Y-m-d H:i:s')."\n\n";

        $rs=$orderTypeModel->where(compact('ebay_id'))->limit(1)->save($save);

        if(false!==$rs){
            writeFile($file,$log);
            echo json_encode(['status'=>true,'msg'=>'订单清空仓库pdf缓存成功!']);
            return;
        }

        echo json_encode(['status'=>false,'msg'=>'订单清空仓库pdf缓存失败!']);
    }
}