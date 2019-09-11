<?php
/**
 * 创建人:朱诗萌 创建时间 2017-04-14 11:48:12
 * 说明 账号利润率
 */
namespace Transport\Model;

use Think\Model;

class ProfitForAccountModel extends Model
{
    protected $tableName = 'sys_profit_for_account';
    protected $_validate = array(
        array('profit', 'require', '利润率必须填写'),
        array('platform', 'require', '平台必须选择'),
        array('account', 'require', '账号必须选择'),
        array('store_id', 'require', '仓库必须选择'),
    );

    /**
     * 创建人:朱诗萌 创建时间 2017-04-14 17:05:48
     * 说明 新增或更新
     */
    public function update($data) {
        $saveData = $this->create($data);
        $organize_id = \Common\Util\Organize::getOrganizeIdByUserName($_SESSION['truename']);
        $saveData['organize_id'] =$organize_id?$organize_id:0;
        $saveData['user_id'] = $_SESSION['id'];
        if (empty($saveData)) {
            return false;
        }
        if ($saveData['id']) {
            $checkRes = $this->checkExists(array('id' => array('neq', $saveData['id']), 'account' => $saveData['account'], 'store_id' => $saveData['store_id']));
            if (empty($checkRes)) {
                $saveData['update_time'] = date('Y-m-d H:i:s',time());
                return $this->save($saveData);
            } else {
                $this->error = '账号和仓库都相同的记录已存在,请修改原记录';
                return false;
            }
        } else {
            $checkRes = $this->checkExists(array('account' => $saveData['account'], 'store_id' => $saveData['store_id']));
            if (empty($checkRes)) {
                $saveData['update_time'] = date('Y-m-d H:i:s',time());
                $saveData['create_time'] = date('Y-m-d H:i:s',time());
                return $this->add($saveData);
            } else {
                $this->error = '账号和仓库都相同的记录已存在,请修改原记录';
                return false;
            }
        }
    }

    /**
     * 创建人:朱诗萌 创建时间 2017-04-14 17:15:44
     * 说明 去除重复的记录
     */
    public function checkExists($condition) {
        return $this->where($condition)->find();
    }
}