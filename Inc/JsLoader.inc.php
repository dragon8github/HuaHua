<?php

class JsLoader
{       
   
    public static function LoadDirective($ProName,$FileName) 
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>",$_SESSION["STATIC_ROOT"].'/Directive/'.$ProName."/".$FileName."?v=".rand(0,9999));
    }
    
    public static function LoadJs($ProName,$FileName)
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>",$_SESSION["STATIC_ROOT"].'/Src/'.$ProName."/".$FileName);
    }
    
    public static function Jquery()
    {
        //...    
       echo  sprintf("<script type='text/javascript' src='%s'></script>","https://cdn.bootcss.com/jquery/1.10.2/jquery.min.js");
    }
    
    public static function Jqm()
    {
        //...
        echo  sprintf("<script type='text/javascript' src='%s'></script>","https://cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.js");
    }
    
    public static function Layer()
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>",$_SESSION["STATIC_ROOT"].'/Src/Layer/layer/layer.js');
    }
    
    public static function weixin()
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>","http://res.wx.qq.com/open/js/jweixin-1.0.0.js");
    }
    
    public static function wilddog()
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>","https://cdn.wilddog.com/js/client/current/wilddog.js");
    }
    
    public static function debug()
    {
        echo  sprintf("<script type='text/javascript' src='%s'></script>",$_SESSION["STATIC_ROOT"].'/Src/Debug/debug.js');
    }
    
}


?>