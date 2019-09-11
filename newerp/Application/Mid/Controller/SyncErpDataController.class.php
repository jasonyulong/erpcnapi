<?php
/**
 * @Copyright (C), 2018-2019, 卓士网络科技有限公司, shawn.sean@foxmail.com
 * @Name SyncErpDataController.class.php
 * @Author Shawn
 * @Version v1.0
 * @Date: 2019/4/11
 * @Time: 13:55
 * @Description 同步erp数据
 */
namespace Mid\Controller;
use Common\Service\SendEmailService;
use Mid\Service\SyncErpDataService;
use Think\Cache\Driver\Redis;
use Think\Controller;
class SyncErpDataController extends Controller
{
    /**
     * redis key值
     * @var string
     */
    protected $redisKey = 'need_sync_data:';
    /**
     * 当前仓库id
     * @var int
     */
    protected $currStoreId = null;


    /**
     * 需要更新的表
     * @var null
     */
    protected $allTable = null;

    /**
     * erp redis 连接配置
     * @var array
     */
    protected $options = array();

    /**
     * 最大执行队列长度
     * @var int
     */
    protected $maxLimit = 0;

    /**
     * redis 分库
     * @var int
     */
    protected $database = 0;

    /**
     * 接收邮箱
     * @var array
     */
    protected $emailArr = array();

    /**
     * 检查数据表
     * @var array
     */
    protected $checkTable = array();


    private $debug=1;

    private $tableArr = array(
        "ebay_goods",
        "ebay_productscombine",
        "ebay_user",
        //"ebay_onhandle_196",
        //"ebay_onhandle_234",
        //"goods_location_history",
        "goods_location_picker_region",
        "ebay_carrier",
        "ebay_carrier_company",
        "internal_store_sku",
        "ebay_countries",
        "ebay_countries_alias",
        "ebay_currency",
        "ebay_glock",

    );
    /**
     * 初始化
     * SyncErpDataController constructor.
     */
    public function _initialize()
    {
        if(!IS_CLI && APP_ENV != "development"){
            exit("must run in cli");
        }
        $this->currStoreId  = C("CURRENT_STORE_ID");
        $this->allTable     = C("NEED_SYNC_TABLE_ARR");
        $this->options      = C("ERP_REDIS_CONFIG");
        $this->maxLimit     = C("MAX_QUEUE_LIMIT");
        $this->database     = C("REDIS_DATABASE_ID");
        $this->emailArr     = C("RECEIVE_EMAIL_ARR");
        $this->checkTable   = C("NEED_CHECK_TABLE_DATA");
    }

    /**
     * @desc 读取队列
     * @cli /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/queue/table/配置里面的数据表/action/对应方法名
     * @return bool
     * @author Shawn
     * @date 2019/4/11
     */
    public function queue()
    {
        $table  = trim(I("table"));
        $action = trim(I("action"));
        $debug  = $this->debug;
        if(empty($table) || empty($action)){
            p("必要参数为空");
            return false;
        }
        if(empty($this->options)){
            p("连接ERP Redis 配置为空");
            return false;
        }
        if($debug == 1){
            p("DEBUG模式开启，不会更新数据表，只会读取redis");
        }
        $redisService = new Redis($this->options);
        $cacheKey = $this->redisKey.$table.':'.$this->currStoreId;
        //选择redis库
        $redisService->select($this->database);
        if($redisService->exists($cacheKey)){
            $len = $redisService->Llen($cacheKey);
            if($len <= 0){
                p("数据表：".$table."不存在需要更新数据");
                return false;
            }
            p("队列总数:".$len);
            $queueLength = ($len <= $this->maxLimit) ? $len : $this->maxLimit;
            p("执行队列数：".$queueLength);
            $syncErpDataService = new SyncErpDataService();
            for($i=0;$i<=$queueLength;$i++){
                $pop = $redisService->BRPOP($cacheKey, 1);
                if (empty($pop) || !is_array($pop)) {
                    p("数据表：".$table."待更新数据为空");
                    break;
                }
                $tempData = json_decode($pop[1], true);
                if(empty($tempData)){
                    p("解析队列数据为空");
                    continue;
                }
                if($debug == 1 && !in_array($table,$this->tableArr)){
                    p("Debug开启：跳过更新数据表");
                    print_r($tempData);
                }else{
                    $result = $syncErpDataService->$action($tempData);
                    p($result['msg']);
                }
            }
        }else{
            p("redis不存在该key：".$cacheKey);
            return false;
        }
        return true;
    }

    /**
     * @desc 根据需要更新的数据表开启多个进程
     * @cli /usr/local/php/bin/php -c /usr/local/php/etc/php.cli.ini /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/multiProcess
     * @return bool
     * @author Shawn
     * @date 2019/4/11
     */
    public function multiProcess()
    {
        $debug = (int)I("debug");
        if(empty($this->allTable)){
            p("需要更新表配置获取失败");
            return false;
        }
        p("需要更新表".print_r($this->allTable));
        $i = 0;
        foreach ($this->allTable as $key=>$value){
            $i++;
            p("开启数据表：".$key."进程");
            $shell="/usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/queue/table/".$key."/action/".$value." 2>/dev/null  >/dev/null & ";
            echo $shell."\n\n";
            system($shell);
            sleep(3);
        }
        p("所有进程开启完毕，总进程数：".$i);
        return true;
    }

