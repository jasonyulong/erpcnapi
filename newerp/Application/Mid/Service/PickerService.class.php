<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name PickerService.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/13
 * @Time: 16:32
 * @Description
 */
namespace Mid\Service;

class PickerService  extends BaseService{

    /**
     * 开始同步数据
     * @param $current_storeid
     * @return array|bool
     * @author Shawn
     * @date 2018/08/13
     */
    public function update($current_storeid){
        $action ='Picker/getPickerByApi';
        $list = $this->getErpData(['limit'=>0,'storeid'=>$current_storeid], $action);
        $pickerModel = M("goods_location_picker_region");
        //把id存起来
        $ids = [];
        if($list['status'] != 1 || empty($list['data'])){
            return false;
        }
        $data = $list['data'];
        foreach ($data as $item){
            $ids[] = $item['id'];
            $saveData['id'] = $item['id'];
            $saveData['picker_id'] = $item['picker_id'];
            $saveData['picker_name'] = $item['picker_name'];
            $saveData['region'] = $item['region'];
            $saveData['region_start'] = $item['region_start'];
            $saveData['region_end'] = $item['region_end'];
            //查看下是不是已经加入了
            $map['id'] = $item['id'];
            $isAdd = $pickerModel->where($map)->find();
            if(!empty($isAdd)){
                $result = $pickerModel->where($map)->save($item);
            }else{
                $result = $pickerModel->add($item);
            }
        }
        //找出没有的id删掉
        $delMap['id'] = array("not in",$ids);
        $delResult = $pickerModel->where($delMap)->delete();
        return $ids;
    }
}