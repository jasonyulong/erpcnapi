<?php

namespace Order\Controller;

use Common\Controller\CommonController;
use Common\Model\OrderModel;
use Order\Model\EbayAccountModel;
use Order\Model\EbayOrderModel;
use Order\Model\EbayOrderDetailModel;
use Order\Service\JoomService;
use Package\Model\WhiteOrderModel;
use Think\Controller;

class RePrintController extends CommonController{
    private $OrderModel;

    function getEbayidByTracknumber(){
        $tracknumber=trim($_GET['tracknumber']);

        if(empty($tracknumber)){
            $this->display('reprint');
            return ;
        }
        $error='';
        $tracknumber=strtoupper($tracknumber);
        if(!preg_match("/^[0-9A-Z]+$/",$tracknumber)){
            $error='跟踪号格式有误!';
            $this->assign('error', $error);
            $this->display('reprint');
            return ;
        }

        $OrderModel=new OrderModel();
        $addtime=strtotime('-25 days');

        if(preg_match("/^\d+$/",$tracknumber&&strlen($tracknumber)<=8)){
            $map['ebay_id']=$tracknumber;
        }else{
            $map['ebay_tracknumber']=$tracknumber;
            $map['ebay_addtime']=['gt',$addtime];
        }


        $map['ebay_status']=['gt',1];

        $RR=$OrderModel->where($map)->field('ebay_id,ebay_carrier')->find();
        if(empty($RR))
        {
            unset($map['ebay_tracknumber']);
            $map['pxorderid'] = $tracknumber;
            $RR = $OrderModel->where($map)->field('ebay_id,ebay_carrier')->find();
        }
        //echo $OrderModel->_sql();
        if(empty($RR)){
            $error='跟踪号找不到订单!';
            $this->assign('error', $error);
            $this->display('reprint');
        }

        $ebay_id      = $RR['ebay_id'];

        $ebay_carrier = $RR['ebay_carrier'];


        $CONFIGD=include(ROOT_PATH.'/newerp/Application/Transport/Conf/config.php');

        $TEMP15=$CONFIGD['CARRIER_TEMPT_15'];
        $TEMP20=$CONFIGD['CARRIER_TEMPT_20'];

        if(array_key_exists($ebay_carrier,$TEMP15)){
            $mod=2;
        }
        else if(array_key_exists($ebay_carrier,$TEMP20)){
            $mod = 3;
        }else{
            $mod=1;
        }

        $url='t.php?s=/Transport/Print/PrintAllCarrier&bill='.$ebay_id.'&mod='.$mod;

        
        /**
        *测试人员谭 2018-06-22 21:03:24
        *说明: 这里这个程序 新加了一个写入的 flag 如果 这个单之前是 白单
		 *    也就是 在 whiteOrder 表里面存在过那么就把他 干到 api_checksku 里面去
        */
        
        $WhiteOrder=new WhiteOrderModel();
        
        $Rs=$WhiteOrder->where(['ebay_id'=>$ebay_id])->getField('id');
        
		$this->assign('insertApiCheck', 0);
        
		if($Rs){
			$this->assign('insertApiCheck', 1);
		}
		

        $this->assign('error', '');
        $this->assign('reprint',$url);
        $this->display('reprint');
    }

    
    
    /**
    *测试人员谭 2018-05-25 14:43:28
    *说明: 有几百单的 速卖通南京仓故障
    */
    public function viewReLable(){
		$tracknumber=trim($_GET['tracknumber']);
	
		$tmpl='nanjing';
		
		
		if(empty($tracknumber)){
			$this->display($tmpl);
			return ;
		}
		
		$error='';
		
		$tracknumber = strtoupper($tracknumber);
	
		$AllowOrder = include(dirname(dirname(__FILE__)).'/Conf/wish_order.php');
	
		if(!in_array($tracknumber, $AllowOrder)){
			$error='交给IT的要换单的跟踪号里面没找到你扫描的这个单号!';
			$this->assign('error', $error);
			$this->display($tmpl);
			return ;
		}
	    $key = array_keys($AllowOrder, $tracknumber);
		$ebay_id = $key[0];
		$OrderModel=new OrderModel();
		$map=[];
		$map['ebay_id']=$ebay_id;
	
		$RR=$OrderModel->where($map)->field('ebay_id,ebay_status,ebay_carrier')->find();
		
		if(empty($RR)){
			$error='跟踪号找不到订单!';
			$this->assign('error', $error);
			$this->display($tmpl);
		}
	
		$ebay_id      = $RR['ebay_id'];
	
		$ebay_carrier = $RR['ebay_carrier'];
	
	
		$CONFIGD=include(ROOT_PATH.'/newerp/Application/Transport/Conf/config.php');
	
		$TEMP15=$CONFIGD['CARRIER_TEMPT_15'];
	
		if(array_key_exists($ebay_carrier,$TEMP15)){
			$mod=2;
		}else{
			$mod=1;
		}
	
		$url='http://erp.spocoo.com/t.php?s=/Transport/Print/PrintAllCarrier&bill='.$ebay_id.'&mod='.$mod;
	
		$this->assign('error', '');
		$this->assign('reprint',$url);
		$this->display($tmpl);
	}

