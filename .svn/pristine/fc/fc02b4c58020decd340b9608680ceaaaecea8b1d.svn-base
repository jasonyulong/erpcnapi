<?php
function d2int($d){
	$int = intval($d);
	return $int;
}

function acount($arr){
	return count($arr);
}

//function money_format($m){
//	return number_format($m,2);
//}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr")){
        $slice = mb_substr($str, $start, $length, $charset);
        $strlen = mb_strlen($str,$charset);
    }elseif(function_exists('iconv_substr')){
        $slice = iconv_substr($str,$start,$length,$charset);
        $strlen = iconv_strlen($str,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        $strlen = count($match[0]);
    }
    if($suffix && $strlen>$length)$slice.='...';
    return $slice;
}

//处理方法
function rmdirr($dirname) {
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            //递归
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
}

//获取文件修改时间
function get_file_time($DataDir,$file) {
    $a = filemtime($DataDir . $file);
    $time = date("Y-m-d H:i:s", $a);
    return $time;
}

//获取文件的大小
function get_file_size($DataDir,$file) {
    $perms = stat($DataDir . $file);
    $size = $perms['size'];
    // 单位自动转换函数
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }
}

function sortTitle($title, $field) {
    $sortFlag = '';
    $sort = 'desc';
    if ($_REQUEST['_field'] == $field ) {
        if($_REQUEST['_order'] == 'asc') {
            $sortFlag = '↑';
            $sort = 'desc';
        } else {
            $sortFlag = '↓';
            $sort = 'asc';
        }
    }
    return '<a href="javascript:void(0);" onclick="sortTitle(\''.$field.'\', \''.$sort.'\');">'.$title.$sortFlag.'</a>';
}


/*
 * array_column兼容
 */
if( ! function_exists('array_column'))
{
  function array_column($input, $columnKey, $indexKey = NULL)
  {
    $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
    $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
    $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
    $result = array();
 
    foreach ((array)$input AS $key => $row)
    { 
      if ($columnKeyIsNumber)
      {
        $tmp = array_slice($row, $columnKey, 1);
        $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
      }
      else
      {
        $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
      }
      if ( ! $indexKeyIsNull)
      {
        if ($indexKeyIsNumber)
        {
          $key = array_slice($row, $indexKey, 1);
          $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
          $key = is_null($key) ? 0 : $key;
        }
        else
        {
          $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
        }
      }
 
      $result[$key] = $tmp;
    }
 
    return $result;
  }
}

/**
* 获取今天是本月第几个星期(以星期日为准,本月有多少个星期日,则有多少个星期)
*/
function getWeekNum($a){
    $monthDayCount = date('t',strtotime($a));

    //获取本月第一天
    $thisMonthFirstDay = strtotime(date('Y-m-01',strtotime($a)));
    $fisrtDayWeek = date('w',$thisMonthFirstDay);

    //第一个星期天是本月几号
    if($fisrtDayWeek != 0){
        $firstSunDay = (7-$fisrtDayWeek)+1;
    }else{
        $firstSunDay = 1;
    }

    //今天是多少号
    $todayJ = date('j',strtotime($a));
    //本月共有多少个星期天
    // return ceil(($todayJ-$firstSunDay)/7);
    $days = $todayJ-$firstSunDay;
    if($days>=0){
        if($days == 0){
            return array(
                'month'=>date('Ym',strtotime($a)),
                'weeknum'=>1
            );
        }
        return array(
            'month'=>date('Ym',strtotime($a)),
            'weeknum'=>ceil(($todayJ-$firstSunDay)/7)    
        );
    }else{
        $lastMonthCount = date('t',$thisMonthFirstDay-1);
        $lastMonthFirstDay = strtotime(date('Y-m-01',$thisMonthFirstDay-1));
        $lastMonthFisrtDayWeek = date('w',$lastMonthFirstDay);
        if($lastMonthFisrtDayWeek != 0){
            $lastMonthFirstSunDay = (7-$lastMonthFisrtDayWeek)+1;
        }else{
            $lastMonthFirstSunDay = 1;
        }
        //上月共有几个星期
        return array(
            'month'=>date('Ym',$thisMonthFirstDay-1),
            'weeknum'=>ceil(($lastMonthCount-$lastMonthFirstSunDay)/7)    
        );
    }
}

