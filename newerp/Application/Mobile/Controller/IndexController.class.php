<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name IndexController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/21
 * @Time: 9:40
 * @Description 首页显示
 */

namespace Mobile\Controller;

use Common\Model\CarrierCompanyModel;
use Order\Model\ApiBagsModel;
use Order\Model\EbayCarrierModel;
use Package\Model\ApiOrderWeightModel;

class IndexController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 发货页面
     * @author Shawn
     * @date 2018/8/22
     */
    public function delivery()
    {
        //找到所有物流公司
        $map['a.status'] = 1;
        $map['a.ebay_warehouse'] = C("CURRENT_STORE_ID");
        $ebayCarrierModel = new EbayCarrierModel();
        $companyData = $ebayCarrierModel->alias("a")
            ->join("inner join ebay_carrier_company as b on a.companyname=b.id")
            ->where($map)
            ->field("b.id,b.sup_abbr,b.sup_code")
            ->group("b.sup_name")
            ->order("b.sup_code+0 asc,b.id asc")
            ->select();
        $this->assign('companyData',$companyData);
        $this->display();
    }

    /**
     * 确认收包操作
     * @author Shawn
     * @date 2018/8/14
     */
    public function makeDelivery(){
        $markCode = trim(I('markCode'));
        $id = (int)I("company_id");
        if(empty($markCode)){
            $this->ajaxReturn(['status'=>0,'msg'=>'请扫描需要交运的包裹编号！']);
        }else{
            if(empty($id)){
                $this->ajaxReturn(['status'=>0,'msg'=>'请选择物流公司！']);
            }
            $apiBagModel = new ApiBagsModel();
            $package = $apiBagModel->field("id, delivery_status,bag_status")
                ->where(['mark_code'=>$markCode])
                ->find();
            if(empty($package)){
                $this->ajaxReturn(['status' => 0, 'msg' => '未找到指定的打包包裹.']);
            }else{
                if($package['delivery_status'] == 1){
                    $this->ajaxReturn(['status' => 0, 'msg' => '请勿重复标记.']);
                }else{
                    //找到包裹内物流公司
                    $apiOrderWeightModel = new ApiOrderWeightModel();
                    $ebayCarrierModel = new EbayCarrierModel();
                    $carrier = $apiOrderWeightModel->where(['bag_mark'=>$markCode])
                        ->order("id desc")
                        ->getField("transport");
                    $companyId = $ebayCarrierModel->where(['name'=>$carrier,'status'=>1])->getField("companyname");
                    if($id != $companyId){
                        $this->ajaxReturn(['status'=>0,'msg'=>'公司错误！']);
                    }
                    $saveData['delivery_status'] = 1;
                    $saveData['delivery_time'] = time();
                    $saveData['delivery_user'] = session("truename");
                    $result = $apiBagModel->where(["id"=>$package['id']])->save($saveData);
                    if($result){
                        $this->ajaxReturn(['status' => 1, 'msg' => '标记收包成功.']);
                    }else{
                        $this->ajaxReturn(['status' => 0, 'msg' => '标记收包失败.']);
                    }
                }
            }
        }
    }
}