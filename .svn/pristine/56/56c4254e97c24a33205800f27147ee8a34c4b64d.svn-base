<?php
namespace Common\Service;

class SendEmailService{

    protected $apiEmailUrl = 'http://erp.wst/t.php?s=/Sale/MailAPI/sendMail';

    //默认接收邮箱
    protected $defaultEmails = array(
        0 => 'tan@oobest.com'
    );

    private $recipient=[];

    private $title;
    private $body;

    public function setRecipient($arr){
        $this->recipient=$arr;
    }

    public function setTitle($title){
        $this->title=$title;
    }

    public function setBody($body){
        $this->body=$body;
    }


    public function Send(){
        $params['subject'] = $this->title;
        $params['body'] = $this->body;
        $params['acceptors_list'] = empty($this->recipient)?$this->defaultEmails:$this->recipient;
        $result = $this->curlPost($params,$this->apiEmailUrl);
        print_r($result);
        echo "done\n\n";
    }

    /**
     * curl请求接口
     * @param $vars
     * @param $url
     * @return mixed
     * @author Shawn
     * @date 2018/9/20
     */
    private function curlPost($vars,$url){
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