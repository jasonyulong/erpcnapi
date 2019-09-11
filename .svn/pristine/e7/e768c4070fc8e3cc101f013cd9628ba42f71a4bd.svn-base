<?php
/**
 * 2018年9月5日10:36:03
 * leo
 */
namespace Package\Controller;

use Common\Controller\CommonController;
use Order\Model\ApiBagsModel;
use Order\Model\EbayOrderModel;
use Think\Controller;

class PackageRePrintController extends CommonController{

    /**
     * @desc 包裹面单重打
     * @Author leo
     */
    public function packagePrint()
    {
        $mark_code=trim($_GET['mark_code']);
        $mark_code1 = $mark_code;
        if(!empty($mark_code)){

            $apiBagsModel = new ApiBagsModel();
            $OrderModel = new EbayOrderModel();

            $field = "a.mark_code,a.weight,a.create_at,a.create_by,b.transport,count(b.id) as counts,a.delivery_status,sum(b.weight) as calc_weight";
            $bagData = $apiBagsModel->alias('a')->field($field)
                ->join("inner join api_orderweight as b on a.mark_code = b.bag_mark")
                ->where(['a.mark_code'=>$mark_code])->group('a.id')
                ->find();

            if(empty($bagData)){
                $mark_code = $OrderModel->alias('a')->field('a.ebay_tracknumber,b.bag_mark,a.ebay_id')
                    ->join("inner join api_orderweight as b on a.ebay_id = b.ebay_id")
                    ->where(['a.ebay_tracknumber|a.pxorderid'=>$mark_code])->group('a.ebay_id')
                    ->select();
                $mark_code = $mark_code[0]['bag_mark'];
                if(count($mark_code) > 1 || empty($mark_code)){
                    $error = "没有找到正确的打包好的袋子";
                }else{
                    $url='inBagPrint.php?bagMark='.$mark_code;
                    $this->assign('reprint',$url);
                }
                $this->assign('error',$error);
            }else{
                $url='inBagPrint.php?bagMark='.$mark_code;
                $this->assign('reprint',$url);
            }
        }
        $this->assign('mark_code',$mark_code1);
        $this->display();
    }
}