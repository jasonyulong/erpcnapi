<?php
/**
 * User: 王模刚
 * Date: 2017/11/10
 * Time: 16:11
 */

namespace Mid\Service;


class EbayHandleService extends BaseService
{
    private $_stime = '2017-12-09 9:30:00';
    /**
     * 获取onhandel表中的数据
     * 王模刚 2017 11 10
     */
    public function getOnhandle($request){
        ini_set('memory_limit','1024M');
        $limit = $request['limit']?:50;
        $action      = 'Onhandle/getOnhandleList/wid/'.$this->currentid;
        $list = $this->getErpData(['limit'=>$limit,'store_id'=>$request['store_id']], $action);
        print_r($list);
        $date = date('Y-m-d H:i:s');
        if ($list['ret'] != 100) {
            exit('No data!');
        }
        $listData   = $list['data'];
        if (count($listData) == 0) {
            exit('No data2!');
        }

        echo count($listData);sleep(3);
        echo "\n\n";
        /* echo '<br/>';
           dump($listData);exit;*/
        $ebayOnhandleModel   = new \Common\Model\EbayOnhandleModel($request['store_id']);
        $skuS = [];
        $errorSku = '';
        foreach($listData as $k=>$v){
            unset($v['id']);
            $existData = $ebayOnhandleModel->where(['goods_sn' => $v['goods_sn']])->find();
            if($existData){
				unset($v['g_location']);
                $v['w_update_time'] = $date;
                $res = $ebayOnhandleModel->where(['goods_sn' => $v['goods_sn']])->save($v);
            }else{
                $v['w_update_time'] = $date;
                $v['w_add_time'] = $date;
                $res = $ebayOnhandleModel->add($v);
            }
            if($res === false){
                $errorSku[] = $v['goods_sn'];
            }else{
                $skuS[] = $v['goods_sn'];
            }
        }
        /* dump($skuS);
          echo '更新错误的sku'.implode(',',$errorSku).'<br>';*/
        if(!empty($skuS)){
            $updateAction      = 'Onhandle/updateOnhandleStatus/wid/'.$this->currentid;
            $updateData = array('stime' => $this->_stime,'skuS' => implode(',',$skuS),'store_id'=>$request['store_id']);
            dump($updateData);
            $updateRes = $this->getErpData($updateData, $updateAction);
            dump($updateRes);
            if($updateRes['ret'] != 100){
                $errorSku = implode(',',$skuS);
            }
        }else{
            echo '更新状态失败的sku:'.$errorSku;
        }
    }
}