<?php

    namespace Transport\Controller;

    use Think\Controller;
    use Transport\Service\LabelService;

    class LableController extends Controller {

        // 加入队列
        //  /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Transport/Lable/addlist
        public function addlist(){
            $LableService=new LabelService();
            $LableService->addList();
        }


        //弹出list check Lable
        // /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php Transport/Lable/popListCheckLable
        public function popListCheckLable(){
            $LableService=new LabelService();
            $LableService->popOrderCheckLables();
        }


        public function testConvert(){
            $LableService=new LabelService();
            $LableService->testConvert();
        }
    }