if(!function_exists('export_excel')){
    //导出excel文件
    function export_excel($filename, $excel_title, $data, $save_method='http', $save_path=''){

        set_time_limit(0);
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

//        require_once ROOT_PATH.'/Classes/PHPExcel.php';
        vendor('PHPExcel', VENDOR_PATH.'PHPExcel/', '.php');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
                    ->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");

// 可以直接将数组写入，不需要进行循环
        $alphabet = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        );
        //防止列数超过26个，拼接双字母组合到：HZ
        $tmp = $alphabet;
        for($i=0; $i<8; $i++){
            foreach ($tmp as $key => $value) {
                $alphabet[] = $tmp[$i] . $value;
            }
        }
        //循环每行
        $row = 1;
        foreach ($data as $key => $value) {
            //循环每列
            $col = 0;
            foreach ($value as $val) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$col].$row, $val);
                $col++;
            }
            $row++;
        }


        //直接将数组写入文件
//        $objPHPExcel->getActiveSheet()->fromArray($data);
        $objPHPExcel->getActiveSheet()->setTitle($excel_title);
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = trim($filename, '.xls');
        $filename = trim($filename, '.xlsx').'.xls';
        if($save_method == 'file'){
            //保存到文件
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $full_path = $save_path.$filename;
            //本地使用，线上注释
            //$full_path = iconv('utf-8', 'gbk//IGNORE', $full_path);
            if(is_file($full_path)) unlink($full_path);
            $objWriter->save($full_path);
            return $full_path;
        }else{
            //直接在浏览器输出
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename={$filename}");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            ob_end_clean();
        }
    }
}

/**
 * 读取csv文件
 */
if(!function_exists('read_csv')){
    function read_csv($filename){
        $data = array_map('str_getcsv', file($filename));
        array_walk($data, function(&$value){
            foreach ($value as $key => $val) {
                $value[$key] = iconv('gbk', 'utf-8//IGNORE', $val);
            }
        });
        return $data;
    }
}

/**
 * 导出csv文件
 */
function export_csv($filename, $data, $save_path='', $save_method='http'){

    $filename = trim($filename, '.csv');
    $filename = trim($filename, '.xls');
    $filename = trim($filename, '.xlsx').'.csv';
    $full_path = $save_path.$filename;

    //本地使用，线上注释
    //$full_path = iconv('utf-8', 'gbk//IGNORE', $full_path);
    if(is_file($full_path)) unlink($full_path);
    //打开文件，不存在则创建文件，a+是以读写方式打开，指针移动到文件最后，b是防止出现怪异情况
    $handle = fopen($full_path, 'w');
    //对正在写入的文件进行锁定
    flock($handle, LOCK_EX);

    array_walk($data, function(&$value){
        foreach ($value as $key => $val) {
            $value[$key] = iconv('utf-8', 'gbk//IGNORE', $val);
        }
    });

    //在页面下载
    if($save_method == 'http'){
        $content = '';
        foreach ($data as $key => $value) {
            $content .= implode(',', $value)."\n";
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);
        echo $content;
        return true;
    }

    $string_length = 0;    //保存写入字符数
    foreach ($data as $key => $value) {
        //将内容写入目标文件,fputcsv执行成功则返回写入的字节数。失败则返回 false。
        $flag = fputcsv($handle, $value);
        if($flag === false){
            return $flag;   //如果写入失败，则返回false
        }
        $string_length += $flag;
    }
    unset($data);

    //销毁主文件资源句柄，锁定会自动解除
    fclose($handle);
    unset($content);
    return $string_length;
}

/**
 * 将秒数转换成时间显示
 */
function secondToTime($seconds){
    if ($seconds > 3600){
        $hours = intval($seconds/3600);
        $minutes = $seconds % 3600;
        $time = $hours.":".gmstrftime('%M:%S', $minutes);
    }else{
        $time = gmstrftime('%H:%M:%S', $seconds);
    }
    return $time;
}

if(!function_exists('read_excel')){
    /**
     * 读取excel
     * @return array
     */
    function read_excel($filename){
        set_time_limit(600);
        ini_set('memory_limit', '600M');

        require_once ROOT_PATH.'/Classes/PHPExcel.php';
        //兼容2003和2007版本
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        if(!$objReader->canRead($filename)){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            if(!$objReader->canRead($filename)){
                return false;
            }
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); 
        $highestColumn = $objWorksheet->getHighestColumn(); 
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
        $excelData = array(); 
        for ($row = 1; $row <= $highestRow; $row++) { 
            for ($col = 0; $col < $highestColumnIndex; $col++) { 
                $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            } 
        }
        return $excelData;
    }
}

/**
 * CURL GET 请求
 * @param $url
 * @param int $timeout
 * @return mixed
 */
function curl_get($url,$timeout = 120){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}

