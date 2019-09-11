<?php
namespace Common\Controller;

use Think\Controller;

class CommonController extends Controller
{
    public function _initialize() {
        $this->checkUser();
//        $this->setRoleName();
        $this->assign('power', $this->getAuth());

    }

    /*
     *判断当前是否登陆
     */
    public function checkUser() {
        if (!$_SESSION['truename']) {
            $this->error('请先登陆', '/login.php');
        }
    }

    /**
     * 创建人:朱诗萌 创建时间 2017-04-24 17:35:28
     * 说明 获取当前登录人员的角色
     */
    public function setRoleName() {
        $userModel = new \Products\Model\EbayUserModel();
        $_SESSION['roleName'] = $userModel->where(array('username' => $_SESSION['truename']))->getField('truename');
    }

    /*
     * 获取当前登陆用户的权限
     */
    public function getAuth() {
        $authstr = $_SESSION['power'];
        $autharr = explode(',', $authstr);
        return $autharr;
    }

    /*
     * 获取当前登陆用户id
     */
    public function getLoginUserId() {
        $loginUser = $_SESSION['truename'];
        $userModel = M('ebay_user');
        return $userModel->where(array('username' => $loginUser))->getField('id');
    }

    /*
     * 任务中心首页
     */
    public function index() {
        $this->display('index');
    }
}