    // html 显示
    function RePrinthtml(){

        $this->display('reprints');
    }

    // 物流退件  重新发货的 东西  根据老的 跟踪号 换面单子
    //TODO 这个方法 打印 面单需要 吧hosts  erpcnapi.com 映射 218
    //TODO 返回
    //TODO ['status'=>0,'msg'=>'订单不在补发待审核','url'=>'']
    //TODO 如果成功的话 url 这里是一个 面单的 地址
    //TODO 不需要称重 直接获取老订单的称重 就好了
    //
    function getReturnedOrderEbayidByTracknumber(){
        $tracknumber=trim($_REQUEST['tracknumber']); // 老跟踪号
        if(empty($tracknumber)){
            echo json_encode(['status'=>0,'msg'=>'跟踪号无效','url'=>'']);
            return ;
        }

        $AllowOrder=include(dirname(dirname(__FILE__)).'/Conf/resend_order.php');


        $NotAllowOrder=[
            7576389,
            7577525,
            7577618,
            7566659,
            7577395,
            7602368,
            7603250,
            7619551,
            7624561,
            7645385,
            7649775,
            7649769,
            7649758,
            7648603,
            7648250,
            7646554,
            7646551,
            7557626,
            7499992,
            7513748,
            7530476,
            7521975,
            7555407,
            7618857,
            7617076,
            7617057,
            7617015,
            7606448,
            7648122,
            7648120,
            7646423,
            7645099,
            7643998,
            7631499,
            7655541,
            7602304,
            7585984,
            7629782
        ];

        if(empty($AllowOrder)){
            echo json_encode(['status'=>0,'msg'=>'未设置允许换单的订单联系IT!','url'=>'']);
            return ;
        }


        $OrderModel=new OrderModel();

        if(preg_match("/^\d+$/",$tracknumber&&strlen($tracknumber)<=8)){
            $map['ebay_id']=$tracknumber;
        }else{
            $map['ebay_tracknumber']=$tracknumber;
        }

        $map['ebay_status']=2;

        $RR=$OrderModel->where($map)->field('ebay_id,ebay_tracknumber')->find();

        if(empty($RR)){
            $error='扫描出来的信息,找不到订单,确定这是老单号? 并且老单在【已经发货】?';
            echo json_encode(['status'=>0,'msg'=>$error,'url'=>'']);
            return ;
        }

        $ebay_id_old=$RR['ebay_id']; // 这是老单号啦～！
        $old_tracknumber=$RR['ebay_tracknumber']; // 这是老单号啦～！

        // 根据配置文件 查找新单号
        $ebay_id=$AllowOrder[$ebay_id_old];

        if(in_array($ebay_id_old,$NotAllowOrder)){
            $error='物流蔡小姐认为此单 '.$ebay_id_old.' 不允许发货';
            echo json_encode(['status'=>0,'msg'=>$error,'url'=>'']);
            return ;
        }

        if(empty($ebay_id)){
            $error='新单号老单号('.$ebay_id_old.')的关系表中未找到!!!!联系IT';
            echo json_encode(['status'=>0,'msg'=>$error,'url'=>'']);
            return ;
        }

        $RR=$OrderModel->where(['ebay_id'=>$ebay_id])
            ->field('ebay_id,ebay_tracknumber,ebay_status')->find();

        if(empty($RR)){
            $error='新单号'.$ebay_id.' 在系统中 不存在！异常!联系IT';
            echo json_encode(['status'=>0,'msg'=>$error,'url'=>'']);
            return ;
        }

        $ebay_tracknumber = $RR['ebay_tracknumber'];
        $ebay_status      = $RR['ebay_status'];

        if(empty($ebay_tracknumber)){
            echo json_encode(['status'=>0,'msg'=>'新单号 '.$ebay_id.' 居然没有跟踪号!开什么玩笑，怎么换单','url'=>'']);
            return ;
        }

        if($ebay_status!=1961){
            echo json_encode(['status'=>0,'msg'=>'新单号 '.$ebay_id.' 必须在【补发待审核才能允许重新换单】','url'=>'']);
            return ;
        }
        //新的 单号 假装出库
        // 订单转到 已发货～！～ ！～！

        /*
       $OrderModel->where(['ebay_id'=>$ebay_id])->save(['ebay_status'=>2]);
        $Orderslog=new OrderslogModel();
        $Orderslog->addordernote($ebay_id,'补发扫描,转到已经发货--'.$_SESSION['truename'],5);

        $OrderReturn=new OrderReturnModel();
        $rr=$OrderReturn->where(['ebay_id'=>$ebay_id])->find();
        if(empty($rr)){
            $save['ebay_id']         = $ebay_id;
            $save['old_ebay_id']     = $ebay_id_old;
            $save['old_tracknumber'] = $old_tracknumber;
            $save['tracknumber']     = $ebay_tracknumber;
            $save['scanuser']        = $_SESSION['truename'];
            $save['scantime']        = time();
            $OrderReturn->add($save);
        }
*/
        $rs=$this->curl_order([
            'ebay_id'=>$ebay_id,
            'ebay_id_old'=>$ebay_id_old,
            'old_tracknumber'=>$old_tracknumber,
            'ebay_tracknumber'=>$ebay_tracknumber,
            'truename'=>''
        ]);

        echo $rs;
        $url='http://192.168.1.73/t.php?s=/Transport/Print/PrintAllCarrier&bill='.$ebay_id.'&mod=1';

        echo json_encode(['status'=>1,'msg'=>'换单成功如果没有出来面单,请记住此单号【'.$ebay_id.'】','url'=>$url]);

    }


