<?php
namespace Package\Service;

use Common\Model\ErrorLocationLogModel;
use Common\Model\ErrorLocationModel;
use Common\Model\ErrorLocationOrderDetailModel;
use Common\Model\GoodsSaleDetailModel;
use Common\Model\InternalStoreSkuModel;
use Common\Model\OrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\EbayGoodsModel;
use Package\Model\PickOrderConfirmModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderDetailSkustrModel;
use Package\Model\PickOrderModel;
use Package\Model\TopMenuModel;
use Task\Model\EbayHandleModel;

/**
 *测试人员谭 2017-12-12 18:38:31
 *说明: 分拣分区 跨仓的 服务
 */
class OrderCrossStockListService
{
    private $current_storeid = 0;
    private $store_names = [];
    private $PickOrderModel = null;
    private $OrderTypeModel = null;
    private $PickOrderDetailModel = null;
    private $PickOrderConfirmModel = null;
    private $PickOrderDetailStrModel = null;
    private $InterNalModel = null;
    private $GoodsSaleDetail = null;

    private $ErrorLocation = null; //
    private $ErrorLocationOrderDetail = null;
    private $ErrorLocationLog = null;

    function __construct() {
        $this->PickOrderConfirmModel   = new PickOrderConfirmModel();
        $this->PickOrderDetailModel    = new PickOrderDetailModel();
        $this->PickOrderModel          = new PickOrderModel();
        $this->PickOrderDetailStrModel = new PickOrderDetailSkustrModel();
        $this->ErrorLocation           = new ErrorLocationModel();
        $this->ErrorLocationOrderDetail= new ErrorLocationOrderDetailModel();
        $this->ErrorLocationLog        = new ErrorLocationLogModel();
        $this->InterNalModel           = new InternalStoreSkuModel();
        $this->GoodsSaleDetail         = new GoodsSaleDetailModel();
        $this->OrderTypeModel          = new OrderTypeModel();
        $this->current_storeid         = C('CURRENT_STORE_ID');
        $this->store_names             = C('STORE_NAMES');
    }


    // 显示看板-----
    //location 库位不可修改
    //ebay_id 订单号
    //status 1=空 2=等待配齐 3 等待打面单
    function StockListView(){
        $RR=$this->ErrorLocation->field('location,ebay_id,status')
            ->order('location asc')->select();


        foreach($RR as $k=>$List){
            $location = $List['location'];
            $ebay_id  = $List['ebay_id'];
            $status   = $List['status'];
            if($status>1){
                $OrderInfo=$this->ErrorLocationOrderDetail
                    ->where(['location'=>$location,'ebay_id'=>$ebay_id])
                    ->field('sku,qty,real_qty,status,addtime,storeid')
                    ->select();
            }else{
                $OrderInfo=[];
            }

            $RR[$k]['order']=$OrderInfo;
        }

        return $RR;

    }

