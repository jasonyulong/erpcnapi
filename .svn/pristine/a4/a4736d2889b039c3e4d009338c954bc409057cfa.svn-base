<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name BaseController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/8/20
 * @Time: 18:17
 * @Description
 */

namespace Mobile\Controller;

use Think\Controller;
class BaseController extends Controller
{
    public function _initialize()
    {
        $this->checkUser();
    }

    /**
     * 判断当前是否登陆
     * @author Shawn
     * @date 2018/8/20
     */
    public function checkUser()
    {
        if (!$_SESSION['truename']) {
            $this->error('请先登陆', U('Mobile/Login/login'));
        }
    }

    /**
     * 获取用户ip地址
     * @return mixed
     * @author Shawn
     * @date 2018/8/21
     */
    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}