    /**
     * @desc 同步异常发送邮件
     * @cli /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/queueAbnormal
     * @return bool
     * @author Shawn
     * @date 2019/4/12
     */
    public function queueAbnormal()
    {
        $syncErpDataService = new SyncErpDataService();
        $data = $syncErpDataService->queueAbnormal($this->maxLimit);
        if(empty($data['data'])){
            p($data['msg']);
        }else{
            $sendEmailService = new SendEmailService();
            $sendEmailService->setTitle("同步ERP数据表异常");
            $sendEmailService->setRecipient($this->emailArr);
            $body = "<table style=\"width:500px;\" cellpadding=\"5\" cellspacing=\"0\" border=\"1\" bordercolor=\"#333\"><tbody><tr><th>表名</th><th>数据</th><th>操作</th><th>message</th></tr>";
            foreach ($data['data'] as $k=>$v){
                $table      = $v['table'];
                $tempData   = $v['data'];
                $action     = $v['action'];
                $message    = $v['message'];
                $body .= "<tr><td>{$table}</td><td>".print_r($tempData,true)."</td><td>{$action}</td><td>{$message}</td></tr>";
            }
            $body .="</tbody></table>";
            $sendEmailService->setBody($body);
            $result = $sendEmailService->Send();
            print_r($result);
        }
        return true;
    }

    /**
     * @desc 根据配置自动分表检测
     * @cli /usr/local/php/bin/php -c /usr/local/php/etc/php.cli.ini /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/autoCheck
     * @return bool
     * @author Shawn
     * @date 2019/4/17
     */
    public function autoCheck()
    {
        if(empty($this->checkTable)){
            p("需要检查表配置获取失败");
            return false;
        }
        p("需要检查表和字段".print_r($this->allTable));
        $i = 0;
        foreach ($this->checkTable as $key=>$value){
            $i++;
            p("开启数据表：".$key."进程");
            system("/usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/checkTableData/table/".$key." 2>/dev/null  >/dev/null & ");
            sleep(3);
        }
        p("所有进程开启完毕，总进程数：".$i);
        return true;
    }

    /**
     * @desc 检查数据表数据是否同步
     * @cli /usr/local/php/bin/php /opt/web/erpcnapi/erpcnapi/tcli.php /Mid/SyncErpData/checkTableData/table/$table/limit/5000
     * @author Shawn
     * @date 2019/4/17
     */
    public function checkTableData()
    {
        $table = trim(I("table"));
        $limit = (int)I("limit");
        if(empty($table)){
            exit("需要检查数据表为空");
        }
        if($limit == 0){
            $limit = 5000;
        }
        $log = '';
        set_time_limit(0);
        ini_set('memory_limit', '2000M');
        $msg = '检查需知：该检测程序以ERP数据为准，所以可能会存在仓库有数据，但是ERP没有数据。'.PHP_EOL;
        p($msg);
        $log .= $msg;
        $msg = '::::检查Table=>'.$table."::::开始时间：".date("Y-m-d H:i:s").PHP_EOL;
        $log .= $msg;
        p($msg);
        $file = dirname(dirname(THINK_PATH))."/log/mid/".date('Ymd').".{$table}.txt";
        $erpTableModel = M("{$table}",'','DB_CONFIG_ERP_READ');
        $wmsTableModel = M("{$table}",'','DB_CONFIG_READ');
        $field = trim($this->checkTable[$table]);
        $fieldArr = array_unique(explode(",",$field));
        $count = $erpTableModel->count();
        $msg = '检查field=>'.$field.PHP_EOL;
        $log .= $msg;
        $count = (int)$count;
        if($count == 0){
            $msg = "ERP数据总数：".$count.PHP_EOL.'::::检查Table=>'.$table."::::结束时间：".date("Y-m-d H:i:s").PHP_EOL;
            $log .= $msg;
            p($msg);
            writeFile($file,$log);exit;
        }
        $uniqueId = ($table == "ebay_goods") ? "goods_id" : "id";
        $page = ceil($count/$limit);
        $msg = "总数：".$count.',每页分页：'.$limit."分页总数：".$page.PHP_EOL;
        p($msg);
        $log .= $msg;
        for ($i=1;$i<=$page;$i++){
            $msg = "====页码：".$i."=>BEGIN====".PHP_EOL;
            p($msg);
            $log .= $msg;
            $begin = ($i-1)*$limit;
            $data = $erpTableModel
                ->field("{$field}")
                ->limit($begin.",".$limit)
                ->order("{$uniqueId} asc")
                ->select();
            if(empty($data)){
                $msg = "查询数据为空".PHP_EOL."====页码：".$i."=>END====".PHP_EOL;
                p($msg);
                $log .= $msg;
                continue;
            }
            foreach ($data as $value){
                $map[$uniqueId] = $value[$uniqueId];
                $wsmData = $wmsTableModel->where($map)->field("{$field}")->find();
                //先检查仓库是否有该数据，有的话在检查字段
                if(empty($wmsData)){
                    $msg = "仓库没有该条数据，{$uniqueId}：".$value[$uniqueId].PHP_EOL;
                    p($msg);
                    $log .= $msg;
                }else{
                    foreach ($fieldArr as $v){
                        if(!empty($v)){
                            $left = $value[$v];
                            $right = $wsmData[$v];
                            if($left !== $right){
                                $msg = "{$uniqueId}：".$value[$uniqueId]."==>field：{$v}数据不同步，ERP：".$left.",WMS：".$right.PHP_EOL;
                                p($msg);
                                $log .= $msg;
                            }
                        }
                    }
                }
            }
            $msg = "====页码：".$i."=>END====".PHP_EOL;
            p($msg);
            $log .= $msg;
        }
        $msg = '::::检查Table=>'.$table."::::结束时间：".date("Y-m-d H:i:s").PHP_EOL;
        $log .= $msg;
        p($msg);
        writeFile($file,$log);exit;
    }
}