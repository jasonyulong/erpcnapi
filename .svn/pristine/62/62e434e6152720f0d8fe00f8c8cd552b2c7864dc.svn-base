<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 13:56
 */

namespace Package\Service;
use Common\Model\CurrentLocationLogModel;
use Common\Model\CurrentLocationModel;
use Common\Model\CurrentLocationOrderDetailModel;
use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;
use Task\Model\EbayHandleModel;
use Task\Model\GoodsSaleDetailModel;

/**
 *测试人员杨 2018-01-03 
 *说明: 分拣分区 本仓 服务
 */
class OrderCurrentStockListService
{
    private $current_storeid = 0;
    private $store_names = [];
    private $PickOrderModel = '';
    private $OrderTypeModel = '';
    private $PickOrderDetailModel = '';
    private $PickOrderConfirmModel = '';
    private $PickOrderDetailStrModel = '';
    private $InterNalModel = '';
    private $GoodsSaleDetail = '';

    private $CurrentLocation = '';
    private $CurrentLocationOrderDetail = '';
    private $CurrentLocationLog = '';

    //属性初始化
    function __construct() {
        $this->PickOrderConfirmModel   = new PickOrderConfirmModel();
        $this->PickOrderDetailModel    = new PickOrderDetailModel();
        $this->PickOrderModel          = new PickOrderModel();
        $this->PickOrderDetailStrModel = new PickOrderDetailSkustrModel();
        $this->CurrentLocation         = new CurrentLocationModel();
        $this->CurrentLocationOrderDetail = new CurrentLocationOrderDetailModel();
        $this->CurrentLocationLog      = new CurrentLocationLogModel();
        $this->InterNalModel           = new InternalStoreSkuModel();
        $this->GoodsSaleDetail         = new GoodsSaleDetailModel();
        $this->current_storeid         = C('CURRENT_STORE_ID');
        $this->store_names             = C('STORE_NAMES');
        $this->OrderTypeModel          = new OrderTypeModel(); //模型命名有差异、不能调用D方法实例化
    }

    /**
     * 说明: 显示看板
     * location 库位(不可修改)
     * ebay_id 订单号
     * status 1=空 2=等待配齐 3 等待打面单
     * @return array $RR
     */
    public function StockListView(){
        $RR = $this->CurrentLocation->field('location,ebay_id,status')
            ->order('location asc')->select();

        foreach($RR as $k=>$List){
            $location = $List['location'];
            $ebay_id  = $List['ebay_id'];
            $status   = $List['status'];
            if($status>1){
                $OrderInfo = $this->CurrentLocationOrderDetail
                    ->where(['location' => $location,'ebay_id' => $ebay_id])
                    ->field('sku,qty,real_qty,status,addtime,storeid')
                    ->select();
            }else{
                $OrderInfo = [];
            }
            $RR[$k]['order'] = $OrderInfo;
        }

        return $RR;
    }

