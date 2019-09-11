<?php
namespace Common\Model;

use Think\Model;

/**
 * Class OrderModel
 * @package Common\Model
 *  运输方式一些 共用的一些操作   by  谭联星
 */
class EbayUserModel extends Model {
    protected $tableName = 'ebay_user';


    function getusername($id){
        $map['id']=$id;
        return $this->where($map)->getField('username');
    }

    /**
     * 保存用户数据
     * @author crazytata
     * @since 2017 10 26 11:17
     */
    public function saveData($data){
        $id = $data['id'];
        $row = $this->where("id='{$id}'")->find();
        if ($row) {
            $data['w_update_time'] = date('Y-m-d H:i:s');
            return $this->where("id='{$id}'")->save($data);
        }else{
            $data['w_update_time'] = date('Y-m-d H:i:s');
            $data['w_add_time'] = date('Y-m-d H:i:s');
            return $this->add($data);
        }
    }


}