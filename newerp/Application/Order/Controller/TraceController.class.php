<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 13:50
 */
namespace Order\Controller;

use Common\Model\EbayOnhandleModel;
use Common\Model\GoodsSaleDetailModel;
use Common\Model\InternalStoreSkuModel;
use Mid\Service\SetGoodsWeightService;
use Order\Model\AbnormalOrderModel;
use Order\Model\EbayAccountModel;
use Order\Model\EbayStoreModel;
use Order\Model\SkuWeightChangeModel;
use Package\Model\ApiCheckskuModel;
use Package\Model\ApiOrderWeightModel;
use Package\Model\EbayGoodsModel;
use Package\Model\OrderPackageModel;
use Package\Model\PackingMaterialModel;
use Package\Model\PickOrderDetailModel;
use Package\Service\MakeBaleService;
use Think\Controller;
use Common\Model\OrderModel;
use Think\Page;

class TraceController extends Controller
{

    /**
     * @var array $topMenus 订单状态
     */
    private $topMenus = [
        '2'    => '已出库',
        '1731' => '回收站',
        '1723' => '可打印',
        '1745' => '等待打印',
        '1724' => '等待扫描',
        '2009' => '待称重',
        '1725' => '缺货订单',
    ];

    /**
     * @var array $topMenus 订单状态
     */
    private $traceStatus = [
        '1'  => 'sku重量修改',
        '7'  => '面单贴漏',
        '13' => '面单贴错',
        '10' => '面单破损',
        '14' => '面单贴皱',
        '5'  => '包错',
        '6'  => '多包',
        '15' => '少包',
        '12' => '包材用错',
        '16' => '包材封口不规范',
        '17'  => '包装破损',
        '11' => '无辅材',
        '2'  => '产品报损',
        '9'  => '缺少配件',
        '3'  => '入库加工错误',
        '4'  => '入库贴标错误',
        '8'  => '退件贴标错误',
    ];

    /**
     * @desc 列表展示
     * @Author leo
     */
    public function index()
    {

        $params             = $_REQUEST;
        $scanNumber         = $params['scanNumber'];
        $orderModel         = new OrderModel();
        $goodSaleDetilModel = new GoodsSaleDetailModel();
        $AbnormalModel      = new AbnormalOrderModel();
        $AccountModel       = new EbayAccountModel();
        $packageModel       = new OrderPackageModel();

        $field = 'a.ebay_id,a.ebay_status,a.ordertype,a.ebay_tracknumber,a.ebay_carrier,a.orderweight,a.ebay_account,a.ebay_warehouse,a.market';
        if ($scanNumber) {
            $map['a.ebay_id|a.ebay_tracknumber'] = $scanNumber;
            $orderData                           = $orderModel->alias('a')->field($field)->where($map)->select();
            if(empty($orderData)){
                $msg = '扫描的订单信息未在系统找到！';
            }
        }

        if ($params['ebay_addtime_start']) {
            $where['b.addtime'][] = ['egt', $params['ebay_addtime_start']];
        } else {
            $params['ebay_addtime_start'] = date("Y-m-d") . " 00:00:00";
            $where['b.addtime'][]         = ['egt', $params['ebay_addtime_start']];
        }

        if ($params['ebay_addtime_end']) {
            $where['b.addtime'][] = ['elt', $params['ebay_addtime_end']];
        } else {
            $params['ebay_addtime_end'] = date("Y-m-d") . " 23:59:59";
            $where['b.addtime'][]       = ['elt', $params['ebay_addtime_end']];

        }

        $count      = $AbnormalModel->alias('b')->where($where)->count('distinct ebay_id');
        $pageServer = new Page($count, 50);

        $AbnormalArr = $AbnormalModel->alias('b')->field($field)->where($where)
            ->join('left join erp_ebay_order as a ON b.ebay_id = a.ebay_id')
            ->group('ebay_id')
            ->order('id desc')
            ->limit($pageServer->firstRow . ',' . $pageServer->listRows)
            ->select();

        if ($orderData) {
            $orderData[0]['rightnow'] = true;
            foreach ($AbnormalArr as $akey => $value) {
                if ($orderData[0]['ebay_id'] == $value['ebay_id']) {
                    unset($AbnormalArr[$akey]);
                }
            }
            $orderData = array_merge($orderData, $AbnormalArr);
        } else {
            $orderData = $AbnormalArr;
        }

        foreach ($orderData as $key => $val) {
            $abnormalData                = $AbnormalModel->field('abnormal,addtime,id as abnormal_id')->where(['ebay_id' => $val['ebay_id']])->order('addtime desc')->find();
            $orderData[$key]['platform'] = $AccountModel->where(['ebay_account' => $val['ebay_account']])->getField('platform');
            $orderData[$key]['abnormal'] = $abnormalData;
            $skuArr                      = $goodSaleDetilModel->alias('a')->field('a.sku,a.qty')->where(['ebay_id' => $val['ebay_id']])->select();
            $skuArr                      = $this->skuInfo($skuArr);
            $orderData[$key]['skuArr']   = $skuArr;
            $orderData[$key]['baleuser'] = $packageModel->field('baleuser,baletime')->where(['ebay_id'=>$val['ebay_id'],'status'=>'1'])->order('id desc')->find();
        }

        $this->assign('data', $orderData);
        $this->assign('request', $params);
        $this->assign('scanNumber', $scanNumber);
        $this->assign('traceStatus', $this->traceStatus);
        $this->assign('topMenus', $this->topMenus);
        $this->assign('store', $this->storeArr());
        $this->assign('show', $pageServer->show());
        $this->assign('msg', $msg);
        $this->assign('toDayCount', $this->toDayCount());
        $this->display();
    }

