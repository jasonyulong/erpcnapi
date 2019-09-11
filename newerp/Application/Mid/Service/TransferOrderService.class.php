<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 10:41
 */
namespace Mid\Service;

class TransferOrderService extends BaseService
{
    /**
     * 创建调拨单
     * @author Simon 2017/12/11
     */
    public function createTransferOrder($data) {
        $action = 'TransferOrder/createTransferOrder';
        return $this->getErpData([
            'wid'                 => $this->currentid,
            'transfer_order_info' => $data
        ], $action);
    }

    /**
     * 获取调拨单状态
     * @author Simon 2017/12/14
     */

    /**
    *测试人员谭 2017-12-14 18:41:31
    *说明:
     *
    $error=[
    0=>'没创建',
    1=>'草稿',
    2=>'调出仓库已出库',
    3=>'调入仓库已入库',
    100=>'检查调拨单网络失败'
    ];
     *
    */
    public function getTransferOrderStatus($pickOrderSn) {
        $action = 'TransferOrder/getTransferOrderStatus';
        $data   = $this->getErpData([
            'pick_ordersn' => $pickOrderSn
        ], $action);


        if ($data['status'] == 100) {
            return $data['io_status']+1;
        } else {
            return 100; // 请求失败
        }
    }
}