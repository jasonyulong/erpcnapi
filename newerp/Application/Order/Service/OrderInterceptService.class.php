<?php
namespace Order\Service;

use Api\Model\OrderInterceptRecordModel;
use Mid\Model\MidEbayOrderModel;
use Order\Model\EbayOrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\GoodsSaleDetailModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Think\Exception;

/**
 * 处理拦截
 * 1、删除erp_order_type中的数据
 * 2、删除erp_goods_sale_detail订单相关的数据
 * 3、将订单转到回收站
 * 4、删除api_checksku验货数据
 * @author Simon 2017/11/22
 */
class OrderInterceptService
{
    public function __construct() {
        $this->interceptModel       = new OrderInterceptRecordModel();
        $this->orderTypeModel       = new OrderTypeModel();
        $this->goodsSaleDetailModel = new GoodsSaleDetailModel();
        $this->ebayOrderModel       = new EbayOrderModel();
        $this->MidOrderModel       = new MidEbayOrderModel();
        $this->apiCheck             = new ApiCheckskuModel();
    }

    /**
     * 处理拦截
     * @author Simon 2017/11/22
     */
    public function controlIntercept($ebay_id) {
        try {
            $data = $this->interceptModel->where(['ebay_id' => $ebay_id, 'status' => 0])->find();
            if(empty($data)){
                throw new Exception('拦截已处理或不存在拦截信息');
            }
            $this->interceptModel->startTrans();
            $ret1 = $this->orderTypeModel->where(['ebay_id' => ['eq', $data['ebay_id']]])->delete();
            if(false === $ret1){
                throw new Exception('删除orderType 时出现错误');
            }
            $ret2 = $this->goodsSaleDetailModel->where(['ebay_id' => ['eq', $data['ebay_id']]])->delete();
            if(false === $ret2){
                throw new Exception('删除saleDetail 时出现错误');
            }
            $ret3 = $this->apiCheck->where(['ebay_id' => ['eq', $data['ebay_id']]])->delete();
            if(false === $ret3){
                throw new Exception('删除apiCheck 时出现错误');
            }
            $ret5 = $this->MidOrderModel->where(['ebay_id' => ['eq', $data['ebay_id']]])->limit(1)->delete();

            //TODO : 2018年11月28日10:32:15 leo 改成一次请求 直接在返回成功在erp进行处理
//            $result = $this->notifyErp([
//                'order_num_str' => $data['ebay_id'],
//                'to_status'     => $data['new_status'] ?: '',
//                'reason'        => $data['update_reason'],
//                'status'        => $data['son_status'],
//                'user'          => $data['add_user'],
//            ]);
//            if(!$result['status']){
//                throw new Exception($result['msg']);
//            }

            $this->interceptModel->where(['id' => $data['id']])->save([
                'note'        => '仓库处理成功',
                'status'      => 1,
                'is_to_erp'   => 1,
                'update_user' => '',
                'update_time' => date('Y-m-d H:i:s')
            ]);
            $this->interceptModel->commit();

            /**
            *测试人员谭 2018-08-07 15:48:15
            *说明: 干掉拣货单
            */
            $pickOrderDetail=new PickOrderDetailModel();
            $OrderslogModel=new OrderslogModel();
            $map=[];
            $map['ebay_id']=$ebay_id;
            $map['is_delete']=0;
            $map['is_baled']=0;
            $pickOrderDetail->where($map)->save(['is_delete'=>1]);
            $log='拦截自动处理成功!';
            $OrderslogModel->addordernote($ebay_id,$log);

            return ['status' => 1, 'msg' => '处理成功'];
        } catch (Exception $e) {
            $this->interceptModel->rollback();
            return ['status' => 0, 'msg' => $e->getMessage() ?: '处理失败'];
        }
    }

    public function notifyErp($postData) {
        $erpHost = C('ONLINE_PIC_URL');
        $url     = '/API/quickStopDelivery.php';
        $i       = 0;
        while ($i < 3) {
            $i++;
            $return = curl_post($erpHost . $url, $postData, 5);
            $return = json_decode($return, true);
            if (!empty($return)) {
                break;
            }
        }
        return $return ?: '';
    }
}