/**
 * CURL POST 请求
 * @param $url
 * @param array $params
 * @param $timeout
 * @return mixed
 */
 function curl_post($url,array $params = array(),$timeout = 120){
    //初始化curl
    $ch = curl_init();
    //抓取指定网页
    curl_setopt($ch,CURLOPT_URL,$url);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //运行curl
    $result = curl_exec($ch);
    curl_close($ch);

    //输出结果
    return $result;
}

if (!function_exists('p')) {
    function p($data){
        //判断是否为命令行运行
        if(!empty($_SERVER['argv'])){
            //windows环境下：将数组或字符串转成gbk编码，解决命令行输出乱码问题
            if(strpos(strtolower(PHP_OS), 'win') !== false){
                //方法：var_export将数组变成php代码(字符串形式)，iconv转码，用eval执行php代码，得到新数组
                $data = eval('return '.iconv('utf-8', 'gbk//IGNORE', var_export($data, true).';'));
            }
            print_r($data);
            echo PHP_EOL;
        }else{
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        return true;
    }
}

if (!function_exists('writeFile')) {
    # 写日志；
    function writeFile($file, $str) {
        if(!empty($_SERVER['argv'])){// 命令行时候
            $file=str_replace('.txt','.cli.txt',$file);
        }
        $index = strripos($file, '/');
        if (!file_exists($file) && strripos($file, '/') !== false) {
            $fileDir = substr($file, 0, $index);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
                chmod($fileDir,0777); // 有写环境 mkdir 的 0777  无效
            }
        }
        file_put_contents($file, "\xEF\xBB\xBF" . $str, FILE_APPEND);
    }
}

/**
 * 判断当前用户是否具有参数指定的权限
 */
if(!function_exists('can'))
{
    function can($operation='')
    {
        $operations = session('power');
        $powers = explode(',' , $operations);

        if(!$operation)
        {
            return $powers;
        }

        return in_array($operation , $powers);
    }
}
/**
 * 删除文文件或路径
 * @param $path
 * @param bool $delDir
 * @return bool
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}

/**
 * 解析XML到数组
 * @param $xml
 *
 * @return mixed
 */
function xmlToArray($xml){
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring),true);
    return $val;
}

if(!function_exists('upload_file')){
    /**
     * 上传文件（默认上传excel）
     */
    function upload_file($path='', $maxSize=2097152, $exts=array('xls', 'xlsx')){
        //上传配置信息
        $config = array(
            'maxSize'    =>    $maxSize,    //上传大小
            'rootPath'   =>    ROOT_PATH.'/cache/xls/',//根路径 根路径居然也要哥哥有写入权限 Local.class.php
            'savePath'   =>    $path,       //保存路径
            'saveName'   =>    array('uniqid',''),//生成唯一文件名
            'exts'       =>    $exts,       //允许的扩展名
            'autoSub'    =>    false,       //生成子目录
            'subName'    =>    array('date','Ymd'),//子目录名称
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        // 上传文件
        $info = $upload->upload();
        //echo $upload->getError();
        return $info;
    }
}

if(!function_exists('delSku')){
    /**
     * 处理sku,去掉前4位,后4位
     * @param $str string sku
     * @return mixed
     */
    function delSku($sku){
        if($sku[0] === '0' && $sku[3] === '-') {
            $beforeStr = substr($sku,0,4);
            $result = explode('-0', str_replace($beforeStr, '', $sku))[0];
        }else{
            $result = explode('-0', $sku)[0];
        }
        return $result;
    }
}



if (!function_exists('array_group')) {

    /**
     * 将数组进行分组处理
     * @param $raw_arr    : 初始数组
     * @param $group_size : 每个分组的大小
     * @param $with_key   : 是否在第二维上保持原来的键名
     * @return array
     */
    function array_group($raw_arr, $group_size, $with_key = false)
    {
        $grouped = [];
        $i = 0;

        array_walk($raw_arr, function($item, $key) use ($group_size, &$grouped, &$i, $with_key) {
            if (count($grouped[$i]) < $group_size) {
                $with_key ? $grouped[$i][$key] = $item : $grouped[$i][] = $item;
            } else {
                $with_key ? $grouped[++$i][$key] = $item : $grouped[++$i][] = $item;
            }
        });

        # 最后一个未满的话补为空
        $counts = count($grouped);
        $lastCounts = count($grouped[--$counts]);
        while ($lastCounts++ < $group_size) {
            $grouped[$counts][] = '';
        }
        return $grouped;
    }
}



/*
 * 千年不变的 debug
 * */
if( ! function_exists('debug')){
    function debug($arr){
        echo '<pre>';
        print_r($arr);
        echo '<pre>';
    }
}
