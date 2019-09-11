<?php
/**
 * 订单拦截日志控制器
 * User: 王模刚
 * Date: 2017/11/7
 * Time: 5:51
 */

namespace Order\Controller;


use Think\Controller;

class OrderInterceptRecordController extends Controller
{
    /**
     * 显示订单拦截填写页面
     * 王模刚  2017 11 7
     * @link   http://local.erpcnapi.com/t.php?s=/Order/OrderInterceptRecord/index
     */
    public function index(){
        $topMenuModel = new \Package\Model\TopMenuModel();
        $data = $topMenuModel->order('ordernumber asc')->getField('id,name', true);//所有订单状态
        $this->assign('ebay_users',$data);
        $this->display();
    }

    /**
     * 保存数据
     */
    public function saveData(){
        $orderInterceptLogModel = new \Order\Model\OrderInterceptRecordModel();
        $ebay_id = $_POST['ebay_id'];
        $old_status = $_POST['old_status'];
        $new_status = $_POST['new_status'];
        $update_reason = $_POST['update_reason'];
        $retInfo['code'] = 0;
        if(!$ebay_id){
            $retInfo['info'] = '数据异常！';
            echo json_encode($retInfo);return;
        }
//        echo 'ebay_id'.$ebay_id.';old_status'.$old_status.';new_status'.$new_status.';update_reason'.$update_reason;
        $save = array(
            'ebay_id'=>$ebay_id,
            'old_status'=>$old_status,
            'new_status'=>$new_status,
            'update_reason'=>$update_reason,
            'update_time'=>date('Y-m-d H:i:s'),
            'update_user'=>$_SESSION['truename']?:''
        );
        $res = $orderInterceptLogModel->add($save);
        if($res !== false){
            $retInfo['code'] = 1;
        }else{
            $retInfo['info'] = '保存错误！';
        }
        echo json_encode($retInfo);
    }
}