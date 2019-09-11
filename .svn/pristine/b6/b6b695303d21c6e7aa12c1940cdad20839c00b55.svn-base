<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/30
 * Time: 15:36
 */
namespace Api\Controller;

use Api\Model\OrderWeightModel;
use Common\Model\EbayOrderExtModel;
use Order\Model\EbayOrderModel;
use Order\Service\OrderInterceptService;
use Package\Model\ApiCheckskuModel;
use Package\Model\TopMenuModel;

class OrderController
{
    /**
     * 取消订单接口   主要做两件事 ① 将订单转入回收站(ebay_status 1731)   ②  记录取消日志
     * 这里面还有两个问题需要处理 一、接口的验证问题   二、当erp那边需要更新的订单id，wms这边没有更新过来怎么办？
     * @author 王模刚
     * @since  2017 10 31
     * @link   http://local.erpanapi.com/t.php?s=/Api/Order/cancelOrders
     */
    public function cancelOrders() {
        $request             = $_REQUEST;
        $ebay_ids            = $request['ebay_id'];
        $ebay_ids            = explode(',', $ebay_ids);
        $user                = $request['user'];
        $content             = $request['content'];
        $type                = $request['type'];
        $newStatus           = $request['new_status'];
        $sonStatus           = $request['son_status'];
        $token               = $request['token'];
        $orderModel          = new \Common\Model\OrderModel();
        $cancleOrderLogModel = new \Api\Model\CancleOrderLogModel();
        $interceptModel      = new \Api\Model\OrderInterceptRecordModel();
        $topMenuModel        = new TopMenuModel();
        $topMenus            = $topMenuModel->getField('id,name');
        $orderWeightModel    = new OrderWeightModel();

        $orderInterceptServer = new OrderInterceptService();

        $webRoot      = dirname(dirname(THINK_PATH));

        $file = $webRoot . '/log/cancel/' . date('YmdH') . '.txt';

        $log = '内容:'.print_r($ebay_ids,true). '---'.$user.'---'. date('ymdHis') . "\n";
        writeFile($file, $log);

        if ('fgrdkjgrnszaqasdfgfdderw' != $token) {
            echo json_encode(['code' => 201, 'ret' => '必传基本参数错误']);
            return;
        }
//        $ip = $_SERVER["REMOTE_ADDR"];
//        if($ip != '39.108.232.133'){
//            $res['code'] = 102;
//            $res['ret'] = 'wrong 2！' .$ip;
//            echo json_encode($res);
//            return;
//        }
        //可打印 等待打印 等待扫描
        $res = [];
        if (empty($ebay_ids) || empty($user) || empty($content) || empty($type)) {
            $res['code'] = 101;
            $res['ret']  = '参数错误！';
            echo json_encode($res);
            return;
        }
        if (!in_array($type, array(1, 2))) {      //type: 1取消，2拦截
            $res['code'] = 102;
            $res['ret']  = '参数错误2！';
            echo json_encode($res);
            return;
        }
        $allowCancelStatus = [2];
        $orders            = $orderModel->where(['ebay_id' => ['in', $ebay_ids]])->select();
        if(empty($orders)){
            $res['code'] = 103;
            $res['ret']  = 'wms没有找到该订单！';
            echo json_encode($res);
            return;
        }

        $successIds=[];

        $allResults=[];

        foreach ($orders as $orderInfo) {
            if (in_array($orderInfo['ebay_status'],$allowCancelStatus)) {
                $allResults[]=  '订单:' . $orderInfo['ebay_id'] .'【'.$topMenus[$orderInfo['ebay_status']].'】无法拦截!';
                continue;
            }
            if (!$orderInfo) {
                $allResults[] = '订单:' . $orderInfo['ebay_id'] .'在wms系统中不存在！' ;
                continue;
            };
            //是否已经添加过
            $exist    = $interceptModel->isExistNotControlInterceptInfo($orderInfo['ebay_id']);
            $isWeight = $orderWeightModel->where(['ebay_id' => $orderInfo['ebay_id']])->find();
            if (!empty($isWeight)) {
                $allResults[] = '订单:' . $orderInfo['ebay_id'] . '已称重,无法拦截!称重时间' . date('Y-m-d H:i:s', $isWeight['scantime']);
                continue;
            }

            $ret4 = $orderModel->where(['ebay_id' => ['eq', $orderInfo['ebay_id']],'ebay_status'=>['in',[2009,1723,1724,1728,1745]]])->limit(1)->save(['ebay_status'=>1731,'ebay_tracknumber'=>'']);
            if(!$ret4){
                $allResults[] = '订单:' . $orderInfo['ebay_id'] . '仓库修改状态失败！';
                continue;
            }
            if (!$exist) {
                //是否已经称重
                $insetId = $interceptModel->add([
                    'ebay_id'       => $orderInfo['ebay_id'],
                    'add_user'      => $user,
                    'add_time'      => date('Y-m-d H:i:s'),
                    'update_user'   => '',
                    'type'          => $type,
                    'is_to_erp'     => 0,
                    'old_status'    => $orderInfo['ebay_status'],
                    'new_status'    => $newStatus,
                    'son_status'    => $sonStatus ?: 0,
                    'status'        => 0,
                    'update_reason' => $content
                ]);
                if (!$insetId) {
                    $allResults[]= '订单'.$orderInfo['ebay_id'] . '提交拦截失败!';
                }


                $Rs = $orderInterceptServer->controlIntercept($orderInfo['ebay_id']);

                $log = $orderInfo['ebay_id'] . '---' .print_r($Rs,true) . date('ymdHis') . "\n";
                writeFile($file, $log);



                if(!isset($Rs['status']) || $Rs['status']!=1){
                    $allResults[]= '订单'.$orderInfo['ebay_id'] . ' 仓库拦截成功！但是反馈ERP拦截失败，请联系IT!';
                }else{
                    $successIds[] = $orderInfo['ebay_id'];
                }

            } else {
                $allResults[] =  '订单'.$orderInfo['ebay_id'].'已经添加过了，正在处理中!';
            }
        }
        echo json_encode(['success_ids'=>implode(',',$successIds),'ret'=>implode('<br>'."\n",$allResults)]);
        return;
    }