    /**
     * 测试人员杨 2017-01-03
     * 占用库位要干的主要事情
     * 1. error_location 数据表 里面的 ebay_id status  都被写入！！！！
     * 2. 初始化 订单的子信息 到 error_location_detail 表中去 初始化的时候 有两个 sku 数量,一个 是订单数量,一个是
     */
    function DoUseLocation($location,$ebay_id){
        $map = [];
        $map['location'] = $location;
        $RR = $this->CurrentLocation->where($map)->find();

        if($RR['status'] != 1){
            return ['status'=>0,'msg'=>$location.'--当前不是空库位请刷新看板!'];
        }

        if(empty($RR)){
            return ['status'=>0,'msg'=>$location.'--系统不存在该异常库位!'];
        }

        $id=$RR['id'];

        // 订单是不是 已经在异常中了
        $ExsitLocation = $this->CurrentLocation->where(['ebay_id'=>$ebay_id])->find();

        if($ExsitLocation){
            $location = $ExsitLocation['location'];
            return ['status'=>0,'msg'=>'订单已经在跨仓异常库位【'.$location.'】中了！'];
        }


        //订单状态
        $OrderModel = new OrderModel();
        $Order = $OrderModel->where(['ebay_id'=>$ebay_id])
            ->field('ebay_status,ebay_id')->limit(1)->find();

        $OrderMenu = new TopMenuModel();
        $TopMenu   = $OrderMenu->getMenuName();
        $ebay_status = $Order['ebay_status'];
        if(!in_array($ebay_status,[1723,1724,1724])){
            return ['status'=>0,'msg'=>'订单：'.$ebay_id.' 当前状态 【'.$TopMenu[$ebay_status].'】不允许发货了!'];
        }


        //订单是不是本仓

        $TypeRs = $this->OrderTypeModel->where(['ebay_id'=>$ebay_id])->field('is_cross,type')->find();

        $is_cross = $TypeRs['is_cross']; //是否跨仓
        $type = $TypeRs['type']; //订单类型

        /**
         *说明: 注意这里 如果这里是 本仓多品的 异常库位看板程序 验证的应该是
         * $type>1 && $is_cross==1
         * $type==1  单品单货 没必要加入异常
         * $is_cross=1 跨仓单 请到跨仓单
         */
        if($is_cross == 1){
            return ['status'=>0,'msg'=>'订单'.$ebay_id.'是跨仓单!不能放在本仓异常库位中'];
        }
        if($type == 1){
            return ['status'=>0,'msg'=>'订单'.$ebay_id.'是单品单货!不能放在本仓异常库位中'];
        }


        //订单分解 是否异常
        $Goods = $this->GoodsSaleDetail->where(['ebay_id'=>$ebay_id])
            ->field('qty,sku,storeid,ebay_id')->select();

        if(empty($Goods)){
            return ['status'=>0,'msg'=>'订单分解缓存找不到订单sku数据'];
        }

        $this->CurrentLocation->where(['id'=>$id])->save(['ebay_id'=>$ebay_id,'status'=>2]);

        foreach($Goods as $k=>$list){
            $Goods[$k]['location']=$location;
        }

        $RR = $this->CurrentLocationOrderDetail->addAll($Goods);

        if(!$RR){
            $file = dirname(dirname(THINK_PATH)).'/log/assignLocation/'.date('YmdH').'.txt';
            $log = $_SESSION['truename'].'---根据订单号获取空库位初始化库位失败!'.print_r($Goods,true);
            $log .= '---'.date('Ymd H:i:s')."\n\n";
            writeFile($file,$log); //TODO===========================
            return ['status'=>0,'msg'=>'占用库位发生了异常！'.$location.','.$ebay_id];
        }

        $log='手动占用异常库位:'.$location;
        $this->CurrentLocationLog->addOneNote($location,$log,'',$ebay_id);

        return ['status'=>1,'msg'=>'占用库位并初始化成功','data'=>$location];

    }

    /**
     * 说明:查询看板中所有的异常订单还需要集多少
     * @param $storeid
     * @return bool|mixed
     */
    function getOrderInfo($storeid){
        $CurrentLocationModel = new CurrentLocationModel();
        $orderIds = $CurrentLocationModel->where(['status' => 2])->getField('ebay_id', true);

        if (empty($orderIds)) {
            return false;
        }

        $where = [
            'storeid' => $storeid,
            'ebay_id' => ['in', $orderIds],
            'status'  => 1,
            '_string' => 'qty>real_qty'
        ];

        $CurrentLocationOrderDetailModel = new CurrentLocationOrderDetailModel();
        $orderInfo = $CurrentLocationOrderDetailModel //->where($where)->select();
        ->table('current_location_order_detail er')
            ->field('er.ebay_id,er.location,er.sku,SUM(er.qty) AS qty,er.storeid,SUM(er.real_qty) AS real_qty, eo.g_location')
            ->join('left join ebay_onhandle_'.$storeid.' eo on er.sku = eo.goods_sn')
            ->where($where)
            ->group('er.sku')
            ->select();

        if (empty($orderIds)) {
            return false;
        }

        return $orderInfo;
    }

