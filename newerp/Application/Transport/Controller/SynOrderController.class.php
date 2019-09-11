<?php

    namespace Transport\Controller;

    use Package\Model\UnableOrderCarrierGroupModel;
    use Think\Controller;
    use Common\Model\OrderModel;
    use Common\Model\CarrierModel;
    use Package\Model\TopMenuModel;
    use Package\Model\CarrierGroupItemModel;
    use Think\Page;


    class SynOrderController extends Controller {

        public $pageSize = 100;

        public function _initialize() {
            $this->pageSize = $_SESSION['pagesize'] ? $_SESSION['pagesize'] : 100;
        }


        /**
         * hank
         * 同步ERP订单
         * 主要处理 ERP添加了新渠道 仓库系统没来得及更新渠道
         */
        public function index() {
            $req_carrier = trim(I('carrier'));
            $groupId = (int)I('groupId');
            $topMenu     = new TopMenuModel();
            $topMenuArr  = $topMenu->getMenuName();
            // 获取无法货找单的渠道
            $needCarrier = $this->getDiffCarrier();
            //获取无法货找单物流分组
            $carrierGroupModel = new UnableOrderCarrierGroupModel();
            $orderModel = new OrderModel();
            $carrierGroup = $carrierGroupModel->select();
            $groupCarrier = array();
            if(!empty($carrierGroup)){
                foreach ($carrierGroup as &$value){
                    $map = [];
                    $map['ebay_carrier'] = ['in', explode(",",$value['carrier'])];
                    $map['ebay_status'] = ['in', [1723, 1724]];
                    $counts     = $orderModel->where($map)->count();
                    $value['count'] = (int)$counts;
                    if($value['id'] == $groupId){
                        $groupCarrier = explode(",",$value['carrier']);
                    }
                }
            }
            if(!empty($needCarrier)){
                $where = [];
                if(empty($groupCarrier)){
                    $where['ebay_carrier'] = ['in', $needCarrier];
                }else{
                    $where['ebay_carrier'] = ['in', $groupCarrier];
                }
                $where['ebay_status'] = ['in', [1723, 1724]];

                if ($req_carrier) {
                    $where['ebay_carrier'] = $req_carrier;
                }

                // 获取无法货找单的订单数
                $counts     = $orderModel->where($where)->count();
                $pageObj    = new Page($counts, $this->pageSize);
                $limit      = $pageObj->firstRow . ',' . $pageObj->listRows;
                $pageString = $pageObj->show();
                $field      = 'ebay_id,ebay_addtime,w_add_time,ebay_carrier,ebay_status,pxorderid';
                $synOrders  = $orderModel->field($field)->where($where)->limit($limit)->select();

                $this->assign('counts', $counts);
                $this->assign('topMenuArr', $topMenuArr);
                $this->assign('needCarrier', $needCarrier);
                $this->assign('req_carrier', $req_carrier);
                $this->pageStr   = $pageString;
                $this->synOrders = $synOrders;
            }
            $this->assign("carrierGroup",$carrierGroup);
            $this->assign("groupId",$groupId);
            $this->display();

        }

        /**
         * hank
         * 获取无法货找单数量
         */
        public function counts() {
            $needCarrier = $this->getDiffCarrier();
            $orderModel = new OrderModel();
            $where = [];
            $where['ebay_carrier'] = ['in', $needCarrier];
            $where['ebay_status'] = ['in', [1723, 1724]];
            $counts = $orderModel->where($where)->count();
            echo $counts;
        }

        /**
         * hank
         * 获取 无法货找单渠道
         */
        public function getDiffCarrier() {
            // 渠道分组表中的渠道
            $carrierGroupItemsModel = new CarrierGroupItemModel('','', C('DB_CONFIG2'));
            $configCarriers  = $carrierGroupItemsModel -> getField('carrier', true);

            // ebay_carrier 表中的启用状态的渠道
            $carrierModel = new CarrierModel();
            $carrierInfo  = $carrierModel->field('name')->select();
            $carriers     = [];

            foreach ($carrierInfo as $carrier) {
                $carriers[] = $carrier['name'];
            }

            // ebay_carrier 表未更新的渠道
            $diffCarrier = array_diff($carriers,$configCarriers);
            $needCarrier = [];

            foreach ($diffCarrier as $k => $item) {
                $needCarrier[] = $diffCarrier[$k];
            }

            return $needCarrier;
        }


    }