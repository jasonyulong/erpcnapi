<?php
/**
 * User: 王模刚
 * Date: 2017/11/10
 * Time: 16:11
 */

namespace Mid\Service;


class InternalService extends BaseService
{
    private $_stime = '2017-12-09 9:00:00';
    /**
     * 获取Internal表中的数据
     * 王模刚 2017 12 10
     */
    public function getSyncInternalList($request){
        $limit = $request['limit']?:1000;
        $action      = 'Internal/getInternalList/wid/'.$this->currentid;
        $list = $this->getErpData(['limit'=>$limit], $action);
dump($list);
        $date = date('Y-m-d H:i:s');
        if ($list['ret'] != 100) {
            exit('No data!');
        }
        $listData   = $list['data'];
        if (count($listData) == 0) {
            exit('No data2!');
        }
        echo count($listData);
        $ebayInternalModel   = new \Mid\Model\MidInternalStoreSkuModel();
        $ids = [];
        $errorId = '';
        foreach($listData as $k=>$v){
            $tmpId = $v['id'];
            unset($v['id']);
            $existData = $ebayInternalModel->where(['sku' => $v['sku'],'storeid' => $v['storeid']])->find();
            if($existData){
                $res = $ebayInternalModel->where(['sku' => $v['sku'],'storeid' => $v['storeid']])->limit(1)->save($v);
            }else{
                $res = $ebayInternalModel->add($v);
            }
            if($res === false){
                $errorId[] = $tmpId;
            }else{
                $ids[] = $tmpId;
            }
        }
        //跟新拉取成功的数据
        if(!empty($ids)){
            $updateAction      = 'Internal/updateInternalSyncStatus/wid/'.$this->currentid;
            $updateData = array('stime' => $this->_stime,'skuS' => implode(',', $ids));
            dump($updateData);
            $updateRes = $this->getErpData($updateData, $updateAction);
            dump($updateRes);
            if($updateRes['ret'] != 100){
                $errorId = implode(',',$ids);
            }
        }else{
            echo '更新状态失败的sku:'.implode(',', $errorId);
        }


    }
    
    
    /**
    *测试人员谭 2018-06-21 18:07:40
    *说明: 检查那啥删除的intsernal sku
    */
    public function getDelInternalList(){
		$ebayInternalModel   = new \Mid\Model\MidInternalStoreSkuModel();
		$skus=$ebayInternalModel->getField('sku',true);
		$updateAction      = 'Internal/getDelInternalList/wid/'.$this->currentid;
		
		for($i=0;$i<count($skus);$i++){
			$skus[$i]=strtoupper($skus[$i]);
		}
		
		$skus=implode(',',$skus);
		$updateData = array('skus'=>$skus);
		$updateRes = $this->getErpData($updateData, $updateAction);
		
		if(count($updateRes)>100){
			echo '异常';
			return;
		}
		//print_r($updateRes);
		
		foreach ($updateRes['data'] as $sku){
			$ebayInternalModel->where("sku='$sku'")->limit(1)->delete();
		}
		
	}
}