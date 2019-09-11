<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name UpdateGoodsShelvesDataController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/7/4
 * @Time: 20:46
 * @Description
 */
namespace Mid\Controller;


use Mid\Service\GoodsShelvesService;
use Think\Controller;

class UpdateGoodsShelvesDataController extends Controller
{
    /**
     * 更新数据
     * @author Shawn
     * @date 2018/7/4
     */
    public function update(){
        //一次性拉取所有数据
        $storeId = C('CURRENT_STORE_ID');
        $goodsShelvesService = new GoodsShelvesService();
        $result = $goodsShelvesService->update($storeId);
        if($result){
            echo "更新成功";
        }else{
            echo "更新失败";
        }
        print_r($result);exit;
    }
}