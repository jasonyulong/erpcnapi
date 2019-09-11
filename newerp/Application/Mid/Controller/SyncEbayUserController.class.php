<?php
/**
 * User: 王模刚
 * Date: 2017/10/27
 * Time: 15:58
 */

namespace Mid\Controller;


use Think\Controller;

class SyncEbayUserController extends Controller
{
    /**
     * mid_ebay_user 同步到 ebay_user  此方法可以废弃，因为ebay_user不用走中间表
     * @author 王模刚
     * @since 2017 10 27
     * @link  http://local.erpanapi.com/t.php?s=/Sync/ebayUser/syncEbayUser
     */
    public function syncEbayUser(){
        $midEbayUserModel = new \Mid\Model\MidEbayUserModel();
        $erpEbayUserModel = new \Common\Model\EbayUserModel();
        $ebayUserInfo = $midEbayUserModel->select();
//        dump($ebayUserInfo);
        foreach($ebayUserInfo as $k => $v){
            $data = array(
                'id' => $v['id'],
                'username' => $v["username"],
                'password' => $v["password"],
                'truename' => $v["truename"]?:'',
                'country' => $v["country"],
                'provience' => $v["provience"],
                'tel' => $v["tel"],
                'mail' => $v["mail"],
                'py' => $v["py"],
                'record' => $v["record"],
                'message' => $v["message"],
                'regdate' => $v["regdate"],
                'logtime' => $v["logtime"],
                'ip' => $v["ip"],
                'active' => $v["active"],
                'user' => $v["user"]?:'',
                'power' => $v["power"],
                'ebayaccounts' => $v["ebayaccounts"],
                'orderscountry' => $v["orderscountry"],
                'cgviewuser' => $v["cgviewuser"]?:'',
                'admin' => $v["admin"]?:'',
                'sup' => $v["sup"]?:'',
                'viewstore' => $v["viewstore"]?:'',
                'vieworder' => $v["vieworder"]?:'',
                'isauth' => $v["isauth"],
                'free_verif' => $v["free_verif"]?:0,
                'is_app' => $v["is_app"],
                'is_del' => $v["is_del"]?:0,
                'del_time' => $v["del_time"],
                'is_use' => $v["is_use"]?:1
            );
            $row = $erpEbayUserModel->where(['username'=>$v['username']])->find();
            if($row){
                $res = $erpEbayUserModel->where(['username'=>$v['username']])->save($data);
                if($res === false){
                    $return = $v['ebay_id'].'修改用户失败！';
                }else{
                    $return = $v['ebay_id'].'修改用户成功！';
                }
            }else{
                $res = $erpEbayUserModel->add($data);
                if($res === false){
                    $return = $v['ebay_id'].'添加用户失败！';
                }else{
                    $return = $v['ebay_id'].'添加用户成功！';
                }
            }
            dump($return);
        }
    }
}