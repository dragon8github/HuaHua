<?php

ini_set('date.timezone','Asia/Shanghai');

/**
* PHP log 类  
*/

class mylog
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
            $obj = new mylog();
        }
        return $obj;
    }
    
    public static function WriteLog($msg,$module = null,$logLevel = "DEBUG")
    {
        $filepath = $_SESSION["APP_ROOT"]."/log/";
        if(!is_dir($filepath)) mkdir($filepath,'0777');
        $MyLogFile = @fopen($filepath.date("Y-m-d").".txt",'a+');     
        
        $time = date("Y-m-d H:i:s");
        if(isset($module)){$module =  sprintf("\r\n归属模块：".$module."\r\n");}
        $logLine = "\r\n-------------------------------  $time -------------------------------\r\n";
        $logLine .= $module;
        $logLine .= "\r\n异常信息：$msg\r\n";
       $logLine .= "\r\n错误等级：$logLevel\r\n";
        fwrite($MyLogFile,$logLine);        
    }
    

    public  function LogMessage($msg,$module = null,$logLevel = mylog::DEBUG)
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
                case mylog::DEBUG:
                     $ret = 'DEBUG';
                     break;
                case mylog::INFO:
                     $ret = 'INFO';
                     break;
                case mylog::NOTICE:
                     $ret = 'NOTICE';
                     break;
                case mylog::WARNING:
                     $ret = 'WARNING';
                     break;
                case mylog::ERROR:
                     $ret = 'ERROR';
                     break;
                case mylog::CRITICAL:
                     $ret = 'CRITICAL';
                     break;
         }
         return $ret;
    }
}
?>

<?php 
//     include $_SESSION["APP_ROOT"].'/Lib/Class/Log.class.php';
//     $logIns = LOG::getInstance();
//     $logIns->LogMessage("测试一下"); 
//或者
//      mylog::WriteLog("可以了");
?>