    /**
     *测试人员杨 2017-01-03
     *说明: 扫描sku with Ordersn
     */
    function ScanOneSKUWithOrdersn($ordersn,$sku){
        //第一步先在 current_location_order_detail 找找
        //TODO 这里有一个性能问题:
        //以后 current_location_order_detail 会越来越大这时候应该先查一下一共有多少单在等待集货

        $Orders = $this->CurrentLocation->where(['status'=>2])->getField('ebay_id',true);

        if(!empty($Orders)){
            $map['ebay_id']=['in',$Orders];
            $map['status']=1; // 1=等待集货 2=放弃集货 3=集货完成
            $map['sku']=$sku;
            $map['_string']=" (qty-real_qty)>0 ";
            $RR = $this->CurrentLocationOrderDetail->where($map)->field('id,location,ebay_id')->order('addtime asc')->find();
            if($RR){
                $id = $RR['id'];
                $ebay_id = $RR['ebay_id'];
                $this->CurrentLocationOrderDetail->where(['id'=>$id])->setInc('real_qty');

                $log = '扫描拣货单和sku--找到了异常库位:'.$RR['location'];
                $this->CurrentLocationLog->addOneNote($RR['location'],$log,$sku,$ebay_id);

                return ['status'=>1,'msg'=>'','data'=>$RR['location']];
            }
        }

        /**
         *测试人员杨 2018-01-03
         *说明: 这里要验证包裹 是不是 已经确认，或者已经完结 ! TODO===
         */
        $map = [];
        $map['ordersn'] = $ordersn;
        $PickOrder = $this->PickOrderModel->where($map)->field('isprint')->find();
        $isprint = $PickOrder['isprint']; //1=已打印未确认 2=已经确认 3=完成 100=删除
        if($isprint <= 1){
            return ['status'=>0,'msg'=>'捡货单必须要是已经确认，已经完成，已经删除才能开始用异常集货','data'=>''];
        }

        // 在有异常的包裹中寻找
        $map=[];
        $map['ordersn'] = $ordersn;
        $map['is_delete'] = 1;
        $map['sku'] = $sku;
        $RR = $this->PickOrderDetailModel->where($map)->field('ebay_id')
            ->order('order_addtime')
            ->group('ebay_id asc')
            ->select();
        if(empty($RR)){
            return ['status'=>0,'msg'=>'捡货单'.$ordersn.'中没有异常的包含【'.$sku.'】的订单','data'=>''];
        }

        $ebayids = [];
        foreach($ebayids as $list){
            $ebayids[] = $list['ebay_id'];
        }

        //订单是不是该进来
        $Rs = $this->PickOrderModel->where([
            'ebay_id'=>['in',$ebayids],
            'ebay_status'=>['in',[1723,1724,1745]]
        ])->field('ebay_id')->order('ebay_addtime asc')->find();

        if(!$Rs){
            return ['status'=>0,'msg'=>'捡货单'.$ordersn.'中没有异常的包含【'.$sku.'】的【合法】订单','data'=>print_r($Rs,true)];
        }

        $ebay_id = $Rs['ebay_id'];

        $initLocationRs = $this->initOneLocation($ebay_id,$sku);


        if($initLocationRs['status']==0){
            return ['status'=>1,'msg'=>$initLocationRs['msg'],'data'=>''];
        }

        $log='扫描拣货单和sku--分配了一个异常库位:'.$initLocationRs['data'];
        $this->CurrentLocationLog->addOneNote($RR['location'],$log,$sku,$ebay_id);

        return $initLocationRs;

    }


