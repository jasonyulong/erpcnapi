<?php
/**
 * 自动加载类
 *
 * Created by PhpStorm.
 * User: mike
 * Date: 16/10/8
 * Time: 下午2:15
 */
class autoload {

    /**
     * 加载
     * @param $classname
     */
    public static function load($classname) {
        $filename = sprintf('%s.php',str_replace('\\','/',$classname));
        $filePath = VENDOR_PATH.$filename;

        if(is_file($filePath)) {
            require_once $filePath;
        }
    }

}

spl_autoload_register(['autoload','load']);