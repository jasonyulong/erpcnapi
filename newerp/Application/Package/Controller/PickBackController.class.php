<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:39
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Common\Model\CarrierModel;
use Common\Model\OrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\TopMenuModel;
use Think\Page;

/**
 * @method $this assign($name, $value)
 * @method display($name = '')
 * Class OrderGroupController
 * @package Package\Controller
 */
class PickBackController extends CommonController
{
    protected $pageSize = 50;

    private  $allow_status=[1723,1724,1745];

    private  $maxExport = 10000;

    public function _initialize() {
        parent::_initialize();
        $this->pageSize = session('pagesize') ? session('pagesize') : 100;
    }

    function getPickBackCount(){
        $OrderType=new OrderTypeModel();
        $map['a.pick_status']=3;

        $RR=$OrderType->alias('a')->join('inner join erp_ebay_order b using(ebay_id)')
            ->where($map)->count();
        return $RR;

    }


    function PickBackList(){
        $OrderType=new OrderTypeModel();
        $currStoreId    = C("CURRENT_STORE_ID");
        $status=(int)I('status');
        $carrier=I('carrier');
        $ebay_id=trim(I('ebay_id'));
        $ebay_addtime_s=trim(I('ebay_addtime_s'));
        $ebay_addtime_e=trim(I('ebay_addtime_e'));
        $label_type=intval(I('label_type'));
        $floor=intval(I('floor'));
        $carriers = array_filter(explode(',',$carrier));
        $ebay_id=str_replace('，',',',$ebay_id);
        if(empty($ebay_id)){
            $ebay_ids=[];
        }else{
            $ebay_ids=explode(',',trim($ebay_id));
        }

        $map['a.pick_status']=3;
        /**
        *测试人员谭 2017-11-18 13:27:40
        *说明: 日后 其他呀条件在 这里修改 map
        */
        if($status){
            $map['b.ebay_status']=$status;
        }else{
            $map['b.ebay_status']=['in',[1723,1745,1724]];
        }

        if(count($carriers)){
            $map['b.ebay_carrier']=['in',$carriers];
            $this->assign('request_carrier',$carriers);
        }

        if(count($ebay_ids)){
            $map['b.ebay_id']=['in',$ebay_ids];
        }
        if($floor){
            $map['a.floor']=$floor;
        }
        if($ebay_addtime_s&&$ebay_addtime_e){
            $start=strtotime($ebay_addtime_s.' 00:00:00');
            $end=strtotime($ebay_addtime_e.' 23:59:59');
            $map['b.ebay_addtime']=['between',[$start,$end]];
        }

        /**
        *测试人员谭 2017-11-20 20:19:58
        *说明: $label_type 这个 条件 在订单表是不存在的，通过运输方式转一转 有意思吧？
        */
        //echo THINK_PATH;
        $TransportArr=include_once(dirname(THINK_PATH).'/Application/Transport/Conf/config.php');
        //CARRIER_TEMPT
        //CARRIER_TEMPT_15
        //debug($TransportArr['CARRIER_TEMPT']);
        //debug($TransportArr['CARRIER_TEMPT_15']);

        if($label_type==1||2==$label_type){
            $carrier_mod2=array_keys($TransportArr['CARRIER_TEMPT_15']);
            $carrier_mod1=[];
            foreach($TransportArr['CARRIER_TEMPT'] as $carriers=>$v){
                if(!in_array($carriers,$carrier_mod2)){
                    $carrier_mod1[]=$carriers;
                }
            }

            if($label_type==1){
                $map['b.ebay_carrier']=['in',$carrier_mod1];
            }else{
                $map['b.ebay_carrier']=['in',$carrier_mod2];
            }

        }





        $field='b.ebay_addtime,b.ebay_id,b.ebay_noteb,b.w_add_time,b.ebay_carrier,b.ebay_status ';

        $counts=$OrderType->alias('a')
            ->join('inner join erp_ebay_order b using(ebay_id)')
            ->where($map)->count();
        //debug($map);
        //echo $this->pageSize;
        $pageObj    = new Page($counts, $this->pageSize);
        $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
        $pageString = $pageObj->show();

        $TopMenu=new TopMenuModel();
        $TopMenuArr=$TopMenu->getMenuName();

        $CarrierModel=new CarrierModel();
        $Carrier=$CarrierModel->where(['ebay_warehouse'=>$currStoreId])->field('distinct name')->select();

        //debug($Carrier);

        $OrderData=$OrderType->alias('a')
            ->join('inner join erp_ebay_order b using(ebay_id)')
            ->where($map)
            ->field($field)
            ->order('b.w_add_time')
            ->limit($limit)
            ->select();

        $OrderslogModel=new OrderslogModel();
        $LogUser=[];
        foreach($OrderData as $List){
            $ebay_id=$List['ebay_id'];
            $LogUser[$ebay_id]=$OrderslogModel->where(['ebay_id'=>$ebay_id,'types'=>3])->order('id desc')->getField('operationuser');
        }


        $this->assign('LogUser',$LogUser);
        $this->assign('OrderData',$OrderData);
        $this->assign('pageStr',$pageString);
        $this->assign('TopMenuArr',$TopMenuArr);
        $this->assign('Carrier',$Carrier);
        $this->assign('currStoreId',C("CURRENT_STORE_ID"));
        $this->assign('erp_url','http://erp.spocoo.com');
        $this->display();
    }


