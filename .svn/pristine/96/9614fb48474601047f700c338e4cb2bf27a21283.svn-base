<?php
namespace Task\Model;

use Common\Model\ConfigsModel;
use Common\Model\OrderModel;
use Think\Model;

class GoodsSaleDetailModel extends Model
{
    protected $tableName = 'erp_goods_sale_detail';

    /**
     *测试人员谭 2017-11-02 14:42:37
     *说明: 分解仓库里面的订单 为最小因子
     * TODO: 这个方法 务必要在 订单 类型（单品单货，单品多活，多屏，楼层） 之前调用！
     */
    function SearchOrders() {
        echo '程序已经转移';
        return true;
        set_time_limit(800);
        echo 'Start:------------------------';
        ini_set('memory_limit', '200M');
        $configModel = new ConfigsModel();
        $configArr   = $configModel->find();
        // 参与拣货单的状态
        $pick_order_status = trim($configArr['pick_order_status'], ',');
        $pick_order_status = explode(',', $pick_order_status);
        echo 'check order from ' . "<br>\n";
        print_r($pick_order_status);
        $orderStatus        = $pick_order_status;
        $Order              = new OrderModel();
        $map['ebay_status'] = ['in', $orderStatus];
        $field              = 'ebay_id,ebay_paidtime,ebay_addtime,ebay_createdtime,';
        $field .= 'ebay_ordersn,ebay_warehouse,ebay_account';
        $fields= [
            'a.ebay_id'          => 'ebay_id',
            'a.ebay_paidtime'    => 'ebay_paidtime',
            'a.ebay_addtime'     => 'ebay_addtime',
            'a.ebay_createdtime' => 'ebay_createdtime',
            'a.ebay_ordersn'     => 'ebay_ordersn',
            'a.ebay_warehouse'   => 'ebay_warehouse',
            'a.ebay_account'     => 'ebay_account'
        ];
//        $RR    = $Order->where($map)
//            ->field($field)
//            ->select();
//        dump($Order->_sql());
        $RR = $Order
            ->table($Order->getTableName() . ' a')
            ->join($this->getTableName() . ' b on a.ebay_id = b.ebay_id','left')
            ->where("a.ebay_status in(1723,1745,1733,1724,2009) and (b.sku='' or b.sku is null)")
            ->field($fields)
            ->limit(1000)
            ->select();
        dump($Order->_sql());
        echo 'check order:' . count($RR) . "\n\n";
        echo 'Time:' . date('YmdHis') . "\n\n";
        foreach ($RR as $List) {
            $ebay_id = $List['ebay_id'];
            $rr      = $this->where(compact('ebay_id'))->field('id')
                ->find();
            if ($rr) { // 分解过了-----
                /**
                 *测试人员谭 2017-11-02 14:23:33
                 *说明: 这里分解过了 就不再分解，唯一要做的事情就是 在订单WMS 中 取消，或者异常什么的时候一定要
                 * 删除 goods_sale_detail 记录
                 */
                continue;
            }
            $paidtime       = $List['ebay_paidtime'];
            $ebay_warehouse = $List['ebay_warehouse'];
            $createdtime    = $List['ebay_createdtime'];
            $ebay_addtime   = $List['ebay_addtime'];
            $account        = $List['ebay_account'];
            $ebay_ordersn   = $List['ebay_ordersn'];
            $addtime        = time();
            if ($ebay_addtime == 0) {
                $ebay_addtime = $createdtime;
            }
            //dump($List);
            $arrResult = $Order->OrderResolve('', $ebay_ordersn);
            //dump($arrResult);
            foreach ($arrResult as $k => $v) {
                $sku                = $k;
                $c                  = $v[0];
                $add['sku']         = $sku;
                $add['storeid']     = $ebay_warehouse;
                $add['ebay_id']     = $ebay_id;
                $add['qty']         = $c;
                $add['paytime']     = $paidtime;
                $add['addtime']     = $addtime;
                $add['erp_addtime'] = $ebay_addtime;
                $add['account']     = $account;
                @$this->add($add);// 唯一索引
            }
        }
        echo 'Time:' . date('YmdHis') . "\n\n";
    }

    // 删除 Saledetail------------
    function deleteSaleDetail($ebay_id) {
        $this->where(compact('ebay_id'))->delete();
    }
}