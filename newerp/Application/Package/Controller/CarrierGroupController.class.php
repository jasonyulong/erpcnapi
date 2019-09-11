<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 11:50
 */

namespace Package\Controller;


use Common\Controller\CommonController;
use Common\Model\CarrierModel;
use Package\Model\CarrierGroupItemModel;
use Package\Model\CarrierGroupModel;
use Package\Model\UnableOrderCarrierGroupModel;
use Think\Exception;
use Think\Log;
use Think\Page;


/**
 * @method $this display($template='')
 * @method $this assign($name, $value)
 *
 * Class CarrierGroupController
 * @package Package\Controller
 */
class CarrierGroupController extends CommonController
{

    private $pageSize = 50;

    private $carriers = [];

    /**
     * 代替构造函数实现相应的初始化操作
     */
    public function _initialize()
    {
        if (!can('view_pick_carrier_group')) {
            echo "<h1 style='color: red'> 您没有相关权限！ </h1>";
            exit;
        }
        parent::_initialize();

        $this -> pageSize = session('pagesize');
        $carriers = load_config('newerp/Application/Transport/Conf/config.php');
        $carriers = array_keys($carriers['CARRIER_TEMPT']);

        $trueCarrier = [];
        foreach ($carriers as $val) {
            $trueCarrier[] = strpos($val, '_') === false ? $val : explode('_', $val)[0];
        }

        $this -> carriers = array_unique($trueCarrier);
    }


    /**
     * 列出现有的分组
     */
    public function groupList()
    {
        $carrierGroupModel = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $counts = $carrierGroupModel -> count();

        $pageObj = new Page($counts, $this -> pageSize);

        $limit   = $pageObj -> firstRow.', '.$pageObj -> listRows;
        $pageStr = $pageObj -> show();

        $carrierGroups = $carrierGroupModel
            -> field('id, group_name, create_by, create_at, memo')
            -> limit($limit)
            -> select();

        $this -> assign('pageString', $pageStr)
              -> assign('carrierGroups', $carrierGroups)
              -> display();
    }


    /**
     * 展示创建新运输方式的页面
     */
    public function showCreateGroup()
    {

        //echo "<h1 style='color: red'>功能禁用.</h1>";
        //return null;

        if (!can('add_pick_carrier_group')) {
            echo "<h1 style='color: red'>没有添加分组的权限.</h1>";
            return null;
        }

        $carrierGroupItemsModel = new CarrierGroupItemModel('','', C('DB_CONFIG2'));
        $usedCarriers = $carrierGroupItemsModel -> getField('carrier', true);

        if(!$usedCarriers){
            $usedCarriers=[];
        }

        $carriers = array_diff($this -> carriers, $usedCarriers);

        //debug($carriers);
        $this -> assign('carriers', $carriers)-> display();
    }



    /**
     * 执行添加新的运输方式分组
     */
    public function addNewGroup()
    {
        //echo "<h1 style='color: red'>功能禁用.</h1>";
        //return null;
        if (!can('add_pick_carrier_group')) {
            echo "<h1 style='color: red'>没有添加分组的权限.</h1>";
            return null;
        }

        $name = trim($_POST['name']);
        $carriers = explode(',', $_POST['carrier']);
        if (!$name) {
            echo json_encode(['status' => false, 'data' => '【分组名称】 不能为空']);
            return null;
        } elseif (!$carriers) {
            echo json_encode(['status' => false, 'data' => '【包含运输方式】 不能为空.']);
            return null;
        }

        $pickCarrierGroupModel = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $pickCarrierGroupItemModel = new CarrierGroupItemModel('', '', C('DB_CONFIG2'));

        $data = [
            'group_name' => $name,
            'create_by'  => session('truename'),
            'create_at'  => date('Y-m-d H:i:s'),
            'memo'       => $_POST['carrierMemo'],
        ];

        try {
            $pickCarrierGroupModel->startTrans();
            $pickCarrierGroupModel-> data($data) -> add();

            $id = $pickCarrierGroupModel -> getLastInsID();
            foreach($carriers as $val) {
                $subData = ['group_id' => $id, 'carrier' => trim($val)];
                $pickCarrierGroupItemModel -> data($subData) -> add();
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'data' => '新创建运输方式分组保存失败.']);
            return null;
        }

