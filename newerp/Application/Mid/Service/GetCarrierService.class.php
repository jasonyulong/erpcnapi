<?php
namespace Mid\Service;
//aleb  2018 -01 -30
class GetCarrierService  extends BaseService{
    const _MODULENAME = 'carrier';
    /**
     * 同步物流渠道将erp 的物流同步到 wms 所有物流部分仓库
     *  物流名字 或者 物流分拣码不同的就更新
     * @return bool
     */
    public function getCarrier(){
        $action     = 'GetCarrier/GetcarrierList/wid/234';
        $limit      = 1000;
        $list       = $this->getErpData(['limit'=>$limit], $action);
        $getCarrierList = $this->getCarrierList();
        $carrierModel = new \Transport\Model\CarrierModel();
        if(!empty($list) && $list['ret'] == 100 ){

            foreach($list['data'] as $key => $val){
                $logArr = ['id'=>$val['id'],'name'=>$val['name']];
                $add = $this->getWiter($val);

                if(!in_array($val['id'],$getCarrierList['key'])){
                    $row = $carrierModel->add($add);
                    $flg = $row ? 1 : '-1';
                    self::writeLog($logArr,$flg,1);
                }else{

                    $row = $carrierModel->where(['id'=>$val['id']])->save($add);
                    $flg = $row === false ? '-1' : 1;
                    self::writeLog($logArr,$flg,2);
                }

            }


        }
        return $list;
    }

    /**
     * 分解数据字段 用于写入数据库
     * @param $list
     * @return array
     */
    private  function getWiter($list){
        $arr = [];
        foreach($list as $key => $val){
            $arr[$key] = $val;
            $arr['update_time'] = date('Y-m-d H:i:s');
        }
        return $arr;
    }

    /**
     * @param $name       物流渠道名称
     * @param $flg        是否成功 1 成功  -1 失败
     * @param int $type    1 新增    2更新
     */
    private static function writeLog($name,$flg,$type=1){
        if($flg == 1 && $type == 1){
            $msg = '物流id：'.$name['id'].' 物流渠道：'.$name['name'].'添加成功，记录时间为：'.date('Y-m-d H:i:s');
        }elseif($flg == '-1' && $type == 1){
            $msg = '物流id：'.$name['id'].' 物流渠道：'.$name['name'].'添加失败，记录时间为：'.date('Y-m-d H:i:s');
        }elseif($flg == 1 && $type == 2){
            $msg = '物流id：'.$name['id'].' 物流渠道：'.$name['name'].'修改成功，记录时间为：'.date('Y-m-d H:i:s');
        }elseif($flg == '-1' && $type == 2){
            $msg = '物流id：'.$name['id'].' 物流渠道：'.$name['name'].'修改成功，记录时间为：'.date('Y-m-d H:i:s');
        }
        $logDir = self::_MODULENAME;
        self::log($msg,$logDir,$flg);

    }

    //记录日志
    private static function log($msg, $logDir, $res = false){
        $color = $res ? "#393" : "#933";
        echo '<meta charset="utf-8">';
        echo '<div style="color:' . $color . '">' . $msg . '</div><br/>';
        $basePath = dirname(dirname(THINK_PATH)) . '/log/' . $logDir;
        $str      = $res ? '/success/' : '/error/';
        $fileName = $basePath . $str . date('Ymd') . '.log';
        $index    = strripos($fileName, '/');
        if (!file_exists($fileName) && strripos($fileName, '/') !== false) {
            $fileDir = substr($fileName, 0, $index);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
            }
        }
        $log = $msg . date('Y-m-d H:i:s', time());
        file_put_contents($fileName, "\xEF\xBB\xBF" . $log . PHP_EOL, FILE_APPEND);
    }


    //获取当前的物流列表
    private function  getCarrierList(){
        $carrier = new \Common\Model\CarrierModel();
        $field = "id,name,sorting_code";
        $list = $carrier->field($field)->select();   //年后更新
        $returnArr = [];
        if(is_array($list) && !empty($list)){
            foreach($list as  $key => $val){
                $returnArr['key'][] = $val['id'];
                $returnArr['val'][] = $val['name'];
                $returnArr['sorting_code'][$val['id']] = $val['sorting_code'];
            } 
        }
        unset($list);
        return $returnArr;
    }
}


