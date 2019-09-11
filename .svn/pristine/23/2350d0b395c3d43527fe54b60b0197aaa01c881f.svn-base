<?php

//2016 -12 -16 跑 cli 专用的
ini_set("default_charset","UTF-8");

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//定义项目名称和路径
define('APP_PATH', dirname(__FILE__).'/newerp/Application/');
define('ROOT_PATH', dirname(__FILE__));

// 开启调试模式
define('APP_DEBUG',true);

define('APP_ENV', 'product');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ('RUNTIME_PATH', dirname(__FILE__). '/newerp/RuntimeCli/' );
//define ('APP_MODE','cli');
// 加载框架入口文件
require( dirname(__FILE__)."/newerp/ThinkPHP/ThinkPHP.php");