    /**
    *测试人员谭 2017-12-21 20:49:32
    *说明: 扫描sku with Ordersn
    */
    function ScanOneSKUWithOrdersn($ordersn,$sku){
        //第一步先在 error_location_order_detail 找找
        //TODO 这里有一个性能问题:
        //以后 error_location_order_detail 会越来越大这时候应该先查一下一共有多少单在等待集货

        $Orders=$this->ErrorLocation->where(['status'=>2])->getField('ebay_id',true);

        if(!empty($Orders)){
            $map['ebay_id']=['in',$Orders];
            $map['status']=1; // 1=等待集货 2=放弃集货  3=集货完成
            $map['sku']=$sku;
            $map['_string']=" (qty-real_qty)>0 ";
            $RR=$this->ErrorLocationOrderDetail->where($map)->field('id,location,ebay_id')->order('addtime asc')->find();
            if($RR){
                $id=$RR['id'];
                $ebay_id=$RR['ebay_id'];
                $this->ErrorLocationOrderDetail->where(['id'=>$id])->setInc('real_qty');

                $log='扫描拣货单和sku--找到了异常库位:'.$RR['location'];
                $this->ErrorLocationLog->addOneNote($RR['location'],$log,$sku,$ebay_id);

                return ['status'=>1,'msg'=>'','data'=>$RR['location']];
            }
        }





        /**
        *测试人员谭 2017-12-21 23:23:21
        *说明: 这里要验证包裹 是不是 已经确认，或者已经完结 ! TODO===
         * TODO=========================
         * TODO=========================
         * TODO=========================
         * TODO=========================
         * TODO=========================
         * TODO=========================
        */
        $map=[];
        $map['ordersn']=$ordersn;
        $PickOrder=$this->PickOrderModel->where($map)->field('isprint')->find();
        $isprint=$PickOrder['isprint']; //1=已打印未确认 2=已经确认 3=完成 100=删除
        if($isprint<=1){
            return ['status'=>0,'msg'=>'捡货单必须要是已经确认，已经完成，已经删除才能开始用异常集货','data'=>''];
        }




        // 在有异常的包裹中寻找
        $map=[];
        $map['ordersn']=$ordersn;
        $map['is_delete']=1;
        $map['sku']=$sku;
        //$map['_string']='b.id is null';
        $RR=$this->PickOrderDetailModel->where($map)->field('ebay_id')
            ->order('order_addtime')
            ->group('ebay_id asc')
            ->select();

        if(empty($RR)){
            return ['status'=>0,'msg'=>'捡货单'.$ordersn.'中没有异常的包含【'.$sku.'】的订单','data'=>''];
        }


        $ebayids=[];
        foreach($ebayids as $list){
            $ebayids[]=$list['ebay_id'];
        }

        //订单是不是该进来
        $Rs=$this->PickOrderModel->where([
            'ebay_id'=>['in',$ebayids],
            'ebay_status'=>['in',[1723,1724,1745]]
        ])->field('ebay_id')->order('ebay_addtime asc')->find();

        if(!$Rs){
            return ['status'=>0,'msg'=>'捡货单'.$ordersn.'中没有异常的包含【'.$sku.'】的【合法】订单','data'=>print_r($Rs,true)];
        }

        $ebay_id=$Rs['ebay_id'];

        $initLocationRs=$this->initOneLocation($ebay_id,$sku);


        if($initLocationRs['status']==0){
            return ['status'=>1,'msg'=>$initLocationRs['msg'],'data'=>''];
        }

        $log='扫描拣货单和sku--分配了一个异常库位:'.$initLocationRs['data'];
        $this->ErrorLocationLog->addOneNote($RR['location'],$log,$sku,$ebay_id);

        return $initLocationRs;

    }

