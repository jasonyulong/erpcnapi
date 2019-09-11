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

class GoodsShelvesService  extends BaseService{

    /**
     * 开始更新数据
     * @param $current_storeid
     * @return array|bool
     * @author Shawn
     * @date 2018/7/4
     */
    public function update($current_storeid){
        $action ='GoodsShelves/getGoodsShelvesByApi';
        $list = $this->getErpData(['limit'=>0,'storeid'=>$current_storeid], $action);
        $goodsLocationModel = M("goods_shelves");
        $ids = [];
        if($list['status'] != 1 || empty($list['data'])){
            return false;
        }
        $data = $list['data'];
        foreach ($data as $item){
            $map['storeid'] = $item['storeid'];
            $map['number'] = $item['number'];
            $map['picker'] = $item['picker'];
            unset($item['id']);
            $isAdd = $goodsLocationModel->where($map)->find();
            if(!empty($isAdd)){
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