    /**
     *测试人员杨 2018-01-03
     *说明: 初始化一个库位
     */
    private function initOneLocation($ebay_id,$sku=''){

        $Goods = $this->GoodsSaleDetail->where(['ebay_id'=>$ebay_id])
            ->field('qty,sku,storeid,ebay_id')->select();
        if(empty($Goods)){
            return ['status'=>0,'msg'=>'订单分解缓存找不到订单sku数据'];
        }

        $RR = $this->CurrentLocation->where(['status'=>1])->order('id asc')->field('id,location')->find();
        if(empty($RR)){
            return ['status'=>0,'msg'=>'找不到多余的本仓异常库位'];
        }
        $location = $RR['location'];
        $id       = $RR['id'];

        $this->CurrentLocation->where(['id'=>$id])->save(['ebay_id'=>$ebay_id,'status'=>2]);


        foreach($Goods as $k=>$list){
            $Goods[$k]['location']=$location;
        }


        $RR = $this->CurrentLocationOrderDetail->addAll($Goods);

        if(!$RR){
            $file=dirname(dirname(THINK_PATH)).'/log/assignLocation/'.date('YmdH').'.txt';
            $log=$_SESSION['truename'].'---根据订单号获取空库位初始化库位失败!'.print_r($Goods,true);
            $log.='---'.date('Ymd H:i:s')."\n\n";
            writeFile($file,$log); //TODO===========================
            return ['status'=>0,'msg'=>'自动初始化异常库位异常！'.$location.','.$ebay_id];
        }

        if($sku){
            $this->AddSkuafterInitLocation($ebay_id,$sku);
        }

        return ['status'=>1,'msg'=>'找到库位并初始化成功','data'=>$location];
    }

    // 初始化库位时 如果sku存在 就一起把sku的数量加1 表示我初始化了一个订单 同时还把sku也扫秒了一个
    private function AddSkuafterInitLocation($ebay_id,$sku){
        $map['status']=1;
        $map['ebay_id']=$ebay_id;
        $map['sku']=$sku;
        $map['_string']=" (qty-real_qty)>0 ";
        $this->CurrentLocationOrderDetail->where($map)->limit(1)->setInc('real_qty');
    }


    /**
     *测试人员杨 2018-01-03
     *说明: 扫描一个SKU 看看在哪一个坑
     */
    function ScanOneSKU($sku){

        $map['status'] = 1;
        $map['sku'] = $sku;
        $map['_string'] = 'qty>real_qty ';

        $RR = $this->CurrentLocationOrderDetail->where($map)
            ->field('location,id,ebay_id')->order('ebay_id asc')->find();

        if(empty($RR)){
            return ['status'=>0,'msg'=>'异常库位中的订单不需要'.$sku,'data'=>''];
        }

        $id       = $RR['id'];
        $location = $RR['location'];
        $ebay_id  = $RR['ebay_id'];

        $this->CurrentLocationOrderDetail->where(['id'=>$id])->limit(1)->setInc('real_qty');
        $log='盲扫描SKU找到一个库位'.$location;
        $this->CurrentLocationLog->addOneNote($location,$log,$sku,$ebay_id);
        return ['status'=>1,'msg'=>'获取库位成功!','data'=>$location];
    }

    /**
     *测试人员杨 2018-01-03
     *说明: 订单的集货进度
     */
    function  getOrderSteps($ebay_id){
        //验证订单号是不是在current_location中
        $orderInfo = $this->CurrentLocation->where(['ebay_id'=>$ebay_id])->find();
        if(empty($orderInfo)){
            return false;
        }

        $where['od.ebay_id'] =  $ebay_id;
        $where['od.status']  = 1;
        $where['_string'] = 'od.sku=eg.goods_sn';
        $orderDetialInfo = $this->CurrentLocationOrderDetail
            ->table('current_location_order_detail od,ebay_goods eg')
            ->field('od.location,od.sku,od.qty,od.storeid,od.real_qty,eg.goods_name,eg.goods_pic')
            ->where($where)
            ->order('od.storeid')
            ->select();
        if(empty($orderDetialInfo)){
            return false;
        }

        //根据仓库编号查找库位
        $orderModel = new OrderModel();
        $storeid = $orderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_warehouse');

        if(empty($storeid)){
            return false;
        }

        $ebayOnhandle = new EbayHandleModel();

        foreach($orderDetialInfo as $num =>$item){
            $storeid = trim($item['storeid']);
            $goods_sn = trim($item['sku']);
            if(empty($storeid) || empty($goods_sn)){
                continue;
            }

            $g_location = $ebayOnhandle->table('ebay_onhandle_'.$storeid)->where(['goods_sn'=>$goods_sn])->getField('g_location');
            $orderDetialInfo[$num]['g_location'] = $g_location;
        }

        return $orderDetialInfo;
    }

