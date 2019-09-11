<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Transport\Model;

use Think\Model;

class OrderTypeModel extends Model
{

    protected $tableName = "erp_order_type";


    //
    public function addPdfPath($ebay_id,$path){
        /**
        *测试人员谭 2018-08-07 20:44:52
        *说明: 注意这个字段长度是  50!!!!
        */
        $save['lable_path']=$path;
        $this->where(compact('ebay_id'))->limit(1)->save($save);
    }


    /**
    *测试人员谭 2018-08-07 20:52:47
    *说明: 从这个表中获取到pdf缓存的干活
    */
    public function getImgUrl($ebay_id){
        $lable_path=$this->where(compact('ebay_id'))->getField('lable_path');

        if(empty($lable_path)){
            return false;
        }


        $jsonFile=dirname(dirname(THINK_PATH)).'/'.$lable_path;
        if(file_exists($jsonFile)){
            return json_decode(file_get_contents($jsonFile),true);
        }

        /**
        *测试人员谭 2018-08-07 20:59:24
        *说明: 文件已经失效了
        */
        return false;

    }

}