<?php

ini_set("default_charset","UTF-8");

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//定义项目名称和路径
define('APP_PATH', dirname(__FILE__).'/newerp/Application/');
define('ROOT_PATH', dirname(__FILE__));

// 开启调试模式
define('APP_DEBUG',true);

//环境 development/test/product
define('APP_ENV', 'development');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', dirname(__FILE__). '/newerp/Runtime/' );

// 加载框架入口文件
require( dirname(__FILE__)."/newerp/ThinkPHP/ThinkPHP.php");