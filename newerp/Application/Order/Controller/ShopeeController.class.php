<?php

namespace Order\Controller;

use Common\Api\Shopee\LoadOrdersApi;
use Common\Api\Shopee\MarketOrdersApi;
use Common\Model\OrderModel;
use Order\Model\EbayAccountModel;
use Order\Model\EbayOrderModel;
use Order\Model\EbayOrderDetailModel;
use Order\Service\JoomService;
use Think\Controller;

class ShopeeController extends Controller{
    private $OrderModel;

    public $pagesize=0;
    public function _initialize() {
        $this->pagesize=$_SESSION['pagesize']?$_SESSION['pagesize']:50;
    }

    public function index() {
//        echo 'linio';
        $requestkey     = I('key');
        $map['shopee_secret']=['neq',''];
        if(!empty($requestkey)){
            $map['ebay_account']=['like','%'.$requestkey.'%'];
        }
        $EbayAccount=new EbayAccountModel();
        $total=$EbayAccount->getCount($map);
        $perpage = $_REQUEST['shownums'] ? $_REQUEST['shownums'] : $this->pagesize;
        $parameter=['key'=>$requestkey];
        $Page       = new \Think\Page($total, $perpage,$parameter);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('header','<li class="disabled"><a>&nbsp;Total: %TOTAL_ROW% &nbsp;</a></li>');
        $show = $Page->show();
        //$order=$sortfield.' '.$sorttype;
        $order=' ebay_account asc';
        $limit = "$Page->firstRow" . ',' . "$Page->listRows";#echo $sql;
        $data=$EbayAccount->getData($map,$limit,$order);
//        echo $EbayAccount->getLastSql();
//        var_dump($data);die;
        $this->assign('mainData', $data);
        $this->assign('show', $show);
        $this->assign('perpage', $perpage);
        $this->display('shopeeaccount');
    }

    public function viewAddaccount() {
        $this->display('addaccount');
    }

    public function bindingaccount() {
        $account = I('account');
        $partnerid   =   I('partner_id');
        $shopid = I('shopid');
        $secret = I('secret');

        $d=[
            'partnerid'=>$partnerid,
            'shopid'=>$shopid,
            'secret'=>$secret
        ];
        $d=serialize($d);

        $save = [];
        $save['shopee_secret'] = $d;
        $save['ebay_account'] = $account;
        $save['ebay_user'] = 'vipadmin';
        $save['platform'] = 'shopee';

        $EbayAccount=new EbayAccountModel();
        $rr=$EbayAccount->saveShopeeAccount($save);
        if($rr!==false){
            echo json_encode(['status'=>1,'msg'=>'绑定成功!']);die();
        }
        echo json_encode(['status'=>0,'msg'=>'绑定失败了!']);
        die();
    }

    public function loadOrders() {
        set_time_limit(0);
        // 判断下载单个用户的订单信息还是所有用户的订单信息
        $account = I('account');
        echo '<meta charset="UTF-8">';
        $Accounts = array(
            ['ebay_account' => $account]
        );

        if($account == 'all'){
            $EbayAccount= new EbayAccountModel();
            $map['shopee_secret']=['neq',''];
            $Accounts=$EbayAccount->where($map)->field('ebay_account')->select();
        }

        $ShopeeApi = new LoadOrdersApi();
        // 如果携带了pertime参数，就是提供定时任务，拉取pertime小时内的订单信息
        $pertime = intval(I('pertime'));
        if($pertime && $pertime>0){
            foreach($Accounts as $list){
                $ShopeeApi->loadOrders($list['ebay_account'],$pertime);
            }
        }else{
            foreach($Accounts as $list){
                $ShopeeApi->loadOrders($list['ebay_account']);
            }
        }

    }

    // 手动标记订单发货
    public function market() {
        set_time_limit(0);
        echo '<meta charset="utf-8"/>';
        $ordersn=I('ordersn');
        $ordersn=preg_replace('/[^,0-9]/','',$ordersn);

        $ordersn=trim($ordersn);
        $ordersn=explode(',',$ordersn);
        $MarketApi = new MarketOrdersApi();
        foreach($ordersn as $ebay_id){
            $MarketApi->shopeeMarket($ebay_id);
        }
    }

    /**
     * 圓通物流打印面單獲取訂單詳情
     */
    public function getSingleOrderInfo($account,$order_id) {
        $orderModel = new \Order\Model\EbayOrderModel();
        $shopeeApi  = new LoadOrdersApi();

        $orderInfo  = $orderModel->where("ebay_id='$order_id'")->find();
        if(!$orderInfo){ return false; }
        $ordersn = str_replace('-'.$account,'',$orderInfo['ebay_ordersn']);

        // 获取反序列化shopee_secret中的信息
        $shopeeInfo = $shopeeApi->getShopeeInfo($account);
        $partnerid  = intval($shopeeInfo['partnerid']);
        $shopid     = intval($shopeeInfo['shopid']);
        $secret     = $shopeeInfo['secret'];
        $orderDetailData = $shopeeApi->getOrderDetail($partnerid, $shopid, $secret, array($ordersn));
        return $orderDetailData;
    }

