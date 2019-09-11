<?php
/**
 * User: 王模刚
 * Date: 2017/12/6
 * Time: 10:38
 */

namespace Common\Model;


use Think\Model;

class EbayOrderExtModel extends Model
{
    protected $tableName = 'erp_ebay_order_ext';
    protected $all_status = ['1723','1745','1724','2009'];

    /**
     * @author 王模刚
     * @since 2017 12 6
     */
    public function saveToExt($ebay_id,$staus){
        if(empty($ebay_id) || empty($staus)){
            return ['code' =>1,'error_msg'=>'ebay_id 或者是状态不能为空!'];
        }
        if(!in_array($staus,$this->all_status)){
            return ['code' =>1,'error_msg'=>'状态错误!'];
        }
        $date = date('Y-m-d H:i:s');
        $origin_info = $this->where(['ebay_id'=>$ebay_id])->find();
        if($origin_info){
            $save_data['to_time_'.$staus] = $date;
            $save_data['w_update_time'] = $date;
            $res = $this->where(['ebay_id'=>$ebay_id])->save($save_data);
        }else{
            $add_data['to_time_'.$staus] = $date;
            $add_data['ebay_id'] = $ebay_id;
            $add_data['w_update_time'] = $date;
            $add_data['w_add_time'] = $date;
            $res = $this->add($add_data);
        }
        if($res === false){
            return ['code' =>1,'error_msg'=>'数据保存失败!'];
        }else{
            return ['code' =>2,'error_msg'=>'数据保存成功!'];
        }
    }
}