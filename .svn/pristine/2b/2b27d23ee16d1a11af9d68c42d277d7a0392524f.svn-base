<?php
class DBMysqli{
    public $link=null;
    /**********************
    使用构造函数连接数据库
     **********************/
    function __construct($arr){
        $host=$arr[0];
        $root=$arr[1];
        $password=$arr[2];
        $dbname=$arr[3];
        $port='3306';
        if(isset($arr[4])){
            $port=$arr[4];
        }
        if(!$this->link=@mysqli_connect($host,$root,$password,$dbname,$port)){
            echo $host."-database connected failure";
            exit;
        }else{
            mysqli_query($this->link,"SET NAMES utf8");
        }

    }

    /*************************
    执行查询语句
     *************************/
    function  query($sql){
        return @mysqli_query($this->link,$sql);
    }

    /*****************************************
    执行查询语句之外的操作 例如:添加，修改，删除
     *****************************************/

    function execute($sql){
        $result=$this->query($sql);
        return $result;
    }

    /*************************
    执行更新语句
     ************************/
    function update($sql){
        @mysqli_query($this->link,$sql);
        $rows	= mysqli_affected_rows($this->link);

        return $rows;
    }


    /**************************
    获得表的记录的行数
     *************************/
    function num_rows($result){
        if($result){
            return @mysqli_num_rows($result);
        }
        else{
            return 0;
        }
    }

    /***********************
    返回对象数据
     ************************/
    function fetch_object($result){
        return @mysqli_fetch_object($result);
    }

    /*************************
    返回关联数据
     *************************/
    function fetch_assoc($result){
        return @mysqli_fetch_assoc($result);
    }

    /**************************
    返回关联数据
     **************************/
    function fetch_array($result,$type='MYSQL_BOTH'){
        return @mysqli_fetch_array($result,$type);

    }

    /*************************
    关闭相关与数据库的信息链接
     **************************/

    function free_result($result){
       mysqli_free_result($result);
    }

    function close(){
        return @mysqli_close($this->link);
    }

    /*********************************
    其他操作例如结果集放入数组中
     *********************************/
    public function getResultArray($result){
        $array=array();
        $i=0;
        while($row=@mysqli_fetch_assoc($result)){
            $array[$i]=$row;
            $i++;
        }
        return $array;
    }
    public function getResultArrayBySql($sql) {
        $sql = $this->execute($sql);
        $sql = $this->getResultArray($sql);
        return $sql;
    }
}