    /**
     * 中间表分配跟踪号 并且同步跟踪号到ebay_order表
     */
    public function allocateTracknumber(){
        echo '<meta charset="UTF-8">';

        $ordersn=I('ordersn');
        $ordersn=preg_replace('/[^,0-9]/','',$ordersn);
        $ordersn=trim($ordersn);
        $ordersn=explode(',',$ordersn);

        // 实例化ebay_order表
        if(!$this->OrderModel){
            $this->OrderModel = new \Order\Model\EbayOrderModel();
        }
        // 实例化shopee关联中间表
        $shopeeToLweModel = new \Order\Model\ShopeeToLweTrackModel();
        // shopee所有账号
        $accountModel = new \Order\Model\EbayAccountModel();
        $shopeeAccountList = $accountModel
            ->where("shopee_secret <> ''")
            ->getField('ebay_account',true);

        // 将ebay_id存入中间表，还要把ebay_order表同步数据
        foreach($ordersn as $k => $v){
            $orderInfo = $this->OrderModel
                ->where("ebay_id=$v")
                ->field('ebay_id,ebay_ordersn,ebay_carrier,ebay_tracknumber')
                ->find();

            $account = explode('-', $orderInfo['ebay_ordersn'])[1]; // 账户名
            $country = explode('_', $account)[1]; //当前账户所属国家

            //判断是不是shopee平台的订单
            if(!in_array($account, $shopeeAccountList)){
                echo '【订单编号】:'.$v.'; 不是shopee订单<br />'.PHP_EOL;
                continue;
            }
            // 判断已有跟踪号的，不需要分配
            if($orderInfo['ebay_tracknumber']){
                echo '【订单编号】:'.$v.'; 已有跟踪号，不用分配<br />'.PHP_EOL;
                continue;
            }
            // 判断物流方式不为LWE，不准分配
            if($orderInfo['ebay_carrier'] !== 'LWE'){
                echo '【订单编号】:'.$v.'; 物流方式不为LWE,不予分配<br />'.PHP_EOL;
                continue;
            }
            // 将ebay_id存进中间表
            $where = array(
                '_string' => "ebay_id is NULL",
                'is_id' => 0
            );
            if($country === 'id'){
                $where['is_id'] = 1;
            }
            $trackInfo = $shopeeToLweModel->where($where)->limit(1)->field('id,ebay_tracknumber')->find();
            // 更新中间表
            $id = $trackInfo['id'];
            $change1 = array(
                'id' => $id,
                'ebay_id' => $v,
                'addtime' => time(),
            );
            $result = $shopeeToLweModel->where("id=$id")->setField($change1);
            if(!$result){
                $log = '【订单编号】:'.$v.'分配跟踪号失败！【操作时间】:'.date('Y-m-d H:i:s').'<br/>'.PHP_EOL;
                $file = date('Ymd').'_allocate.txt';
                $this->writeLog($log,$file);
                $this->echoMessage($log, 0);continue;
            }else{
                $log = '【订单编号】:'.$v.'分配跟踪号成功！【操作时间】:'.date('Y-m-d H:i:s').'<br/>'.PHP_EOL;

                // 同步数据到ebay_order表
                $change2 = array(
                    'ebay_id' => $v,
                    'ebay_tracknumber' => $trackInfo['ebay_tracknumber'],
//                    'ebay_markettime' => time(),
//                    'ShippedTime' => time()
                );
                $res = $this->OrderModel->where("ebay_id=$v")->setField($change2);
                if(!$res){
                    $log .= '【订单编号】:'.$v.'分配后同步ebay_order表失败！【操作时间】:'.date('Y-m-d H:i:s').'<br/>'.PHP_EOL;
                }else{
                    $log .= '【订单编号】:'.$v.'分配后同步ebay_order表成功！【操作时间】:'.date('Y-m-d H:i:s').'<br/>'.PHP_EOL;
                }
                $file = date('Ymd').'_allocate.txt';
                $this->writeLog($log,$file);
                $this->echoMessage($log);
            }
        }

    }

    /**
     * 保存lwe 2万3千条跟踪号 进中间表的任务
     */
    public function saveTracknumberTask(){
        // 两个文件的文件路径组 顺序不能乱 不包含ID国家在前 包含的在后
        $file = array(
            './LWE_temp/not_for_ID.xlsx',
            './LWE_temp/for_ID.xlsx'
        );

        foreach($file as $kk => $vv){
            $data = read_excel($vv);

            echo '<meta charset="utf-8">';
            $shopeeToLweModel = new \Order\Model\ShopeeToLweTrackModel();
            static $yes = 0; // 记录成功的数量
            static $no = 0; // 记录失败的数量

            foreach($data as $k=>$v){
                if(intval($kk) === 0){ // 不包含ID国家 is_id为默认值0
                    $save = array('ebay_tracknumber' => $v[0]);
                }else{ // 包含ID国家 is_id为1
                    $save = array('ebay_tracknumber' => $v[0],'is_id' => 1);
                }

                $result = $shopeeToLweModel->add($save);
                if(!$result){
                    echo '【tracknumber】:'.$v[0].'存库失败！<br />'.PHP_EOL;
                    $no += 1;
                }else{
                    $yes += 1;
                }
            }
        }

        echo '存库成功：'.$yes.'条<br />'.PHP_EOL;
        echo '存库失败：'.$no.'条<br />'.PHP_EOL;
        unset($yes);unset($no);
    }

    public function writeLog($log,$file){
        $path = './log/LWEAllocate/';
        is_dir($path)?'':mkdir($path,0777,true);
        file_put_contents($path.$file, $log, FILE_APPEND);
    }

    public function echoMessage($str, $flag=1){
        $flag===0 ? $str = '<font colot="red">'.$str.'</font>' : $str = '<font color="blue">'.$str.'</font>';
        echo $str;
    }
}