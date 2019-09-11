<?php
/**
 * 正式环境数据库配置
 *
 * User: Administrator
 * Date: 2017/1/6
 * Time: 17:50
 */
return  array(
    //分布式数据库配置定义
    //'DB_DEPLOY_TYPE'=> 1, // 设置分布式数据库支持
    'DB_TYPE'	=>	'mysql',
    'DB_HOST'	=>	'dbhost',
    'DB_NAME'	=>	'erpapi',
    'DB_USER'	=>	'www',
    'DB_PWD'	=>	'123456',
    'DB_PORT'	=>	'3306',
    'DB_PREFIX'	=>	'',

    'DB_CONFIG2' => array(
        'DB_TYPE'  => 'mysql',
        'DB_HOST'	=>	'dbhost',
        'DB_NAME'	=>	'erpapi',
        'DB_USER'  => 'www',
        'DB_PWD'   => '123456',
        'DB_PORT'	=>	'3306',
        'DB_PREFIX'	=>	'',
    ),
    //读取erp读库 ，在任务erp代码统一化中有用，其他时候无用
    'DB_CONFIG_ERP_READ' => array(
        'DB_TYPE'   => 'mysql',
        'DB_HOST'   => 'dbhost',
        'DB_NAME'   => 'v3-all',
        'DB_USER'   => 'www',
        'DB_PWD'    => '123456',
        'DB_PORT'   => '3306',
        'DB_PREFIX' => '',
    ),

);