    // 占用一个 location
    /**
    *测试人员谭 2017-12-27 14:37:49
     * 占用库位要干的主要事情
     * 1. error_location 数据表 里面的 ebay_id status  都被写入！！！！
     * 2. 初始化 订单的子信息 到 error_location_detail 表中去 初始化的时候 有两个 sku 数量,一个 是订单数量,一个是
    */
    function DoUseLocation($location,$ebay_id){
        $map=[];
        $map['location']=$location;
       // $map['ebay_id']=$ebay_id;
        $RR=$this->ErrorLocation->where($map)->find();

        if($RR['status']!=1){
            return ['status'=>0,'msg'=>$location.'--当前不是空库位请刷新看板!'];
        }

        if(empty($RR)){
            return ['status'=>0,'msg'=>$location.'--系统不存在该异常库位!'];
        }

        $id=$RR['id'];

        // 订单是不是 已经在异常中了
        $ExsitLocation=$this->ErrorLocation->where(['ebay_id'=>$ebay_id])->find();

        if($ExsitLocation){
            $location=$ExsitLocation['location'];
            return ['status'=>0,'msg'=>'订单已经在跨仓异常库位【'.$location.'】中了！'];
        }


        //订单状态
        $OrderModel=new OrderModel();
        $Order=$OrderModel->where(['ebay_id'=>$ebay_id])
            ->field('ebay_status,ebay_id')->limit(1)->find();

        $OrderMenu = new TopMenuModel();
        $TopMenu   = $OrderMenu->getMenuName();
        $ebay_status = $Order['ebay_status'];
        if(!in_array($ebay_status,[1723,1724,1724])){
            return ['status'=>0,'msg'=>'订单：'.$ebay_id.' 当前状态 【'.$TopMenu[$ebay_status].'】不允许发货了!'];
        }


        //订单是不是 跨仓

        $TypeRs=$this->OrderTypeModel->where(['ebay_id'=>$ebay_id])->field('is_cross,type')->find();

        $is_cross=$TypeRs['is_cross'];
        $type=$TypeRs['type'];

        /**
        *测试人员谭 2017-12-23 17:50:26 TODO === 如果有同事参考这里 当前仓库普通多品异常看板功能的话
        *说明: 注意这里 如果这里是 本仓多品的 异常库位看板程序 验证的应该是
         * $type>1 && $is_cross==1
         * $type==1  单品单货 没必要加入异常
         * $is_cross=1 跨仓单 请到跨仓单
        */


        if($is_cross!=1){
            return ['status'=>0,'msg'=>'订单'.$ebay_id.'不是跨仓单!不能放在跨仓异常库位中'];
        }


        //订单分解 是否异常
        $Goods=$this->GoodsSaleDetail->where(['ebay_id'=>$ebay_id])
            ->field('qty,sku,storeid,ebay_id')->select();

        if(empty($Goods)){
            return ['status'=>0,'msg'=>'订单分解缓存找不到订单sku数据'];
        }


        $this->ErrorLocation->where(['id'=>$id])->save(['ebay_id'=>$ebay_id,'status'=>2]);


        foreach($Goods as $k=>$list){
            $Goods[$k]['location']=$location;
        }

        $RR=$this->ErrorLocationOrderDetail->addAll($Goods);

        if(!$RR){
            $file=dirname(dirname(THINK_PATH)).'/log/assignLocation/'.date('YmdH').'.txt';
            $log=$_SESSION['truename'].'---根据订单号获取空库位初始化库位失败!'.print_r($Goods,true);
            $log.='---'.date('Ymd H:i:s')."\n\n";
            writeFile($file,$log);//TODO===========================
            return ['status'=>0,'msg'=>'占用库位发生了异常！'.$location.','.$ebay_id];
        }

        $log='手动占用异常库位:'.$location;
        $this->ErrorLocationLog->addOneNote($location,$log,'',$ebay_id);

        return ['status'=>1,'msg'=>'占用库位并初始化成功','data'=>$location];



    }


