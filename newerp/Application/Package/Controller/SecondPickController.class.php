<?php

namespace Package\Controller;

use Common\Controller\CommonController;
use Order\Model\EbayOrderModel;
use Order\Model\GoodsLocationModel;
use Order\Model\OrderTypeModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderLogModel;
use Package\Model\PickOrderModel;
use Package\Model\PickRecordModel;
use Package\Service\SecondPickService;

/**
 * Class CreatePickController
 * @package Package\Controller
 *  二次分拣单
 */
class SecondPickController extends CommonController{

    function index(){
        #echo 'SecondPickController';
        if($_REQUEST['ordersn']){
            $ordersn = trim($_REQUEST['ordersn']);
            $PickOrderModel       = new PickOrderModel();
            $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint,is_work,sorting_status')->find();
            $sorting_status = $RR['sorting_status'];
            $html = $this->SecondPickView();
            $this->assign('html',$html);
            $this->assign('sorting_status',$sorting_status);
        }
        $this->assign('ordersn',$_REQUEST['ordersn']);
        $this->assign('sortingTime',2);
        $this->display();
    }


    function thirdTime(){
        #echo 'SecondPickController';
        if($_REQUEST['ordersn']){
            $ordersn = trim($_REQUEST['ordersn']);
            $PickOrderModel       = new PickOrderModel();
            $RR=$PickOrderModel->where("ordersn='$ordersn'")->field('type,isprint,is_work,sorting_status')->find();
            $sorting_status = $RR['sorting_status'];
            $html = $this->SecondPickView();
            $this->assign('html',$html);
            $this->assign('sorting_status',$sorting_status);
        }
        $this->assign('ordersn',$_REQUEST['ordersn']);
        $this->assign('sortingTime',3);
        $this->display('index');
    }

    //扫描单号开始 第二次分拣
    function getPickStstus(){
        $ordersn=$_GET['ordersn'];
        $HeadSuc='<!--SUCCESS-->';
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }


        $SecondPk=new SecondPickService();

        $RR=$SecondPk->getSecondPickStatus($ordersn);

        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $MainArr=$RR['data'];


        //debug();

        //debug($MainArr);//die();

        $url=C('ONLINE_PIC_URL');

