<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 20:51
 */

namespace Package\Controller;

use Package\Model\PickOrderModel;
use Package\Service\OverOrderService;
use Think\Controller;

class OverPickOrderController extends Controller{


    /**
    *测试人员谭 2018-07-26 14:18:36
    *说明: 自动检查订单是不是 需要结束 如果 需要结束 拿就愉快地结束把~!
    */
    //TODO: /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Package/OverPickOrder/OverPickOrders
    public function OverPickOrders(){
        $PickOrderModel=new PickOrderModel();

        $map['isprint']=['in',[1,2]];
        $map['type']=['lt',4];
        //$map['type']=3;
        $map['addtime']=['lt',strtotime('-3 hours')];
        $Rs=$PickOrderModel->where($map)->field('ordersn')->select();

        $OverService=new OverOrderService();

        foreach($Rs as $List){
            $OverService->OverPickOrder($List['ordersn']);
        }
    }


}