        $pickCarrierGroupModel -> commit();
        echo json_encode(['status' => true, 'data' => '新运输方式分组数据保存成功.']);
    }


    /**
     * 展示更新运输方式组的页面
     * @param $groupId
     */
    public function showUpdateGroup($groupId)
    {
        if (!can('update_pick_carrier_group')) {
            echo "<h1 style='color: red'>没有更新分组的权限.</h1>";
            exit;
        }

        $carrierGroupModel = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $carrierGroupItemsModel = new CarrierGroupItemModel('','', C('DB_CONFIG2'));

        $carrierGroupInfo  = $carrierGroupModel -> field('id, group_name, memo') -> find($groupId);
        $groupCarrier  = $carrierGroupItemsModel -> where(['group_id' => $groupId]) -> getField('carrier', true);
        $carriers = $carrierGroupItemsModel -> where(['group_id' => ['neq', $groupId]]) -> getField('carrier', true);

        $unUsed = array_diff($this -> carriers, $carriers ? $carriers : []);

        $this -> assign('carrierInfo', $carrierGroupInfo)
              -> assign('avaliable', $unUsed)
              -> assign('groupCarrier', $groupCarrier)
              -> display();
    }


    /**
     * 执行运输方式更新的操作
     * @param $groupId
     * @return null
     */
    public function doUpdateGroup($groupId)
    {
        if (!can('update_pick_carrier_group')) {
            echo "<h1 style='color: red'>没有更新分组的权限.</h1>";
            exit;
        }

        $name = trim($_POST['name']);
        $memo = $_POST['carrierMemo'];

        // 新确认的分组中的运输方式
        $carriers = explode(',', trim($_POST['carrier']));

        if (!$name) {
            echo json_encode(['status' => false, 'data' => '运输方式分组名称不能为空.']);
            return null;
        } elseif (!$carriers) {
            echo json_encode(['status' => false, 'data' => '运输方式分组中的运输方式不能为空.']);
            return null;
        }

        $carrierGroupModel     = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $carrierGroupItemModel = new CarrierGroupItemModel('', '', C('DB_CONFIG2'));

        // 该运输方式分组中原有的运输方式
        $oldCarriers = $carrierGroupItemModel -> where(['group_id' => $groupId]) -> getField('carrier', true);

        // 找出需要添加的 和 需要删除的运输方式
        $deleteOnes = array_diff($oldCarriers, $carriers);
        $addOnes    = array_diff($carriers, $oldCarriers);

        $isFind = $carrierGroupModel -> find($groupId);
        if (!$isFind) {
            echo json_encode(['status' => false, 'data' => '未找到指定的运输方式分组.']); return null;
        }

        try {
            $carrierGroupModel->startTrans();
            $carrierGroupModel->where(['id' => $groupId]) -> data(['group_name' => $name, 'memo' => $memo]) -> save();

            foreach ($deleteOnes as $k => $val) { $carrierGroupItemModel -> where(['group_id' => $groupId, 'carrier' => $val]) -> delete();}
            foreach ($addOnes as $key => $value) { $carrierGroupItemModel -> add(['group_id' => $groupId, 'carrier' => $value]);}

        } catch (Exception $e) {
            echo json_encode(['status' => false, 'data' => '运输方式分组更新失败。']); return null;
        }

        $carrierGroupModel -> commit();
        echo json_encode(['status' => true, 'data' => '运输方式分组数据更新成功!']);
    }


    /**
     * 删除一个分组包括其运输方式
     * @param $groupId
     * @return boolean
     */
    public function deleteGroup($groupId)
    {
        $carrierGroupModel     = new CarrierGroupModel('', '', C('DB_CONFIG2'));
        $carrierGroupItemModel = new CarrierGroupItemModel('', '', C('DB_CONFIG2'));

        try {
            $carrierGroupModel->startTrans();

            // 分别删除分组的主信息和子信息
            $carrierGroupModel->where(['id' => $groupId])->delete();
            $carrierGroupItemModel -> where(['group_id' => $groupId]) -> delete();

        } catch (Exception $e) {

            echo json_encode(['status' => false, 'data' => '运输方式分组删除失败.']);
            return false;
        }

        $carrierGroupModel -> commit();
        echo json_encode(['status' => true, 'data' => '运输方式分组删除成功.']);
        return true;
    }

    /**
     * @desc 无法或找单物流分组配置首页
     * @author Shawn
     * @date 2019/3/26
     */
    public function unableOrderSetting()
    {
        $carrierGroupModel = new UnableOrderCarrierGroupModel();
        $counts = $carrierGroupModel -> count();

        $pageObj = new Page($counts, $this -> pageSize);

        $limit   = $pageObj -> firstRow.', '.$pageObj -> listRows;
        $pageStr = $pageObj -> show();

        $carrierGroups = $carrierGroupModel
            -> limit($limit)
            -> select();

        $this -> assign('pageString', $pageStr)
            -> assign('carrierGroups', $carrierGroups)
            -> display();
        $this->display();
    }

    /**
     * @desc 新建无法货找单分组
     * @author Shawn
     * @date 2019/3/26
     */
    public function showCreateUnableGroup()
    {
        $id = (int)I("groupId");
        $group['id']            = $id;
        $group['group_name']    = '';
        $group['note']          = '';
        $group['carrier']       = '';
        if($id > 0){

            $carrierGroupModel = new UnableOrderCarrierGroupModel();
            $data = $carrierGroupModel->where(['id'=>$id])->find();
            if(!empty($data)){
                $group = $data;
                $group['carrier'] = explode(",",$data['carrier']);
            }
        }
        $carrierGroupItemsModel = new CarrierGroupItemModel('','', C('DB_CONFIG2'));
        $configCarriers  = $carrierGroupItemsModel -> getField('carrier', true);

        // ebay_carrier 表中的启用状态的渠道
        $carrierModel = new CarrierModel();
        $carrierInfo  = $carrierModel->field('name')->select();
        $carriers     = [];

        foreach ($carrierInfo as $carrier) {
            $carriers[] = $carrier['name'];
        }

        // ebay_carrier 表未更新的渠道
        $diffCarrier = array_diff($carriers,$configCarriers);
        $needCarrier = [];

        foreach ($diffCarrier as $k => $item) {
            $needCarrier[] = $diffCarrier[$k];
        }
        $this -> assign('carriers', $needCarrier);
        $this -> assign('group', $group);
        $this -> display();
    }

    /**
     * @desc 添加无法货找单物流分组
     * @author Shawn
     * @date 2019/3/26
     */
    public function addUnableGroup()
    {
        $name = trim($_POST['name']);
        $carriers = trim($_POST['carrier']);
        if (!$name) {
            $this->ajaxReturn(['status' => false, 'data' => '【分组名称】 不能为空']);
        } elseif (!$carriers) {
            $this->ajaxReturn(['status' => false, 'data' => '【包含运输方式】 不能为空']);
        }
        $id = (int)trim($_POST['id']);
        $carrierGroupModel = new UnableOrderCarrierGroupModel();

        $data = [
            'group_name' => $name,
            'create_by'  => session('truename'),
            'note'       => $_POST['carrierMemo'],
            'carrier'    => $carriers,
        ];
        if($id>0){
            $result = $carrierGroupModel->where(["id"=>$id])->save($data);
        }else{
            $result = $carrierGroupModel->add($data);
        }
        if($result !== false){
            $this->ajaxReturn(['status' => true, 'data' => '分组数据保存成功']);
        }else{
            $this->ajaxReturn(['status' => true, 'data' => '分组数据保存失败']);
        }
    }

    /**
     * @desc 删除无法货找单分组
     * @author Shawn
     * @date 2019/3/26
     */
    public function deleteUnableGroup()
    {
        $groupId = (int)I("groupId");
        $carrierGroupModel = new UnableOrderCarrierGroupModel();
        $result = $carrierGroupModel->where(["id"=>$groupId])->delete();
        if($result !== false){
            $this->ajaxReturn(['status' => true, 'data' => '删除成功']);
        }else{
            $this->ajaxReturn(['status' => true, 'data' => '删除失败']);
        }
    }


}