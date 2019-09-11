<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/24
 * Time: 17:39
 */
namespace Common\Model;
use Think\Exception;
use Think\Model;

/**
 * 所有账号销量模型
 * Class GoodsSaleDetailModel
 * @package Common\Model
 */
class GoodsSaleDetailModel extends Model
{
    protected $tableName = 'erp_goods_sale_detail';

    private $currentStoreid=196;

    private $internal_store=[196,234];

    /**
     * @desc 初始化
     * @author Shawn
     * @date 2019/4/18
     */
    public function _initialize()
    {
        $currStoreId    = C("CURRENT_STORE_ID");
        $StoreArr       = C("STORE_NAMES");
        if(!empty($currStoreId)){
            $this->currentStoreid = $currStoreId;
        }
        if(!empty($StoreArr)){
            $this->internal_store = array_keys($StoreArr);
        }
    }
    /**
    *测试人员谭 2017-12-06 22:36:32
    *说明: 这里加一个 非常神奇的方法
     * TODO: 就是插入数据！ 注意这里的插入数据和以往的 不一样！
     *      因为一个同一个订单仓库 还有可能出现不同的 仓库sku
    */
    function addSaleDetail($skus){
        try{
            $this->addAll($skus);
        }catch (Exception $e){
            echo 'addSaleDetail Error'."\n\n";
        }
    }

    // 判断订单仓库 有没有跨仓库
    /**
     * @param $skus
     * @param $storeid
     * $doAdd 是不是要添加
     * 返回带有多个仓库的 订单sku 最小粒子
     */

    /*
     0=> [
        'sku'      => $sku,
        'storeid' => 111,
        'ebay_id'  => 111,
        'qty'      => 12,
        'paytime'  => 15666666,
        'addtime'  => 15666666,
        'erp_addtime' => 156121212,
        'account'  => 'aaa',
    ]
    1=>[]
    */
    function haveMoreStoreid(&$skus,$storeid,$doAdd,InternalStoreSkuModel $InternalStoreSkuModel){

        if(count($skus)<=1){
            if($doAdd==true){
                $this->addSaleDetail($skus);
            }
            return false;
        }

        // 如果不是 国内仓 多仓 相关，就不要 给每个sku 分配仓库
        if(!in_array($storeid,$this->internal_store)){
            if($doAdd==true){
                $this->addSaleDetail($skus);
            }
            return false;
        }

        /**
        *测试人员谭 2017-12-06 22:46:26
        *说明: 多品多货就是这一波！！！
        */

        foreach($skus as $k=>$list){
            $oldStoreid=$list['storeid'];
            $storeid=$InternalStoreSkuModel->getOrderSkuStore($list['sku'],$oldStoreid);
            $skus[$k]['storeid']=$storeid;
        }

        if($doAdd==true){
            $this->addSaleDetail($skus);
        }

        return null;
    }

    function SearchOrders() {
        $InternalStoreSkuModel=new InternalStoreSkuModel();
        $configModel = new ConfigsModel();
        $configArr   = $configModel->find();
        // 参与拣货单的状态
        $pick_order_status = trim($configArr['pick_order_status'], ',');
        $pick_order_status = explode(',', $pick_order_status);
        echo 'check order from ' . "<br>\n";
        print_r($pick_order_status);
        print_r("\n\n");
        $orderStatus        = $pick_order_status;
        $Order              = new OrderModel();
        $map['ebay_status'] = ['in', $orderStatus];

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
        $RR = $Order->alias('a')->join($this->getTableName() . ' b on a.ebay_id = b.ebay_id','left')
            ->where("a.ebay_status in(1723,1745,1733,1724,2009) and (b.sku='' or b.sku is null)")
            //->where("a.ebay_status in(1723,1745,1733,1724,2009) and (b.sku='' or b.sku is null) and a.ebay_id=10728961")
            ->field($fields)
            ->limit(1500)
            ->select();

        print_r($Order->_sql());
        print_r("\n\n");
        echo 'check order:' . count($RR) . "\n\n";

        echo 'Time:' . date('YmdHis') . "\n\n";

        foreach ($RR as $List) {
            $ebay_id = $List['ebay_id'];
            $paidtime       = $List['ebay_paidtime'];
            $ebay_warehouse = $List['ebay_warehouse'];
            $createdtime    = $List['ebay_createdtime'];
            $ebay_addtime   = $List['ebay_addtime'];
            $account        = $List['ebay_account'];
            $ebay_ordersn   = $List['ebay_ordersn'];
            $addtime        = time();

            if($ebay_warehouse!=$this->currentStoreid){
                continue;
            }

            $rr = $this->where(compact('ebay_id'))
                ->field('id')->find();
            if ($rr) { // 分解过了-----
                /**
                 *测试人员谭 2017-11-02 14:23:33
                 *说明: 这里分解过了 就不再分解，唯一要做的事情就是 在订单WMS 中 取消，或者异常什么的时候一定要
                 * 删除 goods_sale_detail 记录
                 */
                continue;
            }


            if ($ebay_addtime == 0) {
                $ebay_addtime = $createdtime;
            }
            //dump($List);
            $arrResult = $Order->OrderResolve('', $ebay_ordersn);
            //dump($arrResult);
            $arr=[];
            foreach ($arrResult as $k => $v) {
                $add=[];
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
                //@$this->add($add);// 唯一索引
                $arr[]=$add;
            }

            $this->haveMoreStoreid($arr,$ebay_warehouse,true,$InternalStoreSkuModel);
        }
        echo 'Time:' . date('YmdHis') . "\n\n";
    }


    /**
     * 获取订单类型
     * @author Simon 2017/11/2
     * 1= 单品单货
     * 2= 单品多货
     * 3= 多品多货
     */
    public function getOrderType($ebay_id) {
        $map['ebay_id'] = $ebay_id;
        $field          = "qty,sku";
        $items          = $this->where($map)->field($field)->limit(2)->select();
        if (count($items) == 0) {
            return [false, false];
        }

        $items = !empty($items) ? $items : array();

        if (count($items) > 1) {
            return [3, ''];
        }
        if ($items[0]['qty'] > 1) {
            return [2, $items[0]['sku']];
        }
        return [1, $items[0]['sku']];
    }


    public function isTlocationOrder($ebay_id){
        $map['a.ebay_id'] = $ebay_id;
        $field          = "a.qty,a.sku,a.storeid,b.g_location";
        $items          = $this->alias('a')->join('left join ebay_onhandle_196 b on a.sku=b.goods_sn')
            ->where($map)->field($field)->select();

        $isIncludeTlocation=false;

        //print_r($items);
        foreach($items as $List){
            if($List['storeid']== C("MERGE_STORE_ID")){  // 混合仓库的时候 还是要让生成捡货单子
                return 0;
            }

            $location=strtoupper(trim($List['g_location']));

            if($location!='' && substr($location,0,1)=='T'){

                //print_r(substr($location,0,1));

                $isIncludeTlocation=true;
            }
        }

//        if($isIncludeTlocation){
//            return 6;// 6floor
//        }

        return 0;
    }

}