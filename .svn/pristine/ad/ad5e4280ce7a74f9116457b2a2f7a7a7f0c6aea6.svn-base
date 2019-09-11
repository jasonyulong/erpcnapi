<?php
/**
 * 利润控制器
 */
namespace Order\Controller;

use Order\Model\CanWeightFailureLogModel;
use Package\Model\OneSkuPackLogModel;
use Package\Model\OrderslogModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
    	echo 'okss';
    }


    //显示日志
    public function showLog(){
        $ebay_id = $_REQUEST['ebay_id'];
        $EbayOrdersLogModel = new OrderslogModel();
        if(empty($ebay_id)){
            echo '<h4 style="color:#911;margin:10px;">订单参数无效</h4>';die;
        }
        //称重失败日志表
        $weightFailureModel = new CanWeightFailureLogModel('','',C('DB_CONFIG_READ'));
        //包装扫描日志表
        $packLogModel = new OneSkuPackLogModel('','',C('DB_CONFIG_READ'));
        $map['ebay_id'] = $ebay_id;

        $PickOrderDetail=new PickOrderDetailModel();
        $PickOrder=new PickOrderModel();

        $field='is_baled,is_delete,ordersn,scaning,scan_user,scan_time,picker';

        // scan_user 包装人员
		// scan_time  包装时间
		// picker 拣货人员
        
        $Rs=$PickOrderDetail->where($map)->field($field)->group('ordersn')->order('id asc')->select();

		
        //获取 拣货时间 其实就是 addtime  忽悠一下业务员 而已 鬼TM知道 这个订单是什么时候拣货的
		
		$Orders=[];
		
		foreach ($Rs as $list){
			$Orders[]=$list['ordersn'];
		}
		
		
		$PickOrderTime=[];
		if(!empty($Orders)){
			$PickOrderTime=$PickOrder->where(['ordersn'=>['in',$Orders]])->getField('ordersn,addtime',true);
		}
		


        //获取操作记录
        $field='operationuser,operationtime,notes';
        $operationLog = $EbayOrdersLogModel->field($field)->where($map)->order('operationtime desc')->select();
        //获取称重失败日志
        $weightFailLog = $weightFailureModel->field($field)->where($map)->order('operationtime desc')->select();
        //获取包装扫描日志
        $map['type'] = 1;
        $packLog = $packLogModel->field($field)->where($map)->order('operationtime desc')->select();
        $operationLog = $this->array_sort(array_merge($operationLog,$weightFailLog,$packLog),'operationtime');

        foreach($operationLog as $key=>$val){
            $operationLog[$key]['operationtime'] = date('Y-m-d H:i:s',$val['operationtime']);
        }


        $this->assign('operationLog',$operationLog);
        $this->assign('PickOrderDetail',$Rs);
        $this->assign('PickOrderTime',$PickOrderTime);

        $this->display();
    }

    /**
     * @param $array 要排序的数组
     * @param $keys 要用来排序的键名
     * @param string $type 排序
     * @return array
     * @author Shawn
     * @date 2018/12/27
     */
    private function array_sort($array,$keys,$type='desc'){
        $keysValue = $new_array = array();
        foreach ($array as $k=>$v){
            $keysValue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysValue);
        }else{
            arsort($keysValue);
        }
        reset($keysValue);
        foreach ($keysValue as $k=>$v){
            $new_array[$k] = $array[$k];
        }
        return $new_array;
    }

}
