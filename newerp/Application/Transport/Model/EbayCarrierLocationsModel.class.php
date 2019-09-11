<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 15:13
 */

namespace Transport\Model;

use Think\Model;

/**
 * Class EbayCarrierLocationsModel
 * @package Transport\Model
 */
class EbayCarrierLocationsModel extends Model
{

    protected $tableName = 'ebay_carrier_locations';


    /**
     * 添加新的数据记录
     * @param array $data  : The values tobe stored in the table.
     * @return int|boolean : if success the last insert-ID will be returned, else false instead.
     *
     */
    public function addNewGroup($data)
    {
        $checkResult = $this -> checkValues($data);

        if (!$checkResult['status']) {
            return $checkResult;
        }

        $addResult = $this -> data($checkResult['data']) -> add();
        if (!$addResult) {
            return ['status' => false, 'data' => '新分组数据添加失败,请重新操作.'];
        }

        return ['status' => true, 'data' => '新分组数据添加成功.'];
    }


    /**
     * 检测传入要保存的数据是否满足必须的都填写了
     * @param array $data ： 要检测的数据数组
     * @return array      ： 返回检测的结果
     */
    public function checkValues($data)
    {
        $newData = [];

        if (!isset($data['name'])) {
            return ['status' => false, 'data' => '国家分组名称未设置.'];
        } elseif (!trim($data['name'])) {
            return ['status' => false, 'data' => '国家分组名称不能为空.'];
        } else {
            $newData['name'] = addslashes(trim($data['name']));
        }

        if (!isset($data['locations'])) {
            return ['status' => false, 'data' => '分组中,国家列表未设置.'];
        } elseif (!trim($data['locations'], ' ,')) {
            return ['status' => false, 'data' => '分组国家列表不能为空值.'];
        } else {
            $newData['locations'] = ','.addslashes(trim($data['locations'], ' ,')).',';
        }

        if (isset($data['note']) && trim($data['note'])) {
            $newData['note'] = addslashes(trim($data['note']));
        }


        return ['status' => true, 'data' => $newData];
    }


    /**
     * 根据主键ID 获取国家分组的记录数据
     * @param $id
     * @return array
     */
    public function getGroupInfoById($id)
    {
        return $this -> field('id, name, locations, note') -> find($id);
    }


    /**
     * 根据指定记录的ID 来更新指定的记录
     * @param $id   ：要更新的记录的ID
     * @param $data ：要更新的数据
     * @return array
     */
    public function updateItemById($id, $data)
    {
        $idVal = $this -> where(['id' => $id]) -> getField('id');

        if (!$idVal) {
            return ['status' => false, 'data' => '未找到要更新的指定记录.'];
        }

        $checkResult = $this -> checkValues($data);

        if (!$checkResult['status']) {
            return $checkResult;
        }

        $saveResult = $this -> where(['id' => $id]) -> save($checkResult['data']);

        if ($saveResult === false) {
            return ['status' => false, 'data' => '记录更新失败.'];
        } elseif ($saveResult === 0) {
            return ['status' => true, 'data' => '数据没有变化.'];
        } else {
            return ['status' => true, 'data' => '数据更新成功.'];
        }
    }


    /**
     * 根据主键删除一个指定的记录
     * @param $id     ： 要删除的记录的主键
     * @return array  ： 删除的结果返回给调用者
     */
    public function deleteItemById($id)
    {
        $id = trim($id);
        if (!$id) {
            return ['status' => false, 'data' => '参数获取失败.'];
        }

        $isFind = $this -> field('id') -> find($id);
        if (!$isFind) {
            return ['status' => false, 'data' => '没有找到要删除的记录.'];
        }

        $deleteResult = $this -> delete($id);
        if (!$deleteResult) {
            return ['status' => false, 'data' => '记录删除失败,请重新操作.'];
        }

        return ['status' => true, 'data' => '指定记录删除成功.'];
    }


    /**
     * @return mixed
     */
    public function getCarrierLocationsPair()
    {
        $arr = $this -> field('name, locations') -> select();

        array_walk($arr, function($val, $key) use (&$arr1) {
            $arr1[$val['name']] = $val['locations'];
        });

        return $arr1;
    }


}