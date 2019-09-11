<?php

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
 
/*
 * 获取用户列表
 */
function getUserList(){
	$data = S('ebayUser');
	if(empty($data)){
		$data = M('ebay_user')->field('username,id')->select();
		S('ebayUser',$data,60);
	}
	return $data;
	
	
}

/*
 * 根据id 获取用户名称
 */
function getUserNameById($id){
	return M('ebay_user')->where(array('id'=>$id))->getField('username');
}

/*
 * 取二维数组差集 
 */
function array_diff_two_dimen($arr1,$arr2){
	foreach($arr1 as $v){
		if(!in_array($v,$arr2)){
			$arr[] = $v;
		}
	}
	return $arr;
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


if (!function_exists('is_store')) {
    defined('TYPE_STORE_NAME') ? : define('TYPE_STORE_NAME', 1);
    defined('TYPE_STORE_ID')   ? : define('TYPE_STORE_ID', 2);
    defined('TYPE_STORE_NAME_EN')? : define('TYPE_STORE_NAME_EN', 3);
    defined('TYPE_STORE_SN')   ? : define('TYPE_STORE_SN', 4);
    function is_store($value, $type = TYPE_STORE_ID)
    {
        switch ($type) {
            case TYPE_STORE_NAME:
                $condition = ['store_name'=>$value];
                break;
            case TYPE_STORE_ID:
                $condition = ['id'=>$value];
                break;
            case TYPE_STORE_NAME_EN:
                $condition = ['store_name_en'=> $value];
                break;
            case TYPE_STORE_SN:
                $condition = ['store_sn'=>$value];
                break;
            default:
                return false;
                break;
        }
        $store = M('ebay_store') -> field('id') -> where($condition) -> find();
        return !!$store;
    }
}

#################################################################################
#
# 此处定义的几个函数是 用于处理一次性session传递消息的操作，定义的消息创建之后读出一次即失效
#

# 定义第一维的键名：
define('MESSAGE_PREFIX', '_message_');


if (!function_exists('set_message')) {
    function set_message($message)
    {
        if(!is_array($message)) {
            return false;
        }
//        \Fun\dump($keys);
        if(array_key_exists('success', $message)) {
            session(MESSAGE_PREFIX.'.status', 1);
            session(MESSAGE_PREFIX.'.success', $message['success']);
        } elseif (array_key_exists('fail', $message)) {
            session(MESSAGE_PREFIX.'.status', 0);
            session(MESSAGE_PREFIX.'.fail', $message['fail']);
        } else {
            return false;
        }
        return true;
    }
}

if (!function_exists('has_message')) {
    function has_message()
    {
        return (session(MESSAGE_PREFIX.'.success') || session(MESSAGE_PREFIX.'.fail')) ? true : false;
    }
}

if (!function_exists('message_status')) {
    function message_status()
    {
        return session(MESSAGE_PREFIX.'.status');
    }
}

if (!function_exists('get_message')) {
    function get_message()
    {
        $key = MESSAGE_PREFIX.'.status';
        if (session('?'.$key) && session($key)) {
            $message = message_get_clean('success');
        } elseif (session('?'.$key) && !session($key)) {
            $message = message_get_clean('fail');
        } else {
            return false;
        }
        message_get_clean('status');
        return $message;
    }
}

if (!function_exists('session_get_clean')) {
    # 去除存储的session内容并删除之；
    function message_get_clean($key)
    {
        $key = MESSAGE_PREFIX.'.'.$key;
        if (session("?{$key}")) {
            $message = session($key);
            session($key, null);
            return $message;
        }
        return null;
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

if (!function_exists('resource_path')) {

    /**
     * @param string $path_to_file
     * @return string
     */
    function resource_path($path_to_file = '')
    {
        if (!$path_to_file) {
            return 'newerp/Public/resource';
        } else {
            return 'newerp/Public/resource/'.trim($path_to_file, ' /');
        }
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



if (!function_exists('field_filter')) {
    /**
     * @param $val
     * @return mixed
     */
    function field_filter(&$val) {
        return htmlspecialchars(trim($val));
    }
}


if (!function_exists('excel_data_trim')) {

    /**
     * Excel 文件数据导入时的数据的过滤函数,主要去除一些表格数据中的特殊字符
     * @param $data
     * @return mixed
     */
    function excel_data_trim($data) {
        return preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/",'' ,trim($data));
    }
}


if (!function_exists('is_between')) {

    /**
     * 检测 第一个值是不是在后面的两个值之间
     * @param $val   : 要检测的值
     * @param $area1 : 区间下限
     * @param $area2 : 区间上限
     * @return bool  : 返回检测结果
     */
    function is_between($val, $area1, $area2)
    {
        return $val >= $area1 && $area2 >= $val;
    }

}



if (!function_exists('file_log')) {

    function carrier_file_log($data, $carrierId, $label , $path = 'log/carrier/') {
        $user     = session('truename');
        $fileName = $path.date('Y-m-d').'.log';
        $time     = date('Y-m-d H:i:s');
        $headerData = <<<TEXT
\n\n\n===============================================================================
\t用户：{$user}; \n\t运输方式:{$carrierId}; \n\t修改时间:{$time};\n\tLabel: {$label}
===============================================================================\n
TEXT;
        file_put_contents($fileName, $headerData, FILE_APPEND);
        return file_put_contents($fileName, print_r($data, true), FILE_APPEND);
    }

    /**
     * @desc   条码生成函数
     * @author mina
     * @param  string readableString 条码号
     * @return string
     */
    if(!function_exists('str2barcode_font'))
    {
        function str2barcode_font($readableString)
        {
            $code128aMapping = array(' '=>' ','!'=>'!','"'=>'"','#'=>'#','$'=>'$','%'=>'%','&'=>'&','\''=>'\'','('=>'(',')'=>')','*'=>'*','+'=>'+',','=>',','-'=>'-','.'=>'.','/'=>'/','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',':'=>':',';'=>';','<'=>'<','='=>'=','>'=>'>','?'=>'?','@'=>'@','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','W'=>'W','X'=>'X','Y'=>'Y','Z'=>'Z','['=>'[','\\'=>'\\',']'=>']','^'=>'^','_'=>'_');
            $code128bMapping = array(' '=>' ','!'=>'!','"'=>'"','#'=>'#','$'=>'$','%'=>'%','&'=>'&','\''=>'\'','('=>'(',')'=>')','*'=>'*','+'=>'+',','=>',','-'=>'-','.'=>'.','/'=>'/','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',':'=>':',';'=>';','<'=>'<','='=>'=','>'=>'>','?'=>'?','@'=>'@','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V','W'=>'W','X'=>'X','Y'=>'Y','Z'=>'Z','['=>'[','\\'=>'\\',']'=>']','^'=>'^','_'=>'_','`'=>'`','a'=>'a','b'=>'b','c'=>'c','d'=>'d','e'=>'e','f'=>'f','g'=>'g','h'=>'h','i'=>'i','j'=>'j','k'=>'k','l'=>'l','m'=>'m','n'=>'n','o'=>'o','p'=>'p','q'=>'q','r'=>'r','s'=>'s','t'=>'t','u'=>'u','v'=>'v','w'=>'w','x'=>'x','y'=>'y','z'=>'z','{'=>'{','|'=>'|','}'=>'}','~'=>'~');
            $code128cMapping = array('00'=>'Â','01'=>'!','02'=>'"','03'=>'#','04'=>'$','05'=>'%','06'=>'&','07'=>'\'','08'=>'(','09'=>')','10'=>'*','11'=>'+','12'=>',','13'=>'-','14'=>'.','15'=>'/','16'=>'0','17'=>'1','18'=>'2','19'=>'3','20'=>'4','21'=>'5','22'=>'6','23'=>'7','24'=>'8','25'=>'9','26'=>':','27'=>';','28'=>'<','29'=>'=','30'=>'>','31'=>'?','32'=>'@','33'=>'A','34'=>'B','35'=>'C','36'=>'D','37'=>'E','38'=>'F','39'=>'G','40'=>'H','41'=>'I','42'=>'J','43'=>'K','44'=>'L','45'=>'M','46'=>'N','47'=>'O','48'=>'P','49'=>'Q','50'=>'R','51'=>'S','52'=>'T','53'=>'U','54'=>'V','55'=>'W','56'=>'X','57'=>'Y','58'=>'Z','59'=>'[','60'=>'\\','61'=>']','62'=>'^','63'=>'_','64'=>'`','65'=>'a','66'=>'b','67'=>'c','68'=>'d','69'=>'e','70'=>'f','71'=>'g','72'=>'h','73'=>'i','74'=>'j','75'=>'k','76'=>'l','77'=>'m','78'=>'n','79'=>'o','80'=>'p','81'=>'q','82'=>'r','83'=>'s','84'=>'t','85'=>'u','86'=>'v','87'=>'w','88'=>'x','89'=>'y','90'=>'z','91'=>'{','92'=>'|','93'=>'}','94'=>'~','95'=>'Ã','96'=>'Ä','97'=>'Å','98'=>'Æ','99'=>'Ç');
            $idToBarFontCharMapping=array('Â','!','"','#','$','%','&','\'','(',')','*','+',',','-','.','/','0','1','2','3','4','5','6','7','8','9',':',';','<','=','>','?','@','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[','\\',']','^','_','`','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','{','|','}','~','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í');
            $barFontCharToIdMapping=array_flip($idToBarFontCharMapping);
            $controlCharMapping = array('Code C'=>'Ç','Code B'=>'È','Code A'=>'É','FNC1'=>'Ê','Start A'=>'Ë','Start B'=>'Ì','Start C'=>'Í','Stop'=>'Î');

            $STARTB=false;
            $STARTC=false;

            $readableStringLenth=strlen($readableString);


            if($readableStringLenth>=4){
                $i=0;
                while(is_numeric($readableString[$i]) && $i<$readableStringLenth){
                    $i++;
                }

                if($i==$readableStringLenth){

                    $STARTC=true;
                }else{

                    $STARTB=true;
                }
            }else{

                $STARTB=true;
            }


            $barFontString="";
            $barFontCharIDarray=array();
            $barFontCharArray=array();
            $readableStringSplited=array();

            if($STARTC){
                $barFontChar=$controlCharMapping['Start C'];
                $barFontString.=$barFontCharArray[]=$barFontChar;
                $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                $readableStringSplited[]="Code128C起始符";
                for($i=0;$i<$readableStringLenth-1;$i+=2){
                    $code128cKey=$readableString[$i].$readableString[$i+1];
                    $barFontChar=$code128cMapping[$code128cKey];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]=$code128cKey;
                }
                if($readableStringLenth%2) {
                    $barFontChar=$controlCharMapping['Code B'];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]="切换至Code128B";
                    $barFontChar=$code128bMapping[$readableString[$readableStringLenth-1]];
                    $barFontString.=$barFontCharArray[]=$barFontChar;
                    $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                    $readableStringSplited[]=$readableString[$readableStringLenth-1];
                }
            }

            if($STARTB){
                $barFontChar=$controlCharMapping['Start B'];
                $barFontString.=$barFontCharArray[]=$barFontChar;
                $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                $readableStringSplited[]="Code128B起始符";

                $i=0;
                while($i<$readableStringLenth){
                    $j=$i;
                    while(is_numeric($readableString[$j])){
                        $j++;

                    }
                    $continuousNumericCharEndPos=$j;
                    $continuousNumericStringLen=$j-$i;
                    if($continuousNumericStringLen>=4){
                        if($continuousNumericStringLen%2){
                            $barFontChar=$code128bMapping[$readableString[$i]];
                            $barFontString.=$barFontCharArray[]=$barFontChar;
                            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                            $readableStringSplited[]=$readableString[$i];
                            $i++;
                        }

                        $barFontChar=$controlCharMapping['Code C'];
                        $barFontString.=$barFontCharArray[]=$barFontChar;
                        $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                        $readableStringSplited[]="切换至Code128C";



                        for(;$i<$continuousNumericCharEndPos;$i+=2){

                            $barFontChar=$code128cMapping[$readableString[$i].$readableString[$i+1]];
                            $barFontString.=$barFontCharArray[]=$barFontChar;
                            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                            $readableStringSplited[]=$readableString[$i].$readableString[$i+1];
                        }
                        if($i<$readableStringLenth){
                            $barFontChar=$controlCharMapping['Code B'];
                            $barFontString.=$barFontCharArray[]=$barFontChar;
                            $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                            $readableStringSplited[]="切换回Code128B";

                        }
                    }else{
                        $barFontChar=$code128bMapping[$readableString[$i]];
                        $barFontString.=$barFontCharArray[]=$barFontChar;
                        $barFontCharIDarray[]=$barFontCharToIdMapping[$barFontChar];
                        $readableStringSplited[]=$readableString[$i];
                        $i++;
                    }
                }
            }

            $checkSum=$barFontCharIDarray[0];

            for($i=1;$i<=count($barFontCharIDarray)-1;$i++){
                $checkSum+=$barFontCharIDarray[$i]*$i;
            }
            $checkNoBarFontCharID=$checkSum % 103;
            $barFontCharIDarray[]=$checkNoBarFontCharID;
            $checkNoBarFontChar=$idToBarFontCharMapping[$checkNoBarFontCharID];
            $barFontString.=$barFontCharArray[]=$checkNoBarFontChar;
            $readableStringSplited[]="校验位";


            $barFontString.=$barFontCharArray[]= $controlCharMapping['Stop'];
            $readableStringSplited[]="结束符";
            return array($barFontString,$readableStringSplited,$barFontCharArray,$barFontCharIDarray);
        }
    }
}

