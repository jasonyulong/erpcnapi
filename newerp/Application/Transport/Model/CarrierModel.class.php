<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 19:05
 */

namespace Transport\Model;

use Think\Model;

class CarrierModel extends Model
{

    protected $tableName = "ebay_carrier";

    function getCarriers(){
        $where['status']=1;
        $where['ebay_warehouse']=['neq',''];
        $rs=$this->where($where)->field('id,name,ebay_warehouse')->select();
        $arr=[];
        foreach($rs as $list){
            $arr[$list['ebay_warehouse']][]=$list;
        }
        return $arr;
    }

    public function getCarrierByname($name){
        return $this->where("name='$name'")->find();
    }

    public function getCarrierById($id){
        return $this->where("id='$id'")->find();
    }

    function getShipNameByid($id){
        $rs=$this->where("id='$id'")->getField('name');
        return $rs;
    }

    public function getCount($where){
        $count=$this->where($where)->count();
        return $count;
    }

    public function getData($where,$limit,$order){
        if(!$order){
            $order = ' name desc ';
        }
        $field='`id`,`name`,value,note,`status`,Priority,illustration,skus,ebay_warehouse,use_fee,ageing,mark_code';
        $List = $this->field($field)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
        return $List;
    }

    //订单详情里面的select 专用的
    //标记 不重复的 运输方式
    //运输方式已经停用的就不要考虑
    public function getDataForOrderdetail(){
        $ebaystoreview=$_SESSION['viewstore'];
        $ebaystoreview=str_replace(['a.ebay_warehouse=','\'','or','and'],['','',',',''],$ebaystoreview);
        if($ebaystoreview!=''){
            $rs=$this->field('`name`,`status`')->where("ebay_warehouse in $ebaystoreview")->select();
        }else{
            $rs=$this->field('`name`,`status`')->select();
        }

        $grouped_values = [];
        foreach ($rs as $val) {
            $grouped_values[$val['name']] = isset($grouped_values[$val['name']]) && $grouped_values[$val['name']] == 1 ? 1 : $val['status'];
        }

        //debug($grouped_values);
        return $grouped_values;

    }

    /**
     * @desc 处理同步数据
     * @param $data
     * @return bool|mixed
     * @author Shawn
     * @date 2019/4/17
     */
    public function handleSyncData($data)
    {
        $result = false;
        if(empty($data)){
            return $result;
        }
        $action     = (string)$data['action'];
        $id         = $data['data']['id'];
        $map['id']  = $id;
        switch ($action){
            case 'del':
                $result = $this->where($map)->delete();
                break;
            case 'update':
                unset($data['data']['id']);
                $result = $this->where($map)->save($data['data']);
                break;
            case 'add':
                $result = $this->add($data['data']);
                break;
        }
        $bol = ($result !== false) ? true :false;
        return $bol;
    }
}