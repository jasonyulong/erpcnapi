<?php
namespace Api\Service;

use Common\Model\OrderModel;
use Mid\Service\BaseService;

class EUBService extends BaseService{
    public function __construct() {
        parent::__construct();
    }


    function UpdateEubPxorderid(){
        $Ordermodel=new OrderModel();
        $status=[1723,1724,2009];
        $carrier=['EUB'];

        $map['ebay_carrier']=['in',$carrier];
        $map['ebay_status']=['in',$status];
        $map['_string']=" pxorderid IS NULL or pxorderid!='EUB已交运(新)' ";

        $Rs=$Ordermodel->where($map)->field('ebay_id')->limit(500)->select();

        echo $Ordermodel->_sql();
        echo "\n\n";

        $str='';
        foreach($Rs as $List){
            $str.=','.$List['ebay_id'];
        }

        $str=trim($str,',');

        if(empty($str)){
            echo '啥都没有'."\n\n";
            return false;
        }

        $action   = 'EubOrder/searchpxorderid/wid/'.$this->currentid;
        $rr=$this->getErpData(['bill'=>$str],$action);



        print_r($rr);



        foreach($rr['data'] as $List){
            $ebay_id=$List['ebay_id'];
            $pxorderid=$List['pxorderid'];

            if($pxorderid=='EUB已交运(新)'){
                $map=[];
                $map['ebay_id']=$ebay_id;
                $map['ebay_status']=['in',$status]; // wms 状态正常 才更新

                $Ordermodel->where($map)->limit(1)->save(['pxorderid'=>$pxorderid]);

                echo $ebay_id.',交运信息更新OK'."\n";
            }else{
                echo $ebay_id.',交运信息不更新---'.$pxorderid."\n";
            }

        }
    }
}