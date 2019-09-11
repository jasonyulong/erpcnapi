<?php

class CLog{

    protected $logFile = '';

    public static function init(){
        return new self;
    }

    public function __construct(){
        $this->logFile = __DIR__."/../log/debug_log/".date("Y-m-d").".log";
        if(!file_exists($this->logFile)){
            file_put_contents($this->logFile , '' , FILE_APPEND);
        }
    }

    public function write($message , $level='DEBUG'){
        $level = $this->judge_level($level);
        $message = print_r($message , true);
        $message = '[ '.date('Y-m-d H:i:s').' ] [ '.$level.' ] : '.$message."\n";
        file_put_contents($this->logFile , $message , FILE_APPEND);
    }

    public function judge_level($level){
        switch($level){
            case 'debug':
            case 'DEBUG':
                $resultLevel = 'DEBUG';
                break;
            case 'info':
            case 'INFO':
                $resultLevel = 'INFO';
                break;
            case 'error':
            case 'ERROR':
                $resultLevel = 'ERROR';
                break;
            case 'warning':
            case 'WARNING':
                $resultLevel = 'WARNING';
                break;
            default:
                $resultLevel = '';
                break;
        }
        return $resultLevel;
    }



}