     /**
     * Abel
     *  导出
     * @return mixed
     */
    public function exportOrder(){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("FILE");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '序号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '订单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'WMS状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '物流方式');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '进入WMS时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '进入erp时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '楼层');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '操作人');
        $list = $this->OrderList();
        $OrderslogModel=new OrderslogModel();
        $LogUser=[];
        foreach($list as $List){
            $ebay_id=$List['ebay_id'];
            $LogUser[$ebay_id]=$OrderslogModel->where(['ebay_id'=>$ebay_id,'types'=>3])->order('id desc')->getField('operationuser');
        }


        $status = ['2'=>'已发货','2009'=>'待称重','1731'=>'回收站','1723'=>'可打印','1745'=>'等待打印','1724'=>'等待扫描'];
        $j=2;
        foreach($list as $key => $item){
            $ebay_addtime = date("Y-m-d H:i:s",$item['ebay_addtime']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$j, $key+1);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$j, $item['ebay_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$j, $status[$item['ebay_status']]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$j, $item['ebay_carrier']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$j, $item['w_add_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$j, $ebay_addtime);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$j, $item['floor']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$j, $LogUser[$item['ebay_id']]);
            $j++;
        }

        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);

        $title = "WMS捡货文档-".date('Y-m-d');
        $titlename = "WMS捡货文档-".date('Y-m-d').".xls";
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$titlename}");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    /**
     * Abel
     *  需要导出的数据
     * @return mixed
     */
    public function OrderList(){
        $get = I('get.');
        $ebay_id=str_replace('，',',',$get['ebay_id']);
        if(!empty($ebay_id) && isset($get['ebay_id'])){
            $ebay_ids   =   explode(',',trim($ebay_id));
            $map['b.ebay_id']=['in',$ebay_ids];
        }

        $map['a.pick_status']   =   3;
        if(!empty($get['status']) && isset($get['status'])){
            $map['b.ebay_status'] = $get['status'];  //1
        }else{
            $map['b.ebay_status']=['in',[1723,1745,1724]];
        }

        if(!empty($get['carrier']) && isset($get['carrier'])){
            $map['b.ebay_carrier']  = $get['carrier'];
        }

        if(!empty($get['floor']) && isset($get['floor']) ){
            $map['a.floor'] = $get['floor'];
        }


        if((!empty($get['ebay_addtime_s']) && isset($get['ebay_addtime_s'])) &&  (!empty($get['ebay_addtime_e']) && isset($get['ebay_addtime_e']))){
            $start = strtotime($get['ebay_addtime_s']);
            $end   = strtotime($get['ebay_addtime_e']);
            if($start > $end){
                exit("<span style='color:red'>开始时间不能早于结束时间</span>");
            }

            $start = $start.' 00:00:00';
            $end   = $end.' 23:59:59';
            $map['b.ebay_addtime']=['between',[$start,$end]];
        }

        if(!empty($get['carrier']) && isset($get['carrier'])){
            $map['b.ebay_carrier']  = $get['carrier'];
        }

        //匹配类型 模板的优先级高于运输方式
        $TransportArr=include_once(dirname(THINK_PATH).'/Application/Transport/Conf/config.php');
        if($get['label_type'] == 1 || $get['label_type'] == 2){
            $carrier_mod2=array_keys($TransportArr['CARRIER_TEMPT_15']);
            $carrier_mod1=[];
            foreach($TransportArr['CARRIER_TEMPT'] as $carriers=>$v){
                if(!in_array($carriers,$carrier_mod2)){
                    $carrier_mod1[]=$carriers;
                }
            }

            $map['b.ebay_carrier'] = $get['label_type'] == 1 ? ['in',$carrier_mod1] : ['in',$carrier_mod2];
        }

        $OrderType  =   new OrderTypeModel();
        $field      =   'b.ebay_addtime,b.ebay_id,b.ebay_noteb,b.w_add_time,b.ebay_carrier,b.ebay_status,';
        $field     .=    'ebay_total,a.type,a.pick_status,a.floor,b.ebay_ordersn';
        $OrderData  = $OrderType->alias('a')
            ->join('inner join erp_ebay_order b using(ebay_id)')->where($map)->field($field) ->order('b.w_add_time')->select();
        if(empty($OrderData)){
            exit("<span style='color:red'>当前没有记录可导出</span>");
        }

        if(count($OrderData) > $this->maxExport){
            exit("<span style='color:red'>当前记录有".count($OrderData)."条系统允许最多可以导出".$this->maxExport."条缩减范围导出</span>");
        }

        return $OrderData;

    }

    function BattchSetOrder2WaitPick(){

        $bill=$_POST['bill'];
        $status=(int)$_POST['status'];

        $bill=explode(',',$bill);

        foreach($bill as $ebay_id){
            if(empty($ebay_id)){
                continue;
            }

            $rs=$this->setOrder2WaitPick($ebay_id,$status);

            if($rs['status']==1){

                //每次操作一个订单，则将 erp_op_id + 1 2018年8月28日09:38:08
                $orderModel = new OrderModel();
                $orderModel->where(['ebay_id'=>$ebay_id])->setInc('erp_op_id',1);

                echo '<div style="color:#191;font-size:14px">'.$rs['msg'].'</div>';
            }else{
                echo '<div style="color:#911;font-size:14px">'.$rs['msg'].'</div>';
            }
        }
    }
    /**
    *测试人员谭 2017-11-18 13:29:45
    *说明: 订单设置为 可以捡货 重新来捡货
    */
    private function setOrder2WaitPick($ebay_id,$p_status){

        $OrderType=new OrderTypeModel();
        $PickOrderDetail=new PickOrderDetailModel();
        $ApiChecksku=new ApiCheckskuModel();


        if($ebay_id==''){
            return ['status'=>0,'msg'=>'订单号错误'];
        }

        $OrderModel=new OrderModel();

        $status=$OrderModel->where(compact('ebay_id'))->getField('ebay_status');

        /**
        *测试人员谭 2018-07-17 20:44:36
        *说明:
        */
        if(!in_array($status,$this->allow_status)){

            return ['status'=>0,'msg'=>$ebay_id.'订单当前状态不允许操作设置为可捡货'];
        }

        $pick_status=$OrderType->where(['ebay_id'=>$ebay_id])->getField('pick_status');

        $Arr=array(
            0=>'未生成拣货单',
            1=>'未生成拣货单',
            2=>'正常生成了拣货单',
            3=>'拣货打回',
        );

        $beforeSttaus=$Arr[$pick_status];

        $map=[];
        $map['is_delete']=0;
        $map['is_normal']=1;
        $map['ebay_id']=$ebay_id;

        $PickDetails=$PickOrderDetail->where($map)->field('id,ordersn')->select();

        $rs=$ApiChecksku->where(compact('ebay_id'))->field('id')->find();

        if($rs){
            return ['status'=>0,'msg'=>$ebay_id.'订单已经验货或者包装了，禁止操作!'];
        }

        $is_cross=$OrderType->where(['ebay_id'=>$ebay_id])->getField('is_cross');

        if(1==$is_cross){
            return ['status'=>0,'msg'=>$ebay_id.'跨仓订单不允许操作!'];
        }


        $log='首页批量操作:';


        /**
        *测试人员谭 2018-07-17 21:02:12
        *说明: 把这些 看起来正常的家伙都统统干到 异常中，这样才能生成拣货单子
        */
        foreach($PickDetails as $List){
            $id      = $List['id'];
            $ordersn = $List['ordersn'];

            $log.="拣货单中{$ordersn} 设置为异常;";

            $saved=[];

            $saved['status'] = 3;// 这个就是 打包的时候 删除的
            $saved['is_delete'] = 1;
            $saved['is_normal'] = 0;

            $PickOrderDetail->where(['id'=>$id])->limit(1)->save($saved);
        }


        $log.="拣货状态修改前:".$beforeSttaus.'('.$pick_status.');';


        $rr=$OrderModel->where(compact('ebay_id'))->limit(1)->save(['ebay_status'=>1723]);


        $save_pick_status=1;

        if($p_status==3){
            $save_pick_status=3;
        }

        $rr2=$OrderType->where(compact('ebay_id'))->save(['pick_status'=>$save_pick_status]);

        if($rr===false || $rr2===false ){
            return ['status'=>0,'msg'=>$ebay_id.' 操作失败!'];
        }



        $Orderslog=new OrderslogModel();

        $topMenu=[
            1723=>'可打印',
            1724=>'等待扫描',
            1745=>'等待打印'
        ];



        $log.='订单捡货状态设置为: '.$Arr[$save_pick_status].' （修改前订单是:'.$topMenu[$status].'）;';


        $Orderslog->addordernote($ebay_id,$log);

        return ['status'=>1,'msg'=>$ebay_id.'订单设置可捡货ok'];

    }
}