    /**
     * 等待打印订单列表
     * @author  Rex
     * @since   2017-12-04 14:13:00
     * @since   http://local.erpcnapi.com/t.php?s=/Api/Order/getWaitPrintOrderList
     */
    public function getWaitPrintOrderList() {
        $ret = array('ret'=>'-100','count'=>'0', 'data'=>array());
        $ebayOrderModel = new \Order\Model\EbayOrderModel();
        $orderList = $ebayOrderModel->getWaitPrintOrderList();
        if (count($orderList) > 1) {
            $ret['ret']= '100';
            $ret['count'] = count($orderList);
            $ret['data'] = $orderList;
        }
        echo json_encode($ret);
    }


    /**
    *   订单信息回传
    */
    public function synchronization(){
        $post  = I('post.');
        if($post['sigin'] <> '@12458455#!edfdcx$'){
            exit(json_encode(['status'=>0]));
        }
        unset($post['sigin']);
        $ebayOrderModel = new \Order\Model\EbayOrderModel();
        $where['ebay_id'] = ["in",$post];
        $field = "ebay_id,ebay_status as wms_ebay_status,ebay_tracknumber as wms_tracknumber,pxorderid as  wms_pxorderid";
        $list = $ebayOrderModel->where($where)->field($field)->select();
         echo  json_encode($list);
         exit;
        
    }

    //g更新跟踪号
    public function synsynTracknum(){
        $request = $_REQUEST;
        if($request['sigin'] <> '@12458455#!edfdcx$'){
            exit(json_encode(['status'=>'-1','msg'=>'参数错误']));
        }
        unset($request['sigin']);
         $error = ''; $success = '';
         $ebayOrderModel = new \Order\Model\EbayOrderModel();
         $MidebayOrderModel = new \Mid\Model\MidEbayOrderModel();
         foreach($request as $key => $val){
            if(empty($val['ebay_tracknumber'])){
                 $error .=   $val['ebay_id'].',';
                 continue;
            }
            if($val['is_to_wms_1'] == 0){
                 $error .=   $val['ebay_id'].',';
                 continue;
            }
            $save['ebay_tracknumber'] = $val['ebay_tracknumber'];
            $save['pxorderid']  = $val['pxorderid'];
            $save['ebay_carrier']  = $val['ebay_carrier'];

            $r = $ebayOrderModel->where(['ebay_id'=>$val['ebay_id']])->save($save);
            $MidebayOrderModel->where(['ebay_id'=>$val['ebay_id']])->save($save);

            if($r === false)
                $error .=   $val['ebay_id'].',';
            else
                $success .= $val['ebay_id'].',';             
         }
         echo  json_encode(['success'=>trim($success,','),'error'=> trim($error,',')]);
         exit;
    }

