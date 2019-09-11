<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 20:51
 */

namespace Package\Controller;
use Common\Model\CurrentLocationModel;
use Common\Model\CurrentLocationOrderDetailModel;
use Common\Model\ErrorLocationModel;
use Common\Model\ErrorLocationOrderDetailModel;
use Package\Service\OrderCrossStockListService;
use Package\Service\OrderCurrentStockListService;
use Think\Controller;

class ErpInstoreApiController extends Controller{

    private $is_test=0;
   //private $is_test=0;

    /**
    *测试人员谭 2017-12-27 17:44:38
    *说明: 异常集货的有什么SKU 在erp 里面 正在上架的车子上 有共同的需求
     * @link http://local.erpanapi.com/t.php?s=/Package/ErpInstoreApi/getErrorLocationNeedSKU
    */
    function getErrorLocationNeedSKU(){
        $skus    = $_POST['skus'];
        $storeid = $_POST['storeid'];

        $sku_arr = array_filter(explode(',',$skus));

        if(empty($skus)){
            echo json_encode(['status'=>0,'msg'=>'传入的sku有误!']);
            return ;
        }

        $ErrorLocationModel       = new ErrorLocationModel();
        $ErrorLocationOrderDetail = new ErrorLocationOrderDetailModel();
        $ebay_ids                 = $ErrorLocationModel->where(['status' => 2])->getField('ebay_id', true);

        if(empty($ebay_ids)){
            echo json_encode(['status'=>0,'msg'=>'当前没有异常SKU!']);
            return ;
        }

        $map=[];
        $map['ebay_id'] = ['in', $ebay_ids];
        $map['sku'] = ['in', $sku_arr];
        $map['storeid'] = $storeid;
        $map['status'] = 1; //
        $map['_string'] = ' (qty-real_qty) > 0';  // 还需要集货的

        $RR=$ErrorLocationOrderDetail->where($map)->field('sku,(qty-real_qty) as cc')->select();

        $skuArr=[];
        foreach($RR as $List){
            $sku=strtoupper($List['sku']);
            $cc=$List['cc'];
            if(array_key_exists($sku,$skuArr)){
                $skuArr[$sku]=0;
            }
            $skuArr[$sku]+=$cc;
        }

        echo json_encode(['status'=>1,'msg'=>'查询成功!','data'=>$skuArr]);

    }


