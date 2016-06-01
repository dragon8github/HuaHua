<?php

ini_set('date.timezone','Asia/Shanghai');

/**
* PHP log 类  
*/

class Log
{
  
    private $LogFile;
    private $logLevel;

    const DEBUG  = 100;
    const INFO   = 75;
    const NOTICE = 50;
    const WARNING =25;
    const ERROR   = 10;
    const CRITICAL = 5; 
    
    private function __construct()
	{  
        $this->logLevel = 100;
		$filepath = $_SESSION["APP_ROOT"]."/log/";
	    $filename = date("Y-m-d").".txt";
		if(!is_dir($filepath)) mkdir($filepath,'0777');
        $this->LogFile = @fopen($filepath.$filename,'a+');   
    } 

    public static function getInstance()
	{
        static $obj;
        if(!isset($obj)){
            $obj = new Log();
        }
        return $obj;
    }

    public function LogMessage($msg,$module = null,$logLevel = Log::DEBUG)
	{
         $time = date("Y-m-d H:i:s");
         $strLogLevel = $this->levelToString($logLevel);
         if(isset($module)){$module =  sprintf("\r\n归属模块：".$module."\r\n");}
		 $logLine = "\r\n-------------------------------  $time -------------------------------\r\n";
		 $logLine .= $module;
		 $logLine .= "\r\n错误信息：$msg\r\n";
		 $logLine .= "\r\n错误等级：$strLogLevel\r\n";
         fwrite($this->LogFile,$logLine);
    }

    public function levelToString($logLevel)
	{
         $ret = '[unknow]';
         switch ($logLevel){
                case LOG::DEBUG:
                     $ret = 'DEBUG';
                     break;
                case LOG::INFO:
                     $ret = 'INFO';
                     break;
                case LOG::NOTICE:
                     $ret = 'NOTICE';
                     break;
                case LOG::WARNING:
                     $ret = 'WARNING';
                     break;
                case LOG::ERROR:
                     $ret = 'ERROR';
                     break;
                case LOG::CRITICAL:
                     $ret = 'CRITICAL';
                     break;
         }
         return $ret;
    }
}
?>

<?php 
// $logIns = LOG::getInstance();
// $logIns->LogMessage("test",log::INFO,'myTest');
?>