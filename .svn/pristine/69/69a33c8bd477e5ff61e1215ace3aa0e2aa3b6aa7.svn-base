<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 15:41
 */

namespace Package\Model;

use Think\Model;

class CarrierGroupItemModel extends Model
{
    protected $tableName = 'pick_carrier_group_items';


    /**
     * @param $id
     * @return mixed
     */
    public function getGroupCarriers($id)
    {
        return $this -> where(['group_id' => $id])
              -> getField('carrier', true);
    }

}