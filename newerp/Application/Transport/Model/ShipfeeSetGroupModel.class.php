<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 *
 * 预估运费设置的 分组关系
 */

namespace Transport\Model;

use Think\Model;

class ShipfeeSetGroupModel extends Model
{

    protected $tableName = "shipfee_calc_group";

    public $typeName=['1'=>'开发用','2'=>'刊登用','3'=>'通用'];
    /**
     * @param $id
     * @return mixed
     *  获取 一个设置好的运费规则的全部 信息
     */
    function getOneBudgetGroup($id){
        if(empty($id)){return [];}
        $rs= $this->where("id='$id'")->find();
        $SHIPFeeSet=new ShipfeeSetModel();
        $Systemshipfee=new SystemshipfeeModel();
        $Carrier      =new CarrierModel();

        $str=$rs['idstr'];
        $feeset=$SHIPFeeSet->where("id in($str)")->select();
        foreach($feeset as $key=>$list){
            $feeset[$key]['carriername']=$Carrier->getShipNameByid($list['shipid']);
            $feeset[$key]['shipfeename']=$Systemshipfee->getShipfeeNameByid($list['usezone']);
        }
        $rs['ShipfeeSet']=$feeset;
        return $rs;
    }

    function getBudgetGroupInfo($id){
        if(empty($id)){return [];}
        return $this->where("id='$id'")->find();
    }

    public function getCount($where){
        $count=$this->where($where)->count();
        return $count;
    }

    public function getData($where,$limit,$order){
        if(!$order){
            $order = ' name desc ';
        }
        $field='*';
        $List = $this->field($field)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();

        $SHIPFee=new SystemshipfeeModel();
        $Carrier=new CarrierModel();
        foreach($List as $k=>$list){
            $usezone=$list['usezone'];
            $r=$Carrier->getCarrierByid($list['shipid']);
            $List[$k]['carriername']=$r['name'];
            $List[$k]['shipfee']=$SHIPFee->where("id='$usezone'")->find();
        }
        return $List;
    }

    function indexOfID($id){
        $rs=$this->where("idstr like '%$id%'")->field('name,idstr')->select();
        $names='';
        foreach($rs as $list){
            $idstr=$list['idstr'];
            $idstrArr=explode(',',$idstr);
            if(in_array($id,$idstrArr)){
                $names.=','.$list['name'];
            }
        }
        return trim($names,',');
    }

    //给开发用的
    //选出所有的通用  和 开发用的组
    // 和组里面的默认的方案!
    function getDefaultForKF($storeid=null){
        if(is_null($storeid)){
            $storeid = C("CURRENT_STORE_ID");
        }
        $map['type']=['in',[1,3]];
        $map['storeid']=$storeid;
        $idstr=$this->where($map)->getField('idstr');
        $map = array('id' => array('in', $idstr));
        $ShipfeeSetModel = new \Transport\Model\ShipfeeSetModel();
        $rr = $ShipfeeSetModel->where($map)->field('usezone,shipname')->select();
//        echo $this->_sql();
        return $rr;
    }
}