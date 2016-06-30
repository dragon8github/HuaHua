<?php

header("Content-type: text/html; charset=utf-8");      


 class Lee
{
     //警告
    public static  function alert($str)
    {
        echo "<script type='text/javascript'>alert('".$str."')</script>";   
        
    }
    
    //页面跳转
    public static  function href($url)
    {
         echo "<script type='text/javascript'>window.location.href('".$url."')</script>";   
    }
    
    public static  function get_获取数组中指定键的值按照逗号隔开返回($arr,$key)
    {
        $ids = null;
        for ($i=0;$i<count($arr);$i++)
        {
            $ids .=  $arr[$i][$key] . ',';
        }
        //去除最后一个字符串“，”号
        $ids =  substr($ids, 0,-1);
        return $ids;
    }
    
        
       public static function xml_to_array($xml)                              
        {                                                        
          $array = (array)(simplexml_load_string($xml));         
          foreach ($array as $key=>$item){                       
            $array[$key]  = Lee::struct_to_array((array)$item);      
            
            
            
          }                                                      
          return $array;                                         
        }    
      public static  function struct_to_array($item) {                        
          if(!is_string($item)) {                                
            $item = (array)$item;                                
            foreach ($item as $key=>$val){                       
              $item[$key]  =  Lee::struct_to_array($val);             
            }                                                    
          }                                                      
          return $item;                                          
        }             
    
        public static function Sub_截取字符串如果超出某位就省略号($str,$num)
        {
            if(mb_strlen($str) > $num)
            {
                $str = substr($str,0,$num)."...";
            }
            return $str;
        }
        
    
    public static function Is_遍历数组中所有的值判断是否有空值($arr)
    {
        $Ok = true;
        foreach($arr as $k => $v)
        {
            if((string)$v == "")
            {
                $Ok = false;
                return false;
            }
        }
        return $Ok;
    }
    
    
    public static function mkFolder($path)
    {
        if(!is_readable($path))
        {
            is_file($path) or mkdir($path);
        }
    }
    
    //$re = chunk_split($str,1,",");
    public static function shuffle_打散并且洗牌字符串($str)
    {
        $re = explode(",",$str);
        shuffle($re);                           //随机重新排序数组
        $newstr = implode($re);         //把数据转为字符串
        return $newstr;
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
    
    
    public static function round($num)
    {
        return round($num,2);
    }
}


function pkcs5_pad ($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

function myCrypt($input,$key)
{
    $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $input =pkcs5_pad($input, $size);
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $data = base64_encode($data);
    return $data;
}

function myDecrypt($str,$key)
{
    $decrypted= mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $key,
        base64_decode($str),
        MCRYPT_MODE_ECB
    );

    $dec_s = strlen($decrypted);
    $padding = ord($decrypted[$dec_s-1]);
    $decrypted = substr($decrypted, 0, -$padding);
    return $decrypted;
}


function POST($get_key)
{

    $str=$_POST[$get_key];
    return Validata($str);
}

function GET($get_key)
{
    $str=$_GET[$get_key];
    return Validata($str);
}

function Validata($str)
{
    //如果值为空，那么返回空字符串
    if(!isset($str)) return "";
    //过滤html标记
    $farr = array("/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU");
    //过滤类似 <script>  <style> <object>  <meta> <iframe> 等
    $str = preg_replace($farr,"",$str);
    //对单引号、双引号等预定义字符 前面加上反斜杠 如'变成\'
    $str=addslashes($str);
    //过滤敏感词汇
    $str=str_replace(explode(",", UNSAFE_WORD),"***",$str);
    //返回结果
    return trim($str);
}


function IP()
{
    if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif(!empty($_SERVER["REMOTE_ADDR"])){
        $cip = $_SERVER["REMOTE_ADDR"];
    }
    else{
        $cip = "";
    }
    return $cip;
}


function AJAX($Msg,$Result,$Status)
{
    $arr = array("Msg" => $Msg,"Result" => $Result,"Status" => $Status);
    exit(json_encode($arr));
}


?>