        echo '<table class="table table-responsive table-condensed table-hover ebay_id_table" cellpadding="0" cellspacing="0">';
        echo '<tr><th>分拣位&nbsp;/&nbsp;订单号</th><th>SKU&nbsp;/&nbsp;还差多少&nbsp;/&nbsp;是否完成</th></tr>';
        $i=1;
        foreach($MainArr as $ebay_id=>$Order){
            $i++;
            $ebay_id=preg_replace("/\w+\./",'',$ebay_id);
            $combineid=$Order[0]['combineid'];
            if($combineid==0){
                $str='<b>无分拣位</b>';
            }else{
                $str='<b class="fenjian_location">'.$combineid.' 号</b>';
            }

            if($i%2==0){
                $trbg='tr_second_bg';
            }else{
                $trbg='';
            }

            echo '<tr class="'.$trbg.'"><td>'.$str.'<br>'.$ebay_id.'</td><td>';
            echo '<table class="ebay_sku_table">';
            foreach($Order as $List){
                $pic        = $List['pic'];
                $qty        = $List['qty'];
                $sku        = $List['sku'];
               // $goods_name = $List['goods_name'];
                $qty_com    = $List['qty_com'];

                $shenyu=$qty-$qty_com;

                if($shenyu==0){
                    $str='<b style="color:#191">OK</b>';
                    $shenyuhtml='<b style="color:#000">0</b>';
                }else{
                    $str='<b style="color:#911">NO</b>';
                    $shenyuhtml='<b style="color:#911">'.$shenyu.'</b>';
                }
                $html= '<tr><td><img style="height:48px;width:48px;" src="'.$url.'/images/'.$pic.'"/><br>';
                $html.='<b>'.$sku.'</b> * '.$qty.'</td>';


                $html.='<td><b>'.$shenyuhtml.'</b></td>';
                $html.='<td><b>'.$str.'</b></td>';
                $html.='</tr>';
                echo $html;
            }
            echo '</table>';
            echo '</td></tr>';
        }
        echo '</table>';

    }

    /**
     * @desc  打印剩余的sku
     * @Author leo
     */
    public function printRemainder()
    {
        $ordersn=trim($_GET['ordersn']);
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }
        $pickOrderModel = new PickOrderModel();
        $pickuser = $pickOrderModel->where(['ordersn'=>$ordersn])->getField('pickuser');
        $SecondPk=new SecondPickService();
        $RR=$SecondPk->getSecondPickStatus($ordersn);
        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $dataArr=$RR['data'];
        $newArr = [];
        $store_id = C('CURRENT_STORE_ID');
        $locationModel = new GoodsLocationModel();
        $onHandleModel = M("ebay_onhandle_{$store_id}");
        foreach($dataArr as $ebay_id=>$Order){
            foreach($Order as $val){
                $location = $onHandleModel->field('g_location')->where(['goods_sn'=>$val['sku']])->find();

                $val['g_location'] = $location['g_location'];
                $field = 'a.shelves_id,b.picker';
                $detil = $locationModel->alias('a')->field($field)
                    ->join("left join goods_shelves as b on a.shelves_id=b.id")
                    ->where(['a.location'=>$val['g_location'],'a.storeid'=>$store_id])
                    ->find();
                $val['picker'] = $detil['picker'];

                if($val['qty'] != $val['qty_com']){
                    $val['remainder'] = $val['qty'] - $val['qty_com'];
                    $newArr[] = $val;
                }
            }
        }

        $newArr = $this->unSkuArr($newArr);
        foreach($newArr as $list2){
            $sort[]=$list2["g_location"];
        }
        array_multisort($sort,SORT_ASC,$newArr);
        $newArr = $this->newSkuArr($newArr);
        $this->assign('pickuser',$pickuser);
        $this->assign('newArr',$newArr);
        $this->assign('ordersn',$ordersn);
        $this->display();
    }

    /**
     * @desc 将重复的sku数量累加
     * @Author leo
     */
    public function unSkuArr($printSkuArr){
        $unPrint_skuArr = [];
        foreach($printSkuArr as $val){
            if($unPrint_skuArr[$val['sku']]){
                $unPrint_skuArr[$val['sku']]['remainder'] += $val['remainder'];
                continue;
            }
            $unPrint_skuArr[$val['sku']] = $val;
        }
        return $unPrint_skuArr;
    }

    /**
     * @desc 数据分页
     * @Author leo
     */
    public function newSkuArr($printSkuArr){
        $num = 1;
        $k = 0;
        $newPrint_skuArr = [];
        foreach($printSkuArr as $skuArr){
            $newPrint_skuArr[$k][] = $skuArr;
            if(mb_strlen($skuArr['goods_name'],'gb2312') >= 17){
                $num+=2;
            }else{
                $num++;
            }
            if($num > 12){
                $num = 1;
                $k++;
            }
        }
        return $newPrint_skuArr;
    }

	/**
     * @desc  打印异常标签
     * @Author leo
     */
    public function printException()
    {
        $ordersn=trim($_GET['ordersn']);
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }
        $SecondPk=new SecondPickService();
        $RR=$SecondPk->getSecondPickStatus($ordersn);
        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $dataArr=$RR['data'];
        $newArr = [];
        $num = 0;
        foreach($dataArr as $ebay_id=>$Order){
            foreach($Order as $val){
                if($val['qty'] != $val['qty_com']){
                    $num = $val['combineid'] ?: $num+1;
                    $newArr[] = (int)$num;
                    break;
                }
            }
        }

        $this->assign('newArr',$newArr);
        $this->assign('ordersn',$ordersn);
        $this->display();
    }

    function getSecondPickLocation(){
        $ordersn  = trim($_POST['ordersn']);
        $sku      = trim($_POST['sku']);
        $sorting3 = (int)trim($_POST['sortingTime']);
        //二维码原文
        $qrcode = isset($_POST['qrcode']) ? trim($_POST['qrcode']) : '';


        if($sorting3==2){
            $sorting3=0;
        }

        $Data['status']=1;
        $Data['msg']='';
        if($ordersn==''||$sku==''){
            $Data['msg']= '<div style="color:#911">拣货单号 或者 SKU 有误!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }


        $SecondPk=new SecondPickService();

        $RR=$SecondPk->getSecondPickLocation($ordersn,$sku,$qrcode,$sorting3);

        if(!$RR['status']){
            $Data['msg']= '<div class="SecondPick_error" style="color:#911">'.$RR['msg'].'!</div>';
            $Data['status']=0;
            echo json_encode($Data);
            return ;
        }

        $number=$RR['data'];

        if($RR['success']){
            $success = $number;
            $Data['success']=$success;
        }

        $skuHtml = '';
        if($RR['returnRR']){
            $returnRR = $RR['returnRR'];
            foreach($returnRR as $rval){
                $skuHtml.='<div class="box_sku">'.$rval['sku'].'*'.$rval['is_notover'].'</div>';
            }
            $Data['skuHtml']=$skuHtml;
        }

        $Data['msg']= '<div class="show_location" sku="'.$sku.'" date="'.date('H:i:s').'">'.$number.'</div>';
        $Data['status']=1;
        $Data['data']=$number;
        echo json_encode($Data);
        return ;

    }

    /**
    *测试人员谭 2017-06-01 20:33:36
    *说明: 中心分拣
    */
    function RePick(){

        $ordersn=strtoupper(trim($_POST['ordersn']));
        if($ordersn==''||!preg_match("/^PK\d{10}$/",$ordersn)){
            echo json_encode(['status'=>0,'msg'=>'拣货单号格式有误!']);
            return ;
        }
        $SecondPk=new SecondPickService();
        $RR=$SecondPk->RePick($ordersn);

        echo json_encode($RR);
        return ;
    }

    /**
    *测试人员谭 2017-06-10 15:16:31
    *说明: 强制结束
     * 1. 先查出所有的有二次分拣的sku 订单
     * 2. 然后自动全部完成
     * 3. 记录日志，主要记录有几个订单 到这一步有问题一般就是 捡货员和 组长不负责任了
     * // TODO
    */
    function ForcedEnd(){
        $HeadErr='<!--Error-->';
        if(!can('second_pick_end')){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">没有权限操作</div>';
            return false;
        }
         //第一步
        $ordersn=trim($_GET['ordersn']);
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }


        $SecondPk=new SecondPickService();

        $RR=$SecondPk->getSecondPickStatus($ordersn);

        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        //第二部分解出未完成的订单 和篓子
        $dataArr=$RR['data'];
        $ebay_idArr = [];
        $numArr = [];
        foreach($dataArr as $ebay_id=>$Order){
            foreach($Order as $val){
                if($val['qty'] != $val['qty_com']){
                    if($val['combineid']) {
                        $numArr[] = $val['combineid'];
                    }
                    $ebay_idArr[] = preg_replace("/\w+\./",'',$ebay_id);
                    break;
                }
            }
        }

        $PickOrderDetilModel = new PickOrderDetailModel();
        $ebayOrderModel      = new EbayOrderModel();
        $OrderTypeModel      = new OrderTypeModel();

        //2.5 步 将点名的订单标记为1732 而且 拣货单详情表的is_delete标记为1  订单类型干到异常
        if($ebay_idArr){
            $PickOrderDetilModel->startTrans();
            $where['ebay_id'] = ['in',$ebay_idArr];

            //is_delete标记为1
            $is_delete = $PickOrderDetilModel->where($where)->save(['is_delete'=>1]);

            //点名的订单pick_status为3
            $order_type = $OrderTypeModel->where($where)->save(['pick_status'=>3]);

            //点名的订单标记为1732 并且为1724,1745
            $where['ebay_status'] = ['in','1724,1745'];
            $order_status = $ebayOrderModel->where($where)->save(['ebay_status'=>1723]);

            if($is_delete && $order_status && $order_type){

                $ebay_idStr = implode(',',$ebay_idArr);
                $num_idStr  = implode(',',$numArr);
                $Log = "强制结束二次分拣：{$ebay_idStr}";
                echo '<div style="color:#191;font-size: 35px;display:block;word-break: break-all;word-wrap: break-word;">订单号'.$ebay_idStr.',强制结束成功!</div>';
                echo '<div style="color:#911;font-size: 35px;">请将当前篓子号为：'.$num_idStr.' 内的物品放到归还区</div>';
                $PickOrderDetilModel->commit();

                // 第三步日志
                $PicOrderLog=new PickOrderLogModel();
                $data['adduser']=$_SESSION['truename'];
                $data['addtime']=time();
                $data['ordersn']=$ordersn;
                $data['note']=$Log;
                $data['type']=2;
                $data['data']=count($ebay_idArr); // 几款订单
                $PicOrderLog->add($data);

            }else{
                $PickOrderDetilModel->rollback();
                echo '<div style="color:#911;font-size: 35px;">强制结束失败!</div>';
            }

        }else{
            echo '<div style="color:#911;font-size: 35px;">分拣信息不存在或已全部完成!</div>';
        }
    }



    /**
    *测试人员谭 2018-08-04 10:52:22
    *说明:  真的点击了结束二次分拣
    */
    public function overSecondPick(){
        $ordersn=trim($_GET['ordersn']);
        $HeadSuc='<!--SUCCESS-->';
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }

        $PickOrder=new PickOrderModel();

        $PickOrderLog=new PickOrderLogModel();

        $status=$PickOrder->where("ordersn='$ordersn'")->getField('sorting_status');

        if($status!=0){
            echo $HeadErr.'拣货单已经结束了二次分拣!';    return;
        }

        $where['ordersn']        = $ordersn;
        $where['sorting_status'] = 0;

        $rs=$PickOrder->where($where)->limit(1)->save(['sorting_status'=>1]);
        if($rs){
            $log='结束了二次分拣';
            $PickOrderLog->addOneLog($ordersn,$log);
            $msg =  $HeadErr.'结束成功!';
            $this->ajaxReturn(['status'=>1,'msg'=>$msg]);
            return;
        }

        echo $HeadErr.'结束失败';
        return;
    }


    /**
    *测试人员谭 2018-08-04 10:52:41
    *说明: 结束二次分拣的 显示界面
    */
    public function overSecondPickView(){
        $ordersn=strtoupper(trim($_GET['ordersn']));
        $HeadSuc='<!--SUCCESS-->';
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }

        $SecondPk=new SecondPickService();

        $RR=$SecondPk->getSecondPickStatus($ordersn);

        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $MainArr=$RR['data'];

        $basketCount=count($MainArr);  // 一共有几个篮子

        $success=0;

        $html= '<div id="box_show"><div><h3>绿色表示已经分拣完毕，篮子可以拿走</h3></div>';

        foreach($MainArr as $List){

            $isOver=true;
            $skuHtml='';
            foreach($List as $SKU){
                $skuHtml.='<div class="box_sku">'.$SKU['sku'].'*'.$SKU['qty'].'</div>';
                if($SKU['is_notover']>0){
                    $isOver=false;
                }
            }

            if($isOver){
                $success++;
                $html.='<div class="box_item_show box_success" onclick="showFullSKU(this)">';
            }else{
                $html.='<div class="box_item_show" onclick="showFullSKU(this)">';
            }

            $boxid=$List[0]['combineid'];
            $k='';
            if($boxid==0){
                $boxid='未分配';
                $k=' noboxid';
            }

            $html.='<div class="boxid'.$k.'">'.$boxid.'</div>';

            $html.=$skuHtml;


            $html.='</div>';
        }
        $html.= '</div>';

        $html.='<div style="clear: both;margin-top:30px;margin-left:30px;padding-top:38px;"><a class="btn btn-sm btn-warning" style="font-size:18px;margin-top:-4px;" onclick="true_over_secondPick()">确定结束</a><br>*注：结束后还可以三次分拣*</div>';

        $html.='<div style="padding-left: 20px;"><h3>拣货单'.$ordersn.' ,分拣进度:'.$success.'/'.$basketCount.'</h3></div>';

        print_r($html);
        //echo '<pre>';print_r($MainArr);


    }


    /**
     *说明: 二次分拣的 显示界面
     */
    public function SecondPickView(){
        $ordersn=strtoupper(trim($_GET['ordersn']));
        $HeadErr='<!--Error-->';
        if($ordersn==''){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">错误的单号</div>';
            return;
        }

        $SecondPk=new SecondPickService();

        $RR=$SecondPk->getSecondPickStatus($ordersn);

        if(!$RR['status']){
            echo $HeadErr.'<div style="color:#a11;font-size:30px;">'.$RR['msg'].'</div>';
            return;
        }

        $MainArr=$RR['data'];

        $basketCount=count($MainArr);  // 一共有几个篮子

        $success=0;
        $html = '';
        $successSku = 0;
        $combineid = 0;
        foreach($MainArr as $ebay_id => $List){
            $isOver=true;
            $skuHtml='';
            $skuDetilHtml = '';
            foreach($List as $SKU){
                $skuDetilHtml.='<div class="Detil_sku">'.$SKU['sku'].'*'.$SKU['qty'].'</div>';
                $successSku += $SKU['qty_com'];
                if($SKU['qty_com'] == $SKU['qty']){
                    continue;
                }else{
                    $SKU['qty'] = $SKU['qty']-$SKU['qty_com'];
                }
                $skuHtml.='<div class="box_sku">'.$SKU['sku'].'*'.$SKU['qty'].'</div>';
                if($SKU['is_notover']>0){
                    $isOver=false;
                }
            }
            $ebay_id = preg_replace("/\w+\./",'',$ebay_id);
            $skuDetilHtml = "<div id='skuDetil' style='display: none;'><div class='Detil_sku'>订单详情：$ebay_id</div>$skuDetilHtml</div>";
            $boxid=$List[0]['combineid'];

            $k='';
            if($boxid==0){
                $combineid++;
                $boxid=$combineid;
                $k=' noboxid';
                $skuHtml = '';
                $skuDetilHtml = '';
            }else{
                $combineid = $boxid;
            }

            if($isOver){
                $success++;
                $skuHtml = '';
                $html.='<div class="box_item_show box_success" id="box_'.$combineid.'" onclick="showFullSKU(this)">';
            }else{
                $html.='<div class="box_item_show" id="box_'.$combineid.'" onclick="showFullSKU(this)">';
            }

            $html.='<div style="text-align:center" class="boxid'.$k.'">'.$boxid.'</div>';

            $html.=$skuHtml;
            $html.=$skuDetilHtml;
            $html.='</div>';
        }
        $html.= '</div>';
        $html.='<div style="clear: both;margin-top:30px;margin-left:30px;padding-top:38px;"></div>';
        $html.= '<div><h3>绿色表示已经分拣完毕，篮子可以拿走</h3></div>';
        $htmlha ='<div id="box_show"><div style="padding-left: 10px;font-size: 30px;"><span style="color: red">成功:<span id="chenggong">'.$successSku.'</span></span><span style="color: red;margin-left: 10%;">计数:<span id="jishu">'.$success.'</span>/'.$basketCount.'</span></div>';
        $html = $htmlha.$html;
        return $html;
    }

    /**
     * @desc 获取单个订单的详情
     * @Author leo
     */
    public function pickOrderDetil(){
        $PickOrderDetailModel = new PickOrderDetailModel();
        $map['ordersn']   = trim($_POST['ordersn']);
        $map['is_stock']  = 0; // 不缺货
        $map['is_delete'] = 0; // 不删除
        $map['combineid'] = trim($_POST['id']);
        $DetailRs=$PickOrderDetailModel->where($map)->field('ebay_id,sku,qty,location,goods_name,pic,order_addtime,combineid,qty_com,(qty-qty_com) as is_notover')
            ->order('(qty_com-qty) desc')
            ->select();

        if(!$DetailRs){
            $this->ajaxReturn('');
        }

        $detilHtml = "订单详情：{$DetailRs[0]['ebay_id']}<br>";
        foreach($DetailRs as $rs) {
            $detilHtml .="{$rs['sku']}*{$rs['qty']}<br>";
        }
        $this->ajaxReturn($detilHtml);

    }

    /**
     * @desc  获取sku的分拣位信息
     * @Author leo
     */
    public function getSkuPickLocation(){
        $pickRecordModel = new PickRecordModel();
        $map['ordersn']  = trim($_POST['ordersn']);
        $map['sku']      = trim($_POST['s_sku']);

        $skuArr    = $pickRecordModel->where($map)->field('addtime,sku,combineid')->order('addtime desc')->select();
        if (empty($skuArr)) {
            $this->ajaxReturn(false);
        }
        $unSku = [];
        $detilHtml = '';
        foreach ($skuArr as $rs) {
            if(in_array($rs['combineid'],$unSku)){
                continue;
            }else{
                $unSku[] = $rs['combineid'];
            }
            $datetime = date("Y-m-d H:i:s",$rs['addtime']);
            $detilHtml .= "<tr><td>{$datetime}</td><td style='color: red;font-size: 16px'><strong>{$rs['sku']}</strong></td><td style='color: red;font-size: 16px'><strong>{$rs['combineid']}</strong></td></tr>";
        }
        $this->ajaxReturn($detilHtml);
    }
}