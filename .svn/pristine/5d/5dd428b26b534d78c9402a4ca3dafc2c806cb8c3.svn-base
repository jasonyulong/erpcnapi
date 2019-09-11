<?php
class DBClass {
    public $link = null;
    /**********************
     * 使用构造函数连接数据库
     **********************/
    //function __construct($host = "localhost", $root = "root", $password = "123456", $dbname = "v3-all") {

    function __construct($host = "192.168.1.91", $root = "www", $password = "123456", $dbname = "erpapi") {
        if (!$this->link = @mysql_connect($host, $root, $password)) {
            echo "database connected failure" . mysql_error();
            exit;
        } else {


        }
        mysql_query("SET NAMES utf8");
        if (!@mysql_select_db($dbname)) {

            echo "数据库连接失败";
            die();


        }

    }

    /*************************
     * 执行查询语句
     *************************/
    function  query($sql) {
        return @mysql_query($sql);
    }

    /*****************************************
     * 执行查询语句之外的操作 例如:添加，修改，删除
     *****************************************/

    function execute($sql) {
        if (@function_exists("mysql_unbuffered_query")) {
            $result = @mysql_unbuffered_query($sql);
        } else {
            $result = @mysql_query($sql);
        }
        return $result;
    }

    /*************************
     * 执行更新语句
     ************************/
    function update($sql) {
        if (@function_exists("mysql_unbuffered_query")) {
            $result = @mysql_unbuffered_query($sql);
        } else {
            $result = @mysql_query($sql);
        }
        $rows = mysql_affected_rows($this->link);

        return $rows;
    }


    /**************************
     * 获得表的记录的行数
     *************************/
    function num_rows($result) {
        if ($result) {
            return @mysql_num_rows($result);
        } else {
            return 0;

        }
    }

    /***********************
     * 返回对象数据
     ************************/
    function fetch_object($result) {
        return @mysql_fetch_object($result);
    }

    /*************************
     * 返回关联数据
     *************************/
    function fetch_assoc($result) {
        return @mysql_fetch_assoc($result);
    }

    /**************************
     * 返回关联数据
     **************************/
    function fetch_array($result, $type = 'MYSQL_BOTH') {
        return @mysql_fetch_array($result, $type);

    }

    /*************************
     * 关闭相关与数据库的信息链接
     **************************/

    function free_result($result) {
        return @mysql_free_result($result);
    }

    function close() {
        return @mysql_close();
    }

    /*********************************
     * 其他操作例如结果集放入数组中
     *********************************/
    public function getResultArray($result) {
        $array = array();
        $i     = 0;
        while ($row = @mysql_fetch_assoc($result)) {
            $array[$i] = $row;
            $i++;
        }

        @mysql_free_result($result);
        return $array;
    }

    public function getResultArrayBySql($sql) {
        $sql = $this->execute($sql);
        $sql = $this->getResultArray($sql);
        return $sql;
    }

    // 如果主键是id 则不需要传 第二个参数
    function getTotal($sql,$replaceStr='id',$debug=false){
        // 去掉字段
        $sql=preg_replace('/select\s(.*)\sfrom/i','select count('.$replaceStr.') as cc from',$sql);
        //去掉排序
        if(stristr($sql,'order by',true)!==false){
            $sql=stristr($sql,'order by',true);
        }

        $sqlrs=$this->getResultArrayBySql($sql);
        if($debug){
            echo $sql.'<br>';
        }
        // 分组查询获取总行数
        if(stristr($sql,' group ')!==false){
            return count($sqlrs);
        }

        return $sqlrs[0]['cc'];
    }
    
}