    /**
     * @desc 当天完成的量
     * @Author leo
     */
    public function toDayCount()
    {
        $AbnormalModel      = new AbnormalOrderModel();
        $params['ebay_addtime_start'] = date("Y-m-d") . " 00:00:00";
        $where['b.addtime'][]         = ['egt', $params['ebay_addtime_start']];
        $params['ebay_addtime_end'] = date("Y-m-d") . " 23:59:59";
        $where['b.addtime'][]       = ['elt', $params['ebay_addtime_end']];
        $where['adduser'] = session('truename');
        $count      = $AbnormalModel->alias('b')->where($where)->count('distinct ebay_id');
        return $count;
    }

    /**
     * @desc 获取sku的信息
     * @Author leo
     */
    public function skuInfo($skuArrs)
    {
        $Goods = new EbayGoodsModel();
        $SkuWeightChangeModel = new SkuWeightChangeModel();
        $storeid = C('CURRENT_STORE_ID');
        $storeArr = C("STORE_NAMES");
        $Onhandle     = new EbayOnhandleModel($storeid);
        $InternalStoreSkuRead     = new InternalStoreSkuModel();

        foreach($skuArrs as $Item){
            $goods_sn=$Item['sku'];
            $c=$Item['qty'];
            $Gs=$Goods->where("goods_sn='$goods_sn' and ebay_user='vipadmin'")->field('goods_sn,isuse')->find();
            if(!empty($Gs)){
                if(!isset($skuSmailSizeArr[$goods_sn])){
                    $skuSmailSizeArr[$goods_sn]=0;
                }
                $skuSmailSizeArr[$goods_sn]+=$c;
            }
        }

        // 第二阶段 将分解好的 sku 获取到什么 计算重量，计算成本等等----
        $skuArr=array(); // 以sku 为索引的 数组  Return 的

        $goodsFeild='a.goods_id,a.goods_name,a.goods_weight as w,a.goods_aribute as attr,a.goods_pic,';
        $goodsFeild.='a.goods_length,a.goods_width,a.goods_height,';
        $goodsFeild.='a.goods_cost,b.id,b.weight,b.price';

        $handleFeild='a.gross_weight as w,a.packingmaterial as pkid,a.average_cost,b.weight,b.price';

        foreach($skuSmailSizeArr as $goods_sn=>$qty) {
            $Gs = $Goods->alias('a')->join('left join ebay_packingmaterial b on a.ebay_packingmaterial=b.model')
                ->field($goodsFeild)
                ->where("a.goods_sn='$goods_sn' and a.ebay_user='vipadmin'")->find();

            $gcost         = $Gs['goods_cost'];     // goods 的成本
            $gweight       = $Gs['w']; // goods 的重量
            $gs_pweight    = $Gs['weight']; // goods 的 包材 重量
            $gs_pcost      = $Gs['price']; // goods 的 包材 chengben
            $goods_name    = $Gs['goods_name'];
            $goods_pic     = $Gs['goods_pic'];

            /**
             *测试人员谭 2017-12-30 11:37:50
             *说明: 这里新+ 一个字段 $storeid 这里主要是为了该死的跨仓单子  sku_storeid  就是sku 实际所在仓库
             *TODO: 我们现在是以1号仓为主仓，才会这样写 以后如果不是 以某一个仓库是主仓的话 事情将变得更加复杂 IT请注意。
             */
            $sku_storeid=$storeid;

            if($storeid == C("CURRENT_STORE_ID")){
                $sku_storeid = $InternalStoreSkuRead->getOrderSkuStore($goods_sn,$storeid);
            }

            $handlesku=$Onhandle->table("ebay_onhandle_{$sku_storeid}")->alias('a')
                ->join('left join ebay_packingmaterial b on a.packingmaterial=b.id')
                ->field($handleFeild)
                ->where("a.goods_sn='$goods_sn'")->find();

            if($handlesku['average_cost']){ //优先获取 来自仓库的平均成本
                $gcost=$handlesku['average_cost'];
            }
            if($handlesku['price']){ // 优先获取 仓库信息的包材成本
                $gs_pcost=$handlesku['price'];
            }

            if($handlesku['weight']){ // 优先获取 仓库信息的 包材重量
                $gs_pweight=$handlesku['weight'];
            }

            if(!isset($storeArr[$storeid])){
                if($handlesku['w']){
                    $gweight=$handlesku['w']/1000; // 毛重的单位居然是 g 要统一
                    $gs_pweight=0;
                }

            }
            // 计算重量的时候 = 105% * (包材重量  +1g  + 产品重量)（仅国内仓）
            if(isset($storeArr[$storeid])){
                $calcWeight=($gweight+$gs_pweight+0.001)*1.05; // 这个sku 的计算重量  kg
                $calcWeight= $qty*$calcWeight;
            }else{
                $calcWeight=$gweight+$gs_pweight; // 这个sku 的计算重量  kg
                $calcWeight= $qty*$calcWeight;
            }
            $calcCost  =$gcost+$gs_pcost;     // 这个sku 的 计算成本

            $issetW = $SkuWeightChangeModel->where([ 'sku' => $goods_sn])->getField('id');
            $skuArr[$goods_sn]['issetW'] = $issetW ? 1 : 0;
            $skuArr[$goods_sn]['qty'] = $qty;
            $skuArr[$goods_sn]['calcWeight'] = $calcWeight;
            $skuArr[$goods_sn]['calcCost'] = $calcCost;
            $skuArr[$goods_sn]['goods_name'] = $goods_name;
            $skuArr[$goods_sn]['weight'] = $gweight;
            $skuArr[$goods_sn]['goods_pic'] = $goods_pic;
        }

        $newSkuArr = [];
        foreach($skuArr as $sku => $skuval){
            $skuval['sku'] = $sku;
            $newSkuArr[] = $skuval;
        }
        return $newSkuArr;
    }

