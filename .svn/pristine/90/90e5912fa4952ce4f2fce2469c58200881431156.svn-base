<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name LockService.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/12/28
 * @Time: 10:15
 * @Description
 */

namespace Mid\Service;

use Mid\Model\EbayGlockModel;

class LockService extends BaseService
{
    /**
     * 开始同步数据
     * @param $current_storeid
     * @return array|bool
     * @author Shawn
     * @date 2018/08/13
     */
    public function getLockData($current_storeid){
        $action ='Lock/getLockDataByApi';
        $list = $this->getErpData(['limit'=>0,'storeid'=>$current_storeid], $action);
        $lockModel = new EbayGlockModel();
        //把id存起来
        $ids = [];
        if($list['status'] != 1){
            return false;
        }
        //表示没有盘点数据，删掉所有
        if(empty($list['data']) && $list['status'] == 1){
            $lockModel->where('1')->delete();
            return true;
        }
        $data = $list['data'];
        foreach ($data as $item){
            $ids[] = $item['id'];
            //查看下是不是已经加入了
            $map['id'] = $item['id'];
            $isAdd = $lockModel->where($map)->find();
            if(!empty($isAdd)){
                $result = $lockModel->where($map)->save($item);
            }else{
                $result = $lockModel->add($item);
            }
        }
        //找出没有的id删掉
        $delMap['id'] = array("not in",$ids);
        $delResult = $lockModel->where($delMap)->delete();
        return $ids;
    }
}