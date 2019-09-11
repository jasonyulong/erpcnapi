<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name GoodsLocationService.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/7/4
 * @Time: 20:39
 * @Description
 */
namespace Mid\Service;
use Package\Model\EbayOnHandleModel;

class GoodsLocationService  extends BaseService{

    /**
     * 更新任务
     * @param $current_storeid
     * @param $begin
     * @param $end
     * @return array|bool
     * @author Shawn
     * @date 2018/7/4
     */
    public function update($current_storeid,$begin,$end){
        $request['limit'] = 0;
        $request['beginTime'] = $begin;
        $request['endTime'] = $end;
        $request['storeid'] = $current_storeid;
        $action ='Location/getGoodsLocationByApi';
        $list = $this->getErpData($request, $action);
        $goodsLocationModel = M("goods_location");
        $ids=[];
        if($list['status'] != 1 || empty($list['data'])){
            return false;
        }
        $data = $list['data'];
        foreach ($data as $item){
            //如果有的就更新
            $map['location'] = $item['location'];
            $map['storeid'] = $item['storeid'];
            $isAdd = $goodsLocationModel->where($map)->find();
            unset($item['id']);
            if($isAdd){
                $result = $goodsLocationModel->where($map)->save($item);
            }else{
                $result = $goodsLocationModel->add($item);
            }
            if($result){
                $ids[] = $result;
            }

        }
        return $ids;
    }
}