    /**
     * @desc 获取所有仓库信息
     * @Author leo
     */
    public function storeArr()
    {
        $ebayStoreModel = new EbayStoreModel();
        return $ebayStoreModel->getField('id,store_name', true);
    }

    /**
     * @desc 修改sku重量
     * @Author leo
     */
    public function updateWeight()
    {
        $SkuWeightChangeModel = new SkuWeightChangeModel();
        if (IS_AJAX) {
            $addData            = $_POST;
            $GoodsModel = new EbayGoodsModel();
            $setGoodsWeight = new SetGoodsWeightService();
            $sku = $_POST['sku'];
            $weight = $GoodsModel->where("goods_sn='$sku' and ebay_user='vipadmin'")->getField('goods_weight');

            $getData = $setGoodsWeight->setGoodsWeight($addData,$weight);
            if($getData['data'] != 'ok'){
                $return = ['status' => 0, 'msg' => "API请求失败！"];
                $this->ajaxReturn($return);
            }
            $kg_Weight = $addData['weight']/1000;
            $GoodsModel->startTrans();
            $succ = $GoodsModel->where("goods_sn='$sku' and ebay_user='vipadmin'")->save(['goods_weight'=>$kg_Weight]);
            $cha = $kg_Weight - $weight;
            $cha = abs($cha);
            $addData['adduser'] = session('truename');
            $addData['addtime'] = date("Y-m-d H:i:s");
            $addData['note']    = "修改前{$weight}kg，修改后{$kg_Weight}kg，差值{$cha}kg";
            if ($SkuWeightChangeModel->add($addData) && $succ) {
                $GoodsModel->commit();
                $return = ['status' => 1, 'msg' => "修改成功！"];
                $this->ajaxReturn($return);
            } else {
                $GoodsModel->rollback();
                $return = ['status' => 0, 'msg' => "API修改成功，系统内修改失败！"];
                $this->ajaxReturn($return);
            }
        }
        $request = $_REQUEST;
        $dataArr = $SkuWeightChangeModel->where([ 'sku' => $request['sku']])->order('id desc')->select(); //'ebay_id' => $request['ebay_id']
        $this->assign('dataArr', $dataArr);
        $this->assign('request', $request);
        $this->display();
    }