    /**
     *测试人员杨
     *说明: 本仓异常集货的有什么SKU 在erp 里面 正在上架的车子上 有共同的需求
     * @link http://local.erpanapi.com/t.php?s=/Package/ErpInstoreApi/getCurrentLocationNeedSKU
     */
    function getCurrentLocationNeedSKU(){
        $skus    = $_POST['skus'];
        $storeid = $_POST['storeid'];

        $sku_arr = array_filter(explode(',',$skus));

        if(empty($skus)){
            echo json_encode(['status'=>0,'msg'=>'传入的sku有误!']);
            return ;
        }

        $CurrentLocationModel       = new CurrentLocationModel();
        $CurrentLocationOrderDetail = new CurrentLocationOrderDetailModel();
        $ebay_ids                 = $CurrentLocationModel->where(['status' => 2])->getField('ebay_id', true);

        if(empty($ebay_ids)){
            echo json_encode(['status'=>0,'msg'=>'当前没有异常SKU!']);
            return ;
        }

        $map=[];
        $map['ebay_id'] = ['in', $ebay_ids];
        $map['sku'] = ['in', $sku_arr];
        $map['storeid'] = $storeid;
        $map['status'] = 1; //
        $map['_string'] = ' (qty-real_qty) > 0';  // 还需要集货的

        $RR=$CurrentLocationOrderDetail->where($map)->field('sku,(qty-real_qty) as cc')->select();

        $skuArr=[];
        foreach($RR as $List){
            $sku=strtoupper($List['sku']);
            $cc=$List['cc'];
            if(array_key_exists($sku,$skuArr)){
                $skuArr[$sku]=0;
            }
            $skuArr[$sku]+=$cc;
        }

        echo json_encode(['status'=>1,'msg'=>'查询成功!','data'=>$skuArr]);

    }



/**
*测试人员谭 2018-01-03 22:00:23
*说明: 异常看板 需要的sku
*/
    function getSKUListByCarnumber(){
        $carnumber=$_POST['carnumber'];

        if(''==$carnumber){
            echo json_encode(['status'=>0,'msg'=>'车子编号为空']);
            return '';
        }

        $skus=$this->getYcLocationNeedSKU();

        $skus_temp=[];
        $skus_temp_with_qty=[];
        foreach($skus as $list){
            $sku=strtoupper($list['sku']);
            $skus_temp[]=$sku;
            $skus_temp_with_qty[$sku]=$list['cc'];  // 数量
        }

        $img_url=C('ONLINE_PIC_URL').'/images/';
        $url=C('ONLINE_PIC_URL').'/t.php?s=/Purchase/PurchaseApi/getSkuListBySkuAndCarnumber';
        if($this->is_test){
            $url='http://erp.wst'.'/t.php?s=/Purchase/PurchaseApi/getSkuListBySkuAndCarnumber';
        }

        $rs=curl_post($url,array('skus'=>json_encode($skus_temp),'carnumber'=>$carnumber));

        $rs=json_decode($rs,true);
        //echo $rs;
        $data=$rs['data'];

        $html='<div style="color:#911;margin:10px 0;">注意:看板对您车上的sku起到提示作用不会即时更新!</div>';
        $html.='<table class="sku_list">';
        $html.='<tr><td>SKU</td><td>车上数量/需要数量</td><td>品名</td></tr>';
        foreach($data as $List){
            $sku        = $List['sku'];
            $name       = $List['name'];
            $img        = $List['img'];
            $count      = $List['count'];
            $need_count = $skus_temp_with_qty[$sku];

            $html.='<tr><td><p class="fb f14" style="margin:0;">'.$sku.'</p>';
            $html.='<p class="mg0"><img style="height: 60px;width:60px;" src="'.$img_url.$img.'"/></p></td>';
            $html.='<td><b>'.$count.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$need_count.'</b></td>';
            $html.='<td><p class="mt3" style="width:120px;">'.$name.'</p></td>';
            $html.='</tr>';
        }
        $html.='</table>';

        $rs['data']=$html;
        echo json_encode($rs);

    }


    /**
     *测试人员杨
     *说明: 异常看板 需要的sku
     */
    function getCurrentSKUListByCarnumber(){
        $carnumber=$_POST['carnumber'];

        if(''==$carnumber){
            echo json_encode(['status'=>0,'msg'=>'车子编号为空']);
            return '';
        }

        $skus=$this->getCurrentYcLocationNeedSKU();

        $skus_temp=[];
        $skus_temp_with_qty=[];
        foreach($skus as $list){
            $sku=strtoupper($list['sku']);
            $skus_temp[]=$sku;
            $skus_temp_with_qty[$sku]=$list['cc'];  // 数量
        }

        $img_url=C('ONLINE_PIC_URL').'/images/';
        $url=C('ONLINE_PIC_URL').'/t.php?s=/Purchase/PurchaseApi/getSkuListBySkuAndCarnumber';
        if($this->is_test){
            $url='http://erp.wst'.'/t.php?s=/Purchase/PurchaseApi/getSkuListBySkuAndCarnumber';
        }

        $rs=curl_post($url,array('skus'=>json_encode($skus_temp),'carnumber'=>$carnumber));

        $rs=json_decode($rs,true);
        //echo $rs;
        $data=$rs['data'];

        $html='<div style="color:#911;margin:10px 0;">注意:看板对您车上的sku起到提示作用不会即时更新!</div>';
        $html.='<table class="sku_list">';
        $html.='<tr><td>SKU</td><td>车上数量/需要数量</td><td>品名</td></tr>';
        foreach($data as $List){
            $sku        = $List['sku'];
            $name       = $List['name'];
            $img        = $List['img'];
            $count      = $List['count'];
            $need_count = $skus_temp_with_qty[$sku];

            $html.='<tr><td><p class="fb f14" style="margin:0;">'.$sku.'</p>';
            $html.='<p class="mg0"><img style="height: 60px;width:60px;" src="'.$img_url.$img.'"/></p></td>';
            $html.='<td><b>'.$count.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$need_count.'</b></td>';
            $html.='<td><p class="mt3" style="width:120px;">'.$name.'</p></td>';
            $html.='</tr>';
        }
        $html.='</table>';

        $rs['data']=$html;
        echo json_encode($rs);

    }


