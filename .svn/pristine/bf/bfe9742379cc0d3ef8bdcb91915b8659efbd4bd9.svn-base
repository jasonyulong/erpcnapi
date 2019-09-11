<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 11:02
 */

namespace Mid\Controller;


use Think\Controller;

class GetUserController extends Controller
{
    /**get ebay order
     * @author 王模刚
     * @since 2017 10 25 18:00
     * @link   http://local.erpanapi.com/t.php?s=/Mid/User/getUserList
     */
    public function getUserList(){
        $userService = new \Mid\Service\UserService();
        $userService -> getSyncUserList();
    }
}