    /**
     * @desc 保存异常订单信息
     * @Author leo
     */
    public function saveAbnormal()
    {
        $return = ['status' => 0, 'msg' => "请求异常！"];
        if (IS_AJAX) {
            $AbnormalModel = new AbnormalOrderModel();
            $find          = $AbnormalModel->where(['ebay_id' => trim($_POST['ebay_id'])])->find();
            if (!empty($find) && $find['abnormal'] == $_POST['traceStatus']) {
                $return = ['status' => 0, 'msg' => "重复提交！"];
                $this->ajaxReturn($return);
            }

            if($find){
                $saveData = [
                    'abnormal' => trim($_POST['traceStatus']),
                    'adduser'  => session('truename'),
                    'addtime'  => date("Y-m-d H:i:s")
                ];
                $arr = $AbnormalModel->where(['id' => $find['id']])->save($saveData);
                if ($arr) {
                    $return = ['status' => 1, 'msg' => "保存成功！"];
                    $this->ajaxReturn($return);
                } else {
                    $return = ['status' => 0, 'msg' => "保存失败！"];
                    $this->ajaxReturn($return);
                }
            }
            $saveData = [
                'ebay_id'  => trim($_POST['ebay_id']),
                'abnormal' => trim($_POST['traceStatus']),
                'adduser'  => session('truename'),
                'addtime'  => date("Y-m-d H:i:s")
            ];

            if ($AbnormalModel->add($saveData)) {
                $return = ['status' => 1, 'msg' => "保存成功！"];
                $this->ajaxReturn($return);
            } else {
                $return = ['status' => 0, 'msg' => "保存失败！"];
                $this->ajaxReturn($return);
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * @desc 查看sku详情
     * @Author leo
     */
    public function checkSkuDetail()
    {
        $goods_sn = I('goods_sn');
        if(!$goods_sn){
            $return = ['status' => 0, 'msg' => "请求异常！"];
            $this->ajaxReturn($return);
        }

        $GoodsModel = new EbayGoodsModel();
        $data = $GoodsModel->field('labletype,ispacking,accessories')->where(['goods_sn'=>$goods_sn])->find();
        if(!$data){
            $return = ['status' => 0, 'msg' => "系统中未找到sku相关信息！"];
            $this->ajaxReturn($return);
        }

        $isPacking = C('isPacking');

        $data['ispacking']    = $isPacking[$data['ispacking']];
        if($data['labletype'] == 1){
            $data['labletype'] = "贴标";
        }
        if($data['labletype'] == 2){
            $data['labletype'] = "加工";
        }

        if($data['accessories']){
            $data['accessories'] = "有辅料";
        }else{
            $data['accessories'] = "无";
        }

        $currStoreId    = C("CURRENT_STORE_ID");
        $handleModel     = new \Task\Model\EbayHandleModel();
        $packageMaterialId = $handleModel->table($handleModel->getPartitionTableName(['store_id' => $currStoreId]))
            ->where(['goods_sn' => $goods_sn])->getField('packingmaterial');
        $MakeSvs = new MakeBaleService();
        $Packingmaterial = $MakeSvs->CachePackingmaterial();
        $data['model']       = $Packingmaterial[$packageMaterialId]['model'];
//        $data['modelid']     = $packageMaterialId;
//        $data['model_note']  = $Packingmaterial[$packageMaterialId]['notes'];

        $return = ['status' => 1, 'data' => $data];
        $this->ajaxReturn($return);
    }


    public function exports(){
        $params             = $_REQUEST;
        $goodSaleDetilModel = new GoodsSaleDetailModel();
        $AbnormalModel      = new AbnormalOrderModel();
        $AccountModel       = new EbayAccountModel();
        $Goods = new EbayGoodsModel();
        $field = 'a.ebay_id,a.ebay_status,a.ordertype,a.ebay_tracknumber,a.ebay_carrier,a.orderweight,a.ebay_account,a.ebay_warehouse,a.market';

        if ($params['ebay_addtime_start']) {
            $where['b.addtime'][] = ['egt', $params['ebay_addtime_start']];
        } else {
            $params['ebay_addtime_start'] = date("Y-m-d") . " 00:00:00";
            $where['b.addtime'][]         = ['egt', $params['ebay_addtime_start']];
        }

        if ($params['ebay_addtime_end']) {
            $where['b.addtime'][] = ['elt', $params['ebay_addtime_end']];
        } else {
            $params['ebay_addtime_end'] = date("Y-m-d") . " 23:59:59";
            $where['b.addtime'][]       = ['elt', $params['ebay_addtime_end']];

        }

        $userIdModel = M('ebay_user');

        if($_REQUEST['type'] == 2){
            $field .=',d.*';
            $where['b.abnormal'] = 1;
            $where['d.addtime'] = $where['b.addtime'];
            $orderData = $AbnormalModel->alias('b')->where($where)->field($field)
                ->join('inner join sku_weight_change as d ON d.abnormal_id=b.id')
                ->join('left join erp_ebay_order as a ON b.ebay_id = a.ebay_id')
                ->order('d.sku,d.addtime asc')
                ->select();
            foreach ($orderData as $key => $val) {
                $sku = $val['sku'];
                $orderData[$key]['goods_name'] = $Goods->where("goods_sn='$sku' and ebay_user='vipadmin'")->getField('goods_name');
                $orderData[$key]['userId']     = $userIdModel->where(['username' => $val['adduser']])->getField('id');
            }
            $fileName = '重量修改导出';
            $table_head = ['仓库','产品名称','SKU','修改前重量','修改后重量','差异重量','异常处理员','异常处理时间',];
            $this->cvsw($table_head,$orderData,$fileName);
        }else{

            $field .=',b.*,c.packinguser,c.addtime as bztime,d.scantime as smtime,d.scan_user';
            $orderData = $AbnormalModel->alias('b')->field($field)->where($where)
                ->join('left join erp_ebay_order as a ON b.ebay_id = a.ebay_id')
                ->join('left join api_checksku as c ON c.ebay_id = a.ebay_id')
                ->join('left join api_orderweight as d ON d.ebay_id = a.ebay_id')
                ->order('b.id desc')
                ->select();

            foreach ($orderData as $key => $val) {
                $user = [];
                if($val['adduser']){
                    $user[]=$val['adduser'];
                }
                if($val['packinguser']){
                    $user[]=$val['packinguser'];
                }
                if($val['scan_user']){
                    $user[]=$val['scan_user'];
                }
                $mapp['username'] = ['in',$user];

                $orderData[$key]['userIdArr']     = $userIdModel->where($mapp)->getField('username,id',true);
                $orderData[$key]['platform'] = $AccountModel->where(['ebay_account'=>$val['ebay_account']])->getField('platform');
                $skuArr                      = $goodSaleDetilModel->alias('a')->field('a.sku,a.qty')->where(['ebay_id' => $val['ebay_id']])->select();
                $skuArr                    = $this->skuInfo($skuArr);

                $orderData[$key]['skuArr'] = $skuArr;
            }
            $fileName = '异常订单导出';
            $table_head = ['仓库','订单状态','订单号','销售平台','销售员','物流渠道','跟踪号','产品名称'
                ,'SKU','重量','库位号','包装员','包装时间','扫描员','扫描时间','异常处理员','异常处理时间','异常原因'];
            $this->cvs($table_head,$orderData,$fileName);
        }

    }

    public function cvs($table_head,$orderData,$fileName)
    {
        $storeArr = $this->storeArr();
        $output = fopen('php://output', 'w') or die("can't open php://output");
        $filename = $fileName . date('Y') . "-" . date('m') . "-" . date('d');
        header("Content-Type: application/csv");
        //判断浏览器，输出双字节文件名不乱码
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        }
        else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.csv"');
        }
        else {
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        }
        //输出Excel列名信息
        foreach ($table_head as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $table_head[$key] = iconv('utf-8', 'gbk', $value);
        }
        fputcsv($output, $table_head);

        $onh = new EbayOnhandleModel(C('CURRENT_STORE_ID'));
        foreach ($orderData as $k => $v) {
            $skuArr = $v['skuArr'];

            $adduser = $v['adduser']."[{$v['userIdArr'][$v['adduser']]}]";
            $bzuser = $v['packinguser']."[{$v['userIdArr'][$v['packinguser']]}]";
            $smuser = $v['scan_user']."[{$v['userIdArr'][$v['scan_user']]}]";

            foreach($skuArr as $svl){
                $location = $onh->where(['goods_sn'=>$svl['sku']])->getField('g_location');
                $arr=[
                    iconv('utf-8', 'gbk', $storeArr[$v['ebay_warehouse']]),
                    iconv('utf-8', 'gbk', $this->topMenus[$v['ebay_status']]),
                    iconv('utf-8', 'gbk', $v['ebay_id']),
                    iconv('utf-8', 'gbk', $v['platform']),
                    iconv('utf-8', 'gbk', $v['market']),
                    iconv('utf-8', 'gbk', $v['ebay_carrier']),
                    iconv('utf-8', 'gbk', $v['ebay_tracknumber']."\t"),
                    iconv('utf-8', 'gbk', $svl['goods_name']),
                    iconv('utf-8', 'gbk', $svl['sku']),
                    iconv('utf-8', 'gbk', $svl['weight']."kg"),
                    iconv('utf-8', 'gbk', $location),
                    iconv('utf-8', 'gbk', $bzuser),
                    iconv('utf-8', 'gbk', date("Y-m-d H:i:s",$v['bztime'])),
                    iconv('utf-8', 'gbk', $smuser),
                    iconv('utf-8', 'gbk', date("Y-m-d H:i:s",$v['smtime'])),
                    iconv('utf-8', 'gbk', $adduser),
                    iconv('utf-8', 'gbk', $v['addtime']),
                    iconv('utf-8', 'gbk', $this->traceStatus[$v['abnormal']]),
                ];
                fputcsv($output, array_values($arr));
            }

        }

        fclose($output) or die("can't close php://output");
        exit;

    }

    public function cvsw($table_head,$orderData,$fileName)
    {

        $storeArr = $this->storeArr();
        $output = fopen('php://output', 'w') or die("can't open php://output");
        $filename = $fileName . date('Y') . "-" . date('m') . "-" . date('d');
        header("Content-Type: application/csv");
        //判断浏览器，输出双字节文件名不乱码
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        }
        else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.csv"');
        }
        else {
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        }

        //输出Excel列名信息
        foreach ($table_head as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $table_head[$key] = iconv('utf-8', 'gbk', $value);
        }
        fputcsv($output, $table_head);

        foreach ($orderData as $k => $v) {
            $user = $v['adduser']."[{$v['userId']}]";
            $node = explode('，',$v['note']);
            $arr=[
                iconv('utf-8', 'gbk', $storeArr[$v['ebay_warehouse']]),
                iconv('utf-8', 'gbk', $v['goods_name']),
                iconv('utf-8', 'gbk', $v['sku']),
                iconv('utf-8', 'gbk', explode('前',$node[0])[1]),
                iconv('utf-8', 'gbk', ($v['weight']/1000)."kg"),
                iconv('utf-8', 'gbk', explode('值',$node[2])[1]),
                iconv('utf-8', 'gbk', $user),
                iconv('utf-8', 'gbk', $v['addtime']),
            ];
            fputcsv($output, array_values($arr));
        }

        fclose($output) or die("can't close php://output");
        exit;
    }

}



