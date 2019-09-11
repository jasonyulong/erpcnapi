<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 11:11
 */

namespace Mid\Model;


use Think\Model;

class MidEbayUserModel extends BaseModel
{
    protected $tableName = 'mid_ebay_user';

    /**
     * 保存用户数据
     * @author crazytata
     * @since 2017 10 26 11:17
     */
    public function saveData($data){
        $id = $data['id'];
        $row = $this->where("id='{$id}'")->find();
        if ($row) {
            return $this->where("id='{$id}'")->save($data);
        }else{
            return $this->add($data);
        }
    }
}