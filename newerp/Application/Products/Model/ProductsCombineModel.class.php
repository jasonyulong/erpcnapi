<?php
namespace Products\Model;

use Think\Model;

class ProductsCombineModel extends Model
{
    protected $tableName = 'ebay_productscombine';

    /**
     * 批量更新
     * @author Simon 2017/11/21
     */
    public function updates($items) {
        $successSkuS = [];
        foreach ($items as $item) {
            $sku = $this->update($item);
            if (!empty($sku)) {
                $successSkuS[] = $sku;
            }
        }
        return $successSkuS;
    }

    /**
     * 单个更新
     * @author Simon 2017/11/21
     */
    public function update($data) {
        $existData             = $this->where(['goods_sn' => $data['goods_sn']])->find();
        $data['w_update_time'] = date('Y-m-d H:i:s', time());
        unset($data['id']);
        if (empty($existData)) {
            $data['w_add_time'] = date('Y-m-d H:i:s', time());
            $id                 = $this->add($data);
            return $id ? $data['goods_sn'] : false;
        } else {
            $status = $this->where(['goods_sn' => $data['goods_sn']])->save($data);
            return $status !== false ? $data['goods_sn'] : false;
        }
    }


}
