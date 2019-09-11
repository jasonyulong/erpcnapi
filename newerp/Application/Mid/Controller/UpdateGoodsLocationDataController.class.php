<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name UpdateGoodsDataController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/7/4
 * @Time: 19:27
 * @Description
 */
namespace Mid\Controller;


use Mid\Service\GoodsLocationService;
use Think\Controller;

class UpdateGoodsLocationDataController extends Controller
{
    /**
     * 更新数据
     * @author Shawn
     * @date 2018/7/4
     */
    public function update(){
        //从当前时间，往前推24小时更新
        $beginTime = time()-86400;
        $endTime = time();
        $storeid = C('CURRENT_STORE_ID');
        $goodsLocationService = new GoodsLocationService();
        $result = $goodsLocationService->update($storeid,$beginTime,$endTime);
        if($result){
            echo "更新成功";
        }else{
            echo "更新失败";
        }
        print_r($result);exit;
    }
}