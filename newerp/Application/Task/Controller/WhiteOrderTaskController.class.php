<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name WhiteOrderTaskController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2018/9/20
 * @Time: 15:04
 * @Description 白单检测程序
 */

namespace Task\Controller;

use Order\Model\EbayOrderModel;
use Order\Model\OrderTypeModel;
use Think\Controller;

class WhiteOrderTaskController extends Controller
{
    //发送邮件api地址
    protected $apiEmailUrl = 'http://erp.wst/t.php?s=/Sale/MailAPI/sendMail';
    //默认接收邮箱
    protected $defaultEmails = array(
        0 => 'tan@oobest.com',
        1 => '376526771@qq.com',
        2 => '1171465336@qq.com',
    );
    //默认最大值，超过给予邮件提醒
    protected $max = 10;
    //邮件标题
    protected $title = '仓库转PDF面单错误';

    /**
     * 初始化
     * @author Shawn
     * @date 2018/9/20
     */
    public function _initialize() {
        if(!IS_CLI){
            echo "please run in cli";exit;
        }
    }

    /**
     * 白单检测程序
     * @author Shawn
     * @date 2018/9/20
     */
    public function whiteOrderCheck(){
        $orderTypeModel = new OrderTypeModel("","",C('DB_CONFIG_READ'));
        $map['a.ebay_id'] = array("gt",'17000000');
        $map['a.have_lable'] = 0;
        $map['a.err_count'] = array("egt",3);
        $map['b.ebay_status'] = array("in",array(1723,1724,1745,2009));
        $data = $orderTypeModel->alias("a")
            ->join("inner join erp_ebay_order b USING(ebay_id)")
            ->where($map)
            ->field("b.ebay_id,b.ebay_carrier")
            ->order("a.ebay_id ASC")
            ->limit(50000)
            ->select();
        echo $orderTypeModel->_sql();
        echo "\n\n";
        echo "异常订单量:". count($data) ."\n\n";

        if(!empty($data) && count($data) > $this->max){
            $body = '<h2>仓库PDF面单错误</h2> <table border="1">';

            foreach($data as $List){
                $body.='<tr><td>'.$List['ebay_id'].'</td><td>'.$List['ebay_carrier'].'</td></tr>';
            }
            $body.='</table>';
            $params['subject'] = $this->title;
            $params['body'] = $body;
            $params['acceptors_list'] = $this->defaultEmails;
            $result = $this->curlPost($params,$this->apiEmailUrl);
            echo "done";exit;
        }else{
            echo count($data)."\n\n";
            echo "don't need email";exit;
        }
    }

    /**
     * curl请求接口
     * @param $vars
     * @param $url
     * @return mixed
     * @author Shawn
     * @date 2018/9/20
     */
    function curlPost($vars,$url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($vars));
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}