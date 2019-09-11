<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name PickerController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/13
 * @Time: 16:28
 * @Description
 */
namespace Mid\Controller;


use Mid\Service\PickerService;
use Think\Controller;

class PickerController extends Controller
{
    /**
     * 同步库位规则表（没有多少数据，一次性拉取所有）
     * @author Shawn
     * @date 2018/7/4
     */
    public function update(){
        $storeId = C('CURRENT_STORE_ID');
        $pickerService = new PickerService();
        $result = $pickerService->update($storeId);
        if($result){
            echo "更新成功";
        }else{
            echo "更新失败";
        }
        print_r($result);exit;
    }
}