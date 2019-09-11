<?php
namespace Test\Controller;

use Common\Model\OrderModel;
use Mid\Service\TransferOrderService;
use Think\Cache\Driver\Redis;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/12
 * Time: 19:59
 */
class TestController extends \Think\Controller
{
	
	public function isStellCrossOrder(){
		$sql="SELECT aa.ebay_id,bb.sku FROM ( 
 SELECT a.ebay_id FROM erp_ebay_order a 
JOIN erp_order_type b USING(ebay_id)
WHERE b.is_cross =1 
AND a.ebay_status IN (1723)
) aa JOIN erp_goods_sale_detail bb
USING(ebay_id)
WHERE sku IN(

)";
		
		$OrderModel=new OrderModel();
		
		$Orders=$OrderModel->query($sql);
		
		
	}
	
    public function testTransferOrder() {
        $transferOrderService = new TransferOrderService();
        $transferOrderService->createTransferOrder(json_encode([
            'pick_ordersn' => 'Test1711100687',
            'from_store'   => 196,
            'to_store'     => 234,
            'contains'     => [
                ['sku' => 'AA0004', 'qty' => 10],
                ['sku' => 'AA0005', 'qty' => 12]
            ]
        ]));
    }


    /**
     * 查看、删除redis，每次redis出问题都要去找老谭，干脆做个功能方便用
     * @author Shawn
     * @date 2018/9/7
     */
    public function operateRedis(){
	    $userName = ["肖华明",'测试人员谭'];
	    $user = session("truename");
	    if(!in_array($user,$userName)){
	        $this->error("你没有权限访问！");
        }else{
	        $key = isset($_POST['key']) ? trim($_POST['key']) : '';
	        $del = isset($_POST['del']) ? (int)$_POST['del'] : 0;
	        $search1 = $del == 0 ? "checked" : '';
	        $search2 = $del == 1 ? "checked" : '';
	        //redis操作
            $redis = new Redis();
            if($del == 1){
                $result = $redis->rm($key);
                $data = $result ? "删除成功" : "删除失败";
            }else{
                $data = $redis->get($key);
            }
	        $html = "<form action='/t.php?s=/Test/Test/operateRedis' method='post'>";
	        $html .= "<p>输入需要操作的KEY值：<input name='key' type='text' value='{$key}' /></p>";
            $html .= "<p>查看<input name='del' {$search1} type='radio' value='0' />删除<input name='del' {$search2} type='radio' value='1'/></p>";
            $html .= "<p><input type='submit' value='提交' /></p>";
	        $html .= "</form>";
	        echo $html;
	        echo "<p style='color: green'>结果展示：</p>";
	        var_dump($data);exit;
        }
    }
    
    
    
}