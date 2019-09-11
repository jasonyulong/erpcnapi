<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name LockController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/12/28
 * @Time: 10:14
 * @Description 盘点sku
 */

namespace Mid\Controller;


use Mid\Service\LockService;
use Think\Controller;

class LockController extends Controller
{
    /**
     * 同步盘点表（没有多少数据，一次性拉取所有）
     * @author Shawn
     * @date 2018/7/4
     * @desc php tcli.php Mid/Lock/getLockData
     */
    public function getLockData(){
        $storeId = C('CURRENT_STORE_ID');
        $lockService = new LockService();
        $result = $lockService->getLockData($storeId);
        if($result){
            echo "更新成功";
        }else{
            echo "更新失败";
        }
        print_r($result);exit;
    }
}