    /**
    *测试人员谭 2017-12-27 15:06:20
    *说明: 订单的集货进度
    */
    function  getOrderSteps($ebay_id){
        // hank 2017/12/27 16:09 验证订单号是不是在  error_location 中
        $errorLocation = new ErrorLocationModel();
        $orderInfo = $errorLocation->where(['ebay_id'=>$ebay_id,'status'=>2])->find();

        if(empty($orderInfo)){
            return false;
        }

        $errorLocationOrderDetailModel = new ErrorLocationOrderDetailModel();
        // hank 2018/1/4 14:51 按sku 和 仓库分组 合计
        $orderDetialInfo = $errorLocationOrderDetailModel
            ->table('error_location_order_detail od,ebay_goods eg')
            ->field('od.location,od.sku,SUM(od.qty) as qty,od.storeid,SUM(od.real_qty) as real_qty,eg.goods_name,eg.goods_pic')
            ->where(
                [
                    'od.ebay_id'=>$ebay_id,
                    'od.status'=>1,
                    '_string'=>'od.sku=eg.goods_sn',
                ])
            ->group('od.sku,od.storeid')
            ->order('od.storeid')
            ->select();

        if(empty($orderDetialInfo)){
            return false;
        }

        //echo $errorLocationOrderDetailModel->_sql();
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
     * hank
     * @param $storeid
     * @return bool|mixed
     * 查询看板中所有的异常订单还需要集多少
     */
    function getOrderInfo($storeid){
        $errorLocationModel = new ErrorLocationModel();
        $orderIds           = $errorLocationModel->where(['status' => 2])->getField('ebay_id', true);

        if (empty($orderIds)) {
            return false;
        }

        $where = [
            'storeid' => $storeid,
            'ebay_id' => ['in', $orderIds],
            'status'  => 1,
            '_string' => 'qty>real_qty'
        ];

        // hank 2018/1/3 14:25 修改库位
        $errorLocationOrderDetailModel = new ErrorLocationOrderDetailModel();
        $orderInfo                     = $errorLocationOrderDetailModel
            ->table('error_location_order_detail er')
            ->field('er.ebay_id,er.location,er.sku,SUM(er.qty) AS qty,er.storeid,SUM(er.real_qty) AS real_qty, eo.g_location')
            ->join('left join ebay_onhandle_'.$storeid.' eo on er.sku = eo.goods_sn')
            ->where($where)
            ->group('er.sku')
            ->select();

        //echo $errorLocationOrderDetailModel->_sql();
        if (empty($orderIds)) {
            return false;
        }

        return $orderInfo;
    }


    /**
    *测试人员谭 2017-12-27 21:41:38
    *说明: 扫描一个SKU 看看在哪一个坑
    */
    function  ScanOneSKU($sku){

         $map['status']=1;
         $map['sku']=$sku;
         $map['_string']=' qty>real_qty ';

         $RR=$this->ErrorLocationOrderDetail->where($map)
             ->field('location,id,ebay_id')->order('ebay_id asc')->find();

        if(empty($RR)){

            return ['status'=>0,'msg'=>'异常库位中的订单不需要'.$sku,'data'=>''];
        }


        $id       = $RR['id'];
        $location = $RR['location'];
        $ebay_id  = $RR['ebay_id'];

        $this->ErrorLocationOrderDetail->where(['id'=>$id])->limit(1)->setInc('real_qty');

        $log='盲扫描SKU找到一个库位'.$location;
        $this->ErrorLocationLog->addOneNote($location,$log,$sku,$ebay_id);

        return ['status'=>1,'msg'=>'获取库位成功!','data'=>$location];

    }



    /**
    *测试人员谭 2017-12-27 21:56:07
    *说明: 取消一个订单,这狗日的订单不知道为啥 要放弃呢
     *
    */
    function GiveUPOrder($ebay_id){
        //$Main_status
        $map['status']=['in',[2,3,4]]; //1=空 2=等待配齐  3 等待打面单 4=系统自动结束等待取走(取消订单啊，拦截啦 什么的)
        $map['ebay_id']=$ebay_id;

        //查一下看看 是不是 可以 操作  清空/放弃
        $Rs=$this->ErrorLocation->where($map)->field('id,status,location')->find();

        if(empty($Rs)){
            return ['status'=>0,'msg'=>'正在异常集货中的订单里面找不到!'.$ebay_id,'data'=>''];
        }

        $Main_status=$Rs['status'];

        $map=[];
        $map['status']=['in',[1,3]]; //1=等待集货 2=放弃集货 3=集货完成
        $map['ebay_id']=$ebay_id;

        $RR= $this->ErrorLocationOrderDetail->where($map)->field('id,status')->select();


        // 如果是已经 集货 完成要清空的话 就是 error_location.status= 3
        // 否则 就是 异常放弃集货 --
        if($Main_status==3){
            $str_log='集货正常,清理掉了库位--';
            $son_status=3;
        }else{
            $str_log='---放弃了异常集货--';
            $son_status=2;
        }

        foreach($RR as $List){
            $id=$List['id'];
            $this->ErrorLocationOrderDetail->where(['id'=>$id])->limit(1)->save(['status'=>$son_status]);
        }

        $id       = $Rs['id'];
        $location = $Rs['location'];

        // 清空当前的库位 清空 之后 给其订单使用
        $this->ErrorLocation->where(['id'=>$id])->limit(1)->save(['ebay_id'=>0,'status'=>1]);

        $log=$str_log.$location;
        // mysql 日志
        $this->ErrorLocationLog->addOneNote($location,$log,'',$ebay_id);

        // 日志路径  文件日志
        $file           = dirname(dirname(THINK_PATH)) . '/log/package/' . date('YmdH') . '.jihuo.txt';
        $log=$_SESSION['truename'].$log.$ebay_id.'----'.date('Y-m-d H:i:s')."\n\n";
        writeFile($file,$log);

        return ['status'=>1,'msg'=>'操作成功了!'.$ebay_id,'data'=>''];
    }

    // 订单异常了 （已经发货，已出库待称重，回收站中）
    //1=空 2=等待配齐  3 等待打面单 4=系统自动结束等待取走
    function OrderAbnormalUpdate(){
        $map['status']=2;
        $RR=$this->ErrorLocation->where($map)->field('ebay_id,location,id')->select();

        $OrderModel=new OrderModel();
        $TopMenuModel=new TopMenuModel();
        $TopMenuArr=$TopMenuModel->getMenuName();

        $stopStatus=[2009,1731,2];

        foreach($RR as $List){
            $ebay_id  = $List['ebay_id'];
            $location = $List['location'];
            $id       = $List['id'];

            $ebay_status=$OrderModel->where(['ebay_id'=>$ebay_id])->getField('ebay_status');

            if(!in_array($ebay_status,$stopStatus)){
                continue;
            }



            $rr=$this->ErrorLocation->where(['id'=>$id])->limit(1)->save(['status'=>4]);
            if($rr){
                $rs=$this->ErrorLocationOrderDetail->StopSortOrder($location,$ebay_id);
                $log='检测到订单['.$ebay_id.']状态是:['.$TopMenuArr[$ebay_status].'] 自动终止集货';
                $this->ErrorLocationLog->addOneNote($location,$log,'',$ebay_id);
            }

        }

    }


    // 订单集合完毕
    function OrderSuccessUpdate(){
        $map['status']=2;
        $RR=$this->ErrorLocation->where($map)->field('ebay_id,location,id')->select();

        foreach($RR as $List){
            $ebay_id  = $List['ebay_id'];
            $location = $List['location'];
            $id     = $List['id'];
            $rs=$this->ErrorLocationOrderDetail->isComplete($location,$ebay_id);
            if($rs){
                $rr=$this->ErrorLocation->where(['id'=>$id])->limit(1)->save(['status'=>3]);
                $log='检测到订单['.$ebay_id.']已经集货完成!';
                $this->ErrorLocationLog->addOneNote($location,$log,'',$ebay_id);
            }

        }
    }


    /**
    *测试人员谭 2017-12-27 15:04:48
    *说明: 初始化一个 库位
    */
    private function initOneLocation($ebay_id,$sku=''){

        $Goods=$this->GoodsSaleDetail->where(['ebay_id'=>$ebay_id])
            ->field('qty,sku,storeid,ebay_id')->select();

        if(empty($Goods)){
            return ['status'=>0,'msg'=>'订单分解缓存找不到订单sku数据'];
        }


        $RR=$this->ErrorLocation->where(['status'=>1])->order('id asc')->field('id,location')->find();

        if(empty($RR)){
            return ['status'=>0,'msg'=>'找不到多余的跨仓异常库位'];
        }
        $location = $RR['location'];
        $id       = $RR['id'];

        $this->ErrorLocation->where(['id'=>$id])->save(['ebay_id'=>$ebay_id,'status'=>2]);


        foreach($Goods as $k=>$list){
            $Goods[$k]['location']=$location;
        }


        $RR=$this->ErrorLocationOrderDetail->addAll($Goods);

        if(!$RR){
            $file=dirname(dirname(THINK_PATH)).'/log/assignLocation/'.date('YmdH').'.txt';
            $log=$_SESSION['truename'].'---根据订单号获取空库位初始化库位失败!'.print_r($Goods,true);
            $log.='---'.date('Ymd H:i:s')."\n\n";
            writeFile($file,$log);//TODO===========================
            //writeFile();TODO===========================
            //writeFile();TODO===========================
            return ['status'=>0,'msg'=>'自动初始化异常库位异常！'.$location.','.$ebay_id];
        }


        if($sku){
            $this->AddSkuafterInitLocation($ebay_id,$sku);
        }

        return ['status'=>1,'msg'=>'找到库位并初始化成功','data'=>$location];
    }


    // 初始化的时候 如果顺便有sku 就一起 吧sku 的数量加1 表示 我初始化了 一个订单 同时还吧sku也扫秒了一个
    private function AddSkuafterInitLocation($ebay_id,$sku){
        $map['status']=1;
        $map['ebay_id']=$ebay_id;
        $map['sku']=$sku;
        $map['_string']=" (qty-real_qty)>0 ";
        $this->ErrorLocationOrderDetail->where($map)->limit(1)->setInc('real_qty');
    }



}
