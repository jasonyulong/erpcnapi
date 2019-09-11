<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 10:54
 */

include_once __DIR__."/dbconnect.php";
$dbc = new DBClass();

class Ebay_Onhandle_all_union{

    private static $eHandle;
    private $dbc ;
    private $fields;
    private $where;
    private $group;
    private $order;
    private $limit;
    private $data = array();
    private $last_sql;
    private $distinct = '';
    private $tableJoin = '';
    private $frontField = '';
    private $owhere = '';
    private $joinCondition = '';


    private function __construct(DBClass $db)
    {
        $this->dbc = $db;
    }


    public static function getInstance(DBClass $db){
        if(!self::$eHandle){
            self::$eHandle = new self($db);
        }
        return self::$eHandle;
    }

    public function setFields($fields = '*'){
        $this->fields = $fields;
        return $this;
    }

    public function where($where){
        $this->where = $where;
        return $this;
    }

    public function groupBy($group){
        $this->group = $group;
        return $this;
    }

    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function orderBy($order){
        $this->order = $order;
        return $this;
    }

    public function data(array $data){
        $this->$data = $data ;
        return $this;
    }

    public function distinct($field){
        $this->distinct = $field;
        return $this;
    }



    private function delete_sql_part(){
        return $this->loop_operate('delete from ebay_onhandle_');
    }

    private function update_sql_part(){
        $data = $this->data;
        $pares = array();
        if(!$data){
            throw new Exception('Update data is not set !');
        }else{
            foreach($data as $key=>$value){
               $pares[] = $key.'="'.$value.'"';
            }
            $setter = ' set '.join(',',$pares);
        }
        return $this->loop_operate('update ebay_onhandle_' , $setter);
    }


    private function loop_operate($condition , $second = ''){
        $ids = $this->get_store_ids();
        foreach($ids as $key=>$id){
            $this->dbc->execute("$condition{$id} {$second} {$this->where}");
        }
        return true;
    }



    private function select_sql_part(){
        $ids = $this->get_store_ids();
        $distinct = $this->distinct ? 'distinct '.$this->distinct : '';
        $fields = $this->fields ? $this->fields : $distinct;
        $headerSql = "(select {$fields} from ebay_onhandle_";
        $tailSql = ") union ";
        $unionSql = '';
        $conditions = $this->getConditions();
        foreach($ids as $id){
            $unionSql .= $headerSql.$id['id'].$conditions.$tailSql;
        }
        return rtrim($unionSql , ' union ');
    }

    private function get_store_ids(){
        $sql = "select id from ebay_store";
        $handler = $this->dbc->execute($sql);
        $ids = $this->dbc->getResultArray($handler);
        return $ids;
    }


    /*
     *
     *
     * SELECT n.goods_id FROM ebay_goods AS g
        INNER JOIN ((SELECT goods_id FROM ebay_onhandle_196 AS on1)
        UNION(SELECT goods_id FROM ebay_onhandle_197 AS on2)
        UNION(SELECT goods_id FROM ebay_onhandle_198 AS on3)) AS n
        ON n.goods_id = g.goods_id;
     *
     * */


    public function tableJoin( $table ){
        $this->tableJoin = $table;
        return $this;
    }

    public function joinCondition($condition){
        $this->joinCondition = $condition ;
        return $this;
    }

    public function frontField($frontField){
        $this->frontField = $frontField;
        return $this;
    }

    public function o_where($where){
        $this->owhere = $where;
        return $this;
    }

    private function join_sql_part($position = 'inner'){

        if(is_array($this->frontField)){
            $frontField = join(',',$this->frontField);
        }else{
            $frontField = $this->frontField;
        }
        $owhere = $this->owhere ? ' where '.$this->owhere :'';
        $limit = $this->limit ? ' limit '.$this->limit : '';
        return $this->tableJoin ? 'select '.$frontField.' from '.$this->tableJoin.' '.$position.' join ('. $this->select_sql_part().') as ebay_onhandle on '.$this->joinCondition.$owhere.$limit : '';
    }

    private function sql($type){

        $sql = '';
        switch ($type){
            case 'select':
                $sql = $this->select_sql_part();
                break;
            case 'delete':
                $sql = $this->delete_sql_part();
                break;
            case 'update':
                $sql = $this->update_sql_part();
                break;
            case 'inner join':
            case 'join':
                $sql = $this->join_sql_part();
                break;
            case 'left join':
                $sql = $this->join_sql_part('left');
                break;
            case 'right join':
                $sql = $this->join_sql_part('right');
                break;
        }
        return $sql;
    }


    public function execute($type='select'){

        $re = $this->last_sql = $this->sql($type);

        if($type=='delete'||$type == 'update'){
            return $re;
        }

        $tmp = $this->dbc->getResultArrayBySql($this->last_sql);

        return $tmp;
    }

    protected function getConditions(){
        $where = $this->where?' where '.$this->where.' ':'';
        $group = $this->group? ' group by '.$this->group.' ':'';
        $order = $this->order ? ' order by '.$this->order.' ':'';
        $limit = $this->limit ? ' limit '.$this->limit.' ': '';
        return $where.$group.$order.$limit;
    }

    public function get_last_sql(){
       return $this->last_sql;
    }


}




