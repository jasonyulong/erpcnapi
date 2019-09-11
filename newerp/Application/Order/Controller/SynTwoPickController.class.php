<?php
namespace Order\Controller;

use Common\Model\GoodsSaleDetailModel;
use Order\Model\SynTwoPickModel;
use Order\Model\TwoPickCountModel;
use Think\Controller;

/**
 * 自动统计二次拣货完成的订单
 */
class SynTwoPickController extends Controller
{

    /**
     * @desc  二次拣货完成的订单且已经发货
     * @Author leo
     */

    //TODO: /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Order/SynTwoPick/index
    public function index()
    {

        if(!IS_CLI){
            echo "订单异常! \n ";
            return ;
        }

        $startTime     = strtotime('-31 days');
        $twoPickModel = new TwoPickCountModel();
        $where['a.status'] = 0;
        $where['b.ebay_status'] = ['in','2,1731'];
        $where['a.end_pick_time'] = ['gt', $startTime];

        $count    = $twoPickModel->alias('a')->join('erp_ebay_order as b on a.ebay_id=b.ebay_id')->where($where)->count();
        $pageshow = 300;
        $pCount   = ceil($count / $pageshow)+1;
        p("Task start count {$count}, Total {$pCount} batch,every {$pageshow} strip");

        for ($page = 0; $page <= $pCount; $page++) {
            if ($page <= 0) {
                $page = 1;
            }
            $pagesize  = ($page - 1) * $pageshow;

            $pickData = $twoPickModel->alias('a')
                ->join('erp_ebay_order as b on a.ebay_id=b.ebay_id')
                ->field('a.id,a.ebay_id,a.pick_user,a.end_pick_time,b.ebay_status')->where($where)->limit($pagesize, $pageshow)->select();

            $this->addSynPick($pickData);

            p("The {$page} batch complete,limit($pagesize,$pageshow)");
        }

        p('End of the task');
    }

    //添加已发货的二次分拣统计明细
    public function addSynPick($pickData)
    {
        if(empty($pickData)){
            return false;
        }

        $twoPickModel = new TwoPickCountModel();
        $synPickModel = new SynTwoPickModel();
        $orderDetilModel = new GoodsSaleDetailModel();
        foreach($pickData as $key => $val){
            $username = $val['pick_user'];
            $scantime = $val['end_pick_time'];
            $ebay_id  = $val['ebay_id'];

            if($val['ebay_status'] == '1731'){
                $twoPickModel->where(['id'=>$val['id']])->save(['status'=>1]);
                continue;
            }

            $skuArr = $orderDetilModel->field('sku,qty,ebay_id')->where(['ebay_id'=>$ebay_id])->select();

            if(empty($skuArr)){
                continue;
            }

            $saveData = [];
            foreach($skuArr as $v){

                $synData = $synPickModel->where(['ebay_id'=>$ebay_id,'sku'=>$v['sku']])->find();
                if($synData){
                    continue;
                }

                $saveData[] = [
                    'ebay_id'  => $ebay_id,
                    'sku'      => $v['sku'],
                    'qty'      => $v['qty'],
                    'username' => $username,
                    'scantime' => $scantime,
                    'addtime'  => time()
                ];
            }

            if(empty($saveData)){
                continue;
            }

            if($synPickModel->addAll($saveData)){
                $twoPickModel->where(['id'=>$val['id']])->save(['status'=>1]);
            }
        }

        return true;
    }
}
