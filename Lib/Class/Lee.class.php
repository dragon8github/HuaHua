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
}




?>