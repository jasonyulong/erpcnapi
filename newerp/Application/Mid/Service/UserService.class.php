<?php
/**
 * User: 王模刚
 * Date: 2017/10/26
 * Time: 11:05
 */

namespace Mid\Service;


class UserService extends BaseService
{
    public function index(){
        echo 'index';
    }
    /*
     * 获取并存储用户数据   直接存储   不需要中间表过度
     * @author 王模刚
     * @since 2017 10 26 11:00
     */
    public function getSyncUserList(){
        ini_set('memory_limit', '628M');
        $requestData = ['stime'=> '2017-10-26 00:00:00'];
        $action = 'User/getUserList/wid/'.$this->currentid;
        $userList = $this->getErpData($requestData,$action);
        echo "==\n";
        print_r($userList);
        if($userList['ret'] != 100){
            die('No data!');
        }
        $userListData = $userList['data'];
        $erpEbayUserModel = new \Common\Model\EbayUserModel();
        $userArr =[];
        foreach($userListData as $k => $v ){
            $saveData = array(
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
//            ,'wms_add_time' => date('Y-m-d H:i:s',time()),
//                'wms_add_time' => date('Y-m-d H:i:s',time())
            );
            $res = $erpEbayUserModel ->saveData($saveData);
            //dump($res);
            //dump($erpEbayUserModel->_sql());
            if(false !== $res){
                $userArr[] = $v['id'];
            }
        }
        if(!empty($userArr)){
            $userids = implode(',',$userArr);
            $updateAction = 'User/updateUserSyncStatus/wid/'.$this->currentid.'/user_ids/'.$userids;
            $msg = '修改用户同步状态';
            $this->updateStatus($updateAction,$msg);
        }
    }

    /**
     * 根据获取的返回值，来改变v3-all中的状态，避免更新过的数据重复更新
     * @author 王模刚
     * @since 2017 10 28
     */
    private function updateStatus($action,$returnmsg){
        $requestData = ['stime' => '2017-10-28 00:00:00'];
        $return = $this->getErpData($requestData,$action);
        if($return['ret'] != 100){
            echo $returnmsg.' 失败!失败原因：'.$return['msg'].'<br>';
        }else{
            echo $returnmsg.' 成功！'.'<br>';
        }
    }
}