     //同步状态   
    public function wmsOrderStatus(){
        $request = $_REQUEST;
        if($request['sigin'] <> '@12458455#!edfdcx$'){
            exit(json_encode(['status'=>'-1','msg'=>'参数错误']));
        }

        $saveStatus = $request['status'];
        unset($request['sigin']);
        unset($request['status']);
        $status = ['1724','1745','1722','1723','2009','2018','2','1731'];
        $error = ''; $success = '';
        $ebayOrderModel = new \Order\Model\EbayOrderModel();
        foreach($request as $k => $v){
            if(!in_array($v['ebay_status'], $status)){
                 $error .=   $v['ebay_id'];   //状态不合法的区间
                 continue;
            }

            //没同步
            if($v['is_to_wms_1'] == 0){
                 $error .=   $v['ebay_id'].',';
                 continue;
            }
           $row = $ebayOrderModel->where(['ebay_id'=>$v['ebay_id']])->setField('ebay_status',$saveStatus);
           if($row === false)
                $error .=   $v['ebay_id'].',';
            else
                $success .= $v['ebay_id'].',';   

         }   
        
        echo  json_encode(['success'=>trim($success,','),'error'=> trim($error,',')]);
        exit;       
    }


    /**
     * @desc  更新出库扫描列表的状态
     * @Author leo
     */
    public function updateCheckSku()
    {
        //$post  = I('post.');
        $post  = $_POST;
        $sigin =  md5('@12458455#!edfdcx$'.$post['action']);;
        if($post['sigin'] <> $sigin){
            exit(json_encode(['status'=>0]));
        }
        unset($post['sigin']);

        $rsData = $post['data'];
        $ebayIdData = $post['ebay_id'];
        if(empty($rsData) && empty($ebayIdData)){
            exit(json_encode(['status'=>0]));
        }

        if($rsData){
            $where['id'] = ["in",$rsData];
        }else{
            if(!is_array($ebayIdData)){
                $ebayIdData = explode(',',trim($ebayIdData,','));
            }
            $where['ebay_id'] = ["in",$ebayIdData];
        }

        $apiCheckModel = new ApiCheckskuModel();
        $orderModel = new EbayOrderModel();
        $orderExtModel = new EbayOrderExtModel();

        $ebayIdArr=$apiCheckModel->field('ebay_id')->where($where)->select();
        $idArr = [];
        foreach ($ebayIdArr as $Eid) {
            $ebay_id = $Eid['ebay_id'];
            $order_status = $orderModel->where(['ebay_id' => $ebay_id])->getField('ebay_status');
            if($order_status == '2009'){
                $save = true;
            }else{
                $save =  $orderModel->where(['ebay_id' => $ebay_id,'ebay_status'=>['in',[1723,1745,1724]]])->limit(1)->save(['ebay_status' => '2009']);
            }

            if(!$save){
                continue;
            }
            $rs = $apiCheckModel->where(['ebay_id' => $ebay_id])->limit(1)->save(['status'=>'2']);
            if($rs === false){
                continue;
            }

            $idArr[] = $ebay_id;
            //订单状态修改,更新修改时间
            $ext_info = $orderExtModel->where(['ebay_id' => $ebay_id])->find();
            $date     = date('Y-m-d H:i:s');
            if ($ext_info) {
                $orderExtModel->where(['ebay_id' => $ebay_id])->save([
                    'to_time_2009'  => $date,
                    'w_update_time' => $date,
                ]);
            } else {
                $orderExtModel->add([
                    'ebay_id'       => $ebay_id,
                    'to_time_2009'  => $date,
                    'w_update_time' => $date,
                    'w_add_time'    => $date
                ]);
            }
        }

        echo  json_encode(['status'=>1,'msg'=> 'api_check 更新成功','ebay_id_arr'=>$idArr]);
        exit;
    }

    /**
     * @desc   根据包裹条件查询订单
     * @author mina
     * @param  int $companyId 公司ID
     * @param  string $carrier 渠道名称
     * @param  date $stime 开始时间 Y-m-d H:i:s
     * @param  date $etime 结束时间 Y-m-d H:i:s
     * @return json
     */
    public function packageOrder()
    {
        $post  = $_POST;
        $sign  = '@12458455#!edfdcx$';
        if($post['sign'] != $sign)
        {
            exit($this->_back(0, '签名错误。'));
        }
        unset($post['sign']);
        if(empty($post['carrier']) && empty($post['companyId']))
        {
            exit($this->_back(0, '揽收公司ID或者渠道名称不能为空。'));
        }
        if(empty($post['stime']))
        {
            exit($this->_back(0, '开始时间不能为空。'));
        }
        $post['stime'] = strtotime($post['stime']);
        $post['etime'] = empty($post['etime']) ? time() : strtotime($post['etime']);

        $service = new \Api\Service\Package();
        $result = $service->getPackageOrder($post);
        if($result['status'])
        {
            exit($this->_back(1, '', $result['data']));
        }
        else
        {
            exit($this->_back(0, $result['msg']));
        }
    }

    /**
     * @desc  
     * @author mina
     * @param  
     * @return
     */
    private function _back($status = 0, $msg = '', $data)
    {
        $return = ['status' => $status, 'msg' => $msg];
        if(!empty($data))
        {
            $return['data'] = $data;
        }
        return json_encode($return);
    }
}