    // 当前的 跨仓异常中所需要的sku  和 正好在上架的车子里面的sku 是否有共同的sku  返回车子列表
    function getCarNumberBySKU(){

        $skus=$this->getYcLocationNeedSKU();

        $storeid=C('CURRENT_STORE_ID');

        $skus_temp=[];

        foreach($skus as $list){
            $skus_temp[]=$list['sku'];

        }

        if(count($skus_temp)==0){
            echo json_encode(['status'=>0,'msg'=>'当前仓库没有需要集货的SKU']);
            return '';
        }


        $url=C('ONLINE_PIC_URL').'/t.php?s=/Purchase/PurchaseApi/getCarnumberBySKUs';
        if($this->is_test){
            $url='http://erp.wst'.'/t.php?s=/Purchase/PurchaseApi/getCarnumberBySKUs';
        }

        $rs=curl_post($url,array('skus'=>json_encode($skus_temp),'storeid'=>$storeid));

        echo $rs;

    }



    /**
    *测试人员谭 2018-01-02 17:14:53
    *说明: 跨仓异常里面 一共有多少sku是需要的 本仓
    */
    function getYcLocationNeedSKU($storeid=0){

        if($storeid==0){
            $storeid=C('CURRENT_STORE_ID');
        }


        $ErrorLocationModel       = new ErrorLocationModel();
        $ErrorLocationOrderDetail = new ErrorLocationOrderDetailModel();
        $ebay_ids                 = $ErrorLocationModel->where(['status' => 2])->getField('ebay_id', true);

        $map=[];
        $map['ebay_id'] = ['in', $ebay_ids];
        $map['storeid'] = $storeid;
        $map['status'] = 1;
        $map['_string'] = ' (qty-real_qty) > 0';  // 还需要集货的

        $RR=$ErrorLocationOrderDetail->where($map)->field('sku,(qty-real_qty) as cc')->select();

        return $RR;

    }



    /**
     * 测试人员杨
     * 当前的 本仓异常中所需要的sku  和 正好在上架的车子里面的sku 是否有共同的sku  返回车子列表
     */
    function getCurrentCarNumberBySKU(){

        $skus=$this->getCurrentYcLocationNeedSKU();

        $storeid=C('CURRENT_STORE_ID');

        $skus_temp=[];

        foreach($skus as $list){
            $skus_temp[]=$list['sku'];

        }

        if(count($skus_temp)==0){
            echo json_encode(['status'=>0,'msg'=>'当前仓库没有需要集货的SKU']);
            return '';
        }

        $url=C('ONLINE_PIC_URL').'/t.php?s=/Purchase/PurchaseApi/getCarnumberBySKUs';
        if($this->is_test){
            $url='http://erp.wst'.'/t.php?s=/Purchase/PurchaseApi/getCarnumberBySKUs';
        }

        $rs=curl_post($url,array('skus'=>json_encode($skus_temp),'storeid'=>$storeid));

        echo $rs;

    }