    /**
     *说明: 清空库位 放弃订单集货
     *
     */
    function GiveUPOrder($ebay_id){
        $map['status'] = ['in',[2,3,4]]; //1=空 2=等待配齐  3 等待打面单 4=系统自动结束等待取走(取消订单啊，拦截啦 什么的)
        $map['ebay_id'] = $ebay_id;

        $Rs = $this->CurrentLocation->where($map)->field('id,status,location')->find();

        if(empty($Rs)){
            return ['status'=>0,'msg'=>'正在异常集货中的订单里面找不到!'.$ebay_id,'data'=>''];
        }

        $Main_status = $Rs['status'];

        $map = [];
        $map['status'] = ['in',[1,3]]; //1=等待集货 2=放弃集货 3=集货完成
        $map['ebay_id'] = $ebay_id;

        $RR = $this->CurrentLocationOrderDetail->where($map)->field('id,status')->select();


        // 如果是已经 集货 完成要清空的话 就是 error_location.status= 3
        // 否则 就是 异常放弃集货 --
        if($Main_status == 3){
            $str_log = '集货正常,清理掉了库位--';
            $son_status = 3;
        }else{
            $str_log = '---放弃了异常集货--';
            $son_status = 2;
        }

        foreach($RR as $List){
            $id = $List['id'];
            $this->CurrentLocationOrderDetail->where(['id'=>$id])->limit(1)->save(['status'=>$son_status]);
        }

        $id       = $Rs['id'];
        $location = $Rs['location'];

        // 清空当前的库位 清空 之后 给其订单使用
        $this->CurrentLocation->where(['id'=>$id])->limit(1)->save(['ebay_id'=>0,'status'=>1]);

        $log = $str_log.$location;
        // mysql 日志
        $this->CurrentLocationLog->addOneNote($location,$log,'',$ebay_id);

        // 日志路径  文件日志
        $file           = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.jihuo.txt';
        $log = $_SESSION['truename'].$log.$ebay_id.'----'.date('Y-m-d H:i:s')."\n\n";
        writeFile($file,$log);

        return ['status'=>1,'msg'=>'操作成功了!'.$ebay_id,'data'=>''];
    }


    // 订单异常了 （已经发货，已出库待称重，回收站中）
    //1=空 2=等待配齐  3 等待打面单 4=系统自动结束等待取走
    function OrderAbnormalUpdate(){
        $map['status'] = 2;
        $RR = $this->CurrentLocation->where($map)->field('ebay_id,location,id')->select();

        $OrderModel = new OrderModel();
        $TopMenuModel = new TopMenuModel();
        $TopMenuArr = $TopMenuModel->getMenuName();

        $stopStatus = [2009,1731,2];

        foreach($RR as $List){
            $ebay_id  = $List['ebay_id'];
            $location = $List['location'];
            $id       = $List['id'];

            $ebay_status = $OrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');
            if(!in_array($ebay_status,$stopStatus)){
                continue;
            }

            $rr = $this->CurrentLocation->where(['id'=>$id])->limit(1)->save(['status'=>4]);
            if($rr){
                $rs = $this->CurrentLocationOrderDetail->StopSortOrder($location,$ebay_id);
                $log = '检测到订单['.$ebay_id.']状态是:['.$TopMenuArr[$ebay_status].'] 自动终止集货';
                $this->CurrentLocationLog->addOneNote($location,$log,'',$ebay_id);
            }

        }

    }

    // 订单集合完毕
    function OrderSuccessUpdate(){
        $map['status'] = 2;
        $RR = $this->CurrentLocation->where($map)->field('ebay_id,location,id')->select();

        foreach($RR as $List){
            $ebay_id  = $List['ebay_id'];
            $location = $List['location'];
            $id     = $List['id'];
            $rs = $this->CurrentLocationOrderDetail->isComplete($location,$ebay_id);
            if($rs){
                $rr=$this->CurrentLocation->where(['id'=>$id])->limit(1)->save(['status'=>3]);
                $log='检测到订单['.$ebay_id.']已经集货完成!';
                $this->CurrentLocationLog->addOneNote($location,$log,'',$ebay_id);
            }

        }
    }
}