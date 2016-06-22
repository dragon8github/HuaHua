<?php

class CssLoader
{
    private $CSS_ROOT = ""; //SRC目录的路径

    public static function Jqm()
    {
        //...
       // echo sprintf("<link rel='stylesheet' href='%s'  />","https://cdn.bootcss.com/jquery-mobile/1.4.4/jquery.mobile.min.css");
        echo sprintf("<link rel='stylesheet' href='%s'  />",$_SESSION["STATIC_ROOT"]."/Css/jqm/jquery.mobile-1.4.5.min.css");
    }
    
    public static function autoComplete()
    {
        //...
        echo  sprintf("<link href='//cdn.bootcss.com/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css' rel='stylesheet'>");
    }
    
    public static function LoadCss($folder,$name)
    {
        echo sprintf("<link rel='stylesheet' href='%s'  />",$_SESSION["STATIC_ROOT"].'/Css/'.$folder.'/'.$name);
    }

}


?>