    /**
     *测试人员杨
     *说明: 本仓异常里面 一共有多少sku是需要的 本仓
     */
    function getCurrentYcLocationNeedSKU($storeId=0){

        if($storeId == 0){
            $storeId = C('CURRENT_STORE_ID');
        }

        $CurrentLocationModel       = new CurrentLocationModel();
        $CurrentLocationOrderDetail = new CurrentLocationOrderDetailModel();
        $ebay_ids                 = $CurrentLocationModel->where(['status' => 2])->getField('ebay_id', true);

        $map=[];
        if($ebay_ids){
            $map['ebay_id'] = ['in', $ebay_ids];
        }
        $map['storeid'] = $storeId;
        $map['status'] = 1;
        $map['_string'] = ' (qty-real_qty) > 0';  // 还需要集货的

        $RR = $CurrentLocationOrderDetail->where($map)->field('sku,(qty-real_qty) as cc')->select();

        return $RR;

    }

    /**
     * 说明: 通过api获取可用库存
     * @param int $storeId 仓库id
     * @param array $skuArr sku数组
     * @return array $rs
     * @Author 测试人员杨
     */
    public function getUsableSkuByApi($storeId,$skuArr){
        $url = C('ONLINE_PIC_URL').'/t.php?s=/Purchase/PurchaseApi/getUsableSku';
        $rs = curl_post($url,array('skuArr'=>json_encode($skuArr),'storeId'=>$storeId));
        return $rs;
    }

    /**
     * 说明: 查询本仓和跨仓集货有没有所需库存 如果有进行短信提示
     * @Author 测试人员杨
     */
    public function messageHintBySku(){
        if(!IS_CLI){
            echo 'Must run in cli'."\n\n";
            die();
        }

        $phoneNumber = '13632708707'; //手机号码 易明
        $url = "http://erp.spocoo.com/t.php?s=Messagecenter/Sms/SendWmsStockMsg";

        //判断本仓异常集货
        $storeId = C('CURRENT_STORE_ID') ? C('CURRENT_STORE_ID') : 196;
        $orderCurrentStockService = new OrderCurrentStockListService();
        $orderInfo = $orderCurrentStockService->getOrderInfo($storeId);
        if($orderInfo){
            $count = $this->checkSku($orderInfo,$storeId);
            if($count>0){
                curl_post($url,array('type_str'=>'1号仓库本仓看板','count'=>$count,'phonenumber'=>$phoneNumber));
            }
        }

        //判判断跨仓异常集货--1号仓
        $storeId = C('CURRENT_STORE_ID');
        $orderCrossStockService = new OrderCrossStockListService();
        $orderInfo               = $orderCrossStockService->getOrderInfo($storeId);
        if($orderInfo){
            $count = $this->checkSku($orderInfo,$storeId);
            if($count>0){
                curl_post($url,array('type_str'=>"1号仓库跨仓看板",'count'=>$count,'phonenumber'=>$phoneNumber));
            }
        }

        //断跨仓异常集货--2号仓
        $storeId = C("MERGE_STORE_ID");
        $orderInfo               = $orderCrossStockService->getOrderInfo($storeId);
        if($orderInfo){
            $count = $this->checkSku($orderInfo,$storeId);
            if($count>0){
                curl_post($url,array('type_str'=>'2号仓库跨仓看板','count'=>$count,'phonenumber'=>$phoneNumber));
            }
        }
    }

    /**
     * 说明: 检查库存 返回可集货sku数量
     * @param array $orderInfo
     * @return  int $count
     * @Author 测试人员杨
     */
    public function checkSku($orderInfo){
        $count = 0;
        $skuArr = array_column($orderInfo,'sku'); $storeId = $orderInfo[0]['storeid'];
        $skuArray = $this->getUsableSkuByApi($storeId,$skuArr);
        $result = json_decode($skuArray,true);
        foreach($orderInfo as $key=>$val) {
            foreach ($result['data'] as $k => $v) {
                if($v['sku'] == $val['sku'] && $v['count']>($val['qty']-$val['real_qty'])){
                    $count++;
                }
            }
        }
        return $count;
    }
}