    function curl_order($data){
        $url='http://47.90.38.119/t.php?s=/Order/RePrint/change_status';
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($connection);
        $info  = curl_getinfo($connection);
        curl_close($connection);
        return $response;
    }


    function getInterceptionOrderEbayidByTracknumber(){
        $tracknumber=trim($_GET['tracknumber']);

        $teml='interception_reprint';

        if(empty($tracknumber)){
            $this->display($teml);
            return ;
        }

        $error='';

        $tracknumber=strtoupper($tracknumber);
        if(!preg_match("/^[0-9A-Z]+$/",$tracknumber)){
            $error='跟踪号格式有误!';
            $this->assign('error', $error);
            $this->display($teml);
            return ;
        }


        $AllowOrder=include(dirname(dirname(__FILE__)).'/Conf/120order.php');

        if(!array_key_exists($tracknumber,$AllowOrder)){
            $error='交给IT的要换单的跟踪号里面没找到你扫描的这个单号!';
            $this->assign('error', $error);
            $this->display($teml);
            return ;
        }

        $ebay_id=$AllowOrder[$tracknumber];

        $OrderModel=new OrderModel();
        $addtime=strtotime('-25 days');



        $map['ebay_id']=$ebay_id;

        $map['ebay_addtime']=['gt',$addtime];

        $map['ebay_status']=array('in',[2,2009]);

        $RR=$OrderModel->where($map)->field('ebay_id,ebay_status')->find();

        //echo $OrderModel->_sql();
        if(empty($RR)){
            $error='跟踪号找不到订单!';
            $this->assign('error', $error);
            $this->display($teml);return;
        }
        $ebay_id=$RR['ebay_id'];
        $ebay_status=$RR['ebay_status'];

        if(2==$ebay_status){
            if(7966697!=$ebay_id){
//                $error='跟踪号找到订单-!'.$ebay_id.',在已经发货里面,无法换单';
//                $this->assign('error', $error);
//                $this->display('reprint');return;
            }
        }




        $url='t.php?s=/Transport/Print/PrintAllCarrier&bill='.$ebay_id.'&mod=1';



        $this->assign('error', '');
        $this->assign('reprint',$url);
        $this->display($teml);
    }


}