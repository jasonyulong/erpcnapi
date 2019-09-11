<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2
 * Time: 16:20
 */

namespace Task\Controller;

use Common\Model\GoodsSaleDetailModel;
use Task\Service\SetOrderTypeService;
use Think\Controller;

class OrderTaskController extends Controller
{
    /**
     * 从订单中分解出订单包含的所有sku
     * @author Simon 2017/11/2
     * @link   http://192.168.1.57/t.php?s=/Task/OrderTask/checkSaleDetail
     *
     *测试人员谭 2017-12-10 21:25:33
     *说明:TODO 这里修改了 修改成了插入GoodsSaleDetail 还要有 对应的仓库
     */
    public function checkSaleDetail()
    {
        echo 'Start:------------------------';
        ini_set('memory_limit', '500M');
        $GoodsSaleDetailModel = new GoodsSaleDetailModel();
        $GoodsSaleDetailModel->SearchOrders();
        die('success');
    }

    /**
     * 生成订单类型数据 下面的数据通常有必要在下面执行
     * @author Simon 2017/11/2
     * @link   http://192.168.1.57/t.php?s=/Task/OrderTask/setOrderType
     */
    public function setOrderType()
    {
        echo 'Start:------------------------';
        ini_set('memory_limit', '900M');
        $setOrderTypeServer = new SetOrderTypeService();
        $setOrderTypeServer->setOrderType();
        echo "success \n\n";
    }


}