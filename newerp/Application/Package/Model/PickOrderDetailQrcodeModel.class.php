<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name PickOrderDetailQrcodeModel.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/9/5
 * @Time: 9:38
 * @Description 二维码扫描记录表
 */
namespace Package\Model;

use Think\Model;

class PickOrderDetailQrcodeModel extends Model
{

    protected $tableName = "pick_order_detail_qrcode";

    /**
     * 二次分拣添加二维码扫描记录
     * @param $data
     * @return mixed
     * @author Shawn
     * @date 2018/9/5
     */
    public function addScanQrcodeData($data){
        //找到这个拣货单的所有订单
        $pickOrderDetailModel = new PickOrderDetailModel();
        $pickMap['ordersn'] = $data['ordersn'];
        $pickMap['is_delete'] = 0;
        $ebayIds = $pickOrderDetailModel->where($pickMap)->getField("ebay_id",true);
        //删除存在于这个拣货单的订单，但又不是这个拣货单
        $delMap['ebay_id'] = array("in",$ebayIds);
        $delMap['ordersn'] = array("neq",$data['ordersn']);
        $delIdResult = $this->where($delMap)->delete();
        $map['qrcode'] = $data['qrcode'];
        $map['ebay_id']  = $data['ebay_id'];
        $find = $this->where($map)->find();
        if(!empty($find)){
            $delResult = $this->where($map)->delete();
        }
        $data['addtime'] = time();
        $result = $this->add($data);
        return $result;
    }

}